@extends('layouts.admin')
@section('title', 'CMS Editor')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-bold text-white">CMS Editor</h1>
        <p class="text-gray-500 text-sm mt-0.5">Edit frontend content — changes update the live landing page instantly</p>
    </div>

    <div class="glass rounded-xl p-3 border border-blue-700/30 bg-blue-900/10 flex items-center gap-2 text-sm text-blue-400">
        <i class="fas fa-circle-info flex-shrink-0"></i>
        All changes are saved to the database and reflected immediately on the live site.
    </div>

    <form method="POST" action="{{ route('admin.cms.update') }}" class="space-y-6">
        @csrf

        {{-- Site Identity --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
                <i class="fas fa-globe text-[#D4AF37]"></i> Site Identity
            </h3>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Site Name</label>
                <input type="text" name="site_name" value="{{ $cms['site_name'] ?? 'VoxTrade' }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                @error('site_name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Hero Section --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
                <i class="fas fa-star text-[#D4AF37]"></i> Hero Section
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Hero Title</label>
                    <input type="text" name="hero_title" value="{{ $cms['hero_title'] ?? '' }}"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                    @error('hero_title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="2"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 resize-none">{{ $cms['hero_subtitle'] ?? '' }}</textarea>
                    @error('hero_subtitle')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Section Titles --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
                <i class="fas fa-heading text-[#D4AF37]"></i> Section Titles
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Features Section Title</label>
                    <input type="text" name="features_title" value="{{ $cms['features_title'] ?? '' }}"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Pricing Section Title</label>
                    <input type="text" name="pricing_title" value="{{ $cms['pricing_title'] ?? '' }}"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
                <i class="fas fa-shoe-prints text-[#D4AF37]"></i> Footer
            </h3>
            <div>
                <label class="block text-sm text-gray-400 mb-2">Footer Text / Copyright</label>
                <input type="text" name="footer_text" value="{{ $cms['footer_text'] ?? '' }}"
                    class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50">
            </div>
        </div>

        {{-- Legal --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
                <i class="fas fa-scale-balanced text-[#D4AF37]"></i> Legal & Disclaimer
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Disclaimer (shown on dashboard + landing page)</label>
                    <textarea name="disclaimer" rows="2"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 resize-none">{{ $cms['disclaimer'] ?? '' }}</textarea>
                    @error('disclaimer')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Full Terms & Conditions Content</label>
                    <textarea name="terms_content" rows="6"
                        class="w-full bg-[#1a1a1a] border border-[#D4AF37]/20 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]/50 resize-none">{{ $cms['terms_content'] ?? '' }}</textarea>
                    @error('terms_content')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-3 rounded-xl font-bold text-sm text-black transition hover:opacity-90" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                <i class="fas fa-save mr-2"></i> Save All Changes
            </button>
            <a href="{{ route('home') }}" target="_blank" class="px-4 py-3 rounded-xl font-semibold text-sm text-gray-400 glass hover:text-white transition flex items-center gap-2">
                <i class="fas fa-external-link-alt"></i> Preview Site
            </a>
        </div>
    </form>
</div>
@endsection
