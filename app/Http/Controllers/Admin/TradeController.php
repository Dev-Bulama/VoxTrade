<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Services\AITradeService;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::latest()->paginate(20);
        return view('admin.trades.index', compact('trades'));
    }

    public function create()
    {
        return view('admin.trades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pair'             => 'required|string|max:20',
            'type'             => 'required|in:BUY,SELL',
            'entry_price'      => 'required|numeric|min:0',
            'stop_loss'        => 'required|numeric|min:0',
            'take_profit'      => 'required|numeric|min:0',
            'confidence'       => 'required|integer|min:0|max:100',
            'duration'         => 'required|string|max:50',
            'category'         => 'required|in:forex,crypto',
            'risk_level'       => 'required|in:low,medium,high',
            'analysis_summary' => 'nullable|string',
        ]);
        Trade::create($data);
        return redirect()->route('admin.trades.index')->with('success', 'Trade signal created.');
    }

    public function edit(Trade $trade)
    {
        return view('admin.trades.edit', compact('trade'));
    }

    public function update(Request $request, Trade $trade)
    {
        $data = $request->validate([
            'pair'             => 'required|string|max:20',
            'type'             => 'required|in:BUY,SELL',
            'entry_price'      => 'required|numeric|min:0',
            'stop_loss'        => 'required|numeric|min:0',
            'take_profit'      => 'required|numeric|min:0',
            'confidence'       => 'required|integer|min:0|max:100',
            'duration'         => 'required|string|max:50',
            'category'         => 'required|in:forex,crypto',
            'risk_level'       => 'required|in:low,medium,high',
            'status'           => 'required|in:active,tp_hit,sl_hit,expired',
            'analysis_summary' => 'nullable|string',
        ]);
        $trade->update($data);
        return redirect()->route('admin.trades.index')->with('success', 'Trade signal updated.');
    }

    public function destroy(Trade $trade)
    {
        $trade->delete();
        return back()->with('success', 'Trade deleted.');
    }

    public function generate()
    {
        try {
            $service = app(AITradeService::class);
            $trades  = $service->generateSignals();
            return back()->with('success', count($trades) . ' AI signals generated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate signals: ' . $e->getMessage());
        }
    }
}
