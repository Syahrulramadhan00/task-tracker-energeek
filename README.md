# Task & Project Tracker (Energeek Fullstack Test)

Aplikasi manajemen proyek dan tugas berbasis Kanban Board. Proyek ini dibangun untuk memenuhi persyaratan tes Fullstack Developer.

## 🛠️ Tech Stack
* **Backend:** Laravel 12, PostgreSQL, Laravel Sanctum (API Authentication), L5-Swagger (API Docs), PHPUnit.
* **Frontend:** Vue 3 (Composition API), TypeScript, Vite, Tailwind CSS, Pinia, Vue Router, Axios, Vuedraggable, Vitest.

## 📋 Prasyarat
Pastikan sistem Anda sudah terinstal:
* PHP >= 8.2
* Composer
* Node.js & npm
* PostgreSQL

---

## 🚀 Cara Instalasi & Menjalankan Aplikasi

### 1. Setup Backend (Laravel API)
1. Buka terminal dan masuk ke folder backend:
   ```bash
   cd backend
   Install dependensi PHP:
    Bash

    composer install

    Salin file environment dan sesuaikan konfigurasi database Anda (PostgreSQL):
    Bash

    cp .env.example .env

    Buka .env dan pastikan DB_CONNECTION=pgsql, DB_DATABASE=task_tracker, dll.

    Generate application key:
    Bash

    php artisan key:generate

    Jalankan migrasi dan seeder (Sangat penting untuk akun default & kategori):
    Bash

    php artisan migrate:fresh --seed

    Jalankan server backend:
    Bash

    php artisan serve

    API akan berjalan di http://localhost:8000.

2. Setup Frontend (Vue 3)

    Buka terminal baru dan masuk ke folder frontend:
    Bash

    cd frontend

    Install dependensi Node.js:
    Bash

    npm install

    Jalankan server frontend:
    Bash

    npm run dev

    Aplikasi web akan berjalan di http://localhost:5173.

📚 Dokumentasi API (Swagger)

Dokumentasi endpoint REST API dapat diakses melalui browser setelah backend berjalan:
👉 http://localhost:8000/api/documentation

(Catatan: Jika dokumentasi tidak muncul, jalankan php artisan l5-swagger:generate di terminal backend).
🧪 Pengujian Otomatis (Unit Testing)

Backend Testing (PHPUnit):
Bash

cd backend
php artisan test

(Catatan: File phpunit.xml telah disesuaikan untuk menggunakan PostgreSQL karena dependensi SQLite).

Frontend Testing (Vitest):
Bash

cd frontend
npm run test

🔐 Akun Login Default (Dari Seeder)

    Email: admin@energeek.id

    Password: password123


---

### 2. Buat file `AI_USAGE.md`
Sesuai aturan di dalam *brief*, penggunaan AI diizinkan namun **WAJIB** didokumentasikan. Ini menunjukkan kejujuran, transparansi, dan kemampuan Anda berkolaborasi dengan *tools* modern.

```markdown
# Deklarasi Penggunaan AI (AI Usage Tracker)

Sesuai dengan persyaratan tes Energeek, dokumen ini mencatat secara transparan riwayat penggunaan Asisten AI (Gemini) selama proses pengembangan proyek ini. AI digunakan sebagai *pair programmer*, sarana *troubleshooting*, dan mempercepat penulisan kode repetitif (*boilerplate*).

## 🤖 Log Penggunaan AI

### 1. Setup Backend & Autentikasi
* **Kendala/Tujuan:** Mengalami `BadMethodCallException` saat setup API Login dengan Laravel Sanctum.
* **Prompt/Query ke AI:** Melampirkan *stack trace error* (`Call to undefined method App\Models\User::createToken()`).
* **Hasil dari AI:** Mengingatkan untuk menambahkan trait `HasApiTokens` dari Laravel Sanctum ke dalam model `User.php`.

### 2. Pembuatan Controller & Logika Bisnis
* **Kendala/Tujuan:** Membuat `ProjectController`, `TaskController`, dan `DashboardController` sesuai aturan spesifik di brief (contoh: Project tanpa fitur hapus, Task dengan soft delete mengisi `deleted_by`).
* **Prompt/Query ke AI:** "now next step controller okay all of the feature" (melanjutkan instruksi sebelumnya).
* **Hasil dari AI:** Menghasilkan kode controller yang mengimplementasikan aturan bisnis tersebut. AI juga membantu memperbaiki relasi Eloquent (`category()`) yang terlewat pada model `Task`.

### 3. Setup Swagger di Laravel 12
* **Kendala/Tujuan:** Menambahkan dokumentasi API menggunakan L5-Swagger, namun terbentur masalah kompatibilitas *dependency* (versi L5-Swagger dan Symfony YAML) pada Laravel 12.
* **Prompt/Query ke AI:** Melampirkan *error log* dari Composer dan Artisan (`There are no commands defined in the "l5-swagger" namespace`).
* **Hasil dari AI:** Memberikan panduan *troubleshooting* untuk mengatasi konflik dependensi dengan menggunakan *flag* `-W` pada Composer dan menyesuaikan anotasi `@OA`.

### 4. Setup Frontend (Vue 3 + Pinia + Axios)
* **Kendala/Tujuan:** Membuat arsitektur frontend, *service layer* API, Pinia *store* untuk autentikasi, dan *slicing* halaman UI berdasarkan *mockup*.
* **Prompt/Query ke AI:** "now move to vue 3 I already install tailwind css slice base on image and consume and create lib fetcher" dan memandu pembuatan `MainLayout`.
* **Hasil dari AI:** Menyusun kode struktur komponen, mengonfigurasi *interceptor* Axios untuk menyematkan *Bearer Token*, menyusun *Vue Router guard*, dan membangun *Dashboard UI*.

### 5. Implementasi Papan Kanban (Drag & Drop)
* **Kendala/Tujuan:** Membuat fitur UI interaktif untuk *Task Board* yang bisa digeser antar kolom status.
* **Prompt/Query ke AI:** Meminta panduan pembuatan halaman Project Detail dengan fungsi Kanban.
* **Hasil dari AI:** Merekomendasikan dan memberikan kode implementasi menggunakan `vuedraggable` untuk menghubungkan aksi *drag-and-drop* di antarmuka langsung dengan API *update status* di *backend*. AI juga membantu melakukan *debugging* error template `_ctx.openModal is not a function`.

### 6. Setup Unit Testing (PHPUnit & Vitest)
* **Kendala/Tujuan:** Memenuhi syarat wajib (Required) pengujian 1 endpoint backend dan 1 komponen frontend.
* **Prompt/Query ke AI:** Meminta pembuatan *test* untuk Auth dan Frontend. Saat menjalankan PHPUnit, terjadi *error* karena *driver* SQLite tidak ditemukan di sistem operasi Arch Linux.
* **Hasil dari AI:** Membantu mem- *bypass* *error* SQLite dengan mengonfigurasi `phpunit.xml` agar menggunakan PostgreSQL, serta membuatkan *script* Vitest untuk pengujian *render form* di `LoginView.vue`.

---
*Dokumen ini dibuat secara jujur sebagai bentuk transparansi alur kerja.*