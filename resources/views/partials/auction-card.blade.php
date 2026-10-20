@php
    $authUser   = auth('web')->user();
    $wishlisted = $authUser && $authUser->favoriteAuctions->contains($auction->id);
@endphp

<article class="group overflow-hidden rounded-[2rem] border border-white/60 bg-white/85 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-2 hover:shadow-2xl dark:border-white/10 dark:bg-white/5 dark:shadow-black/20">
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-200 dark:bg-slate-800">
        @if($auction->image_path)
            <img src="{{ asset('storage/'.$auction->image_path) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" alt="{{ $auction->title }}">
        @else
            <div class="skeleton grid h-full place-items-center text-slate-500 dark:text-slate-400">No image</div>
        @endif

        {{-- Category badge --}}
        <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-700 shadow dark:bg-slate-950/80 dark:text-slate-100">{{ $auction->category->name }}</div>

        {{-- Status badge --}}
        <div class="absolute right-4 top-4 rounded-full px-3 py-1 text-xs font-black shadow {{ $auction->statusBadgeClasses() }}">
            {{ $auction->status() }}
        </div>

        {{-- ❤ Wishlist floating button --}}
        @if($authUser)
            <form method="POST" action="{{ route('wishlist.toggle', $auction) }}" class="absolute bottom-3 right-3">
                @csrf
                <button type="submit"
                    title="{{ $wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                    class="wishlist-btn flex h-10 w-10 items-center justify-center rounded-full shadow-lg backdrop-blur-sm transition duration-200
                           {{ $wishlisted
                               ? 'bg-rose-500 text-white hover:bg-rose-600 scale-110'
                               : 'bg-white/80 text-slate-400 hover:bg-rose-50 hover:text-rose-500 dark:bg-slate-900/80 dark:text-slate-400 dark:hover:text-rose-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition duration-200" viewBox="0 0 24 24"
                         fill="{{ $wishlisted ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </form>
        @else
            <a href="{{ route('user.login') }}"
               title="Login to add to Wishlist"
               class="absolute bottom-3 right-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/80 text-slate-400 shadow-lg backdrop-blur-sm transition duration-200 hover:bg-rose-50 hover:text-rose-500 dark:bg-slate-900/80 dark:text-slate-400 dark:hover:text-rose-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </a>
        @endif
    </div>

    <div class="p-5">
        <div class="mb-3 flex items-start justify-between gap-3">
            <div>
                <h3 class="line-clamp-2 text-lg font-black tracking-tight text-slate-950 dark:text-white">{{ $auction->title }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">by {{ $auction->user->name }}</p>
            </div>
        </div>
        @if($auction->isClosed())
            <div class="rounded-2xl bg-rose-50 p-4 dark:bg-rose-950/30">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex flex-col items-center"><p class="text-[11px] uppercase tracking-wider text-rose-500">Final bid</p><p class="font-black whitespace-nowrap">${{ number_format($auction->finalBidAmount(), 2) }}</p></div>
                    <div class="flex flex-col items-center"><p class="text-[11px] uppercase tracking-wider text-rose-500">Bids</p><p class="font-black whitespace-nowrap">{{ $auction->bids_count ?? $auction->bids()->count() }}</p></div>
                    <div class="flex flex-col items-center max-w-[100px]"><p class="text-[11px] uppercase tracking-wider text-rose-500">Winner</p><p class="truncate font-black w-full text-center" title="{{ $auction->winningUser()?->name ?? 'No bids' }}">{{ $auction->winningUser()?->name ?? 'None' }}</p></div>
                </div>
                <p class="mt-3 rounded-xl bg-white/70 px-3 py-2 text-center text-xs font-bold text-rose-700 dark:bg-white/10 dark:text-rose-200">Auction Ended</p>
            </div>
            <a href="{{ route('auctions.show', $auction) }}" class="mt-5 block rounded-2xl bg-slate-950 px-5 py-3 text-center text-sm font-black text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">View Result</a>
        @else
            <div class="flex items-center justify-between gap-2 rounded-2xl bg-slate-50 p-4 dark:bg-slate-900/80">
                <div class="flex flex-col items-center"><p class="text-[11px] uppercase tracking-wider text-slate-500">Bid</p><p class="font-black whitespace-nowrap">${{ $auction->displayPrice() }}</p></div>
                <div class="flex flex-col items-center"><p class="text-[11px] uppercase tracking-wider text-slate-500">Bids</p><p class="font-black whitespace-nowrap">{{ $auction->bids_count ?? $auction->bids()->count() }}</p></div>
                <div class="flex flex-col items-center">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500">{{ $auction->isUpcoming() ? 'Starts' : 'Time' }}</p>
                    <p class="font-black whitespace-nowrap" data-countdown="{{ $auction->countdownTarget()->toIso8601String() }}">Live</p>
                </div>
            </div>
            <a href="{{ route('auctions.show', $auction) }}" class="mt-5 block rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3 text-center text-sm font-black text-white shadow-lg shadow-cyan-500/20 transition hover:from-cyan-400 hover:to-blue-500">{{ $auction->isUpcoming() ? 'Preview Auction' : 'Bid Now' }}</a>
        @endif
    </div>
</article>
