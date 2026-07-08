# Chicken Health AI Chatbot

Aplikasi chatbot berbasis AI untuk monitoring dan konsultasi kesehatan ayam. Dibangun dengan Laravel 12, Filament v5, dan React/Inertia.js.

---

## Fitur Utama

- **Panel Admin** (Filament) untuk mengelola seluruh data aplikasi
- **Knowledge Base** dengan dokumen PDF/TXT yang dapat diunggah dan di-embed sebagai vector
- **RAG (Retrieval-Augmented Generation)** — chatbot menjawab berdasarkan dokumen yang telah di-embed
- **System Prompt** yang dapat dikustomisasi oleh admin
- **Manajemen Farm & Ayam** — sesi chat dapat dikaitkan dengan kandang dan ayam tertentu
- **Catatan Kesehatan** untuk mencatat riwayat kesehatan ayam
- **Riwayat Penyakit & Obat** sebagai referensi
- **Halaman Chat Publik** yang dapat dibagikan via link ke pengguna/peternak

---

## Persyaratan Sistem

| Kebutuhan | Versi |
|---|---|
| **PHP** | >= 8.2 |
| **Composer** | >= 2.x |
| **Node.js** | >= 20.x |
| **NPM** | >= 10.x |
| **SQLite** | (sudah termasuk di PHP) |

> **Catatan:** Pastikan ekstensi PHP berikut sudah aktif: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `zip`

---

## Instalasi

Ikuti langkah-langkah berikut secara berurutan.

### 1. Clone / Ekstrak Proyek

```bash
# Jika menggunakan Git
git clone <url-repository> chatbot-ayam
cd chatbot-ayam

# Jika menggunakan file ZIP, ekstrak lalu masuk ke foldernya
cd chatbot-ayam
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Install Dependensi JavaScript

```bash
npm install
```

### 4. Salin File Konfigurasi

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Buat File Database

```bash
touch database/database.sqlite
```

### 7. Jalankan Migrasi Database

```bash
php artisan migrate
```

### 8. Buat Storage Link (untuk upload file)

```bash
php artisan storage:link
```

---

## Konfigurasi `.env`

Buka file `.env` dan sesuaikan nilai-nilai berikut:

```env
APP_NAME="Chatbot Kesehatan Ayam"
APP_URL=http://localhost:8000

# Konfigurasi AI Chat (WAJIB diisi) — TokenRouter
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxx
OPENAI_BASE_URL=https://api.tokenrouter.com/v1
OPENAI_MODEL=openai/gpt-4o-mini

# Konfigurasi Embeddings (WAJIB untuk fitur RAG) — TokenRouter tidak punya model embedding,
# gunakan Google Gemini (API key gratis: https://aistudio.google.com/apikey)
EMBEDDING_API_KEY=xxxxxxxxxxxxxxxxxxxx
EMBEDDING_BASE_URL=https://generativelanguage.googleapis.com/v1beta/openai
OPENAI_EMBEDDING_MODEL=gemini-embedding-001

# Upload file sementara
LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local
```

> **Cara mendapatkan API Key:**
> - Chat: daftar di [TokenRouter](https://www.tokenrouter.com), buat API Key, tempelkan ke `OPENAI_API_KEY`
> - Embeddings: buat API Key gratis di [Google AI Studio](https://aistudio.google.com/apikey), tempelkan ke `EMBEDDING_API_KEY`
> - Setelah ganti model embedding, jalankan `php artisan rag:embed --force` untuk re-embed semua chunk

---

## Menjalankan Aplikasi

Anda perlu membuka **3 terminal** secara bersamaan:

### Terminal 1 — Web Server

```bash
php artisan serve
```

Aplikasi dapat diakses di: **http://localhost:8000**

### Terminal 2 — Frontend (Vite)

```bash
npm run dev
```

### Terminal 3 — Queue Worker (untuk proses embedding)

```bash
php artisan queue:work
```

> Queue worker diperlukan agar proses embedding dokumen berjalan di background.

---

## Membuat Akun Admin

Jalankan perintah berikut untuk membuat akun admin pertama:

```bash
php artisan make:filament-user
```

Isi nama, email, dan password saat diminta.

Panel admin dapat diakses di: **http://localhost:8000/admin**

---

## Cara Menggunakan Aplikasi

### A. Setup Awal (Dilakukan Sekali)

#### 1. Tambahkan System Prompt

1. Login ke panel admin
2. Buka menu **System Prompts**
3. Klik **Tambah** dan isi instruksi untuk AI (misalnya: "Kamu adalah asisten kesehatan ayam yang ramah...")
4. Aktifkan prompt dengan toggle **Is Active**

#### 2. Tambahkan Referensi Penyakit & Obat (opsional)

- Buka **Tipe Ayam** → tambahkan jenis-jenis ayam
- Buka **Kategori Penyakit** → tambahkan kategori
- Buka **Penyakit** → tambahkan daftar penyakit beserta gejala dan obatnya
- Buka **Obat** → tambahkan daftar obat

---

### B. Mengelola Knowledge Base (Agar Chatbot Lebih Pintar)

Knowledge Base adalah dokumen referensi yang digunakan chatbot untuk menjawab pertanyaan.

#### Langkah 1 — Upload Dokumen

1. Buka menu **Knowledge Base → Dokumen**
2. Klik **Tambah Dokumen**
3. Isi **Judul** dan **Tipe** (PDF, Manual, Jurnal, atau Web)
4. Upload file PDF atau TXT di kolom **File Dokumen**
5. Simpan

#### Langkah 2 — Ekstrak Teks dari File

1. Buka dokumen yang baru ditambahkan (klik **Lihat**)
2. Klik tombol **Ekstrak Teks**
3. Konfirmasi → sistem akan membaca isi file secara otomatis
4. Teks hasil ekstraksi akan tersimpan di kolom konten dokumen

> Jika file PDF berbasis gambar/scan, teks tidak bisa diekstrak otomatis. Salin teks secara manual ke kolom **Konten Dokumen**.

#### Langkah 3 — Buat Chunks

1. Masih di halaman detail dokumen
2. Klik tombol **Buat Chunks**
3. Konfirmasi → dokumen akan dipecah menjadi potongan-potongan kecil (chunks)

#### Langkah 4 — Embed Chunks

1. Klik tombol **Embed Chunks**
2. Konfirmasi → proses embedding dikirim ke background queue
3. Pastikan **Queue Worker** sedang berjalan (Terminal 3)
4. Refresh halaman beberapa saat untuk melihat status embedding

> Setelah di-embed, chatbot akan menggunakan dokumen ini sebagai referensi jawaban.

---

### C. Membuat Sesi Chat

1. Buka menu **Sesi Chat**
2. Klik **Tambah Sesi**
3. Isi:
   - **Model AI** yang digunakan (misal: `gpt-4o-mini`)
   - **Farm/Kandang** (opsional) — kaitkan dengan kandang tertentu
   - **Ayam** (opsional) — kaitkan dengan ayam tertentu
4. Klik **Simpan**
5. Sistem akan otomatis membuka **halaman chat publik**

#### Berbagi Link Chat ke Pengguna/Peternak

- Setelah sesi dibuat, salin URL halaman chat (format: `http://localhost:8000/chat/xxxx-xxxx-xxxx`)
- Bagikan URL tersebut ke pengguna/peternak
- Pengguna dapat langsung berkonsultasi tanpa perlu login

---

### D. Memantau Sesi Chat

1. Buka menu **Sesi Chat**
2. Klik **Lihat** pada sesi yang ingin dipantau
3. Anda dapat melihat:
   - Riwayat percakapan
   - Total token yang digunakan
   - Status sesi (Aktif / Ditutup)
4. Klik tombol **Buka Chat (Publik)** untuk membuka halaman chat

---

### E. Mengelola Farm & Ayam

1. Buka menu **Farm** → tambahkan kandang/peternakan
2. Buka menu **Ayam** → tambahkan data ayam, kaitkan dengan farm
3. Buka menu **Catatan Kesehatan** → catat kondisi kesehatan ayam

---

## Perintah Artisan Berguna

```bash
# Jalankan semua migrasi ulang (HATI-HATI: data akan terhapus)
php artisan migrate:fresh

# Embed chunks via CLI (alternatif dari UI)
php artisan rag:embed                    # Embed semua chunk yang belum di-embed
php artisan rag:embed --document=1       # Embed chunk dari dokumen ID 1
php artisan rag:embed --chunk=5          # Embed chunk ID 5 saja
php artisan rag:embed --force            # Re-embed semua chunk (termasuk yang sudah di-embed)

# Jalankan queue secara manual satu per satu
php artisan queue:work --once

# Cek status queue
php artisan queue:monitor
```

---

## Struktur Folder Penting

```
app/
├── Filament/Resources/     # Semua resource panel admin
├── Http/Controllers/       # ChatController (halaman chat publik)
├── Jobs/                   # EmbedKnowledgeChunkJob
├── Models/                 # Semua model database
└── Services/
    ├── ChatService.php     # Logika utama chatbot (RAG + OpenAI)
    └── EmbeddingService.php # Embedding & cosine similarity

resources/js/
└── Pages/Chat.jsx          # Halaman chat publik (React)

database/
├── migrations/             # Semua migrasi database
└── database.sqlite         # File database SQLite
```

---

## Troubleshooting

### Error: `OPENAI_API_KEY not set`
Pastikan file `.env` sudah diisi dengan API key yang valid dan jalankan `php artisan config:clear`.

### Chat tidak merespons / timeout
- Periksa koneksi internet
- Pastikan API key valid dan memiliki kredit
- Cek log error di `storage/logs/laravel.log`

### Embedding tidak berjalan
- Pastikan queue worker aktif: `php artisan queue:work`
- Cek apakah ada failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

### Upload file gagal
- Pastikan folder `storage/app/public` dapat ditulis (writable)
- Pastikan `storage:link` sudah dijalankan
- Periksa nilai `upload_max_filesize` di `php.ini` (minimal 20M)

### Halaman kosong / error 500
- Aktifkan debug mode: `APP_DEBUG=true` di `.env`
- Jalankan `php artisan config:clear && php artisan cache:clear`
- Cek log di `storage/logs/laravel.log`

---

## Lisensi

Proyek ini dibuat untuk keperluan akademik.
