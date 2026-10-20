@extends('layouts.app')

@section('title', 'Explore Auctions')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Marketplace</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight">Explore live auctions</h1>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[280px_1fr]">
            <aside class="h-fit rounded-[2rem] border border-white/60 bg-white/80 p-5 shadow-xl backdrop-blur dark:border-white/10 dark:bg-white/5">
                <form class="space-y-4">
                    <div>
                        <label class="text-sm font-bold">Search</label>
                        <input name="search" value="{{ request('search') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Watch, laptop, art">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Category</label>
                        <select name="category" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-bold">Status</label>
                        <select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900">
                            <option value="all" @selected(request('status', 'all') === 'all')>All statuses</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="upcoming" @selected(request('status') === 'upcoming')>Upcoming</option>
                            <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input name="min_price" value="{{ request('min_price') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Min">
                        <input name="max_price" value="{{ request('max_price') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Max">
                    </div>
                    <button class="w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3 font-black text-white">Apply Filters</button>
                </form>
            </aside>

            <div>
                <div class="mb-5 flex items-center justify-between rounded-3xl bg-white/70 p-4 shadow-sm dark:bg-white/5">
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $auctions->total() }} auctions found</p>
                    <div class="flex gap-2">
                        <span class="h-3 w-3 rounded-full bg-cyan-400"></span>
                        <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    </div>
                </div>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($auctions as $auction)
                        @include('partials.auction-card')
                    @empty
                        <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 bg-white/70 p-12 text-center dark:border-white/10 dark:bg-white/5">
                            <h2 class="text-2xl font-black">No auctions found</h2>
                            <p class="mt-2 text-slate-500">Try changing your filters.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-8">{{ $auctions->links() }}</div>
            </div>
        </div>
    </section>
@endsection
