<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin / Pihak Sekolah
        User::updateOrCreate(
            ['email' => 'admin@mbg.sch.id'],
            [
                'name' => 'Admin MBG SMA KP Margahayu',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. 9 Aspek Kualitas Menu MBG
        $aspects = [
            ['code' => 'cita_rasa', 'name' => 'Cita Rasa', 'sort_order' => 1, 'description' => 'Evaluasi rasa enak, pas, atau hambar menu makanan'],
            ['code' => 'porsi', 'name' => 'Kesesuaian Porsi', 'sort_order' => 2, 'description' => 'Kesesuaian jumlah porsi dengan kebutuhan makan siang siswa'],
            ['code' => 'variasi', 'name' => 'Variasi Menu', 'sort_order' => 3, 'description' => 'Keberagaman dan keunikan menu makanan harian'],
            ['code' => 'kebersihan', 'name' => 'Kebersihan & Penyajian', 'sort_order' => 4, 'description' => 'Tingkat kebersihan wadah, kemasan, dan higienitas penyajian'],
            ['code' => 'kesegaran', 'name' => 'Kesegaran Bahan Makanan', 'sort_order' => 5, 'description' => 'Kondisi kesegaran sayuran, lauk pauk, dan buah-buahan'],
            ['code' => 'masalah', 'name' => 'Masalah yang Ditemukan', 'sort_order' => 6, 'description' => 'Kendala, insiden makanan rusak/basi/benda asing'],
            ['code' => 'hal_disukai', 'name' => 'Hal yang Paling Disukai', 'sort_order' => 7, 'description' => 'Menu atau elemen favorit siswa dalam program'],
            ['code' => 'saran', 'name' => 'Saran Perbaikan', 'sort_order' => 8, 'description' => 'Usulan konkret untuk meningkatkan kualitas makanan'],
            ['code' => 'kesimpulan', 'name' => 'Kritik & Kesimpulan Umum', 'sort_order' => 9, 'description' => 'Penilaian dan masukan umum program MBG'],
        ];

        $aspectModels = [];
        foreach ($aspects as $asp) {
            $aspectModels[$asp['code']] = \App\Models\Aspect::updateOrCreate(
                ['code' => $asp['code']],
                $asp
            );
        }

        // 3. Periode Kuesioner MBG
        $period = \App\Models\Period::firstOrCreate(
            ['name' => 'Evaluasi Program MBG - Semester Ganjil 2026'],
            [
                'description' => 'Kuesioner evaluasi kualitas menu program Makan Bergizi Gratis (MBG) di SMA Karya Pembangunan Margahayu.',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'is_active' => true,
            ]
        );

        // 4. Pertanyaan Kuesioner (9 Esai + 1 Rating)
        $questions = [
            1 => [
                'aspect_code' => 'cita_rasa',
                'question_number' => 1,
                'question_text' => 'Bagaimana pendapat Anda secara umum mengenai cita rasa (rasa enak/kurang pas/hambar) menu makanan MBG yang disajikan?',
                'type' => 'essay',
            ],
            2 => [
                'aspect_code' => 'porsi',
                'question_number' => 2,
                'question_text' => 'Apakah porsi makanan sudah sesuai dengan kebutuhan makan siang Anda? Jelaskan alasannya.',
                'type' => 'essay',
            ],
            3 => [
                'aspect_code' => 'variasi',
                'question_number' => 3,
                'question_text' => 'Bagaimana pendapat Anda mengenai variasi menu makanan yang diberikan setiap hari?',
                'type' => 'essay',
            ],
            4 => [
                'aspect_code' => 'kebersihan',
                'question_number' => 4,
                'question_text' => 'Bagaimana tingkat kebersihan dan cara penyajian makanan (kemasan/tempat makan) yang Anda terima?',
                'type' => 'essay',
            ],
            5 => [
                'aspect_code' => 'kesegaran',
                'question_number' => 5,
                'question_text' => 'Menurut Anda, bagaimana tingkat kesegaran bahan makanan yang disajikan (misalnya sayuran, lauk, atau buah-buahan)?',
                'type' => 'essay',
            ],
            6 => [
                'aspect_code' => 'masalah',
                'question_number' => 6,
                'question_text' => 'Apakah Anda pernah menemukan masalah pada makanan MBG? Jika pernah, jelaskan.',
                'type' => 'essay',
            ],
            7 => [
                'aspect_code' => 'hal_disukai',
                'question_number' => 7,
                'question_text' => 'Apa hal yang paling Anda sukai dari menu makanan yang pernah disajikan selama program ini berjalan?',
                'type' => 'essay',
            ],
            8 => [
                'aspect_code' => 'saran',
                'question_number' => 8,
                'question_text' => 'Apa saran Anda agar kualitas menu makanan MBG menjadi lebih baik?',
                'type' => 'essay',
            ],
            9 => [
                'aspect_code' => 'kesimpulan',
                'question_number' => 9,
                'question_text' => 'Tuliskan kritik, saran, atau kesimpulan Anda mengenai Program MBG secara keseluruhan.',
                'type' => 'essay',
            ],
            10 => [
                'aspect_code' => null,
                'question_number' => 10,
                'question_text' => 'Menurut Anda, bagaimana kualitas menu makanan MBG secara keseluruhan?',
                'type' => 'rating',
            ],
        ];

        $questionModels = [];
        foreach ($questions as $qNum => $qData) {
            $aspectId = isset($qData['aspect_code']) && isset($aspectModels[$qData['aspect_code']])
                ? $aspectModels[$qData['aspect_code']]->id
                : null;

            $questionModels[$qNum] = \App\Models\Question::updateOrCreate(
                ['question_number' => $qNum],
                [
                    'aspect_id' => $aspectId,
                    'question_text' => $qData['question_text'],
                    'type' => $qData['type'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Training Dataset Sentimen (Lexicon & data latih dasar bahasa Indonesia untuk MBG)
        $this->seedTrainingDataset($aspectModels);

        // 6. Impor Data Awal dari Excel jika belum ada respons
    }

    protected function seedTrainingDataset(array $aspectModels): void
    {
        $samples = [
            // Positif
            ['text' => 'rasa makanannya sangat enak dan gurih', 'label' => 'Positif', 'aspect' => 'cita_rasa'],
            ['text' => 'cita rasa lezat bumbunya meresap mantap', 'label' => 'Positif', 'aspect' => 'cita_rasa'],
            ['text' => 'enak banget apalagi ayam goreng dan kuahnya sedap', 'label' => 'Positif', 'aspect' => 'cita_rasa'],
            ['text' => 'porsi pas mengenyangkan untuk makan siang', 'label' => 'Positif', 'aspect' => 'porsi'],
            ['text' => 'porsi cukup banyak dan bikin kenyang sampai sore', 'label' => 'Positif', 'aspect' => 'porsi'],
            ['text' => 'variasi menu sangat beragam tidak membosankan', 'label' => 'Positif', 'aspect' => 'variasi'],
            ['text' => 'menunya variatif dan kreatif berganti setiap hari', 'label' => 'Positif', 'aspect' => 'variasi'],
            ['text' => 'penyajian sangat bersih rapi dan higienis', 'label' => 'Positif', 'aspect' => 'kebersihan'],
            ['text' => 'kemasan tertutup rapat bersih dan aman', 'label' => 'Positif', 'aspect' => 'kebersihan'],
            ['text' => 'sayuran dan buah selalu segar tidak layu', 'label' => 'Positif', 'aspect' => 'kesegaran'],
            ['text' => 'bahan makanan fresh daging empuk dan segar', 'label' => 'Positif', 'aspect' => 'kesegaran'],
            ['text' => 'tidak pernah ada masalah sama sekali selalu bagus', 'label' => 'Positif', 'aspect' => 'masalah'],
            ['text' => 'tidak ada kendala makanan selalu dalam keadaan baik', 'label' => 'Positif', 'aspect' => 'masalah'],
            ['text' => 'paling suka susu buah dan ayam fillet krispi', 'label' => 'Positif', 'aspect' => 'hal_disukai'],
            ['text' => 'sangat suka menu lengkap ada susu dan buah segar', 'label' => 'Positif', 'aspect' => 'hal_disukai'],
            ['text' => 'program ini sangat bagus bermanfaat sekali membantu siswa', 'label' => 'Positif', 'aspect' => 'kesimpulan'],
            ['text' => 'puas sekali dengan program mbg semoga terus berjalan', 'label' => 'Positif', 'aspect' => 'kesimpulan'],

            // Negatif
            ['text' => 'rasa makanan hambar kurang garam dan tidak ada rasa', 'label' => 'Negatif', 'aspect' => 'cita_rasa'],
            ['text' => 'kurang enak bumbu terasa aneh dan anyep', 'label' => 'Negatif', 'aspect' => 'cita_rasa'],
            ['text' => 'porsi nasi terlalu sedikit kurang mengenyangkan lapar lagi', 'label' => 'Negatif', 'aspect' => 'porsi'],
            ['text' => 'lauknya sangat kecil porsi tidak cukup', 'label' => 'Negatif', 'aspect' => 'porsi'],
            ['text' => 'menu membosankan itu-itu saja setiap minggu sama', 'label' => 'Negatif', 'aspect' => 'variasi'],
            ['text' => 'kurang variatif sering menu yang sama berulang', 'label' => 'Negatif', 'aspect' => 'variasi'],
            ['text' => 'kotor berminyak kemasan ada yang rusak kotor', 'label' => 'Negatif', 'aspect' => 'kebersihan'],
            ['text' => 'ditemukan rambut dan ulat di dalam sayur jorok', 'label' => 'Negatif', 'aspect' => 'masalah'],
            ['text' => 'makanan basi berbau asam tidak layak makan', 'label' => 'Negatif', 'aspect' => 'masalah'],
            ['text' => 'sayur layu dan sudah tidak segar buah masam kecut', 'label' => 'Negatif', 'aspect' => 'kesegaran'],
            ['text' => 'bahan kurang segar tempe keras dan bau apek', 'label' => 'Negatif', 'aspect' => 'kesegaran'],
            ['text' => 'tolong perbaiki kebersihan dan rasa jangan hambar', 'label' => 'Negatif', 'aspect' => 'saran'],
            ['text' => 'kecewa sering basi dan lambat pembagiannya', 'label' => 'Negatif', 'aspect' => 'kesimpulan'],
            ['text' => 'kurang puas kualitas sangat buruk dan mengecewakan', 'label' => 'Negatif', 'aspect' => 'kesimpulan'],

            // Netral
            ['text' => 'rasanya biasa saja standar seperti makanan pada umumnya', 'label' => 'Netral', 'aspect' => 'cita_rasa'],
            ['text' => 'lumayan kadang enak kadang biasa saja', 'label' => 'Netral', 'aspect' => 'cita_rasa'],
            ['text' => 'porsinya cukup standar tidak kurang tidak lebih', 'label' => 'Netral', 'aspect' => 'porsi'],
            ['text' => 'kadang porsi pas kadang kurang tergantung menu', 'label' => 'Netral', 'aspect' => 'porsi'],
            ['text' => 'variasi biasa saja cukup standar kantin', 'label' => 'Netral', 'aspect' => 'variasi'],
            ['text' => 'kebersihan cukup baik standar makanan kotak', 'label' => 'Netral', 'aspect' => 'kebersihan'],
            ['text' => 'kesegaran standar rata-rata seperti biasa', 'label' => 'Netral', 'aspect' => 'kesegaran'],
            ['text' => 'tidak ada masalah yang berarti sejauh ini aman', 'label' => 'Netral', 'aspect' => 'masalah'],
            ['text' => 'belum menemukan masalah apapun', 'label' => 'Netral', 'aspect' => 'masalah'],
            ['text' => 'semua menu biasa saja tidak ada yang spesial', 'label' => 'Netral', 'aspect' => 'hal_disukai'],
            ['text' => 'dipertahankan saja yang sudah ada', 'label' => 'Netral', 'aspect' => 'saran'],
            ['text' => 'cukup baik dan perlu dijaga konsistensinya', 'label' => 'Netral', 'aspect' => 'kesimpulan'],
        ];

        $nlp = app(\App\Services\NlpService::class);

        foreach ($samples as $s) {
            $prep = $nlp->preprocess($s['text']);
            $aspectId = isset($s['aspect']) && isset($aspectModels[$s['aspect']])
                ? $aspectModels[$s['aspect']]->id
                : null;

            \App\Models\TrainingDataset::firstOrCreate(
                ['text' => $s['text']],
                [
                    'label' => $s['label'],
                    'aspect_id' => $aspectId,
                    'stemmed_text' => $prep['stemmed_text'],
                ]
            );
        }
    }

}
