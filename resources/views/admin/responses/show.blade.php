@extends('layouts.admin', ['page_title' => 'Detail Jawaban Responden ' . $response->respondent_code, 'page_subtitle' => 'Hasil rincian 9 pertanyaan esai beserta tahapan preprocessing NLP'])

@section('content')
<div class="space-y-6">
    <!-- Header Responden Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-xl font-mono font-black text-emerald-600">{{ $response->respondent_code }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $response->overall_rating == 'Sangat Puas' ? 'bg-emerald-100 text-emerald-800' : '' }}
                    {{ $response->overall_rating == 'Cukup Puas' ? 'bg-teal-100 text-teal-800' : '' }}
                    {{ $response->overall_rating == 'Kurang Puas' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ in_array($response->overall_rating, ['Tidak Puas', 'Sangat Tidak Puas']) ? 'bg-rose-100 text-rose-800' : '' }}">
                    Rating: {{ $response->overall_rating }} ({{ $response->overall_rating_score }}/5)
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Periode: <strong>{{ $response->period?->name }}</strong> &bull; 
                Dikirim pada: {{ $response->created_at ? $response->created_at->format('d M Y, H:i:s') : '-' }}
            </p>
        </div>

        <a href="{{ route('admin.responses.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-1.5 w-fit">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- 9 Jawaban Esai per Aspek -->
    <div class="space-y-4">
        @foreach($response->answers as $idx => $ans)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs flex items-center justify-center">
                        {{ $ans->question?->question_number ?? ($idx + 1) }}
                    </span>
                    <span class="font-bold text-slate-800 text-sm">
                        Aspek: {{ $ans->aspect?->name ?? 'Umum' }}
                    </span>
                </div>

                <!-- Label Sentimen -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400">Sentimen:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase
                        {{ $ans->sentiment == 'Positif' ? 'bg-emerald-100 text-emerald-800' : '' }}
                        {{ $ans->sentiment == 'Netral' ? 'bg-slate-200 text-slate-800' : '' }}
                        {{ $ans->sentiment == 'Negatif' ? 'bg-rose-100 text-rose-800' : '' }}">
                        {{ $ans->sentiment }} ({{ round($ans->confidence_score * 100, 1) }}%)
                    </span>
                </div>
            </div>

            <p class="text-xs text-slate-500 mb-2 italic">"{{ $ans->question?->question_text }}"</p>

            <!-- Jawaban Asli Siswa -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 text-sm font-medium">
                {{ $ans->raw_answer }}
            </div>

            <!-- Preprocessing NLP Pipeline Details (Accordion/Toggle) -->
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-code-fork text-emerald-600"></i>
                    <span>Pipeline NLP (Case Folding &rarr; Cleaning &rarr; Tokens &rarr; Stopword &rarr; Stemming Sastrawi):</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 bg-slate-50 rounded-lg">
                        <span class="font-semibold text-slate-500 block text-[10px]">Teks Bersih (Cleaned):</span>
                        <code class="text-slate-700 font-mono text-[11px]">{{ $ans->clean_answer ?: '-' }}</code>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg">
                        <span class="font-semibold text-slate-500 block text-[10px]">Hasil Stemming Sastrawi:</span>
                        <code class="text-emerald-700 font-mono font-bold text-[11px]">{{ $ans->stemmed_text ?: '-' }}</code>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection