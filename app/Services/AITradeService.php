<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Trade;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AITradeService
{
    /**
     * Fetch market data from Binance for crypto pairs.
     */
    public function fetchBinanceData(string $symbol): ?array
    {
        try {
            $response = Http::get('https://api.binance.com/api/v3/klines', [
                'symbol'   => $symbol,
                'interval' => '1h',
                'limit'    => 50,
            ]);

            if (!$response->successful()) {
                Log::warning("Binance API request failed for {$symbol}", ['status' => $response->status()]);
                return null;
            }

            $klines = $response->json();

            if (empty($klines)) {
                return null;
            }

            $prices = array_map(fn($k) => (float) $k[4], $klines); // close prices
            $volume = (float) end($klines)[5];
            $currentPrice = end($prices);

            return [
                'symbol'        => $symbol,
                'prices'        => $prices,
                'current_price' => $currentPrice,
                'volume'        => $volume,
            ];
        } catch (\Throwable $e) {
            Log::error("Exception fetching Binance data for {$symbol}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch forex data from Alpha Vantage.
     */
    public function fetchForexData(string $fromCurrency, string $toCurrency): ?array
    {
        try {
            $apiKey = ApiKey::where('service_name', 'alpha_vantage')->value('api_key')
                ?? config('services.alphavantage.key', '');

            $response = Http::get('https://www.alphavantage.co/query', [
                'function'    => 'FX_INTRADAY',
                'from_symbol' => $fromCurrency,
                'to_symbol'   => $toCurrency,
                'interval'    => '60min',
                'apikey'      => $apiKey,
            ]);

            if (!$response->successful()) {
                Log::warning("Alpha Vantage request failed for {$fromCurrency}/{$toCurrency}");
                return null;
            }

            $data = $response->json();
            $timeSeriesKey = 'Time Series FX (60min)';

            if (empty($data[$timeSeriesKey])) {
                Log::warning("Alpha Vantage returned no time series for {$fromCurrency}/{$toCurrency}", ['response' => $data]);
                return null;
            }

            $timeSeries = $data[$timeSeriesKey];
            // Sort by time ascending
            ksort($timeSeries);

            $prices = array_map(fn($bar) => (float) $bar['4. close'], array_values($timeSeries));
            $currentPrice = end($prices);

            return [
                'symbol'        => $fromCurrency . $toCurrency,
                'prices'        => $prices,
                'current_price' => $currentPrice,
                'volume'        => null,
            ];
        } catch (\Throwable $e) {
            Log::error("Exception fetching Alpha Vantage data for {$fromCurrency}/{$toCurrency}: " . $e->getMessage());
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
     * Build a prompt for OpenAI market analysis.
     */
    public function buildAnalysisPrompt(array $marketData): string
    {
        $symbol       = $marketData['symbol'] ?? 'UNKNOWN';
        $currentPrice = $marketData['current_price'] ?? 0;
        $rsi          = $marketData['rsi'] ?? 50;
        $ema9         = $marketData['ema9'] ?? $currentPrice;
        $ema21        = $marketData['ema21'] ?? $currentPrice;
        $trend        = $marketData['trend'] ?? 'neutral';
        $volume       = $marketData['volume'] ?? 'N/A';
        $category     = $marketData['category'] ?? 'crypto';

        return <<<PROMPT
You are an expert financial analyst specializing in {$category} trading signals.

Analyze the following market data for {$symbol} and generate a trading signal.

Market Data:
- Symbol: {$symbol}
- Current Price: {$currentPrice}
- RSI (14): {$rsi}
- EMA 9: {$ema9}
- EMA 21: {$ema21}
- Trend: {$trend}
- Volume: {$volume}

Based on this data, provide a trading signal. Respond ONLY with a valid JSON object in this exact format:
{
  "pair": "{$symbol}",
  "type": "BUY or SELL",
  "entry": <entry price as number>,
  "stop_loss": <stop loss price as number>,
  "take_profit": <take profit price as number>,
  "confidence": <confidence score 0-100 as integer>,
  "duration": "<suggested trade duration e.g. 4h, 1d>",
  "risk_level": "<low, medium, or high>",
  "analysis_summary": "<brief explanation of the signal>"
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
     * Generate signals for all default crypto and forex pairs.
     */
    public function generateSignals(): array
    {
        $cryptoPairs = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT'];
        $forexPairs  = [
            ['from' => 'EUR', 'to' => 'USD', 'pair' => 'EURUSD'],
            ['from' => 'GBP', 'to' => 'USD', 'pair' => 'GBPUSD'],
            ['from' => 'USD', 'to' => 'JPY', 'pair' => 'USDJPY'],
        ];

        $created = [];

        foreach ($cryptoPairs as $symbol) {
            $trade = $this->processPair($symbol, 'crypto');
            if ($trade) {
                $created[] = $trade;
            }
        }

        foreach ($forexPairs as $forex) {
            $trade = $this->processPair($forex['pair'], 'forex');
            if ($trade) {
                $created[] = $trade;
            }
        }

        Log::info('AITradeService: generated ' . count($created) . ' signals.');

        return $created;
    }

    /**
     * Process a single trading pair and store the signal as a Trade record.
     */
    public function processPair(string $pair, string $category): ?Trade
    {
        try {
            // Fetch market data
            if ($category === 'forex' && strlen($pair) === 6) {
                $from       = substr($pair, 0, 3);
                $to         = substr($pair, 3, 3);
                $marketData = $this->fetchForexData($from, $to);
            } else {
                $marketData = $this->fetchBinanceData($pair);
            }

            if (!$marketData || empty($marketData['prices'])) {
                Log::warning("No market data for pair: {$pair}");
                return null;
            }

            $prices = $marketData['prices'];

            // Enrich market data with indicators
            $marketData['rsi']      = $this->calculateRSI($prices);
            $marketData['ema9']     = $this->calculateEMA($prices, 9);
            $marketData['ema21']    = $this->calculateEMA($prices, 21);
            $marketData['trend']    = $this->determineTrend($prices);
            $marketData['category'] = $category;

            // Analyze with OpenAI
            $signal = $this->analyzeWithOpenAI($marketData);

            if (!$signal) {
                Log::warning("No signal generated for pair: {$pair}");
                return null;
            }

            // Only store signals with sufficient confidence
            if ((int) $signal['confidence'] < 60) {
                Log::info("Signal confidence too low for {$pair}: {$signal['confidence']}");
                return null;
            }

            // Create Trade record
            $trade = Trade::create([
                'pair'             => $signal['pair'],
                'type'             => strtoupper($signal['type']),
                'entry'            => $signal['entry'],
                'stop_loss'        => $signal['stop_loss'],
                'take_profit'      => $signal['take_profit'],
                'confidence'       => $signal['confidence'],
                'duration'         => $signal['duration'],
                'risk_level'       => $signal['risk_level'],
                'analysis_summary' => $signal['analysis_summary'],
                'category'         => $category,
                'status'           => 'active',
                'rsi'              => $marketData['rsi'],
                'ema9'             => $marketData['ema9'],
                'ema21'            => $marketData['ema21'],
                'trend'            => $marketData['trend'],
            ]);

            Log::info("Trade created for {$pair}: ID {$trade->id}, confidence {$signal['confidence']}");

            return $trade;
        } catch (\Throwable $e) {
            Log::error("Exception processing pair {$pair}: " . $e->getMessage());
            return null;
        }
    }
}
