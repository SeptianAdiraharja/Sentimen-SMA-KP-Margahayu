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