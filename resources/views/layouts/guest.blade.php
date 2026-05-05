<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VoxTrade') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{gold:{DEFAULT:'#D4AF37',light:'#FFD700',dark:'#B8960C'}}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{font-family:'Inter',sans-serif;}
        .gold-gradient{background:linear-gradient(135deg,#D4AF37 0%,#FFD700 50%,#B8960C 100%);}
        .gold-text{background:linear-gradient(135deg,#D4AF37,#FFD700);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .glass{background:rgba(255,255,255,0.04);backdrop-filter:blur(12px);border:1px solid rgba(212,175,55,0.15);}
        .input-dark{background:#111;border:1px solid rgba(212,175,55,0.2);color:#e5e5e5;border-radius:12px;padding:12px 16px;width:100%;font-size:14px;outline:none;transition:border-color .2s;}
        .input-dark:focus{border-color:rgba(212,175,55,.5);}
        .input-dark::placeholder{color:#4b5563;}
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-10 bg-[#0a0a0a]" style="background:radial-gradient(ellipse at 50% -20%,rgba(212,175,55,.07) 0%,transparent 60%),#0a0a0a">
    {{ $slot }}
</body>
</html>
