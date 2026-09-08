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
                <td>{{ $m['total'] == 0 ? 'Belum Ada Data' : ($m['negatif_pct'] >= 30 ? 'Perlu Perbaikan Segera' : ($m['negatif_pct'] >= 15 ? 'Perlu Perhatian' : 'Kualitas Baik')) }}</td>
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