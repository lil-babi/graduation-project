@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">← Back to Admin Console</a>
            <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Registered Readers & Accounts</h1>
            <p class="text-sm text-stone-600 mt-1">Audit accounts, assigned roles, and subscription status (SRS Section 6.12).</p>
        </div>

        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                        <tr>
                            <th class="py-3.5 px-6">User</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Active Plan</th>
                            <th class="py-3.5 px-6">Pages Read</th>
                            <th class="py-3.5 px-6">Saved Books</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @foreach($users as $u)
                            <tr class="hover:bg-stone-50/60">
                                <td class="py-4 px-6 font-semibold text-stone-900">
                                    {{ $u->name }}
                                    <span class="block text-[11px] text-stone-400 font-normal">{{ $u->email }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->role === 'admin' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700' }}">
                                        {{ strtoupper($u->role) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($u->isPremium())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            PREMIUM (SIMULATED)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-600">
                                            FREE
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-bold text-stone-900">
                                    {{ number_format($u->totalPagesRead()) }}
                                </td>
                                <td class="py-4 px-6 text-stone-600">
                                    {{ $u->userBooks->count() }} books
                                </td>
                                <td class="py-4 px-6 text-stone-500">
                                    {{ $u->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
