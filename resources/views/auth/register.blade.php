@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="grid min-h-[calc(100vh-5rem)] place-items-center px-4 py-12">
        <form method="POST" action="{{ route('user.register.store') }}" class="w-full max-w-lg rounded-[2rem] bg-white/85 p-8 shadow-2xl dark:bg-white/5">
            @csrf
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600">Join marketplace</p>
            <h1 class="mt-2 text-3xl font-black">Create your buyer account</h1>
            <div class="mt-6 grid gap-4">
                <input name="name" value="{{ old('name') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Full name" required>
                <input name="email" type="email" value="{{ old('email') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Email" required>
                <input name="password" type="password" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Password" required>
                <input name="password_confirmation" type="password" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 placeholder-slate-400 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900 dark:placeholder-slate-500" placeholder="Confirm password" required>
                <button class="rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-4 font-black text-white">Create Account</button>
            </div>
            <p class="mt-5 text-center text-sm text-slate-500">Already registered? <a href="{{ route('user.login') }}" class="font-bold text-cyan-600">Login</a></p>
        </form>
    </section>
@endsection
