@csrf
<div class="grid gap-5">
    <div class="grid gap-5 md:grid-cols-[1.5fr_0.8fr]">
        <div>
            <label class="text-sm font-bold">Title</label>
            <input name="title" value="{{ old('title', $auction->title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>
        </div>
        <div>
            <label class="text-sm font-bold">Category</label>
            <select name="category_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900 dark:text-white" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $auction->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="text-sm font-bold">Description</label>
        <textarea name="description" rows="6" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>{{ old('description', $auction->description) }}</textarea>
    </div>
    <div class="grid gap-5 md:grid-cols-4">
        <div>
            <label class="text-sm font-bold">Starting price</label>
            <input type="number" step="0.01" min="1" name="starting_price" value="{{ old('starting_price', $auction->starting_price) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>
        </div>
        <div>
            <label class="text-sm font-bold">Min increment</label>
            <input type="number" step="0.01" min="1" name="min_increment" value="{{ old('min_increment', $auction->min_increment ?? 1) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>
        </div>
        <div>
            <label class="text-sm font-bold">Start time</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($auction->starts_at)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>
        </div>
        <div>
            <label class="text-sm font-bold">End time</label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($auction->ends_at)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" required>
        </div>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="text-sm font-bold">Condition</label>
            <input name="condition" value="{{ old('condition', $auction->condition ?? 'Excellent') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
        </div>
        <div>
            <label class="text-sm font-bold">Shipping details</label>
            <input name="shipping_details" value="{{ old('shipping_details', $auction->shipping_details) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
        </div>
    </div>
    <div>
        <label class="text-sm font-bold">Specifications</label>
        <textarea name="specifications" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500" placeholder="Storage: 256GB&#10;Condition: Like New">{{ old('specifications', collect($auction->specifications ?? [])->map(fn ($value, $key) => $key.': '.$value)->implode("\n")) }}</textarea>
    </div>
    <div x-data="{ preview: null }" class="rounded-3xl border border-dashed border-slate-300 p-5 dark:border-white/10">
        <label class="text-sm font-bold">Product image</label>
        <input type="file" name="image" class="mt-3 block w-full text-sm" accept="image/*" @change="preview = URL.createObjectURL($event.target.files[0])">
        <template x-if="preview"><img :src="preview" class="mt-4 h-48 w-full rounded-2xl object-cover"></template>
        @if($auction->image_path)
            <img src="{{ asset('storage/'.$auction->image_path) }}" class="mt-4 h-48 w-full rounded-2xl object-cover" alt="{{ $auction->title }}">
        @endif
    </div>
    <div class="flex flex-wrap gap-3">
        <button class="rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-7 py-3 font-black text-white">{{ $button }}</button>
        <a href="{{ $cancelRoute ?? route('dashboard') }}" class="rounded-full border border-slate-200 px-7 py-3 font-black dark:border-white/10">Cancel</a>
    </div>
</div>
