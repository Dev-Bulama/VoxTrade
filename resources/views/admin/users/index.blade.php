@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">User Management</h1>
            <p class="text-gray-500 text-sm mt-0.5">Manage trader accounts and subscriptions</p>
        </div>
        <span class="text-sm text-gray-400">{{ $users->total() }} total users</span>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex items-center gap-3">
        <select name="status" class="bg-[#1a1a1a] border border-[#D4AF37]/20 text-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-[#D4AF37]/50 focus:outline-none">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-black" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">Filter</button>
        @if(request('status'))<a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-white transition px-3">Clear</a>@endif
    </form>

    {{-- Table --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#D4AF37]/10">
                        <th class="text-left px-5 py-4 text-gray-400 font-medium">User</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden md:table-cell">Status</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden md:table-cell">Subscription</th>
                        <th class="text-left px-5 py-4 text-gray-400 font-medium hidden lg:table-cell">Joined</th>
                        <th class="text-right px-5 py-4 text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a1a1a]">
                    @forelse($users as $user)
                    <tr class="hover:bg-white/2 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-black text-sm" style="background:linear-gradient(135deg,#D4AF37,#FFD700)">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $user->status === 'active' ? 'bg-green-900/40 text-green-400 border border-green-700/30' : 'bg-red-900/40 text-red-400 border border-red-700/30' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            @if($user->subscription && $user->subscription->status === 'active' && $user->subscription->expires_at > now())
                                <div>
                                    <span class="text-xs text-[#D4AF37] font-semibold">{{ ucfirst($user->subscription->plan) }}</span>
                                    <p class="text-xs text-gray-500">Exp: {{ $user->subscription->expires_at->format('M d, Y') }}</p>
                                </div>
                            @else
                                <span class="text-xs text-gray-500">No active plan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-gray-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="w-8 h-8 rounded-lg bg-blue-900/30 text-blue-400 hover:bg-blue-900/50 flex items-center justify-center transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center transition {{ $user->status === 'active' ? 'bg-yellow-900/30 text-yellow-400 hover:bg-yellow-900/50' : 'bg-green-900/30 text-green-400 hover:bg-green-900/50' }}" title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 flex items-center justify-center transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-[#D4AF37]/10">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
