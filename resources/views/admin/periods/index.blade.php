@extends('layouts.admin', ['page_title' => 'Manajemen Periode Kuesioner', 'page_subtitle' => 'Buka, tutup, dan kelola jadwal kuesioner evaluasi program MBG'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Periode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Buka Periode Kuesioner Baru</h3>
        <p class="text-xs text-slate-500 mb-4">Tambahkan siklus evaluasi MBG baru untuk siswa</p>

        <form action="{{ route('admin.periods.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Periode</label>
                <input type="text" name="name" required placeholder="Contoh: Evaluasi Menu MBG - Maret 2026"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-800 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Catatan</label>
                <textarea name="description" rows="2" placeholder="Catatan kuesioner..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-800 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-800 focus:outline-none">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-blue-800 focus:ring-blue-800">
                    <span class="text-xs font-semibold text-slate-700">Set sebagai periode aktif saat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-amber-400 hover:bg-amber-300 text-blue-950 font-extrabold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Simpan Periode</span>
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Periode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="font-bold text-slate-800 text-sm mb-1">Daftar Seluruh Periode</h3>
        <p class="text-xs text-slate-500 mb-4">Daftar periode kuesioner beserta status buka/tutup dan jumlah respon siswa</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-3 font-bold">Nama Periode</th>
                        <th class="p-3 font-bold">Rentang Waktu</th>
                        <th class="p-3 font-bold text-center">Responden</th>
                        <th class="p-3 font-bold text-center">Status</th>
                        <th class="p-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($periods as $period)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3">
                            <span class="font-bold text-slate-800 text-sm block">{{ $period->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ Str::limit($period->description, 50) }}</span>
                        </td>
                        <td class="p-3 text-slate-600">
                            {{ $period->start_date ? date('d/m/Y', strtotime($period->start_date)) : '-' }} s/d 
                            {{ $period->end_date ? date('d/m/Y', strtotime($period->end_date)) : '-' }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full font-bold bg-blue-50 text-blue-700 text-xs">
                                {{ $period->responses_count }} siswa
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @if($period->is_active)
                            <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[11px] inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Buka (Aktif)
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full font-bold bg-slate-100 text-slate-600 text-[11px]">
                                Ditutup
                            </span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('admin.periods.toggle', $period) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded text-xs font-semibold transition {{ $period->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                                        {{ $period->is_active ? 'Tutup' : 'Buka' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" onsubmit="return confirm('Hapus periode ini beserta seluruh datanya?');">
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
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada periode yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $periods->links() }}
        </div>
    </div>
</div>
@endsection