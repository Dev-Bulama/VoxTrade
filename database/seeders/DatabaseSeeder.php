<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // -----------------------------------------------------------------
        // Admin user
        // -----------------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@voxtrade.io'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // -----------------------------------------------------------------
        // Default settings
        // -----------------------------------------------------------------
        $settings = [
            ['key' => 'site_name',          'value' => 'VoxTrade',                                                                                                                'type' => 'text'],
            ['key' => 'hero_title',         'value' => 'Trade Smarter with AI-Powered Signals',                                                                                   'type' => 'text'],
            ['key' => 'hero_subtitle',      'value' => 'Real-time Forex & Crypto trade signals powered by advanced AI analysis',                                                  'type' => 'text'],
            ['key' => 'features_title',     'value' => 'Why Choose VoxTrade',                                                                                                     'type' => 'text'],
            ['key' => 'pricing_title',      'value' => 'Simple Transparent Pricing',                                                                                              'type' => 'text'],
            ['key' => 'footer_text',        'value' => '© 2025 VoxTrade. AI-assisted trade insights. Not financial advice.',                                                      'type' => 'text'],
            ['key' => 'terms_content',      'value' => 'Trading involves significant risk...',                                                                                    'type' => 'textarea'],
            ['key' => 'disclaimer',         'value' => 'This platform provides AI-assisted trade insights. Not financial advice. Trading involves risk.',                         'type' => 'textarea'],
            ['key' => 'refresh_interval',   'value' => '5',                                                                                                                       'type' => 'number'],
            ['key' => 'default_risk_level', 'value' => 'medium',                                                                                                                  'type' => 'text'],
            ['key' => 'ai_sensitivity',     'value' => '70',                                                                                                                      'type' => 'number'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        // -----------------------------------------------------------------
        // Sample trades (mix of forex and crypto)
        // -----------------------------------------------------------------
        $trades = [
            [
                'pair'             => 'EUR/USD',
                'type'             => 'BUY',
                'entry_price'      => 1.08450000,
                'stop_loss'        => 1.07900000,
                'take_profit'      => 1.09300000,
                'confidence'       => 82,
                'duration'         => 'Intraday',
                'category'         => 'forex',
                'risk_level'       => 'low',
                'status'           => 'active',
                'analysis_summary' => 'EUR/USD showing bullish momentum above key support at 1.0840. RSI trending upward with MACD crossover confirmed on H4 chart. Target aligns with prior resistance zone.',
            ],
            [
                'pair'             => 'GBP/JPY',
                'type'             => 'SELL',
                'entry_price'      => 192.50000000,
                'stop_loss'        => 193.80000000,
                'take_profit'      => 190.00000000,
                'confidence'       => 74,
                'duration'         => 'Swing (2-3 days)',
                'category'         => 'forex',
                'risk_level'       => 'medium',
                'status'           => 'active',
                'analysis_summary' => 'GBP/JPY rejected from a major resistance confluence at 192.50. Bearish engulfing candle on D1 with divergence on RSI. Risk sentiment favouring JPY strength short-term.',
            ],
            [
                'pair'             => 'BTC/USDT',
                'type'             => 'BUY',
                'entry_price'      => 62500.00000000,
                'stop_loss'        => 60000.00000000,
                'take_profit'      => 68000.00000000,
                'confidence'       => 78,
                'duration'         => 'Swing (3-5 days)',
                'category'         => 'crypto',
                'risk_level'       => 'medium',
                'status'           => 'active',
                'analysis_summary' => 'Bitcoin consolidating above $62k after breaking out of a multi-week descending channel. On-chain accumulation data supports bullish bias. Next target is the $68k supply zone.',
            ],
            [
                'pair'             => 'ETH/USDT',
                'type'             => 'BUY',
                'entry_price'      => 3250.00000000,
                'stop_loss'        => 3050.00000000,
                'take_profit'      => 3700.00000000,
                'confidence'       => 71,
                'duration'         => 'Swing (3-5 days)',
                'category'         => 'crypto',
                'risk_level'       => 'medium',
                'status'           => 'active',
                'analysis_summary' => 'Ethereum holding above the $3,200 demand zone with increasing whale accumulation. EMA ribbon on H4 turning bullish. Upside target at $3,700 resistance.',
            ],
            [
                'pair'             => 'XAU/USD',
                'type'             => 'BUY',
                'entry_price'      => 2320.00000000,
                'stop_loss'        => 2295.00000000,
                'take_profit'      => 2380.00000000,
                'confidence'       => 85,
                'duration'         => 'Intraday',
                'category'         => 'forex',
                'risk_level'       => 'low',
                'status'           => 'active',
                'analysis_summary' => 'Gold maintaining upward trend supported by geopolitical uncertainty and USD weakness. Key support at $2,300 intact. Strong buy signal with a favourable risk-reward of 1:2.4.',
            ],
        ];

        foreach ($trades as $trade) {
            Trade::create($trade);
        }

        // -----------------------------------------------------------------
        // Dummy trader users
        // -----------------------------------------------------------------
        $dummyUsers = [
            [
                'name'     => 'James Okafor',
                'email'    => 'james@voxtrade.io',
                'password' => Hash::make('trader123'),
                'role'     => 'trader',
                'status'   => 'active',
                'plan'     => 'monthly',
                'expires'  => now()->addDays(25),
            ],
            [
                'name'     => 'Amina Bello',
                'email'    => 'amina@voxtrade.io',
                'password' => Hash::make('trader123'),
                'role'     => 'trader',
                'status'   => 'active',
                'plan'     => 'weekly',
                'expires'  => now()->addDays(5),
            ],
            [
                'name'     => 'Chidi Nwosu',
                'email'    => 'chidi@voxtrade.io',
                'password' => Hash::make('trader123'),
                'role'     => 'trader',
                'status'   => 'active',
                'plan'     => 'daily',
                'expires'  => now()->addHours(18),
            ],
            [
                'name'     => 'Fatima Yusuf',
                'email'    => 'fatima@voxtrade.io',
                'password' => Hash::make('trader123'),
                'role'     => 'trader',
                'status'   => 'active',
                'plan'     => null, // no subscription
                'expires'  => null,
            ],
            [
                'name'     => 'Emeka Adeyemi',
                'email'    => 'emeka@voxtrade.io',
                'password' => Hash::make('trader123'),
                'role'     => 'trader',
                'status'   => 'inactive',
                'plan'     => null, // inactive account
                'expires'  => null,
            ],
        ];

        foreach ($dummyUsers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => $data['password'],
                    'role'     => $data['role'],
                    'status'   => $data['status'],
                ]
            );

            if ($data['plan'] && $data['expires']) {
                $planPrices = ['daily' => 500, 'weekly' => 2000, 'monthly' => 5000];
                $amount     = $planPrices[$data['plan']] ?? 0;

                $sub = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan'       => $data['plan'],
                        'amount'     => $amount,
                        'currency'   => 'NGN',
                        'expires_at' => $data['expires'],
                        'status'     => 'active',
                    ]
                );

                Payment::updateOrCreate(
                    ['reference' => 'DEMO-' . strtoupper($data['plan']) . '-' . $user->id],
                    [
                        'user_id'          => $user->id,
                        'subscription_id'  => $sub->id,
                        'amount'           => $amount,
                        'currency'         => 'NGN',
                        'gateway'          => 'paystack',
                        'status'           => 'successful',
                        'gateway_response' => ['demo' => true],
                    ]
                );
            }
        }
    }
}
