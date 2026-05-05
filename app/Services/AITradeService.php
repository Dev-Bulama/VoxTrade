<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Setting;
use App\Models\Trade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AITradeService
{
    /**
     * Map trading symbols to CoinGecko coin IDs.
     */
    private array $coinGeckoMap = [
        'BTCUSDT' => 'bitcoin',
        'ETHUSDT' => 'ethereum',
        'BNBUSDT' => 'binancecoin',
        'SOLUSDT' => 'solana',
        'XRPUSDT' => 'ripple',
        'ADAUSDT' => 'cardano',
    ];

    /**
     * Fetch crypto OHLC data from CoinGecko (no API key required).
     * Returns hourly close prices for the last 2 days.
     */
    public function fetchCryptoData(string $symbol): ?array
    {
        $coinId = $this->coinGeckoMap[strtoupper($symbol)] ?? null;

        if (!$coinId) {
            Log::warning("No CoinGecko mapping for symbol {$symbol}");
            return null;
        }

        try {
            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->timeout(15)
                ->get("https://api.coingecko.com/api/v3/coins/{$coinId}/market_chart", [
                    'vs_currency' => 'usd',
                    'days'        => '2',
                    'interval'    => 'hourly',
                ]);

            if (!$response->successful()) {
                Log::warning("CoinGecko request failed for {$symbol}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data        = $response->json();
            $pricePoints = $data['prices'] ?? [];
            $volPoints   = $data['total_volumes'] ?? [];

            if (empty($pricePoints)) {
                return null;
            }

            $prices       = array_map(fn($p) => (float) $p[1], $pricePoints);
            $currentPrice = end($prices);
            $volume       = !empty($volPoints) ? (float) end($volPoints)[1] : null;

            return [
                'symbol'        => $symbol,
                'prices'        => $prices,
                'current_price' => $currentPrice,
                'volume'        => $volume,
            ];
        } catch (\Throwable $e) {
            Log::error("Exception fetching CoinGecko data for {$symbol}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch forex/commodity data from Yahoo Finance (no API key, no rate limits).
     */
    public function fetchForexData(string $fromCurrency, string $toCurrency): ?array
    {
        // Build Yahoo Finance symbol; gold/silver use futures codes
        $from = strtoupper($fromCurrency);
        $to   = strtoupper($toCurrency);
        $sym  = match(true) {
            $from === 'XAU' => 'GC=F',   // Gold futures
            $from === 'XAG' => 'SI=F',   // Silver futures
            $from === 'OIL' => 'CL=F',   // Crude oil futures
            default         => "{$from}{$to}=X",
        };

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept'     => 'application/json',
            ])->timeout(15)->get("https://query1.finance.yahoo.com/v8/finance/chart/{$sym}", [
                'interval' => '1h',
                'range'    => '2d',
            ]);

            if (!$response->successful()) {
                Log::warning("Yahoo Finance failed for {$sym}", ['status' => $response->status()]);
                return null;
            }

            $chartData = $response->json('chart.result.0');
            if (!$chartData) return null;

            $closes = array_values(array_filter(
                $chartData['indicators']['quote'][0]['close'] ?? [],
                fn($v) => $v !== null
            ));

            if (empty($closes)) return null;

            return [
                'symbol'        => "{$from}{$to}",
                'prices'        => $closes,
                'current_price' => end($closes),
                'volume'        => null,
            ];
        } catch (\Throwable $e) {
            Log::error("Exception fetching Yahoo Finance {$sym}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate RSI from a price array.
     */
    public function calculateRSI(array $prices, int $period = 14): float
    {
        if (count($prices) < $period + 1) {
            return 50.0;
        }

        $gains  = [];
        $losses = [];

        for ($i = 1; $i <= $period; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $gains[]  = max(0, $change);
            $losses[] = max(0, -$change);
        }

        $avgGain = array_sum($gains) / $period;
        $avgLoss = array_sum($losses) / $period;

        $start = $period + 1;
        $total = count($prices);

        for ($i = $start; $i < $total; $i++) {
            $change   = $prices[$i] - $prices[$i - 1];
            $gain     = max(0, $change);
            $loss     = max(0, -$change);
            $avgGain  = (($avgGain * ($period - 1)) + $gain) / $period;
            $avgLoss  = (($avgLoss * ($period - 1)) + $loss) / $period;
        }

        if ($avgLoss == 0) {
            return 100.0;
        }

        $rs  = $avgGain / $avgLoss;
        $rsi = 100 - (100 / (1 + $rs));

        return round($rsi, 2);
    }

    /**
     * Calculate EMA for a price array.
     */
    public function calculateEMA(array $prices, int $period): float
    {
        if (count($prices) < $period) {
            return end($prices) ?: 0.0;
        }

        $k   = 2 / ($period + 1);
        $ema = array_sum(array_slice($prices, 0, $period)) / $period;

        for ($i = $period; $i < count($prices); $i++) {
            $ema = ($prices[$i] * $k) + ($ema * (1 - $k));
        }

        return round($ema, 8);
    }

    /**
     * Determine market trend: bullish, bearish, or neutral.
     */
    public function determineTrend(array $prices): string
    {
        if (count($prices) < 10) {
            return 'neutral';
        }

        $emas = [];
        $total = count($prices);

        for ($i = $total - 5; $i < $total; $i++) {
            $emas[] = $this->calculateEMA(array_slice($prices, 0, $i + 1), 9);
        }

        $rising  = 0;
        $falling = 0;

        for ($i = 1; $i < count($emas); $i++) {
            if ($emas[$i] > $emas[$i - 1]) {
                $rising++;
            } elseif ($emas[$i] < $emas[$i - 1]) {
                $falling++;
            }
        }

        if ($rising >= 3) {
            return 'bullish';
        }

        if ($falling >= 3) {
            return 'bearish';
        }

        return 'neutral';
    }

    /**
     * Build a prompt for OpenAI market analysis, including past trade outcomes
     * so the model can calibrate its future prediction accuracy.
     */
    public function buildAnalysisPrompt(array $marketData): string
    {
        $symbol       = $marketData['symbol'] ?? 'UNKNOWN';
        $displayPair  = $marketData['display_pair'] ?? $symbol;
        $currentPrice = $marketData['current_price'] ?? 0;
        $rsi          = $marketData['rsi'] ?? 50;
        $ema9         = $marketData['ema9'] ?? $currentPrice;
        $ema21        = $marketData['ema21'] ?? $currentPrice;
        $trend        = $marketData['trend'] ?? 'neutral';
        $volume       = $marketData['volume'] ?? 'N/A';
        $category     = $marketData['category'] ?? 'crypto';

        // Pull recent closed trades for this pair to inform the prediction
        $pastTrades = Trade::where('pair', $displayPair)
            ->whereIn('status', ['tp_hit', 'sl_hit'])
            ->latest()
            ->take(10)
            ->get(['type', 'confidence', 'status', 'created_at']);

        $pastContext = '';
        if ($pastTrades->isNotEmpty()) {
            $wins    = $pastTrades->where('status', 'tp_hit')->count();
            $losses  = $pastTrades->where('status', 'sl_hit')->count();
            $total   = $pastTrades->count();
            $winRate = round($wins / $total * 100);
            $avgConf = round($pastTrades->avg('confidence'));

            $history = $pastTrades->map(fn($t) =>
                "{$t->type} → " . ($t->status === 'tp_hit' ? 'WIN' : 'LOSS') . " (conf: {$t->confidence}%)"
            )->implode(', ');

            $pastContext = <<<PAST

Past performance for {$displayPair} (last {$total} closed trades):
- Win rate: {$winRate}% ({$wins}W / {$losses}L)
- Average confidence on past signals: {$avgConf}%
- Recent outcomes: {$history}

Use this history to calibrate your confidence. Only issue signals you believe have ≥75% probability of hitting take profit before stop loss. If current conditions are ambiguous, set confidence lower than 70.
PAST;
        }

        return <<<PROMPT
You are an elite quantitative trading analyst generating a FORWARD-LOOKING trade signal for {$displayPair}.

Current Market Data ({$category}):
- Pair: {$displayPair}
- Price: {$currentPrice}
- RSI (14): {$rsi}
- EMA 9: {$ema9}  |  EMA 21: {$ema21}
- Trend: {$trend}
- Volume: {$volume}
{$pastContext}

Signal Timeframe — choose ONE based on how strong the setup is:
• SCALP  → "2 minutes" | "5 minutes" | "15 minutes" | "30 minutes"
  (use only when RSI is extreme >80 or <20 with strong immediate momentum)
• SHORT  → "1 hour" | "2 hours" | "3 hours" | "4 hours"
  (clear EMA cross + confirming RSI, intraday move)
• MID    → "6 hours" | "8 hours" | "12 hours"
  (strong trend continuation with volume)
• DAILY  → "1 day" | "2 days"
  (multi-session trend, solid confluence across indicators)
• SWING  → "3 days" | "5 days"
  (major level break or sustained trend, weekly alignment)

Rules:
1. Only issue a signal when you are genuinely confident (≥70%).
2. Stop loss MUST be at a meaningful technical level (support/resistance, not arbitrary %).
3. Risk:reward MUST be at least 1:1.5 (take profit ≥ 1.5× the stop distance).
4. Shorter durations require higher certainty — scalp signals need RSI extremes + volume.
5. If conditions are mixed or unclear, set confidence below 70 — the system will reject it.

Respond ONLY with a valid JSON object, no markdown, no extra text:
{
  "pair": "{$displayPair}",
  "type": "BUY or SELL",
  "entry": <number>,
  "stop_loss": <number>,
  "take_profit": <number>,
  "confidence": <integer 0-100>,
  "duration": "<exact string from the list above, e.g. '5 minutes' or '4 hours' or '2 days'>",
  "risk_level": "<low | medium | high>",
  "analysis_summary": "<2-3 sentences: what the indicators say and why price will move this direction>"
}
PROMPT;
    }

    /**
     * Call OpenAI API with market data and return a structured signal or null.
     */
    public function analyzeWithOpenAI(array $marketData): ?array
    {
        try {
            $apiKey = ApiKey::where('service_name', 'openai')->value('api_key')
                ?? config('services.openai.key', '');

            if (empty($apiKey)) {
                Log::warning('OpenAI API key not configured.');
                return null;
            }

            $prompt = $this->buildAnalysisPrompt($marketData);

            $response = Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'messages'    => [
                        [
                            'role'    => 'system',
                            'content' => 'You are a professional trading signal analyst. Always respond with valid JSON only, no markdown, no extra text.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens'  => 500,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAI API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (empty($content)) {
                Log::warning('OpenAI returned empty content.');
                return null;
            }

            // Strip any accidental markdown fences
            $content = trim(preg_replace('/^```(?:json)?\s*/i', '', $content));
            $content = trim(preg_replace('/\s*```$/', '', $content));

            $signal = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse OpenAI JSON response.', ['content' => $content]);
                return null;
            }

            // Validate required fields
            $required = ['pair', 'type', 'entry', 'stop_loss', 'take_profit', 'confidence', 'duration', 'risk_level', 'analysis_summary'];
            foreach ($required as $field) {
                if (!array_key_exists($field, $signal)) {
                    Log::warning("OpenAI signal missing field: {$field}", ['signal' => $signal]);
                    return null;
                }
            }

            return $signal;
        } catch (\Throwable $e) {
            Log::error('Exception calling OpenAI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse a duration string into minutes.
     * Handles: "2 minutes", "5m", "1 hour", "4hr", "1 day", "2-3 days", "Intraday", "Swing"
     */
    public static function parseDurationMinutes(string $duration): int
    {
        $d = strtolower(trim($duration));

        // Minutes: "5 minutes", "15 min", "30m", "2mins"
        if (preg_match('/(\d+)\s*min/i', $d, $m)) {
            return max(1, (int) $m[1]);
        }

        // Hours: "1 hour", "2 hours", "1hr", "4h", "1.5 hours"
        if (preg_match('/(\d+(?:\.\d+)?)\s*h(?:ou?rs?|r)?(?:\s|$)/i', $d, $m)) {
            return (int) round((float) $m[1] * 60);
        }

        // Days: "1 day", "2 days", "2-3 days" (use lower bound)
        if (preg_match('/(\d+)(?:-\d+)?\s*day/i', $d, $m)) {
            return (int) $m[1] * 1440;
        }

        if (str_contains($d, 'scalp'))   return 5;
        if (str_contains($d, 'intraday')) return 240;
        if (str_contains($d, 'swing'))   return 2880;

        return 240; // default 4 hours
    }

    /**
     * Return true if the pair already has an unexpired active signal.
     */
    private function hasFreshSignal(string $pair): bool
    {
        $active = Trade::where('pair', $pair)->where('status', 'active')->latest()->first();
        if (!$active) return false;
        $expiresAt = $active->created_at->addMinutes(self::parseDurationMinutes($active->duration ?? ''));
        return $expiresAt->isFuture();
    }

    /**
     * Expire all active signals whose duration window has passed.
     */
    private function expireStaleSignals(): void
    {
        $active = Trade::where('status', 'active')->get(['id', 'duration', 'created_at']);
        $expiredIds = [];
        foreach ($active as $trade) {
            $expiresAt = $trade->created_at->addMinutes(self::parseDurationMinutes($trade->duration ?? ''));
            if ($expiresAt->isPast()) {
                $expiredIds[] = $trade->id;
            }
        }
        if (!empty($expiredIds)) {
            Trade::whereIn('id', $expiredIds)->update(['status' => 'expired']);
            Log::info('AITradeService: duration-expired ' . count($expiredIds) . ' signal(s).');
        }
    }
    public static function watchedPairs(): array
    {
        return [
            // Crypto
            ['type' => 'crypto', 'symbol' => 'BTCUSDT', 'display' => 'BTC/USDT'],
            ['type' => 'crypto', 'symbol' => 'ETHUSDT', 'display' => 'ETH/USDT'],
            ['type' => 'crypto', 'symbol' => 'BNBUSDT', 'display' => 'BNB/USDT'],
            ['type' => 'crypto', 'symbol' => 'SOLUSDT', 'display' => 'SOL/USDT'],
            ['type' => 'crypto', 'symbol' => 'XRPUSDT', 'display' => 'XRP/USDT'],
            ['type' => 'crypto', 'symbol' => 'ADAUSDT', 'display' => 'ADA/USDT'],
            // Forex & Commodities
            ['type' => 'forex', 'from' => 'EUR', 'to' => 'USD', 'display' => 'EUR/USD'],
            ['type' => 'forex', 'from' => 'GBP', 'to' => 'USD', 'display' => 'GBP/USD'],
            ['type' => 'forex', 'from' => 'USD', 'to' => 'JPY', 'display' => 'USD/JPY'],
            ['type' => 'forex', 'from' => 'XAU', 'to' => 'USD', 'display' => 'XAU/USD'],
            ['type' => 'forex', 'from' => 'AUD', 'to' => 'USD', 'display' => 'AUD/USD'],
            ['type' => 'forex', 'from' => 'GBP', 'to' => 'JPY', 'display' => 'GBP/JPY'],
            ['type' => 'forex', 'from' => 'USD', 'to' => 'CHF', 'display' => 'USD/CHF'],
            ['type' => 'forex', 'from' => 'EUR', 'to' => 'JPY', 'display' => 'EUR/JPY'],
        ];
    }

    /**
     * Generate signals for all watched pairs.
     */
    public function generateSignals(): array
    {
        $cryptoPairs = array_filter(self::watchedPairs(), fn($p) => $p['type'] === 'crypto');
        $forexPairs  = array_filter(self::watchedPairs(), fn($p) => $p['type'] === 'forex');

        $created = [];

        // Expire stale signals first so hasFreshSignal() sees clean state
        $this->expireStaleSignals();

        foreach ($cryptoPairs as $crypto) {
            if ($this->hasFreshSignal($crypto['display'])) {
                Log::info("AITradeService: skipping {$crypto['display']} — unexpired signal exists.");
                continue;
            }
            $trade = $this->processPair($crypto['symbol'], 'crypto', $crypto['display']);
            if ($trade) $created[] = $trade;
        }

        foreach ($forexPairs as $forex) {
            if ($this->hasFreshSignal($forex['display'])) {
                Log::info("AITradeService: skipping {$forex['display']} — unexpired signal exists.");
                continue;
            }
            $trade = $this->processForexPair($forex['from'], $forex['to'], $forex['display']);
            if ($trade) $created[] = $trade;
        }

        Log::info('AITradeService: generated ' . count($created) . ' signals.');

        return $created;
    }

    /**
     * Process a single crypto pair and store the signal as a Trade record.
     */
    public function processPair(string $symbol, string $category, string $displayPair = ''): ?Trade
    {
        try {
            $marketData = $this->fetchCryptoData($symbol);

            if (!$marketData || empty($marketData['prices'])) {
                Log::warning("No market data for crypto pair: {$symbol}");
                return null;
            }

            return $this->buildAndStoreTrade($marketData, $category, $displayPair ?: $symbol);
        } catch (\Throwable $e) {
            Log::error("Exception processing crypto pair {$symbol}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Process a single forex pair.
     */
    public function processForexPair(string $from, string $to, string $displayPair): ?Trade
    {
        try {
            $marketData = $this->fetchForexData($from, $to);

            if (!$marketData || empty($marketData['prices'])) {
                Log::warning("No market data for forex pair: {$from}/{$to}");
                return null;
            }

            return $this->buildAndStoreTrade($marketData, 'forex', $displayPair);
        } catch (\Throwable $e) {
            Log::error("Exception processing forex pair {$from}/{$to}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Enrich market data with indicators, call OpenAI, and persist the signal.
     */
    private function buildAndStoreTrade(array $marketData, string $category, string $displayPair): ?Trade
    {
        $prices = $marketData['prices'];

        $marketData['display_pair'] = $displayPair;
        $marketData['rsi']          = $this->calculateRSI($prices);
        $marketData['ema9']         = $this->calculateEMA($prices, 9);
        $marketData['ema21']        = $this->calculateEMA($prices, 21);
        $marketData['trend']        = $this->determineTrend($prices);
        $marketData['category']     = $category;

        $signal = $this->analyzeWithOpenAI($marketData);

        if (!$signal) {
            Log::warning("No signal generated for pair: {$displayPair}");
            return null;
        }

        if ((int) $signal['confidence'] < 70) {
            Log::info("Signal confidence too low for {$displayPair}: {$signal['confidence']}");
            return null;
        }

        $trade = Trade::create([
            'pair'             => $displayPair,
            'type'             => strtoupper($signal['type']),
            'entry_price'      => $signal['entry'],
            'stop_loss'        => $signal['stop_loss'],
            'take_profit'      => $signal['take_profit'],
            'confidence'       => min(100, (int) $signal['confidence']),
            'duration'         => $signal['duration'],
            'risk_level'       => $signal['risk_level'],
            'analysis_summary' => $signal['analysis_summary'],
            'category'         => $category,
            'status'           => 'active',
        ]);

        Log::info("Trade created for {$displayPair}: ID {$trade->id}, confidence {$signal['confidence']}%");

        return $trade;
    }
}
