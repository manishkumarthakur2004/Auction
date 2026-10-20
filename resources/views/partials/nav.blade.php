@php
    $webUser = auth('web')->user()?->load('favoriteAuctions');
    if ($webUser) {
        $navUnreadCount  = $webUser->unreadNotifications()->count();
        $navRecentNotifs = $webUser->notifications()->latest()->take(5)->get();
    }
@endphp

<header class="sticky top-0 z-40 border-b border-white/20 bg-white/75 shadow-sm backdrop-blur-xl transition dark:border-white/10 dark:bg-slate-950/70">
    {{-- Single Alpine scope for the whole nav --}}
    <nav x-data="{ open: false, notifOpen: false }"
         @click.outside="open = false; notifOpen = false"
         class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 font-black text-white shadow-lg shadow-cyan-500/25">A</span>
            <span>
                <span class="block text-lg font-black tracking-tight">AuctionPro</span>
                <span class="hidden text-xs font-medium text-slate-500 dark:text-slate-400 sm:block">Live bidding marketplace</span>
            </span>
        </a>

        {{-- Desktop nav links --}}
        <div class="hidden items-center gap-1 lg:flex">
            <a href="{{ route('auctions.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">Explore</a>
            @if($webUser)
                <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">Dashboard</a>
                <a href="{{ route('bids.mine') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">My Bids</a>
                <a href="{{ route('wishlist.index') }}" class="relative flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Wishlist
                    @php $wishlistCount = $webUser->favoriteAuctions->count(); @endphp
                    @if($wishlistCount > 0)
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-black text-white">{{ $wishlistCount }}</span>
                    @endif
                </a>
            @endif
        </div>

        {{-- Desktop right actions --}}
        <div class="hidden items-center gap-3 lg:flex">

            {{-- Theme toggle — prevent accidental default behavior --}}
            <button type="button" @click.prevent="toggleTheme()"
                    class="rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 dark:border-white/10 dark:bg-white/10">
                Theme
            </button>

            @if($webUser)
                {{-- Notification bell — @click.outside is on the WRAPPER div, not the button --}}
                <div class="relative" @click.outside="notifOpen = false">
                    <button type="button"
                            @click.prevent="notifOpen = !notifOpen"
                            class="relative flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 dark:border-white/10 dark:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        Alerts
                        @if($navUnreadCount > 0)
                            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-black text-white">
                                {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-cloak
                         x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-80 origin-top-right rounded-3xl border border-white/20 bg-white/95 shadow-2xl backdrop-blur dark:bg-slate-900 dark:border-white/10">

                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-white/10">
                            <p class="text-sm font-black">Notifications</p>
                            @if($navUnreadCount > 0)
                                <form method="POST" action="{{ route('notifications.read') }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-cyan-600 hover:underline">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-72 overflow-y-auto">
                            @forelse($navRecentNotifs as $notif)
                                @php
                                    $isUnread = is_null($notif->read_at);
                                    $icon = match(true) {
                                        str_contains($notif->type, 'Won')    => '🏆',
                                        str_contains($notif->type, 'Outbid') => '📣',
                                        str_contains($notif->type, 'Ending') => '⏳',
                                        default                               => '🔔',
                                    };
                                @endphp
                                <a href="{{ route('notifications.mark-read', $notif->id) }}"
                                   class="flex items-start gap-3 px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-white/5 {{ $isUnread ? 'bg-cyan-50/60 dark:bg-cyan-950/20' : '' }}">
                                    <span class="mt-0.5 text-lg leading-none">{{ $icon }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="line-clamp-2 text-sm {{ $isUnread ? 'font-bold text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">
                                            {{ $notif->data['message'] ?? 'Auction update' }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if($isUnread)
                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-cyan-500"></span>
                                    @endif
                                </a>
                            @empty
                                <p class="px-4 py-6 text-center text-sm text-slate-400">No notifications yet.</p>
                            @endforelse
                        </div>

                        <div class="border-t border-slate-100 px-4 py-3 dark:border-white/10">
                            <a href="{{ route('notifications.index') }}" class="block text-center text-xs font-bold text-cyan-600 hover:underline">
                                View all notifications →
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('sell-requests.create') }}" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 transition hover:-translate-y-0.5 hover:bg-slate-800 dark:bg-white dark:text-slate-950">Create Auction</a>

                <form method="POST" action="{{ route('user.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10">Logout</button>
                </form>
            @else
                <a href="{{ route('user.login') }}" class="rounded-full px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-white/10">Login</a>
                <a href="{{ route('user.register') }}" class="rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-cyan-500/25 transition hover:-translate-y-0.5">Join Free</a>
            @endif
        </div>

        {{-- Mobile hamburger — type="button" is critical --}}
        <button type="button"
                class="rounded-2xl border border-slate-200 p-3 lg:hidden dark:border-white/10"
                @click.prevent.stop="open = !open; notifOpen = false">
            <span class="sr-only">Open menu</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>

        {{-- Mobile menu --}}
        <div x-cloak x-show="open" x-transition
             class="absolute left-4 right-4 top-24 rounded-3xl border border-white/20 bg-white/95 p-4 shadow-2xl dark:bg-slate-900 lg:hidden">
            <div class="grid gap-2">
                <a href="{{ route('auctions.index') }}" class="rounded-2xl px-4 py-3 font-semibold hover:bg-slate-100 dark:hover:bg-white/10">Explore</a>
                @if($webUser)
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-3 font-semibold hover:bg-slate-100 dark:hover:bg-white/10">Dashboard</a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 rounded-2xl px-4 py-3 font-semibold hover:bg-slate-100 dark:hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Wishlist
                    </a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-2xl px-4 py-3 font-semibold hover:bg-slate-100 dark:hover:bg-white/10">
                        <span class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-cyan-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            Notifications
                        </span>
                        @if($navUnreadCount > 0)
                            <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-rose-500 px-1.5 text-xs font-black text-white">{{ $navUnreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('sell-requests.create') }}" class="rounded-2xl bg-slate-950 px-4 py-3 font-semibold text-white">Sell Item</a>
                    <form method="POST" action="{{ route('user.logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left font-semibold text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">Logout</button>
                    </form>
                @else
                    <a href="{{ route('user.login') }}" class="rounded-2xl px-4 py-3 font-semibold hover:bg-slate-100 dark:hover:bg-white/10">Login</a>
                    <a href="{{ route('user.register') }}" class="rounded-2xl bg-blue-600 px-4 py-3 font-semibold text-white">Join Free</a>
                @endif
            </div>
        </div>
    </nav>
</header>
