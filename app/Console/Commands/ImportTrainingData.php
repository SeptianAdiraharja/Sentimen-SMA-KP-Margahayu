<?php

namespace App\Console\Commands;

use App\Models\Aspect;
use App\Models\TrainingDataset;
use App\Services\NlpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTrainingData extends Command
{
    protected $signature = 'training:import
                            {file=database/data/data_latih_mbg.csv : Lokasi file CSV, relatif terhadap folder proyek}
                            {--fresh : Hapus SEMUA data training lama (termasuk data dummy) sebelum impor}';

    protected $description = 'Impor data latih sentimen dari CSV (kolom: text, label, aspect_code opsional)';

    private const LABEL_VALID = ['Positif', 'Negatif', 'Netral'];

    public function handle(NlpService $nlp): int
    {
        $path = base_path($this->argument('file'));
        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        try {
            [$rows, $dilewati] = $this->bacaCsv($path);
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        if (empty($rows)) {
            $this->error('Tidak ada baris valid di file CSV.');
            return self::FAILURE;
        }

        if ($this->option('fresh') && ! $this->confirm('Ini akan MENGHAPUS semua data training yang ada sekarang (termasuk data dummy). Lanjutkan?')) {
            $this->warn('Dibatalkan. Tidak ada data yang diubah.');
            return self::FAILURE;
        }

        // Preprocessing dilakukan lebih dulu (di luar transaksi) dengan NlpService yang sama
        // seperti saat prediksi, supaya data latih dan data uji diproses dengan cara yang sama.
        $this->info('Memproses ' . count($rows) . ' baris (stemming bisa memakan waktu beberapa saat)...');

        $records = [];
        $aspekCache = [];
        $stemKosong = 0;

        foreach ($rows as $r) {
            $prep = $nlp->preprocess($r['text']);
            if (trim($prep['stemmed_text']) === '') {
                $stemKosong++;
                continue;
            }

            $aspectId = null;
            if ($r['aspect_code'] !== '') {
                if (! array_key_exists($r['aspect_code'], $aspekCache)) {
                    $aspekCache[$r['aspect_code']] = Aspect::where('code', $r['aspect_code'])->value('id');
                }
                $aspectId = $aspekCache[$r['aspect_code']];
                if (! $aspectId) {
                    $dilewati[] = "aspect_code '{$r['aspect_code']}' tidak ditemukan di tabel aspects";
                    continue;
                }
            }

            $records[] = [
                'text'         => $r['text'],
                'label'        => $r['label'],
                'aspect_id'    => $aspectId,
                'stemmed_text' => $prep['stemmed_text'],
            ];
        }

        DB::transaction(function () use ($records) {
            if ($this->option('fresh')) {
                TrainingDataset::query()->delete();
            }
            foreach ($records as $rec) {
                TrainingDataset::updateOrCreate(['text' => $rec['text']], $rec);
            }
        });

        $per = [];
        foreach ($records as $rec) {
            $per[$rec['label']] = ($per[$rec['label']] ?? 0) + 1;
        }
        $this->info('Impor selesai: ' . count($records) . ' baris tersimpan.');
        $this->table(['Label', 'Jumlah'], array_map(fn ($l, $n) => [$l, $n], array_keys($per), $per));

        if ($stemKosong > 0) {
            $this->warn("{$stemKosong} baris dilewati karena teksnya kosong setelah preprocessing.");
        }
        foreach (array_slice($dilewati, 0, 10) as $d) {
            $this->warn('Dilewati: ' . $d);
        }
        if (count($dilewati) > 10) {
            $this->warn('... dan ' . (count($dilewati) - 10) . ' baris lain dilewati.');
        }

        return self::SUCCESS;
    }

    /**
     * Baca CSV. Mendukung pemisah koma atau titik koma (Excel versi Indonesia
     * biasanya menyimpan CSV dengan titik koma) dan menghapus BOM UTF-8.
     */
    private function bacaCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $barisPertama = fgets($handle);
        if ($barisPertama === false) {
            fclose($handle);
            throw new \RuntimeException('File CSV kosong.');
        }

        $barisPertama = preg_replace('/^\xEF\xBB\xBF/', '', $barisPertama);
        $delimiter = substr_count($barisPertama, ';') > substr_count($barisPertama, ',') ? ';' : ',';
        $header = array_map(fn ($h) => strtolower(trim($h)), str_getcsv($barisPertama, $delimiter, '"', ''));

        foreach (['text', 'label'] as $wajib) {
            if (! in_array($wajib, $header, true)) {
                fclose($handle);
                throw new \RuntimeException("Kolom wajib '{$wajib}' tidak ada. Kolom ditemukan: " . implode(', ', $header));
            }
        }

        $rows = [];
        $dilewati = [];
        $no = 1;

        while (($baris = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $no++;
            if ($baris === [null]) {
                continue; // baris kosong
            }

            $baris = array_slice(array_pad($baris, count($header), ''), 0, count($header));
            $data = array_combine($header, $baris);

            $text  = trim((string) $data['text']);
            $label = ucfirst(strtolower(trim((string) $data['label'])));

            if ($text === '') {
                $dilewati[] = "baris ~{$no}: teks kosong";
                continue;
            }
            if (! in_array($label, self::LABEL_VALID, true)) {
                $dilewati[] = "baris ~{$no}: label '{$data['label']}' tidak dikenal (harus Positif/Negatif/Netral)";
                continue;
            }

            $rows[] = [
                'text'        => $text,
                'label'       => $label,
                'aspect_code' => trim((string) ($data['aspect_code'] ?? '')),
            ];
        }

        fclose($handle);
        return [$rows, $dilewati];
    }
}
