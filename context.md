# Konteks Proyek: ElementVerse (LMS Kimia Interaktif POE)

Dokumen ini menjelaskan visi, landasan pedagogis, dan komponen utama platform **ElementVerse** (yang sebelumnya bernama EduChem LMS).

---

## 1. Visi & Tujuan Utama
**ElementVerse** adalah *Learning Management System* (LMS) interaktif yang dirancang khusus untuk pembelajaran ilmu Kimia tingkat SMA. Sistem ini bertujuan agar siswa dapat mengeksplorasi fenomena kimia secara aktif melalui simulasi, video, dan panduan terstruktur, didukung oleh asisten cerdas AI Tutor Chatbot untuk memperdalam nalar kritis siswa secara langsung.

---

## 2. Landasan Pedagogis: Model Pembelajaran POE
Platform ini sepenuhnya berbasis pada siklus belajar **POE (Predict, Observe, Explain)**:

1. **Predict (Prediksi)**:
   - **Tujuan**: Menggali pengetahuan awal siswa dan merangsang rasa ingin tahu.
   - **Alur**: Siswa diberikan suatu stimulus berupa gambar/video demonstrasi fenomena kimia, lalu diminta membuat prediksi ilmiah tertulis mengenai apa yang akan terjadi beserta alasannya.
   - **Peran AI**: AI memberikan feedback awal untuk mengenali arah hipotesis siswa.

2. **Observe (Observasi)**:
   - **Tujuan**: Membimbing siswa melakukan pembuktian empiris.
   - **Alur**: Siswa mengamati eksperimen secara langsung, menonton video demonstrasi lengkap, atau berinteraksi dengan simulasi virtual untuk mengumpulkan fakta konkret.
   - **Peran AI**: AI membantu mengarahkan fokus observasi siswa agar tidak melewatkan detail kunci reaksi.

3. **Explain (Eksplorasi/Penjelasan)**:
   - **Tujuan**: Mengonstruksi pemahaman baru dengan membandingkan prediksi awal dengan hasil observasi.
   - **Alur**: Siswa merumuskan penjelasan logis/teoretis mengenai fenomena tersebut.
   - **Peran AI**: AI mengevaluasi ketepatan pemahaman konsep kimia siswa secara otomatis dan memberikan ulasan skor/feedback perbaikan.

---

## 3. Fitur Utama & Peran Pengguna
* **Superadmin**: Mengelola data sekolah, akun guru/siswa, dan memvalidasi permintaan reset sandi.
* **Guru (Teacher)**:
   - *Dynamic Content Builder*: Menyusun bab/topik pembelajaran dan merangkai instruksi di setiap fase POE (Predict, Observe, Explain).
   - *Review & Grading*: Menilai jawaban siswa secara semi-otomatis berkat asistensi penilaian AI.
   - *Chat Logs & Dashboard Analytics*: Memantau aktivitas interaksi chat AI siswa secara transparan untuk mendeteksi kesulitan belajar.
* **Siswa (Student)**:
   - *Interactive Learning Sheet*: Mengisi jawaban di tiap fase POE secara berurutan.
   - *AI Tutor Chatbot*: Asisten belajar personal yang tersemat di setiap worksheet untuk menjawab pertanyaan seputar materi kimia tanpa memberikan bocoran jawaban langsung.


## 📌 Log Pencapaian Deployment (26 Juni 2026)

1. **Infrastruktur & Keamanan Web Root:** - ElementVerse sukses di-hosting di Hostinger Shared Hosting (`elementverse.my.id`).
   - Seluruh repositori ditarik via GitHub Auto-Deploy langsung ke folder `public_html`.
   - Keamanan berkas sistem (`.env`, `app`, `vendor`, dll.) diproteksi menggunakan `.htaccess` master yang mengalihkan seluruh trafik publik secara otomatis ke folder `/public` serta memblokir akses langsung ke file sensitif.

2. **Database & Seeding Sukses:**
   - Database MySQL Hostinger terhubung secara aman via konfigurasi `.env`.
   - Tabel sistem (termasuk skema POE, Chatbot AI, dan evaluasi esai) berhasil dimigrasi dan di-seed dengan akun uji coba (*Guru & Siswa*) memanfaatkan rute darurat sementara.

3. **Storage Symlink Terbuka:**
   - Menghubungkan folder penyimpanan file/gambar menggunakan fungsi bawaan `symlink()` PHP native untuk melewati blokir fungsi `exec()` oleh pihak Hostinger.

4. **Resolusi Bug Kompilasi & Routing:**
   - Menuntaskan error *Vite Manifest* dengan menyederhanakan pemanggilan aset `@vite` di `app.blade.php` (hanya memanggil `app.css` dan `app.ts` karena halaman Vue dimuat secara *eager loading*).
   - Memperbaiki kegagalan rute masuk (*Login net::ERR_CONNECTION_REFUSED*) akibat perekaman URL `localhost:8000` pada peta rute Ziggy.

---

## 🚀 Mekanisme & Aturan Alur Kerja Deployment (PENTING UNTUK AI AGENT)

Karena lingkungan *production* menggunakan Shared Hosting **TANPA AKSES SSH**, seluruh proses kompilasi aset dan pemetaan rute WAJIB diselesaikan di komputer lokal sebelum melakukan push ke GitHub. 

### ⚠️ Prosedur Wajib Sebelum Push ke Branch `main`:

1. **Sinkronisasi URL Produksi untuk Ziggy:**
   Sebelum membuat peta rute baru, pastikan file `.env` lokal mengubah variabel `APP_URL` ke domain produksi agar tautan di Vue tidak mengarah ke localhost:
   ```bash
   # Ubah sementara di .env lokal
   APP_URL=[https://elementverse.my.id](https://elementverse.my.id)
   ```

2. **Generate Ulang Rute Ziggy:**
   Jalankan perintah ini agar file `resources/js/ziggy.js` merekam alamat domain produksi yang benar:
   ```bash
   php artisan ziggy:generate
   ```


3. **Kompilasi Aset Vue & Tailwind (Vite):**
   Wajib melakukan kompilasi aset secara lokal agar folder `public/build` menghasilkan manifest teranyar yang siap pakai di server:
   ```bash
   npm run build
   ```

4. **Pastikan Folder Build Ikut Terlacak oleh Git:**
   Folder `public/build` **TIDAK BOLEH** diabaikan oleh `.gitignore`. Jika Git menolak, paksa masuk menggunakan perintah:
   ```bash
   git add public/build/ -f
   ```

5. **Kembalikan Environment Lokal & Push:**
   Kembalikan `APP_URL` lokal ke `http://localhost:8000` (jika ingin lanjut coding di lokal), lalu lakukan commit dan push. Hostinger akan otomatis melakukan *pull* dan memperbarui aplikasi secara aman.

