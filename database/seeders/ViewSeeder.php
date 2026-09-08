<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $views = [
            // 1. layouts/base.blade.php
            'resources/views/layouts/base.blade.php' => <<<'BLADE'
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Penilaian Kualitas Menu MBG' }} - SMA KP Margahayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased flex flex-col justify-between">
    @yield('body')
    @stack('scripts')
</body>
</html>
BLADE,

            // 2. layouts/admin.blade.php
            'resources/views/layouts/admin.blade.php' => <<<'BLADE'
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
BLADE,

            // 3. auth/login.blade.php
            'resources/views/auth/login.blade.php' => <<<'BLADE'
@extends('layouts.base', ['title' => 'Login Admin - Sistem Penilaian MBG'])

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <div class="p-8 text-center bg-slate-900 text-white relative">
            <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/40 text-2xl">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight">Portal Admin & Sekolah</h2>
            <p class="text-xs text-slate-300 mt-1">Sistem Penilaian Menu MBG - SMA KP Margahayu</p>
        </div>

        <div class="p-8">
            @if(session('status'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
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
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required value="admin123"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Default kredensial: <span class="font-mono text-slate-600">admin@mbg.sch.id</span> / <span class="font-mono text-slate-600">admin123</span></p>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('questionnaire.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Form Kuesioner Siswa</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE,

            // 4. questionnaire/index.blade.php (Siswa tanpa login)
            'resources/views/questionnaire/index.blade.php' => <<<'BLADE'
@extends('layouts.base', ['title' => 'Kuesioner Evaluasi MBG - SMA KP Margahayu'])

@section('body')
<div class="min-h-screen bg-slate-50">
    <!-- Header Hero -->
    <header class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-3xl shadow-inner">
                        🍱
                    </div>
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-400 text-slate-900 mb-1">
                            Kuesioner Siswa (Anonim)
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kuesioner Kualitas Menu MBG</h1>
                        <p class="text-xs sm:text-sm text-emerald-100">SMA Karya Pembangunan Margahayu</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 border border-white/20 text-white transition">
                        <i class="fa-solid fa-lock"></i>
                        <span>Login Admin</span>
                    </a>
                </div>
            </div>

            @if($activePeriod)
            <div class="mt-6 p-4 rounded-xl bg-white/10 backdrop-blur border border-white/20 text-xs sm:text-sm text-emerald-50 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-amber-300 mt-0.5 text-base"></i>
                <div>
                    <p class="font-semibold text-white">{{ $activePeriod->name }}</p>
                    <p class="text-emerald-100/90 text-xs mt-0.5">{{ $activePeriod->description ?? 'Bantu sekolah mengevaluasi cita rasa, kebersihan, porsi, dan kesegaran makanan Program Makan Bergizi Gratis (MBG).' }}</p>
                </div>
            </div>
            @endif
        </div>
    </header>

    <!-- Main Container Form -->
    <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6">
        @if(!$activePeriod)
        <div class="p-8 text-center bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Kuesioner Sedang Ditutup</h2>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Saat ini belum ada periode kuesioner aktif yang dibuka oleh admin sekolah. Silakan kembali lagi nanti.</p>
        </div>
        @else

        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
            <i class="fa-solid fa-user-secret text-amber-600 text-lg"></i>
            <div>
                <span class="font-bold">Privasi Anonim Terjamin:</span> Anda tidak perlu login atau mencantumkan nama/identitas. Jawaban Anda akan dianalisis secara objektif untuk evaluasi perbaikan kualitas menu MBG.
            </div>
        </div>

        <form action="{{ route('questionnaire.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 9 Pertanyaan Esai Aspek -->
            @foreach($questions->where('type', 'essay') as $q)
            <div class="p-6 rounded-2xl bg-white shadow-sm border border-slate-200 hover:border-emerald-300 transition">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-sm flex items-center justify-center">
                            {{ $q->question_number }}
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-snug">
                                {{ $q->question_text }}
                            </h3>
                            @if($q->aspect)
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                Aspek: {{ $q->aspect->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <textarea name="answers[{{ $q->id }}]" rows="3" required
                              placeholder="Tuliskan jawaban atau pendapat Anda sejujur-jujurnya di sini..."
                              class="w-full p-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"></textarea>
                </div>
            </div>
            @endforeach

            <!-- Pertanyaan 10: Rating Kepuasan Keseluruhan -->
            <div class="p-6 rounded-2xl bg-white shadow-sm border-2 border-emerald-400">
                <div class="flex items-start gap-3 mb-4">
                    <span class="flex-shrink-0 w-8 h-8 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center">
                        10
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-snug">
                            Menurut Anda, bagaimana kualitas menu makanan MBG secara keseluruhan?
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih salah satu tingkat kepuasan Anda terhadap program ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @php
                        $ratings = [
                            ['label' => 'Sangat Puas', 'emoji' => '😍', 'color' => 'peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-white'],
                            ['label' => 'Cukup Puas', 'emoji' => '😊', 'color' => 'peer-checked:bg-teal-500 peer-checked:border-teal-500 peer-checked:text-white'],
                            ['label' => 'Kurang Puas', 'emoji' => '😐', 'color' => 'peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:text-white'],
                            ['label' => 'Tidak Puas', 'emoji' => '🙁', 'color' => 'peer-checked:bg-orange-500 peer-checked:border-orange-500 peer-checked:text-white'],
                            ['label' => 'Sangat Tidak Puas', 'emoji' => '😡', 'color' => 'peer-checked:bg-rose-500 peer-checked:border-rose-500 peer-checked:text-white'],
                        ];
                    @endphp

                    @foreach($ratings as $r)
                    <label class="cursor-pointer">
                        <input type="radio" name="overall_rating" value="{{ $r['label'] }}" required class="sr-only peer" {{ $r['label'] == 'Cukup Puas' ? 'checked' : '' }}>
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-center flex flex-col items-center gap-1.5 transition {{ $r['color'] }} hover:bg-slate-100">
                            <span class="text-2xl">{{ $r['emoji'] }}</span>
                            <span class="text-xs font-semibold">{{ $r['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="p-6 rounded-2xl bg-white shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    Pastikan seluruh pertanyaan telah terisi sebelum mengirim kuesioner.
                </div>
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Kuesioner MBG</span>
                </button>
            </div>
        </form>
        @endif
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400 mt-12">
        <p>&copy; {{ date('Y') }} SMA Karya Pembangunan Margahayu - Sistem Penilaian Menu MBG Berbasis Analisis Sentimen NLP.</p>
    </footer>
</div>
@endsection
BLADE,

            // 5. questionnaire/success.blade.php
            'resources/views/questionnaire/success.blade.php' => <<<'BLADE'
@extends('layouts.base', ['title' => 'Terima Kasih - Kuesioner MBG'])

@section('body')
<div class="min-h-screen flex items-center justify-center bg-slate-50 p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 text-center">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">
            <i class="fa-solid fa-heart-circle-check"></i>
        </div>

        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mb-3">
            Kuesioner Berhasil Dikirim!
        </span>

        <h2 class="text-2xl font-black text-slate-800">Terima Kasih Banyak!</h2>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Umpan balik dan penilaian Anda telah tersimpan dengan aman ke dalam sistem secara anonim dan langsung diproses oleh mesin Analisis Sentimen.
        </p>

        @if(session('respondent_code'))
        <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Kode Responden Anonim</span>
            <div class="font-mono text-lg font-bold text-emerald-600 mt-0.5">{{ session('respondent_code') }}</div>
        </div>
        @endif

        <div class="mt-8 flex flex-col gap-3">
            <a href="{{ route('questionnaire.index') }}" class="w-full py-3 px-4 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/30 transition">
                Isi Kuesioner Lainnya
            </a>
            <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl font-semibold text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                Login ke Dashboard Admin
            </a>
        </div>
    </div>
</div>
@endsection
BLADE,
            // 6. admin/dashboard.blade.php
            'resources/views/admin/dashboard.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Dashboard Analisis Sentimen MBG', 'page_subtitle' => 'Ringkasan Evaluasi Kualitas Menu Program MBG SMA Karya Pembangunan Margahayu'])

@section('content')
<!-- Filter Periode & Action Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full md:w-auto">
        <label class="text-xs font-semibold text-slate-500 uppercase">Pilih Periode:</label>
        <select name="period_id" onchange="this.form.submit()" class="text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>
                {{ $p->name }} {{ $p->is_active ? '(Aktif)' : '(Selesai)' }}
            </option>
            @endforeach
        </select>
    </form>

    <div class="flex items-center gap-2 w-full md:w-auto justify-end">
        <form action="{{ route('admin.analysis.run') }}" method="POST">
            @csrf
            <input type="hidden" name="period_id" value="{{ $activePeriod?->id }}">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Jalankan Ulang Analisis Sentimen</span>
            </button>
        </form>

        <a href="{{ route('admin.reports.index', ['period_id' => $activePeriod?->id]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
            <i class="fa-solid fa-file-invoice"></i>
            <span>Buka Laporan Rekomendasi</span>
        </a>
    </div>
</div>

<!-- Stat KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Total Responden Siswa</span>
            <div class="text-2xl font-black text-slate-800">{{ number_format($totalResponses) }}</div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-thumbs-up"></i>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Sentimen Positif</span>
            <div class="text-2xl font-black text-emerald-600">
                {{ $sentimentCounts['Positif'] }}
                <span class="text-xs font-normal text-slate-400">
                    ({{ $totalAnswers > 0 ? round(($sentimentCounts['Positif'] / $totalAnswers) * 100, 1) : 0 }}%)
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-thumbs-down"></i>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Sentimen Negatif</span>
            <div class="text-2xl font-black text-rose-600">
                {{ $sentimentCounts['Negatif'] }}
                <span class="text-xs font-normal text-slate-400">
                    ({{ $totalAnswers > 0 ? round(($sentimentCounts['Negatif'] / $totalAnswers) * 100, 1) : 0 }}%)
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
            <i class="fa-solid fa-star"></i>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Rata-rata Rating Kepuasan</span>
            <div class="text-2xl font-black text-amber-600">{{ $avgSatisfaction }} <span class="text-xs font-normal text-slate-400">/ 5.0</span></div>
        </div>
    </div>
</div>

<!-- Charts Row: Distribusi Sentimen & Rating Kepuasan -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-slate-800 text-sm mb-1">Distribusi Sentimen Keseluruhan</h3>
            <p class="text-xs text-slate-500 mb-4">Hasil klasifikasi Naïve Bayes dari seluruh jawaban esai (9 aspek)</p>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="sentimentDoughnut"></canvas>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-100 text-center">
            <div>
                <span class="text-[11px] text-emerald-600 font-bold block">Positif</span>
                <span class="text-sm font-extrabold text-slate-800">{{ $sentimentCounts['Positif'] }}</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-bold block">Netral</span>
                <span class="text-sm font-extrabold text-slate-800">{{ $sentimentCounts['Netral'] }}</span>
            </div>
            <div>
                <span class="text-[11px] text-rose-600 font-bold block">Negatif</span>
                <span class="text-sm font-extrabold text-slate-800">{{ $sentimentCounts['Negatif'] }}</span>
            </div>
        </div>
    </div>

    <!-- Rating Kepuasan Bar Chart -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Distribusi Rating Kepuasan Keseluruhan (Pertanyaan 10)</h3>
                    <p class="text-xs text-slate-500">Validasi silang antara rating umum siswa dengan analisis sentimen aspek</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    Skor Rata-Rata: {{ $avgSatisfaction }}/5
                </span>
            </div>
            <div class="h-64">
                <canvas id="ratingBarChart"></canvas>
            </div>
        </div>
        <div class="text-[11px] text-slate-500 italic mt-3 pt-3 border-t border-slate-100">
            *Korelasi: Jika rating "Kurang/Tidak Puas" tinggi, cek aspek dengan sentimen negatif terbesar untuk mengetahui penyebab teknisnya.
        </div>
    </div>
</div>

<!-- Prioritas Evaluasi (Aspek Sentimen Negatif Tertinggi) -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
        <div>
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                <span>Prioritas Evaluasi Kualitas Menu (Urutan Sentimen Negatif Tertinggi)</span>
            </h3>
            <p class="text-xs text-slate-500">Identifikasi aspek yang paling banyak dikeluhkan siswa sebagai bahan teguran / evaluasi SPPG</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach(array_slice($negativeRanking, 0, 3) as $idx => $nr)
        <div class="p-4 rounded-xl border {{ $idx == 0 ? 'bg-rose-50/70 border-rose-200' : 'bg-slate-50 border-slate-200' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $idx == 0 ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                    Prioritas #{{ $idx + 1 }}
                </span>
                <span class="text-xs font-bold text-rose-600">{{ $nr['negatif_pct'] }}% Negatif</span>
            </div>
            <h4 class="font-bold text-slate-800 text-sm">{{ $nr['name'] }}</h4>
            <p class="text-xs text-slate-500 mt-1">Total keluhan negatif: <strong>{{ $nr['negatif_count'] }}</strong> dari {{ $nr['total'] }} jawaban.</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Analisis Sentimen Rinci per 9 Aspek -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-slate-800 text-base">Rincian Distribusi Sentimen per 9 Aspek Menu</h3>
            <p class="text-xs text-slate-500">Persentase perbandingan Positif, Netral, dan Negatif pada setiap parameter MBG</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3 font-bold">No</th>
                    <th class="p-3 font-bold">Aspek Evaluasi</th>
                    <th class="p-3 font-bold text-center">Total Respon</th>
                    <th class="p-3 font-bold">Distribusi Sentimen (%)</th>
                    <th class="p-3 font-bold text-center">Positif</th>
                    <th class="p-3 font-bold text-center">Netral</th>
                    <th class="p-3 font-bold text-center">Negatif</th>
                    <th class="p-3 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($aspectAnalysis as $i => $item)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-3 font-bold text-slate-400">{{ $i + 1 }}</td>
                    <td class="p-3">
                        <span class="font-bold text-slate-800 block text-sm">{{ $item['name'] }}</span>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $item['code'] }}</span>
                    </td>
                    <td class="p-3 text-center font-semibold text-slate-700">{{ $item['total'] }}</td>
                    <td class="p-3 min-w-[200px]">
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden flex shadow-inner">
                            <div class="bg-emerald-500 h-3" style="width: {{ $item['positif_pct'] }}%" title="Positif: {{ $item['positif_pct'] }}%"></div>
                            <div class="bg-slate-400 h-3" style="width: {{ $item['netral_pct'] }}%" title="Netral: {{ $item['netral_pct'] }}%"></div>
                            <div class="bg-rose-500 h-3" style="width: {{ $item['negatif_pct'] }}%" title="Negatif: {{ $item['negatif_pct'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-500 mt-1 font-semibold">
                            <span class="text-emerald-600">{{ $item['positif_pct'] }}%</span>
                            <span class="text-slate-500">{{ $item['netral_pct'] }}%</span>
                            <span class="text-rose-600">{{ $item['negatif_pct'] }}%</span>
                        </div>
                    </td>
                    <td class="p-3 text-center text-emerald-700 font-bold bg-emerald-50/50 rounded">{{ $item['positif'] }}</td>
                    <td class="p-3 text-center text-slate-600 font-medium">{{ $item['netral'] }}</td>
                    <td class="p-3 text-center text-rose-700 font-bold bg-rose-50/50 rounded">{{ $item['negatif'] }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.analysis.index', ['aspect_id' => $item['id'], 'period_id' => $activePeriod?->id]) }}" class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 transition">
                            Lihat Jawaban
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Kata Kunci Dominan & Evaluasi Model -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Kata Kunci Dominan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Kata Kunci Dominan (Hasil Stopword Removal & TF-IDF)</h3>
        <p class="text-xs text-slate-500 mb-4">Term yang paling sering muncul dalam respon kuesioner siswa</p>

        <div class="space-y-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1.5 mb-2">
                    <i class="fa-solid fa-circle-dot"></i> Kata Dominan Respon Positif:
                </span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($topWords['Positif'] ?? [] as $w => $c)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $w }} <strong class="text-emerald-950 font-bold">({{ $c }})</strong>
                    </span>
                    @endforeach
                </div>
            </div>

            <div>
                <span class="text-xs font-bold text-rose-600 flex items-center gap-1.5 mb-2">
                    <i class="fa-solid fa-circle-dot"></i> Kata Dominan Respon Negatif (Keluhan):
                </span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($topWords['Negatif'] ?? [] as $w => $c)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200">
                        {{ $w }} <strong class="text-rose-950 font-bold">({{ $c }})</strong>
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Metrik Evaluasi Model Naive Bayes -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Kinerja Model Naïve Bayes</h3>
                    <p class="text-xs text-slate-500">Pengujian model klasifikasi berbasis Confusion Matrix</p>
                </div>
                <a href="{{ route('admin.analysis.metrics') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Kelola Data Latih &rarr;</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                    <span class="text-[10px] uppercase font-bold text-emerald-700 block">Accuracy</span>
                    <span class="text-xl font-black text-emerald-800">{{ $modelEvaluation['accuracy'] ?? 0 }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                    <span class="text-[10px] uppercase font-bold text-blue-700 block">Precision</span>
                    <span class="text-xl font-black text-blue-800">{{ $modelEvaluation['precision'] ?? 0 }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-[10px] uppercase font-bold text-amber-700 block">Recall</span>
                    <span class="text-xl font-black text-amber-800">{{ $modelEvaluation['recall'] ?? 0 }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-purple-50 border border-purple-200">
                    <span class="text-[10px] uppercase font-bold text-purple-700 block">F1-Score</span>
                    <span class="text-xl font-black text-purple-800">{{ $modelEvaluation['f1_score'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total Sampel Data Latih: <strong>{{ $modelEvaluation['total_samples'] ?? 0 }}</strong> teks</span>
            <span class="text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Model Siap Digunakan</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Doughnut Chart Sentimen
    const ctxSent = document.getElementById('sentimentDoughnut').getContext('2d');
    new Chart(ctxSent, {
        type: 'doughnut',
        data: {
            labels: ['Positif', 'Netral', 'Negatif'],
            datasets: [{
                data: [{{ $sentimentCounts['Positif'] }}, {{ $sentimentCounts['Netral'] }}, {{ $sentimentCounts['Negatif'] }}],
                backgroundColor: ['#10b981', '#94a3b8', '#f43f5e'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Bar Chart Rating
    const ratingData = @json($ratingTotals);
    const ratingLabels = ['Sangat Puas', 'Cukup Puas', 'Kurang Puas', 'Tidak Puas', 'Sangat Tidak Puas'];
    const ratingCounts = ratingLabels.map(l => ratingData[l] || 0);

    const ctxRat = document.getElementById('ratingBarChart').getContext('2d');
    new Chart(ctxRat, {
        type: 'bar',
        data: {
            labels: ratingLabels,
            datasets: [{
                label: 'Jumlah Siswa',
                data: ratingCounts,
                backgroundColor: ['#10b981', '#0d9488', '#f59e0b', '#f97316', '#ef4444'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
BLADE,

            // 7. admin/periods/index.blade.php
            'resources/views/admin/periods/index.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Manajemen Periode Kuesioner', 'page_subtitle' => 'Buka, tutup, dan kelola jadwal kuesioner evaluasi program MBG'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Periode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Buka Periode Kuesioner Baru</h3>
        <p class="text-xs text-slate-500 mb-4">Tambahkan siklus evaluasi MBG baru untuk siswa</p>

        <form action="{{ route('admin.periods.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Periode</label>
                <input type="text" name="name" required placeholder="Contoh: Evaluasi Menu MBG - Maret 2026"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Catatan</label>
                <textarea name="description" rows="2" placeholder="Catatan kuesioner..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-slate-700">Set sebagai periode aktif saat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Simpan Periode</span>
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Periode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Daftar Seluruh Periode</h3>
        <p class="text-xs text-slate-500 mb-4">Daftar periode kuesioner beserta status buka/tutup dan jumlah respon siswa</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-3 font-bold">Nama Periode</th>
                        <th class="p-3 font-bold">Rentang Waktu</th>
                        <th class="p-3 font-bold text-center">Responden</th>
                        <th class="p-3 font-bold text-center">Status</th>
                        <th class="p-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($periods as $period)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3">
                            <span class="font-bold text-slate-800 text-sm block">{{ $period->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ Str::limit($period->description, 50) }}</span>
                        </td>
                        <td class="p-3 text-slate-600">
                            {{ $period->start_date ? date('d/m/Y', strtotime($period->start_date)) : '-' }} s/d 
                            {{ $period->end_date ? date('d/m/Y', strtotime($period->end_date)) : '-' }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full font-bold bg-blue-50 text-blue-700 text-xs">
                                {{ $period->responses_count }} siswa
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @if($period->is_active)
                            <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[11px] inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Buka (Aktif)
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full font-bold bg-slate-100 text-slate-600 text-[11px]">
                                Ditutup
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('admin.periods.toggle', $period) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded text-xs font-semibold transition {{ $period->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}">
                                        {{ $period->is_active ? 'Tutup' : 'Buka' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" onsubmit="return confirm('Hapus periode ini beserta seluruh datanya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada periode yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $periods->links() }}
        </div>
    </div>
</div>
@endsection
BLADE,

            // 8. admin/questions/index.blade.php
            'resources/views/admin/questions/index.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Kelola Pertanyaan Kuesioner', 'page_subtitle' => 'Konfigurasi pertanyaan esai per aspek kualitas menu MBG'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Pertanyaan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Tambah Pertanyaan Kuesioner</h3>
        <p class="text-xs text-slate-500 mb-4">Tambahkan butir pertanyaan esai aspek kualitas menu</p>

        <form action="{{ route('admin.questions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Urut</label>
                <input type="number" name="question_number" value="{{ $questions->count() + 1 }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Aspek Terkait</label>
                <select name="aspect_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Tanpa Aspek Khusus (Rating Umum) --</option>
                    @foreach($aspects as $asp)
                    <option value="{{ $asp->id }}">{{ $asp->name }} ({{ $asp->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Pertanyaan</label>
                <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="essay">Esai (Dianalisis Sentimen NLP)</option>
                    <option value="rating">Rating Skala (Kepuasan 1-5)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Teks Pertanyaan</label>
                <textarea name="question_text" rows="3" required placeholder="Tuliskan pertanyaan untuk siswa..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-slate-700">Aktifkan pertanyaan dalam kuesioner</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Simpan Pertanyaan</span>
            </button>
        </form>
    </div>

    <!-- Tabel Pertanyaan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Daftar Pertanyaan Aktif (9 Esai + 1 Rating)</h3>
        <p class="text-xs text-slate-500 mb-4">Pertanyaan ini akan otomatis muncul pada form publik pengisian siswa</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-3 font-bold text-center w-12">No</th>
                        <th class="p-3 font-bold">Pertanyaan & Aspek</th>
                        <th class="p-3 font-bold text-center">Tipe</th>
                        <th class="p-3 font-bold text-center">Status</th>
                        <th class="p-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $q)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 text-center font-bold text-slate-500">{{ $q->question_number }}</td>
                        <td class="p-3">
                            <span class="font-bold text-slate-800 text-sm block mb-0.5">{{ $q->question_text }}</span>
                            @if($q->aspect)
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aspek: {{ $q->aspect->name }}
                            </span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                Umum / Rating Kepuasan
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $q->type == 'essay' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $q->type }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $q->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada pertanyaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
BLADE,
        ];

        foreach ($views as $path => $content) {
            $fullPath = base_path($path);
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0777, true);
            }
            file_put_contents($fullPath, $content);
            $this->command->info("Created: {$path}");
        }
    }
}
