<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    public function index(Request $request)
    {
        $query = Trade::latest();
        if ($request->category) $query->where('category', $request->category);
        if ($request->risk_level) $query->where('risk_level', $request->risk_level);
        if ($request->status) $query->where('status', $request->status);
        if ($request->type) $query->where('type', $request->type);
        $signals = $query->paginate(12);
        return view('trader.signals', compact('signals'));
    }

    public function show(Trade $trade)
    {
        return view('trader.signal-detail', compact('trade'));
    }
}
