<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ViewSeederPart2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $views = [
            // 9. admin/responses/index.blade.php
            'resources/views/admin/responses/index.blade.php' => <<<'BLADE'
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
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Impor Dataset Excel (.xlsx)</span>
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.responses.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
        <div>
            <select name="period_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Periode</option>
                @foreach($periods as $p)
                <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="rating" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
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
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition">
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
        <span class="text-xs font-bold text-slate-700">Daftar Pengisian (Total: {{ $responses->total() }})</span>
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
                    <td class="p-3.5 font-mono font-bold text-emerald-600">
                        {{ $resp->respondent_code }}
                    </td>
                    <td class="p-3.5 text-slate-600">
                        {{ $resp->period?->name ?? '-' }}
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $resp->overall_rating == 'Sangat Puas' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $resp->overall_rating == 'Cukup Puas' ? 'bg-teal-100 text-teal-800' : '' }}
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
                            <a href="{{ route('admin.responses.show', $resp) }}" class="px-2.5 py-1 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
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
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
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
                <select name="period_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">File Excel Dataset (.xlsx / .xls)</label>
                <input type="file" name="excel_file" required accept=".xlsx,.xls,.csv"
                       class="w-full p-2 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[11px] text-slate-400 mt-1">Sistem mendukung format dataset kuesioner MBG (9 kolom pertanyaan esai + 1 kolom rating kepuasan).</p>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-[11px]">
                <i class="fa-solid fa-bolt text-amber-600"></i>
                <strong>Otomatisasi:</strong> Sistem akan langsung menjalankan preprocessing NLP dan klasifikasi Naïve Bayes untuk setiap jawaban yang diimpor.
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                    Unggah & Analisis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE,

            // 10. admin/responses/show.blade.php
            'resources/views/admin/responses/show.blade.php' => <<<'BLADE'
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
BLADE,

            // 11. admin/analysis/index.blade.php
            'resources/views/admin/analysis/index.blade.php' => <<<'BLADE'
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
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-microchip"></i>
                <span>Jalankan Klasifikasi Ulang</span>
            </button>
        </form>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.analysis.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
        <div>
            <select name="period_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Periode</option>
                @foreach($periods as $p)
                <option value="{{ $p->id }}" {{ $periodId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="aspect_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua 9 Aspek</option>
                @foreach($aspects as $asp)
                <option value="{{ $asp->id }}" {{ $aspectId == $asp->id ? 'selected' : '' }}>{{ $asp->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="sentiment" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Sentimen</option>
                <option value="Positif" {{ $sentiment == 'Positif' ? 'selected' : '' }}>Positif</option>
                <option value="Netral" {{ $sentiment == 'Netral' ? 'selected' : '' }}>Netral</option>
                <option value="Negatif" {{ $sentiment == 'Negatif' ? 'selected' : '' }}>Negatif</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition">
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
                    <td class="p-3.5 font-mono font-bold text-emerald-600">
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
BLADE,

            // 12. admin/analysis/tfidf.blade.php
            'resources/views/admin/analysis/tfidf.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Ekstraksi Fitur TF-IDF', 'page_subtitle' => 'Perhitungan Term Frequency & Inverse Document Frequency untuk pembobotan kata'])

@section('content')
<!-- Filter Aspek TF-IDF -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <form method="GET" action="{{ route('admin.analysis.tfidf') }}" class="flex flex-wrap items-center gap-3">
        <label class="text-xs font-semibold text-slate-500 uppercase">Pilih Aspek:</label>
        <select name="aspect_id" onchange="this.form.submit()" class="text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Aspek Menu</option>
            @foreach($aspects as $asp)
            <option value="{{ $asp->id }}" {{ $aspectId == $asp->id ? 'selected' : '' }}>{{ $asp->name }}</option>
            @endforeach
        </select>

        <span class="text-xs text-slate-400">Menampilkan sampel pembobotan 50 dokumen jawaban esai</span>
    </form>
</div>

<!-- Penjelasan Rumus TF-IDF -->
<div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200 text-indigo-950 text-xs flex items-start gap-3">
    <i class="fa-solid fa-calculator text-indigo-600 text-lg mt-0.5"></i>
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
BLADE,

            // 13. admin/analysis/metrics.blade.php
            'resources/views/admin/analysis/metrics.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Evaluasi Kinerja Model Naïve Bayes', 'page_subtitle' => 'Metrik performa (Akurasi, Presisi, Recall, F1-Score) dan pengelolaan data latih'])

@section('content')
<!-- KPI Cards 4 Metrik Utama -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Accuracy</span>
        <div class="text-3xl font-black text-emerald-600">{{ $evaluation['accuracy'] }}%</div>
        <span class="text-[11px] text-slate-500">Rasio tebakan tepat</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Macro Precision</span>
        <div class="text-3xl font-black text-blue-600">{{ $evaluation['precision'] }}%</div>
        <span class="text-[11px] text-slate-500">Ketepatan prediksi kelas</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Macro Recall</span>
        <div class="text-3xl font-black text-amber-600">{{ $evaluation['recall'] }}%</div>
        <span class="text-[11px] text-slate-500">Sensitivitas mendeteksi kelas</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Macro F1-Score</span>
        <div class="text-3xl font-black text-purple-600">{{ $evaluation['f1_score'] }}%</div>
        <span class="text-[11px] text-slate-500">Harmonic mean P & R</span>
    </div>
</div>

<!-- Confusion Matrix & Form Tambah Data Latih -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Confusion Matrix Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Confusion Matrix (3 Kelas Sentimen)</h3>
        <p class="text-xs text-slate-500 mb-4">Perbandingan antara label aktual dataset latih vs hasil prediksi Naïve Bayes</p>

        <div class="overflow-x-auto">
            <table class="w-full text-center text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="p-3 border-r border-b border-slate-200" rowspan="2">Label Aktual</th>
                        <th class="p-2 border-b border-slate-200 font-bold" colspan="3">Label Prediksi (Naïve Bayes)</th>
                    </tr>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold">
                        <th class="p-2.5 text-emerald-700 bg-emerald-50/50">Prediksi Positif</th>
                        <th class="p-2.5 text-slate-600 bg-slate-100">Prediksi Netral</th>
                        <th class="p-2.5 text-rose-700 bg-rose-50/50">Prediksi Negatif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Positif', 'Netral', 'Negatif'] as $act)
                    <tr class="border-b border-slate-100">
                        <td class="p-3 font-bold bg-slate-50 border-r border-slate-200 text-left">
                            {{ $act }}
                        </td>
                        @foreach(['Positif', 'Netral', 'Negatif'] as $pred)
                        <td class="p-3 font-bold {{ $act == $pred ? 'bg-emerald-100 text-emerald-900 font-black text-sm' : 'text-slate-500' }}">
                            {{ $evaluation['confusion_matrix'][$act][$pred] ?? 0 }}
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Tambah Data Latih -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Tambah Data Latih</h3>
        <p class="text-xs text-slate-500 mb-4">Tingkatkan akurasi model dengan menambahkan contoh kalimat</p>

        <form action="{{ route('admin.analysis.dataset.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contoh Teks</label>
                <textarea name="text" rows="3" required placeholder="Contoh: sayurnya sangat bersih dan segar..."
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Label Sentimen</label>
                <select name="label" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="Positif">Positif</option>
                    <option value="Netral">Netral</option>
                    <option value="Negatif">Negatif</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Aspek Terkait (Opsional)</label>
                <select name="aspect_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Umum / Semua Aspek --</option>
                    @foreach($aspects as $asp)
                    <option value="{{ $asp->id }}">{{ $asp->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Simpan ke Training Set
            </button>
        </form>
    </div>
</div>

<!-- Daftar Data Latih -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-700">Daftar Data Latih Saat Ini (Total: {{ $trainingCount }} Sampel)</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <th class="p-3.5 font-bold w-12">No</th>
                    <th class="p-3.5 font-bold">Teks Data Latih</th>
                    <th class="p-3.5 font-bold">Aspek</th>
                    <th class="p-3.5 font-bold text-center">Label Sentimen</th>
                    <th class="p-3.5 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($datasets as $idx => $ds)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-3.5 font-bold text-slate-400">{{ $datasets->firstItem() + $idx }}</td>
                    <td class="p-3.5 text-slate-800 font-medium">
                        "{{ $ds->text }}"
                    </td>
                    <td class="p-3.5 text-slate-500">
                        {{ $ds->aspect?->name ?? 'Umum' }}
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase
                            {{ $ds->label == 'Positif' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $ds->label == 'Netral' ? 'bg-slate-200 text-slate-800' : '' }}
                            {{ $ds->label == 'Negatif' ? 'bg-rose-100 text-rose-800' : '' }}">
                            {{ $ds->label }}
                        </span>
                    </td>
                    <td class="p-3.5 text-right">
                        <form action="{{ route('admin.analysis.dataset.destroy', $ds) }}" method="POST" onsubmit="return confirm('Hapus data latih ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-100">
        {{ $datasets->links() }}
    </div>
</div>
@endsection
BLADE,

            // 14. admin/reports/index.blade.php
            'resources/views/admin/reports/index.blade.php' => <<<'BLADE'
@extends('layouts.admin', ['page_title' => 'Laporan Hasil Evaluasi MBG & Rekomendasi', 'page_subtitle' => 'Dasar rekomendasi perbaikan menu kepada pihak penyedia SPPG/pengelola'])

@section('content')
<!-- Action Bar: Cetak PDF & Ekspor Excel -->
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
        <label class="text-xs font-semibold text-slate-500 uppercase">Periode:</label>
        <select name="period_id" onchange="this.form.submit()" class="text-xs font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @foreach($periods as $p)
            <option value="{{ $p->id }}" {{ $activePeriod && $activePeriod->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
        <a href="{{ route('admin.reports.export-excel', ['period_id' => $activePeriod?->id]) }}"
           class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            <span>Unduh Excel (.xlsx)</span>
        </a>

        <a href="{{ route('admin.reports.print', ['period_id' => $activePeriod?->id]) }}" target="_blank"
           class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Cetak PDF</span>
        </a>
    </div>
</div>

<!-- Kartu Rekomendasi Penting untuk SPPG -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div>
        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
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
                        @if($m['negatif_pct'] >= 30)
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
BLADE,

            // 15. admin/reports/print.blade.php
            'resources/views/admin/reports/print.blade.php' => <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Evaluasi Menu MBG - SMA KP Margahayu</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1e293b; margin: 30px; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 14px; margin: 4px 0; }
        .header p { font-size: 11px; margin: 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; }
        th { background-color: #f1f5f9; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .recommendation-box { border: 1px solid #fecdd3; background-color: #fff1f2; padding: 12px; border-radius: 6px; margin-top: 20px; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { width: 200px; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen Sekarang (PDF)
        </button>
    </div>

    <div class="header">
        <h1>Laporan Evaluasi Kualitas Menu Makan Bergizi Gratis (MBG)</h1>
        <h2>SMA Karya Pembangunan Margahayu</h2>
        <p>Periode: {{ $activePeriod ? $activePeriod->name : 'Seluruh Periode' }} &bull; Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <div>
        <strong>Ringkasan Responden:</strong> Total {{ $totalResponses }} Siswa | Rata-rata Skor Kepuasan: {{ $avgSatisfaction }}/5.0
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Aspek Kualitas Menu</th>
                <th class="text-center">Total Respon</th>
                <th class="text-center">Positif (%)</th>
                <th class="text-center">Netral (%)</th>
                <th class="text-center">Negatif (%)</th>
                <th>Kesimpulan Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aspectMetrics as $idx => $m)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="font-bold">{{ $m['name'] }}</td>
                <td class="text-center">{{ $m['total'] }}</td>
                <td class="text-center">{{ $m['positif_pct'] }}%</td>
                <td class="text-center">{{ $m['netral_pct'] }}%</td>
                <td class="text-center">{{ $m['negatif_pct'] }}%</td>
                <td>{{ $m['negatif_pct'] >= 30 ? 'Perlu Perbaikan Segera' : ($m['negatif_pct'] >= 15 ? 'Perlu Perhatian' : 'Kualitas Baik') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="recommendation-box">
        <strong style="color: #9f1239;">Rekomendasi Utama untuk Pihak Pengelola / SPPG:</strong>
        <ul style="margin: 6px 0 0 16px; padding: 0;">
            @forelse($recommendations as $r)
            <li><strong>{{ $r['aspect'] }}:</strong> {{ $r['notes'] }}</li>
            @empty
            <li>Seluruh aspek menu MBG berjalan dengan baik dan tingkat kepuasan siswa memenuhi standar standar mutu sekolah.</li>
            @endforelse
        </ul>
    </div>

    <table style="border: none; margin-top: 60px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; text-align: center;">
                Mengetahui,<br>Kepala Sekolah SMA KP Margahayu<br><br><br><br>
                <strong>( _________________________ )</strong>
            </td>
            <td style="border: none; width: 50%; text-align: center;">
                Margahayu, {{ date('d F Y') }}<br>Admin Program MBG<br><br><br><br>
                <strong>( {{ auth()->user()?->name ?? 'Admin MBG' }} )</strong>
            </td>
        </tr>
    </table>
</body>
</html>
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
