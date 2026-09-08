@extends('layouts.admin', ['page_title' => 'Kelola Pertanyaan Kuesioner', 'page_subtitle' => 'Konfigurasi pertanyaan esai per aspek kualitas menu MBG'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Pertanyaan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Tambah Pertanyaan Kuesioner</h3>
        <p class="text-xs text-slate-500 mb-4">Tambahkan butir pertanyaan esai aspek kualitas menu</p>

        <form action="{{ route('admin.questions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Urut</label>
                <input type="number" name="question_number" value="{{ $questions->count() + 1 }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Aspek Terkait</label>
                <select name="aspect_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Tanpa Aspek Khusus (Rating Umum) --</option>
                    @foreach($aspects as $asp)
                    <option value="{{ $asp->id }}">{{ $asp->name }} ({{ $asp->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Pertanyaan</label>
                <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="essay">Esai (Dianalisis Sentimen NLP)</option>
                    <option value="rating">Rating Skala (Kepuasan 1-5)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Teks Pertanyaan</label>
                <textarea name="question_text" rows="3" required placeholder="Tuliskan pertanyaan untuk siswa..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-slate-700">Aktifkan pertanyaan dalam kuesioner</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Simpan Pertanyaan</span>
            </button>
        </form>
    </div>

    <!-- Tabel Pertanyaan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Daftar Pertanyaan Aktif (9 Esai + 1 Rating)</h3>
        <p class="text-xs text-slate-500 mb-4">Pertanyaan ini akan otomatis muncul pada form publik pengisian siswa</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-3 font-bold text-center w-12">No</th>
                        <th class="p-3 font-bold">Pertanyaan & Aspek</th>
                        <th class="p-3 font-bold text-center">Tipe</th>
                        <th class="p-3 font-bold text-center">Status</th>
                        <th class="p-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $q)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 text-center font-bold text-slate-500">{{ $q->question_number }}</td>
                        <td class="p-3">
                            <span class="font-bold text-slate-800 text-sm block mb-0.5">{{ $q->question_text }}</span>
                            @if($q->aspect)
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aspek: {{ $q->aspect->name }}
                            </span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                Umum / Rating Kepuasan
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $q->type == 'essay' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $q->type }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $q->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada pertanyaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection