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