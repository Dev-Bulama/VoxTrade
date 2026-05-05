<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    public function index(Request $request)
    {
        $query    = Trade::latest();
        $category = $request->input('category', 'all');
        $type     = $request->input('type', 'all');
        $risk     = $request->input('risk', 'all');
        $status   = $request->input('status', 'active');

        if ($category && $category !== 'all') $query->where('category', $category);
        if ($type     && $type     !== 'all') $query->where('type', strtoupper($type));
        if ($risk     && $risk     !== 'all') $query->where('risk_level', $risk);
        if ($status   && $status   !== 'all') $query->where('status', $status);

        $signals = $query->paginate(12);
        return view('trader.signals', compact('signals'));
    }

    public function show(Trade $trade)
    {
        return view('trader.signal-detail', ['signal' => $trade]);
    }
}
