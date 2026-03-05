# Deklarasi Penggunaan AI (AI Usage Tracker)

Sesuai dengan persyaratan tes Energeek, dokumen ini mencatat secara transparan riwayat penggunaan Asisten AI (Gemini) selama proses pengembangan proyek "Task & Project Tracker". 

AI digunakan sebagai *pair programmer* untuk melakukan *debugging* error, men- *generate* kode repetitif (boilerplate), mengatasi masalah dependensi (L5-Swagger & SQLite), dan menyusun struktur frontend Vue 3.

Berikut adalah log *prompt* (instruksi) yang saya gunakan selama sesi pengembangan ini:

## 🛠️ Fase 1: Backend Setup & Debugging Auth
1. **Prompt:** Melampirkan *stack trace error* panjang: `# BadMethodCallException - Internal Server Error. Call to undefined method App\Models\User::createToken()...`
   * **Hasil:** AI menginstruksikan untuk menambahkan trait `HasApiTokens` di model `User`.
2. **Prompt:** Melampirkan ulang pesan *error* yang sama karena saya belum mengaplikasikan perbaikannya dengan benar.
   * **Hasil:** AI memberikan panduan langkah demi langkah dan kode yang tepat untuk file `User.php`.
3. **Prompt:** `"now next step controller okay all of the feature"`
   * **Hasil:** AI men- *generate* `ProjectController`, `TaskController`, dan `DashboardController` sesuai spesifikasi *brief*.
4. **Prompt:** Melampirkan *stack trace error*: `# BadMethodCallException - Internal Server Error. Call to undefined method App\Models\Task::category()...`
   * **Hasil:** AI membantu mendefinisikan relasi Eloquent (`belongsTo`, `hasMany`) di model `Task`, `Project`, dan `Category`.

## 📚 Fase 2: Implementasi Swagger API Docs
5. **Prompt:** `"before to vue 3 or frontend I want to make swagger documentaion with laravel 12 okay?"`
   * **Hasil:** AI memberikan panduan instalasi `darkaonline/l5-swagger` dan contoh anotasi `@OA`.
6. **Prompt:** Melampirkan terminal *error* `"ERROR There are no commands defined in the "l5-swagger" namespace."` dan menanyakan `"can you fix it?"` beserta kode controller.
   * **Hasil:** AI memberikan solusi untuk meregistrasi `L5SwaggerServiceProvider` secara manual di `bootstrap/providers.php` untuk Laravel 12.
7. **Prompt:** Melampirkan terminal log instalasi Composer yang menunjukkan pesan *error* yang sama `"still issue"`.
   * **Hasil:** AI menganalisis log dan menyadari versi yang terinstal adalah versi kuno (2.1.3), lalu menginstruksikan *upgrade* ke versi `^8.6`.
8. **Prompt:** Melampirkan log *error* Composer: `"Your requirements could not be resolved... symfony/yaml version conflict"`.
   * **Hasil:** AI memberikan solusi menggunakan *flag* `-W` (`--with-all-dependencies`) pada Composer.
9. **Prompt:** Melampirkan log sukses instalasi dan *generate*, lalu bertanya `"bagaiaman cara akses dokumentasi nya?"` (bagaimana cara akses dokumentasinya?).
   * **Hasil:** AI memberikan URL akses UI Swagger dan tips *troubleshooting*.
10. **Prompt:** Melampirkan kode dari ke-3 Controller (Project, Task, Dashboard) dan meminta: `"use it for all cases okay?"`
    * **Hasil:** AI membuatkan blok anotasi Swagger (`@OA`) lengkap untuk seluruh *endpoint* CRUD dan Dashboard.

## 💻 Fase 3: Frontend Vue 3 (Auth & Layout)
11. **Prompt:** `"now move to vue 3 I already install tailwind css slice base on image and consume and create lib fetcher okay"`
    * **Hasil:** AI memberikan kode untuk `axios` interceptor, Pinia auth store, dan *slicing* halaman `LoginView.vue`.
12. **Prompt:** `"create api services for login okay?"`
    * **Hasil:** AI memisahkan logika pemanggilan API ke dalam `auth.service.ts` (*best practice*).
13. **Prompt:** Melampirkan draf `Dashboard.vue` sederhana dan meminta `"replace above code to base on dashboard and create mainlayout vue to use the layout when is authenticated routes okay?"`
    * **Hasil:** AI membuatkan `MainLayout.vue` dengan Sidebar dan Header, serta memperbarui halaman Dashboard dengan data statistik API.
14. **Prompt:** Melampirkan file `router/index.ts` saya dan meminta `"no adjust the route base on above code okay? add main alyout in protected route"`
    * **Hasil:** AI menerapkan konsep *nested routing* di Vue Router agar halaman terlindungi terbungkus oleh `MainLayout`.

## 📋 Fase 4: Frontend Pages & Kanban Board (Drag & Drop)
15. **Prompt:** `"yes good now move to another pages base on docs okay"`
    * **Hasil:** AI memberikan kode untuk `project.service.ts` dan halaman `Projects.vue` (tabel daftar project & modal form).
16. **Prompt:** `"oke lanjutkan"`
    * **Hasil:** AI memberikan implementasi `vuedraggable` untuk membuat papan Kanban interaktif di halaman Project Detail.
17. **Prompt:** Melampirkan log *error* browser `"Uncaught TypeError: _ctx.openModal is not a function"` beserta kode `ProjectDetail.vue`.
    * **Hasil:** AI memperbaiki file tersebut dengan menambahkan fungsi modal, state form, dan logika *update* API saat *card* di-*drag-and-drop*.

## 🧪 Fase 5: Unit Testing & Finalisasi
18. **Prompt:** `"ok now next pages"`
    * **Hasil:** AI menyadari halaman sudah lengkap dan beralih ke fitur wajib terakhir: PHPUnit & Vitest. AI memberikan kode `AuthTest.php`.
19. **Prompt:** Melampirkan hasil *test* PHPUnit yang *FAILED* dengan pesan `"QueryException: could not find driver (Connection: sqlite...)"`.
    * **Hasil:** AI memberikan instruksi untuk menginstal driver `php-sqlite` di Arch Linux.
20. **Prompt:** Melampirkan hasil `php -m | grep sqlite` yang menunjukkan *error* `Unable to load dynamic library`.
    * **Hasil:** AI menyarankan *bypass* dengan menghapus konfigurasi SQLite di `phpunit.xml` agar *test* berjalan menggunakan PostgreSQL.
21. **Prompt:** `"move to frontend test"`
    * **Hasil:** AI memberikan panduan setup Vitest dan kode `Login.spec.ts` untuk menguji render komponen Vue.
22. **Prompt:** `"ok before usage there is menu task can you add that base on docs that i give you"`
    * **Hasil:** AI membuatkan halaman `Tasks.vue` untuk melihat semua task secara global sesuai spesifikasi *brief*.
23. **Prompt:** `"oke buat AI_USAGE and README"`
    * **Hasil:** AI membuatkan draf pertama untuk file dokumentasi.
24. **Prompt:** `"now create AI_USage ALl rpomt hat i use in these sections write down"`
    * **Hasil:** AI membuat ulang dokumen ini dengan menyertakan *log* seluruh *prompt* secara detail dan presisi.

---
*Dokumen ini digenerate secara akurat berdasarkan riwayat obrolan untuk menjamin transparansi pengerjaan tes sesuai instruksi Energeek.*