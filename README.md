# Sistem Penilaian Kualitas Menu MBG Berbasis Analisis Sentimen (NLP + TF-IDF + Naive Bayes)
### Studi Kasus: SMA Karya Pembangunan Margahayu

Sistem informasi berbasis web yang mengintegrasikan formulir survei evaluasi kepuasan program Makan Bergizi Gratis (MBG), pengolahan bahasa alami (*Natural Language Processing / NLP*), pembobotan kata *Term Frequency - Inverse Document Frequency (TF-IDF)*, dan klasifikasi algoritma *Multinomial Naive Bayes* untuk menilai 9 aspek kualitas menu makanan secara objektif.

---

## 1. Alur Proses Bisnis Sistem

Berdasarkan perancangan analisis kebutuhan (lur-sistem-mbg.md), alur proses bisnis sistem berjalan sebagai berikut:

`
[ Siswa (Tanpa Login) ]
       │
       ▼
1. Akses Form Kuesioner MBG (9 Pertanyaan Esai Aspek + 1 Rating Skala Kepuasan)
       │
       ▼
2. Submit Kuesioner (Tersimpan sebagai respon anonim di MySQL)
       │
       ▼
[ Laravel Controller ] ──(Panggil Engine NLP via Python)──► [ Python + Sastrawi ]
                                                                   │
                                                            - Preprocessing:
                                                              * Cleaning & Regex
                                                              * Case Folding
                                                              * Tokenizing
                                                              * Stopword Removal (Kata negasi dijaga)
                                                              * Stemming
                                                                   │
                                                                   ▼
                                                            - Ekstraksi Fitur TF-IDF
                                                            - Klasifikasi Multinomial Naive Bayes
                                                            - Model Pipeline: model_tfidf_naive_bayes.pkl
                                                                   │
       ◄──(Simpan Hasil Klasifikasi: Positif/Netral/Negatif)───────┘
       │
       ▼
[ MySQL Database (mbg) ]
- Data Responden (questionnaire_responses)
- Jawaban & Sentimen (
esponse_answers)
       │
       ▼
[ Admin / Pihak Sekolah (Wajib Login) ]
1. Buka/Tutup Periode Kuesioner
2. Kelola Pertanyaan & Aspek Menu MBG
3. Import Dataset Kuesioner (.xlsx)
4. Dashboard Visual:
   - Distribusi sentimen 9 aspek kualitas menu
   - Korelasi rating kepuasan vs analisis sentimen
   - Identifikasi keluhan negatif tertinggi sebagai prioritas perbaikan SPPG
   - Word cloud kata kunci dominan siswa
5. Metrik Evaluasi Model (Akurasi, Presisi, Recall, F1-Score, Confusion Matrix)
6. Unduh Laporan Rekomendasi Menu MBG (Cetak/PDF & Ekspor Excel 2 Sheet)
`

### 9 Aspek Kualitas Menu MBG:
1. **Cita Rasa**: Evaluasi kelezatan rasa, bumbu pas, atau hambar.
2. **Kesesuaian Porsi**: Kecukupan takaran porsi makan siang siswa.
3. **Variasi Menu**: Keberagaman dan variasi pergantian menu harian.
4. **Kebersihan & Penyajian**: Wadah kemasan, tempat makan, dan higienitas.
5. **Kesegaran Bahan Makanan**: Kualitas kesegaran sayuran, lauk pauk, dan buah.
6. **Masalah yang Ditemukan**: Kejadian makanan rusak/basi/benda asing.
7. **Hal yang Paling Disukai**: Menu atau elemen favorit siswa.
8. **Saran Perbaikan**: Usulan konstruktif perbaikan menu.
9. **Kritik & Kesimpulan Umum**: Masukan evaluasi program secara keseluruhan.

---

## 2. Arsitektur Komponen & Tanggung Jawab

1. **Google Colab (rillia.ipynb)**:
   - Eksplorasi data, pelabelan sentimen dari skor rating kepuasan.
   - *Split dataset* (80% Data Latih / 20% Data Uji).
   - Melatih pipeline Scikit-Learn (TfidfVectorizer + MultinomialNB).
   - Evaluasi performa model: **Accuracy 57.14%**, **Precision 32.65%**, **Recall 57.14%**, **F1-Score 41.56%**.
   - Menyimpan model terlatih ke file serialisasi model_tfidf_naive_bayes.pkl.
2. **Python Engine (python_sentiment.py)**:
   - Menjalankan pipeline preprocessing bahasa Indonesia (library Sastrawi).
   - Memuat model_tfidf_naive_bayes.pkl untuk inferensi *single* maupun *batch*.
   - Mengembalikan data terstruktur dalam format JSON (*clean text*, *tokens*, *stemmed text*, *sentiment*, *confidence*, *class scores*).
3. **Model Serialisasi (model_tfidf_naive_bayes.pkl)**:
   - Menyimpan 538 fitur bobot *vocabulary* TF-IDF serta parameter probabilitas *prior* dan *likelihood* Naive Bayes.
4. **Laravel Framework (pp-mbg)**:
   - Antarmuka web modern dengan Tailwind CSS & Chart.js.
   - Formulir kuesioner siswa anonim (tanpa login).
   - Autentikasi dan hak akses Admin pihak sekolah.
   - *Service bridge* (PythonSentimentService.php) untuk eksekusi Python runtime.
   - Visualisasi dashboard analitik, matriks evaluasi, dan cetak laporan.
5. **MySQL Database (mbg)**:
   - Menyimpan data periode, aspek, pertanyaan, responden anonim, dan jawaban esai lengkap beserta tahapan NLP dan kelas sentimennya.

---

## 3. Prasyarat Sistem & Dependensi

Pastikan perangkat Anda telah terpasang:
- **PHP**: Versi >= 8.2 (dengan ekstensi pdo_mysql, mbstring, ileinfo, xml, gd, zip)
- **Composer**: Versi >= 2.x
- **Node.js & NPM**: Versi >= 18.x
- **MySQL / MariaDB**: Server aktif (misalnya melalui Laragon atau XAMPP)
- **Python**: Versi >= 3.10 atau 3.12
- **Git**: Untuk clone repository

---

## 4. Panduan Instalasi & Menjalankan Proyek (Tutorial Clone)

Ikuti langkah-langkah berikut secara berurutan:

### Langkah 1: Clone Repository
Buka terminal / Git Bash di folder tujuan Anda:
`ash
git clone https://github.com/SeptianAdiraharja/Sentimen-SMA-KP-Margahayu.git
cd Sentimen-SMA-KP-Margahayu
`

---

### Langkah 2: Install Dependensi PHP (Laravel)
`ash
composer install
`

---

### Langkah 3: Install Dependensi Node.js (Frontend / Tailwind CSS)
`ash
npm install
npm run build
`

---

### Langkah 4: Install Dependensi Python (NLP & Machine Learning)
Pastikan Python dan pip terdaftar di *Environment Variables / PATH*:
`ash
pip install -r requirements.txt
`
> **Daftar Library Python yang Diinstall:**
> - scikit-learn : Menjalankan pipeline TF-IDF dan model Naive Bayes
> - Sastrawi : Preprocessing stopword removal dan stemming bahasa Indonesia
> - pandas : Manipulasi data tabular
> - 
umpy : Operasi komputasi matriks numerik
> - openpyxl : Membaca dan menulis file dataset Excel (.xlsx)

---

### Langkah 5: Konfigurasi File Environment (.env)
Salin file .env.example menjadi .env:
`ash
cp .env.example .env
`
*(Di Windows PowerShell gunakan: copy .env.example .env)*

Buka file .env dan sesuaikan koneksi database MySQL:
`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mbg
DB_USERNAME=root
DB_PASSWORD=
`

Buat database baru bernama mbg di MySQL Anda:
`sql
CREATE DATABASE mbg CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
`

Generate App Key Laravel:
`ash
php artisan key:generate
`

---

### Langkah 6: Jalankan Migrasi dan Database Seeder
Perintah ini akan membuat struktur tabel, menginisialisasi 9 aspek menu MBG, membuat 10 pertanyaan standar, akun admin default, dan data latih awal:
`ash
php artisan migrate --seed
`

> **Kredensial Login Admin Default:**
> - **URL Login**: http://127.0.0.1:8000/login
> - **Email**: dmin@mbg.sch.id
> - **Password**: dmin123

---

### Langkah 7: Verifikasi Integrasi Python & Model .pkl
Jalankan perintah pengujian bawaan sistem untuk memastikan Python dan file model_tfidf_naive_bayes.pkl berkomunikasi lancar dengan Laravel:
`ash
php artisan mbg:test-python
`

---

### Langkah 8: Menjalankan Server Lokal
Jalankan server pengembangan Laravel:
`ash
php artisan serve
`
Akses aplikasi melalui browser:
- **Kuesioner Siswa (Anonim)**: http://127.0.0.1:8000/
- **Portal Admin MBG**: http://127.0.0.1:8000/login

---

## 5. Struktur Direktori Utama
`
Sentimen-SMA-KP-Margahayu/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── AnalysisController.php    # Pengendali klasifikasi sentimen, TF-IDF, metrik
│   │   │   ├── DashboardController.php   # Visualisasi agregasi aspek & chart
│   │   │   ├── PeriodController.php      # Pengelolaan periode survei MBG
│   │   │   ├── QuestionController.php    # Pengelolaan pertanyaan kuesioner
│   │   │   ├── ReportController.php      # Ekspor Excel & cetak laporan evaluasi
│   │   │   └── ResponseController.php    # Impor respon Excel & kelola jawaban
│   │   └── QuestionnaireController.php   # Form publik kuesioner siswa
│   ├── Models/                           # Model Eloquent (Aspect, Question, Response, dll)
│   └── Services/
│       ├── NaiveBayesService.php         # Engine Naive Bayes lokal & kalkulasi TF-IDF
│       ├── NlpService.php                # Layanan NLP Sastrawi PHP
│       └── PythonSentimentService.php    # Jembatan eksekusi Python & model .pkl
├── database/
│   ├── migrations/                       # Skema database MySQL
│   └── seeders/                          # Seeder akun admin, 9 aspek, pertanyaan, dataset
├── resources/views/                      # Antarmuka Blade (Admin Dashboard & Form Siswa)
├── python_sentiment.py                   # Script Python inferensi NLP + model .pkl
├── model_tfidf_naive_bayes.pkl           # File model TF-IDF + MultinomialNB serialisasi
├── requirements.txt                      # Dependensi library Python
└── routes/web.php                        # Rute aplikasi web
`
