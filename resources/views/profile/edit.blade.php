@extends('layouts.trader')
@section('title', 'Profile')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-white">My <span class="gold-text">Profile</span></h1>
        <p class="text-gray-500 text-sm mt-1">Manage your account information</p>
    </div>

    {{-- Profile Info --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
            <i class="fas fa-user text-[#D4AF37]"></i> Profile Information
        </h3>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Password --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-[#D4AF37]/10">
            <i class="fas fa-lock text-[#D4AF37]"></i> Update Password
        </h3>
        @include('profile.partials.update-password-form')
    </div>

    {{-- Delete Account --}}
    <div class="glass rounded-2xl p-6 border border-red-800/20">
        <h3 class="text-red-400 font-semibold mb-4 flex items-center gap-2 pb-3 border-b border-red-800/20">
            <i class="fas fa-triangle-exclamation"></i> Danger Zone
        </h3>
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
