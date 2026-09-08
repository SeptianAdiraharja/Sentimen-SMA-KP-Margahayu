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