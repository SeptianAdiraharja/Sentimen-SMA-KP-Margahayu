@extends('layouts.admin', ['page_title' => 'Data Responden Siswa', 'page_subtitle' => 'Daftar seluruh jawaban kuesioner siswa yang masuk secara anonim'])

@section('content')
<!-- Filter & Import Excel Header -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">Filter & Pencarian Jawaban Siswa</h3>
            <p class="text-xs text-slate-500">Cari berdasarkan kode responden anonim atau kata dalam esai</p>
        </div>

        <!-- Tombol Modal Import Excel Dataset -->
        <div>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-amber-400 hover:bg-amber-300 text-blue-950 font-bold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Impor Dataset Excel (.xlsx)</span>
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.responses.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
        <div>
            <select name="period_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                <option value="">Semua Periode</option>
                @foreach($periods as $p)
                <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="rating" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                <option value="">Semua Rating Kepuasan</option>
                <option value="Sangat Puas" {{ $rating == 'Sangat Puas' ? 'selected' : '' }}>Sangat Puas</option>
                <option value="Cukup Puas" {{ $rating == 'Cukup Puas' ? 'selected' : '' }}>Cukup Puas</option>
                <option value="Kurang Puas" {{ $rating == 'Kurang Puas' ? 'selected' : '' }}>Kurang Puas</option>
                <option value="Tidak Puas" {{ $rating == 'Tidak Puas' ? 'selected' : '' }}>Tidak Puas</option>
                <option value="Sangat Tidak Puas" {{ $rating == 'Sangat Tidak Puas' ? 'selected' : '' }}>Sangat Tidak Puas</option>
            </select>
        </div>

        <div>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode/kata respon..."
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 px-3 bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs rounded-xl transition">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
            <a href="{{ route('admin.responses.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Responden -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-700">Daftar Hasil Jawaban Siswa (Total: {{ $responses->total() }})</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3.5 font-bold">Kode Responden</th>
                    <th class="p-3.5 font-bold">Periode</th>
                    <th class="p-3.5 font-bold text-center">Rating Umum</th>
                    <th class="p-3.5 font-bold text-center">Ringkasan Sentimen Jawaban</th>
                    <th class="p-3.5 font-bold">Waktu Submit</th>
                    <th class="p-3.5 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($responses as $resp)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-3.5 font-mono font-bold text-blue-900">
                        {{ $resp->respondent_code }}
                    </td>
                    <td class="p-3.5 text-slate-600">
                        {{ $resp->period?->name ?? '-' }}
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $resp->overall_rating == 'Sangat Puas' ? 'bg-blue-900 text-amber-300' : '' }}
                            {{ $resp->overall_rating == 'Cukup Puas' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $resp->overall_rating == 'Kurang Puas' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ in_array($resp->overall_rating, ['Tidak Puas', 'Sangat Tidak Puas']) ? 'bg-rose-100 text-rose-800' : '' }}">
                            {{ $resp->overall_rating }}
                        </span>
                    </td>
                    <td class="p-3.5 text-center">
                        @php
                            $pCount = $resp->answers->where('sentiment', 'Positif')->count();
                            $neCount = $resp->answers->where('sentiment', 'Netral')->count();
                            $ngCount = $resp->answers->where('sentiment', 'Negatif')->count();
                        @endphp
                        <div class="inline-flex items-center gap-1.5 font-bold text-[11px]">
                            <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">{{ $pCount }} Positif</span>
                            <span class="text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $neCount }} Netral</span>
                            <span class="text-rose-600 bg-rose-50 px-2 py-0.5 rounded">{{ $ngCount }} Negatif</span>
                        </div>
                    </td>
                    <td class="p-3.5 text-slate-400 text-[11px]">
                        {{ $resp->created_at ? $resp->created_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="p-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('admin.responses.show', $resp) }}" class="px-2.5 py-1 rounded text-xs font-bold bg-blue-50 text-blue-900 hover:bg-blue-100 transition">
                                Detail 9 Aspek
                            </a>
                            <form action="{{ route('admin.responses.destroy', $resp) }}" method="POST" onsubmit="return confirm('Hapus data respon ini?');">
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
                    <td colspan="6" class="p-6 text-center text-slate-400">Tidak ada data responden ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100">
        {{ $responses->links() }}
    </div>
</div>

<!-- Modal Import Excel Dataset -->
<div id="importModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-amber-500"></i>
                <span>Impor Dataset Kuesioner Excel</span>
            </h3>
            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.responses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Periode Tujuan</label>
                <select name="period_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                    @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih File Excel Dataset (.xlsx / .xls)</label>
                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv"
                       class="w-full p-2 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-900 hover:file:bg-blue-100">
                <p class="text-[11px] text-slate-500 mt-1">
                    <i class="fa-solid fa-circle-info text-blue-800"></i> Kosongkan jika ingin langsung menggunakan file lokal di lokasi: <code class="font-mono text-blue-900 bg-blue-50 px-1 py-0.5 rounded">C:\Users\user\OneDrive\Desktop\vrillia\data kuesioner.xlsx</code>.
                </p>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-[11px] space-y-1">
                <div class="flex items-center gap-1.5 font-bold">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i>
                    <span>Fitur Deteksi Pertanyaan Otomatis:</span>
                </div>
                <p>Jika pertanyaan dari header kolom Excel belum ada di database, sistem akan otomatis mendaftarkannya ke database dan memetakan aspeknya.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-blue-950 font-extrabold text-xs shadow-md shadow-amber-400/20 transition">
                    Unggah & Analisis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection