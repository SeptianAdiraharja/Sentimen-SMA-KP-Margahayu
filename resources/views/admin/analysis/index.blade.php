@extends('layouts.admin', ['page_title' => 'Hasil Klasifikasi Sentimen Naïve Bayes', 'page_subtitle' => 'Daftar hasil klasifikasi per jawaban esai siswa pada setiap aspek kualitas menu'])

@section('content')
<!-- Action & Filter Bar -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">Filter Berdasarkan Aspek & Sentimen</h3>
            <p class="text-xs text-slate-500">Telusuri jawaban berdasarkan label Positif, Negatif, atau Netral</p>
        </div>

        <form action="{{ route('admin.analysis.run') }}" method="POST">
            @csrf
            <input type="hidden" name="period_id" value="{{ $periodId }}">
            <input type="hidden" name="aspect_id" value="{{ $aspectId }}">
            <button type="submit" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-blue-950 font-extrabold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-microchip"></i>
                <span>Jalankan Klasifikasi Ulang</span>
            </button>
        </form>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.analysis.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
        <div>
            <select name="period_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                <option value="">Semua Periode</option>
                @foreach($periods as $p)
                <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="aspect_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                <option value="">Semua 9 Aspek</option>
                @foreach($aspects as $asp)
                <option value="{{ $asp->id }}" {{ $aspectId == $asp->id ? 'selected' : '' }}>{{ $asp->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="sentiment" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                <option value="">Semua Sentimen</option>
                <option value="Positif" {{ $sentiment == 'Positif' ? 'selected' : '' }}>Positif</option>
                <option value="Netral" {{ $sentiment == 'Netral' ? 'selected' : '' }}>Netral</option>
                <option value="Negatif" {{ $sentiment == 'Negatif' ? 'selected' : '' }}>Negatif</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 px-3 bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs rounded-xl transition">
                <i class="fa-solid fa-filter"></i> Terapkan
            </button>
            <a href="{{ route('admin.analysis.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Jawaban dan Sentimen -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3.5 font-bold">Responden</th>
                    <th class="p-3.5 font-bold">Aspek Menu</th>
                    <th class="p-3.5 font-bold">Jawaban Esai Siswa</th>
                    <th class="p-3.5 font-bold">Stemmed Teks (Sastrawi)</th>
                    <th class="p-3.5 font-bold text-center">Hasil Sentimen</th>
                    <th class="p-3.5 font-bold text-right">Confidence</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($answers as $ans)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-3.5 font-mono font-bold text-blue-900">
                        {{ $ans->response?->respondent_code ?? '-' }}
                    </td>
                    <td class="p-3.5">
                        <span class="font-bold text-slate-800">{{ $ans->aspect?->name ?? 'Umum' }}</span>
                    </td>
                    <td class="p-3.5 max-w-xs text-slate-700">
                        {{ $ans->raw_answer }}
                    </td>
                    <td class="p-3.5 max-w-xs font-mono text-[11px] text-slate-500">
                        {{ $ans->stemmed_text ?: '-' }}
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase
                            {{ $ans->sentiment == 'Positif' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $ans->sentiment == 'Netral' ? 'bg-slate-200 text-slate-800' : '' }}
                            {{ $ans->sentiment == 'Negatif' ? 'bg-rose-100 text-rose-800' : '' }}">
                            {{ $ans->sentiment }}
                        </span>
                    </td>
                    <td class="p-3.5 text-right font-mono font-bold text-slate-600">
                        {{ round($ans->confidence_score * 100, 1) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">Tidak ada data jawaban sesuai filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100">
        {{ $answers->links() }}
    </div>
</div>
@endsection