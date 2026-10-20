@extends(($heading ?? '') === 'Admin Login' ? 'layouts.admin-auth' : 'layouts.app')

@section('title', $title ?? 'Login')

@section('content')
    <section class="grid min-h-[calc(100vh-5rem)] place-items-center px-4 py-12">
        <form method="POST" action="{{ $action ?? route('user.login.store') }}" class="w-full max-w-md rounded-[2rem] border border-white/60 bg-white/85 p-8 shadow-2xl backdrop-blur dark:border-white/10 dark:bg-white/5">
            @csrf
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">{{ ($heading ?? 'Login') === 'Admin Login' ? 'Secure admin access' : 'Welcome back' }}</p>
            <h1 class="mt-2 text-3xl font-black">{{ $heading ?? 'Login' }}</h1>
            @if(session('error'))
                <div class="mb-4 rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-700 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-700 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="mt-6 space-y-4">
                <div>
                    <label class="text-sm font-bold">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900" required autofocus>
                </div>
                <div>
                    <label class="text-sm font-bold">Password</label>
                    <input name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-500"><input name="remember" type="checkbox" class="rounded"> Remember me</label>
                <button class="w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-4 font-black text-white shadow-lg shadow-cyan-500/20">Login</button>
            </div>
            <div class="mt-5 flex justify-between text-sm font-semibold">
                <a href="{{ route('password.request') }}" class="text-cyan-600">Forgot password?</a>
                @if($registerRoute ?? null)
                    <a href="{{ $registerRoute }}">Create account</a>
                @endif
            </div>
        </form>
    </section>
@endsection
