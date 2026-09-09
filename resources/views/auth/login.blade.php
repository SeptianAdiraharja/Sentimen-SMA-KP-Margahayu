@extends('layouts.base', ['title' => 'Login Admin - Sistem Penilaian MBG'])

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-950 to-blue-900 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <!-- Header Modal/Card Login -->
        <div class="p-8 text-center bg-slate-900 text-white relative">
            <div class="w-20 h-20 bg-white/10 rounded-2xl p-2 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/20 border border-white/10">
                <img src="{{ asset('images/logos/icon.png') }}" alt="Logo MBG" class="w-full h-full object-contain">
            </div>
            <h2 class="text-2xl font-bold tracking-tight">Portal Admin & Sekolah</h2>
            <p class="text-xs text-slate-300 mt-1">Sistem Penilaian Menu MBG - SMA KP Margahayu</p>
        </div>

        <div class="p-8">
            @if(session('status'))
            <div class="mb-4 p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-blue-800"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-800">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Sekolah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email', 'admin@mbg.sch.id') }}" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-800 focus:border-blue-800 text-sm transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required value="admin123"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-800 focus:border-blue-800 text-sm transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Default kredensial: <span class="font-mono text-slate-600">admin@mbg.sch.id</span> / <span class="font-mono text-slate-600">admin123</span></p>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-800 focus:ring-blue-800">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Tombol Submit Warna Emas/Kuning -->
                <button type="submit" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-300 text-blue-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-400/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('questionnaire.index') }}" class="text-xs font-bold text-blue-800 hover:text-blue-900 flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Form Kuesioner Siswa</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection