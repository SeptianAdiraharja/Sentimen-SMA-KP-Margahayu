@extends('layouts.admin', ['page_title' => 'Laporan Hasil Evaluasi MBG & Rekomendasi', 'page_subtitle' => 'Dasar rekomendasi perbaikan menu kepada pihak penyedia SPPG/pengelola'])

@section('content')
<!-- Action Bar: Cetak PDF & Ekspor Excel -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
        <label class="text-xs font-semibold text-slate-500 uppercase">Periode:</label>
        <select name="period_id" onchange="this.form.submit()" class="text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-800">
            @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
        <a href="{{ route('admin.reports.export-excel', ['period_id' => $activePeriod?->id]) }}"
           class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-blue-950 text-xs font-extrabold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            <span>Unduh Excel (.xlsx)</span>
        </a>

        <a href="{{ route('admin.reports.print', ['period_id' => $activePeriod?->id]) }}" target="_blank"
           class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Cetak PDF</span>
        </a>
    </div>
</div>

<!-- Kartu Rekomendasi Penting untuk SPPG -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div>
        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <i class="fa-solid fa-clipboard-check text-blue-800"></i>
            <span>Rekomendasi Perbaikan untuk Pihak Pengelola / SPPG</span>
        </h3>
        <p class="text-xs text-slate-500">Berdasarkan hasil analisis sentimen pada 9 aspek kualitas makanan MBG</p>
    </div>

    <div class="space-y-3">
        @forelse($recommendations as $rec)
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5 text-base"></i>
            <div class="text-xs">
                <span class="font-bold text-rose-900 text-sm block">Perhatian Khusus: Aspek {{ $rec['aspect'] }}</span>
                <p class="text-rose-800 mt-0.5 leading-relaxed">{{ $rec['notes'] }}</p>
            </div>
        </div>
        @empty
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <div>
                <strong>Kualitas Menu Secara Umum Sangat Baik:</strong> Tidak ada aspek menu dengan keluhan negatif di atas batas ambang toleransi 20%.
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Tabel Ringkasan Evaluasi Kualitas Menu -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-700">Tabel Evaluasi Sentimen per Aspek Kualitas Menu MBG</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3.5 font-bold w-12">No</th>
                    <th class="p-3.5 font-bold">Aspek Kualitas</th>
                    <th class="p-3.5 font-bold text-center">Total Respon</th>
                    <th class="p-3.5 font-bold text-center text-emerald-700 bg-emerald-50/50">Positif (%)</th>
                    <th class="p-3.5 font-bold text-center text-slate-700">Netral (%)</th>
                    <th class="p-3.5 font-bold text-center text-rose-700 bg-rose-50/50">Negatif (%)</th>
                    <th class="p-3.5 font-bold text-center">Status Evaluasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($aspectMetrics as $idx => $m)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-3.5 font-bold text-slate-400">{{ $idx + 1 }}</td>
                    <td class="p-3.5 font-bold text-slate-800 text-sm">{{ $m['name'] }}</td>
                    <td class="p-3.5 text-center font-semibold text-slate-600">{{ $m['total'] }}</td>
                    <td class="p-3.5 text-center font-bold text-emerald-700 bg-emerald-50/30">
                        {{ $m['positif'] }} ({{ $m['positif_pct'] }}%)
                    </td>
                    <td class="p-3.5 text-center text-slate-600">
                        {{ $m['netral'] }} ({{ $m['netral_pct'] }}%)
                    </td>
                    <td class="p-3.5 text-center font-bold text-rose-700 bg-rose-50/30">
                        {{ $m['negatif'] }} ({{ $m['negatif_pct'] }}%)
                    </td>
                    <td class="p-3.5 text-center">
                        @if($m['total'] == 0)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                            Belum Ada Data
                        </span>
                        @elseif($m['negatif_pct'] >= 30)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800">
                            Perlu Perbaikan Segera
                        </span>
                        @elseif($m['negatif_pct'] >= 15)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                            Perlu Perhatian
                        </span>
                        @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                            Kualitas Baik
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection