# Panduan Desain Visual ElementVerse (Dark-Tech Theme)

Panduan ini mendokumentasikan aturan dan standar gaya visual untuk **ElementVerse** dengan pendekatan tema gelap (*dark-tech/glassmorphic*) terpadu. Seluruh komponen baru maupun lama **wajib** mengikuti prinsip-prinsip yang tertuang di bawah ini agar antarmuka konsisten secara visual maupun perilaku.

---

## 1. Skema Warna & Token Desain

Kami menggunakan palet warna neon dengan kontras tinggi yang berlatar belakang gelap gulita/siber.

| Token | Nilai CSS | Peran / Penggunaan |
| :--- | :--- | :--- |
| **Primary (Neon Green)** | `#d2ff00` | Tombol utama, hover link aktif, teks penunjuk prioritas tinggi. |
| **Accent (Neon Cyan)** | `#00ffff` | Aksen visual, link penting, border interaktif, status aktif. |
| **Destructive (Red)** | `#ff4d4f` | Tombol hapus, pesan error, aksi destruktif. |
| **Background (Dark-Tech)** | `#08091a` s.d. `#0c0d26` | Latar belakang utama aplikasi (diimplementasikan via `--theme-bg-gradient`). |
| **Card / Panel Background** | `rgba(16, 17, 43, 0.75)` | Panel mengambang / Glassmorphic Card (`bg-card/60 backdrop-blur-md`). |
| **Border Color** | `rgba(159, 122, 234, 0.15)` | Border halus pada panel dan komponen input. |
| **Text Primary** | `hsl(226, 64%, 98%)` (`#f8fafc`) | Warna teks konten utama dan judul halaman. |
| **Text Secondary** | `hsl(226, 15%, 70%)` (`#94a3b8`) | Deskripsi singkat, metadata, label form. |

---

## 2. Aturan Kontras Teks (Wajib Dipatuhi)

Kegagalan kontras adalah penyebab teks tidak terbaca. Gunakan tabel berikut sebagai acuan mutlak untuk memilih warna teks berdasarkan warna latar tombol atau elemen.

| Jenis Latar Belakang | Contoh | Warna Teks yang Benar | Alasan |
| :--- | :--- | :--- | :--- |
| **Terang / Neon** (luminansi tinggi) | `#d2ff00`, `#00ffff`, gradien neon, putih | `text-[#070814]` (hitam pekat) | Teks gelap wajib di atas warna terang agar rasio kontras WCAG AA ≥ 4.5:1 terpenuhi. |
| **Gelap / Transparan** (luminansi rendah) | `bg-white/5`, `bg-white/10`, `bg-transparent`, warna panel | `text-slate-200` atau `text-white` | Teks terang wajib di atas warna gelap. |
| **Merah (Destructive)** | `#ff4d4f`, `bg-red-500/20` | `text-white` | Merah cukup gelap/jenuh sehingga teks putih terbaca. |

> ⚠️ **Jangan pernah** menggunakan `text-white` atau `text-slate-200` di atas latar neon/terang — teks akan menghilang ke latar. Sebaliknya, `text-[#070814]` di atas latar gelap juga tidak terlihat.

---

## 3. Tipografi

* **Font Utama**: **Outfit** (`'Outfit', sans-serif`) untuk judul (`h1`, `h2`, `h3`) — memberikan kesan modern, futuristik, dan kokoh.
* **Font Konten**: **Instrument Sans** untuk teks isi paragraf dan data tabel — memastikan keterbacaan tinggi pada resolusi apapun.

---

## 4. Sistem Tombol

### 4.1 Varian yang Tersedia

| Varian | Kapan Digunakan |
| :--- | :--- |
| **Primary (Neon)** | Aksi utama & CTA — "Simpan", "Kirim", "Mulai" |
| **Secondary (Outlined)** | Aksi opsional atau navigasi — "Batal", "Kembali" |
| **Destructive** | Aksi tidak dapat dibatalkan — "Hapus", "Reset" |
| **Ghost** | Aksi tersier, link-like — konteks padat seperti dalam tabel atau sidebar |

---

### 4.2 Tabel State Referensi

Setiap varian **wajib** mengimplementasikan semua state berikut. Tidak ada state yang boleh dilewati.

| State | Deskripsi | Implementasi Umum |
| :--- | :--- | :--- |
| **Default** | Tampilan awal/normal | Warna & border standar |
| **Hover** | Kursor berada di atas tombol | Cerahkan latar, perkuat glow/border |
| **Active / Pressed** | Tombol sedang ditekan | `scale-[0.97]` + kurangi kecerahan sedikit |
| **Focus-Visible** | Navigasi keyboard (Tab) | `ring-2` + `ring-offset-2` berwarna kontrastif |
| **Disabled** | Tombol tidak aktif | `opacity-40` + `cursor-not-allowed` + `pointer-events-none` |
| **Loading** | Aksi sedang diproses | Tampilkan spinner, nonaktifkan klik, jaga dimensi tetap |

---

### 4.3 Primary Button (Neon Gradient)

**Kontras teks: GELAP** — latar gradien neon sangat terang, wajib `text-[#070814]`.

**Default & semua state:**
```html
<button
  class="
    h-10 px-5 rounded-lg
    bg-gradient-to-r from-[#d2ff00] to-[#00ffff]
    font-bold text-[#070814]
    shadow-[0_0_15px_rgba(210,255,0,0.25)]
    transition-all duration-200
    flex items-center justify-center gap-2

    hover:brightness-110
    hover:shadow-[0_0_28px_rgba(210,255,0,0.45)]

    active:scale-[0.97]
    active:brightness-95

    focus-visible:outline-none
    focus-visible:ring-2
    focus-visible:ring-[#d2ff00]
    focus-visible:ring-offset-2
    focus-visible:ring-offset-[#08091a]

    disabled:opacity-40
    disabled:cursor-not-allowed
    disabled:pointer-events-none
    disabled:shadow-none
  "
>
  <span>Aksi Utama</span>
</button>
```

**State Loading** (gunakan kelas + kondisi JavaScript):
```html
<button
  disabled
  class="h-10 px-5 rounded-lg bg-gradient-to-r from-[#d2ff00] to-[#00ffff]
         font-bold text-[#070814] opacity-80 cursor-not-allowed
         flex items-center justify-center gap-2 transition-all duration-200"
>
  <!-- Spinner SVG animasi -->
  <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#070814" stroke-width="4"/>
    <path class="opacity-75" fill="#070814"
      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
  </svg>
  <span>Memproses...</span>
</button>
```

---

### 4.4 Secondary Button (Outlined)

**Kontras teks: TERANG** — latar gelap/transparan, gunakan `text-slate-200` → `text-white` saat hover.

```html
<button
  class="
    h-10 px-5 rounded-lg
    border border-white/20 bg-white/5
    font-semibold text-slate-200
    transition-all duration-200
    flex items-center justify-center gap-2

    hover:bg-white/10
    hover:border-white/40
    hover:text-white

    active:scale-[0.97]
    active:bg-white/5

    focus-visible:outline-none
    focus-visible:ring-2
    focus-visible:ring-slate-400
    focus-visible:ring-offset-2
    focus-visible:ring-offset-[#08091a]

    disabled:opacity-40
    disabled:cursor-not-allowed
    disabled:pointer-events-none
  "
>
  Batal / Kembali
</button>
```

**State Loading:**
```html
<button
  disabled
  class="h-10 px-5 rounded-lg border border-white/20 bg-white/5
         font-semibold text-slate-400 opacity-80 cursor-not-allowed
         flex items-center justify-center gap-2 transition-all duration-200"
>
  <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#94a3b8" stroke-width="4"/>
    <path class="opacity-75" fill="#94a3b8"
      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
  </svg>
  <span>Memproses...</span>
</button>
```

---

### 4.5 Destructive Button

**Kontras teks: TERANG** — latar merah cukup gelap, gunakan `text-white`.

```html
<button
  class="
    h-10 px-5 rounded-lg
    border border-red-500/40 bg-red-500/15
    font-semibold text-red-400
    transition-all duration-200
    flex items-center justify-center gap-2

    hover:bg-red-500/25
    hover:border-red-500/70
    hover:text-red-300

    active:scale-[0.97]
    active:bg-red-500/20

    focus-visible:outline-none
    focus-visible:ring-2
    focus-visible:ring-red-500
    focus-visible:ring-offset-2
    focus-visible:ring-offset-[#08091a]

    disabled:opacity-40
    disabled:cursor-not-allowed
    disabled:pointer-events-none
  "
>
  Hapus
</button>
```

> Untuk varian solid merah (aksi irreversibel kritis), gunakan `bg-red-600 text-white hover:bg-red-500`.

---

### 4.6 Ghost Button

**Kontras teks: TERANG** — transparan total, gunakan `text-slate-400` → `text-white` saat hover. Cocok untuk konteks padat (tabel, sidebar).

```html
<button
  class="
    h-9 px-3 rounded-md
    bg-transparent
    font-medium text-slate-400
    transition-all duration-200
    flex items-center justify-center gap-1.5

    hover:bg-white/8
    hover:text-slate-100

    active:scale-[0.97]
    active:bg-white/5

    focus-visible:outline-none
    focus-visible:ring-2
    focus-visible:ring-slate-500
    focus-visible:ring-offset-1
    focus-visible:ring-offset-[#08091a]

    disabled:opacity-40
    disabled:cursor-not-allowed
    disabled:pointer-events-none
  "
>
  Detail
</button>
```

---

### 4.7 Ringkasan Aturan Disabled (Semua Varian)

Terapkan kelas-kelas berikut **secara seragam** pada semua varian tombol yang sedang dalam kondisi `disabled`. Jangan mendesain ulang tombol disabled — cukup turunkan opasitas agar tetap dapat dikenali sebagai "versi inactive" dari varian aslinya.

```
disabled:opacity-40
disabled:cursor-not-allowed
disabled:pointer-events-none
disabled:shadow-none
```

Pada elemen HTML, pastikan atribut `disabled` juga dipasang di tag `<button>` agar browser native turut mencegah event klik.

---

### 4.8 Pola Loading State (Semua Varian)

Saat tombol memicu proses asinkron:

1. Ganti ikon/teks dengan **spinner + label proses** (`Menyimpan...`, `Mengirim...`).
2. Tambahkan `disabled` pada elemen `<button>`.
3. Pertahankan **ukuran tombol yang sama** (`h-10 px-5`) agar layout tidak bergeser.
4. Turunkan `opacity` ke `80` (bukan `40` seperti disabled pasif) agar pengguna tahu proses sedang berjalan.

Warna spinner **harus mengikuti aturan kontras teks** varian yang bersangkutan:
- Primary (neon) → `stroke="#070814"` / `fill="#070814"`  
- Secondary, Destructive, Ghost → `stroke="#94a3b8"` / `fill="#94a3b8"`

---

## 5. Komponen Standar Lainnya

### A. Glassmorphic Card

```html
<div class="rounded-2xl border border-border/40 bg-card/60 p-5 backdrop-blur-md
            text-card-foreground shadow-sm transition-all
            hover:border-[var(--theme-accent)]/30">
  <!-- Konten Kartu -->
</div>
```

### B. Input Form

```html
<input
  type="text"
  class="w-full rounded-lg border border-white/10 bg-white/5
         px-4 py-2.5 text-white placeholder-slate-500
         shadow-sm transition-colors
         focus:border-[var(--theme-primary)]
         focus:ring-[var(--theme-primary)]/20
         focus:ring-2 focus:outline-none
         disabled:opacity-40 disabled:cursor-not-allowed"
/>
```

> Untuk state error, tambahkan `border-red-500/60 focus:border-red-500 focus:ring-red-500/20` dan tampilkan pesan error di bawah input dengan `text-red-400 text-sm`.

---

## 6. Micro-Animations & Glow Effects

* Gunakan `transition-all duration-200` sebagai standar pada **seluruh** elemen interaktif. Hindari `duration-300` ke atas untuk tombol — terasa lambat.
* Efek glow saat hover: `shadow-[0_0_28px_rgba(210,255,0,0.4)]` untuk neon green, `shadow-[0_0_20px_rgba(0,255,255,0.3)]` untuk cyan.
* Gunakan `active:scale-[0.97]` secara konsisten untuk memberikan umpan balik klik yang natural. Hindari `active:scale-95` (terlalu dramatis) atau kurang dari `scale-[0.96]`.
* Untuk pengguna yang memilih `prefers-reduced-motion`, animasi glow dan scale sebaiknya dinonaktifkan via `motion-safe:` prefix Tailwind:

```html
class="... motion-safe:hover:shadow-[0_0_28px_rgba(210,255,0,0.4)] motion-safe:active:scale-[0.97]"
```