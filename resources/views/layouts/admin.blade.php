@extends('layouts.base')

@section('body')
<div class="min-h-screen flex bg-slate-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0 shadow-xl">
        <div class="p-5 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-emerald-500/30">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <div>
                <h1 class="font-bold text-white leading-tight">MBG Sentimen</h1>
                <p class="text-xs text-emerald-400">SMA KP Margahayu</p>
            </div>
        </div>

        <div class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Navigasi Utama</div>

        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span>Dashboard Visual</span>
            </a>

            <a href="{{ route('admin.periods.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.periods.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                <span>Periode Kuesioner</span>
            </a>

            <a href="{{ route('admin.questions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.questions.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-list-check w-5 text-center"></i>
                <span>Pertanyaan Aspek</span>
            </a>

            <a href="{{ route('admin.responses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.responses.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-comments w-5 text-center"></i>
                <span>Data Respon Siswa</span>
            </a>

            <div class="px-4 pt-4 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Mesin NLP & Sentimen</div>

            <a href="{{ route('admin.analysis.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.analysis.index') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-brain w-5 text-center"></i>
                <span>Hasil Klasifikasi Sentimen</span>
            </a>

            <a href="{{ route('admin.analysis.tfidf') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.analysis.tfidf') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-calculator w-5 text-center"></i>
                <span>Ekstraksi TF-IDF</span>
            </a>

            <a href="{{ route('admin.analysis.metrics') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.analysis.metrics') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-bullseye w-5 text-center"></i>
                <span>Evaluasi Model (Akurasi)</span>
            </a>

            <div class="px-4 pt-4 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Output & Pelaporan</div>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-file-invoice w-5 text-center"></i>
                <span>Laporan & Rekomendasi</span>
            </a>

            <a href="{{ route('questionnaire.index') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-amber-400 hover:bg-slate-800">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                <span>Lihat Form Siswa (Publik)</span>
            </a>
        </nav>

        <!-- User Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-xs text-white">
                        AD
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-white truncate max-w-[120px]">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400">Admin / Pihak Sekolah</div>
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-semibold bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20 px-6 py-4 flex items-center justify-between shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">@yield('page_subtitle', 'Analisis Sentimen Kualitas Menu Program MBG Menggunakan Naïve Bayes & TF-IDF')</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 p-6 space-y-6">
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-lg"></i>
                <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5 text-lg"></i>
                <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Terdapat beberapa kesalahan input:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@endsection