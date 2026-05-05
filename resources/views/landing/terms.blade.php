<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Disclaimer — VoxTrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family:'Inter',sans-serif; background:#0a0a0a; color:#e5e5e5; }
        .gold-text { background:linear-gradient(135deg,#D4AF37,#FFD700); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .glass { background:rgba(255,255,255,0.04); backdrop-filter:blur(12px); border:1px solid rgba(212,175,55,0.15); }
    </style>
</head>
<body class="min-h-screen">
    <nav class="border-b border-[#D4AF37]/15 py-4 px-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#D4AF37,#B8960C)">
                    <i class="fas fa-chart-line text-black text-sm"></i>
                </div>
                <span class="font-bold text-lg gold-text">VoxTrade</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-[#D4AF37] transition">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">Terms & <span class="gold-text">Disclaimer</span></h1>
            <p class="text-gray-400">Please read these terms carefully before using VoxTrade.</p>
        </div>

        <div class="glass rounded-2xl p-8 space-y-8">
            <div class="p-4 rounded-xl border border-[#D4AF37]/30 bg-[#D4AF37]/5">
                <div class="flex gap-3">
                    <i class="fas fa-triangle-exclamation text-[#D4AF37] text-xl mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-bold text-[#D4AF37] mb-1">Important Disclaimer</p>
                        <p class="text-gray-300 text-sm leading-relaxed">{{ $settings['disclaimer'] ?? 'This platform provides AI-assisted trade insights. Not financial advice. Trading involves risk.' }}</p>
                    </div>
                </div>
            </div>

            <div class="prose prose-invert max-w-none">
                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $settings['terms_content'] ?? 'Trading in financial markets involves substantial risk of loss and is not suitable for all investors. The information provided by VoxTrade is for educational and informational purposes only and should not be considered financial advice.

VoxTrade uses AI algorithms to analyze market data and generate trade signals. These signals are not guarantees of profit and should not be relied upon as the sole basis for trading decisions.

By using VoxTrade, you acknowledge:
• You understand and accept the risks involved in trading
• VoxTrade signals are AI-generated insights, not financial advice
• Past performance does not guarantee future results
• You are solely responsible for your trading decisions
• VoxTrade is not liable for any financial losses incurred

Always conduct your own research and consider consulting a licensed financial advisor before making investment decisions.' }}</div>
            </div>

            <div class="pt-6 border-t border-[#D4AF37]/10 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-black text-sm transition" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                    <i class="fas fa-check"></i> I Understand, Get Started
                </a>
            </div>
        </div>
    </main>
</body>
</html>
