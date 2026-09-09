@extends('layouts.admin', ['page_title' => 'Ekstraksi Fitur TF-IDF', 'page_subtitle' => 'Perhitungan Term Frequency & Inverse Document Frequency untuk pembobotan kata'])

@section('content')
<!-- Filter Aspek TF-IDF -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <form method="GET" action="{{ route('admin.analysis.tfidf') }}" class="flex flex-wrap items-center gap-3">
        <label class="text-xs font-semibold text-slate-500 uppercase">Pilih Aspek:</label>
        <select name="aspect_id" onchange="this.form.submit()" class="text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-800">
            <option value="">Semua Aspek Menu</option>
            @foreach($aspects as $asp)
            <option value="{{ $asp->id }}" {{ $aspectId == $asp->id ? 'selected' : '' }}>{{ $asp->name }}</option>
            @endforeach
        </select>

        <span class="text-xs text-slate-400">Menampilkan sampel pembobotan 50 dokumen jawaban esai</span>
    </form>
</div>

<!-- Penjelasan Rumus TF-IDF -->
<div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-950 text-xs flex items-start gap-3">
    <i class="fa-solid fa-calculator text-blue-800 text-lg mt-0.5"></i>
    <div>
        <h4 class="font-bold mb-0.5">Metodologi TF-IDF (Term Frequency - Inverse Document Frequency):</h4>
        <p class="leading-relaxed">
            TF dihitung berdasarkan frekuensi kemunculan term dalam satu respon esai: <code>TF(t, d) = count(t) / total_words(d)</code>.<br>
            IDF dihitung dengan penghalusan (smoothing): <code>IDF(t) = log((N + 1) / (DF(t) + 1)) + 1</code>. Bobot akhir: <code>W = TF &times; IDF</code>.
        </p>
    </div>
</div>

<!-- Tabel TF-IDF Dokumen Jawaban -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-700">Sampel Skor Bobot TF-IDF per Dokumen Respon</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3.5 font-bold w-12">ID</th>
                    <th class="p-3.5 font-bold">Aspek</th>
                    <th class="p-3.5 font-bold">Teks Hasil Stemming (Dokumen)</th>
                    <th class="p-3.5 font-bold">Term Dominan & Bobot TF-IDF</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($answers as $ans)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-3.5 font-bold text-slate-400">#{{ $ans->id }}</td>
                    <td class="p-3.5 font-semibold text-slate-800">{{ $ans->aspect?->name ?? 'Umum' }}</td>
                    <td class="p-3.5 max-w-sm font-mono text-[11px] text-slate-600">{{ $ans->stemmed_text }}</td>
                    <td class="p-3.5">
                        <div class="flex flex-wrap gap-1.5">
                            @php
                                $tfidf = $tfidfData['tfidf'][$ans->id] ?? [];
                            @endphp
                            @forelse(array_slice($tfidf, 0, 5, true) as $term => $val)
                            <span class="px-2 py-0.5 rounded font-mono text-[10px] bg-slate-100 text-slate-800 border border-slate-200">
                                <strong>{{ $term }}</strong>: {{ $val }}
                            </span>
                            @empty
                            <span class="text-slate-400 italic text-[11px]">-</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada dokumen esai untuk dihitung.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection