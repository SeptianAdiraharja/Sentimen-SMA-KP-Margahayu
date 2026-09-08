@extends('layouts.admin', ['page_title' => 'Evaluasi Kinerja Model Naïve Bayes', 'page_subtitle' => 'Metrik performa (Akurasi, Presisi, Recall, F1-Score) dan pengelolaan data latih'])

@section('content')
<!-- Google Colab & Model .pkl Training & Evaluation Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl border border-indigo-900 shadow-md text-white">
    <!-- Metrik Colab Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-800">
        <div class="bg-white/5 p-3 rounded-xl border border-white/10 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Colab Accuracy</span>
            <div class="text-2xl font-black text-emerald-400">57.14%</div>
            <span class="text-[10px] text-slate-400">8 benar dari 14 data uji</span>
        </div>
        <div class="bg-white/5 p-3 rounded-xl border border-white/10 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Weighted Precision</span>
            <div class="text-2xl font-black text-blue-400">32.65%</div>
            <span class="text-[10px] text-slate-400">Rata-rata terbobot kelas</span>
        </div>
        <div class="bg-white/5 p-3 rounded-xl border border-white/10 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Weighted Recall</span>
            <div class="text-2xl font-black text-amber-400">57.14%</div>
            <span class="text-[10px] text-slate-400">Sensitivitas data uji</span>
        </div>
        <div class="bg-white/5 p-3 rounded-xl border border-white/10 text-center">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Weighted F1-Score</span>
            <div class="text-2xl font-black text-purple-400">41.56%</div>
            <span class="text-[10px] text-slate-400">Harmonic mean P & R</span>
        </div>
    </div>
</div>

<!-- KPI Cards 4 Metrik Model Database Laravel -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Local Accuracy</span>
        <div class="text-3xl font-black text-emerald-600">{{ $evaluation['accuracy'] }}%</div>
        <span class="text-[11px] text-slate-500">Evaluasi Data Latih DB</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Local Precision</span>
        <div class="text-3xl font-black text-blue-600">{{ $evaluation['precision'] }}%</div>
        <span class="text-[11px] text-slate-500">Ketepatan prediksi kelas</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Local Recall</span>
        <div class="text-3xl font-black text-amber-600">{{ $evaluation['recall'] }}%</div>
        <span class="text-[11px] text-slate-500">Sensitivitas kelas</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Local F1-Score</span>
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