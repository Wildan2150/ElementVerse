<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AOS from 'aos';
import 'aos/dist/aos.css';
import {
    Brain,
    FlaskConical,
    ListChecks,
    BookOpen,
    TrendingUp,
    Smartphone,
    Sparkles,
    GraduationCap,
    UserCheck,
    Bot,
    FileText,
    CheckCircle2,
    Zap,
    Download,
    HelpCircle,
    Layers,
    Sliders,
    Award,
    ShieldCheck,
} from 'lucide-vue-next';
import { h, markRaw, ref, onMounted, onUpdated } from 'vue';

defineOptions({ layout: null as any });

const activeSection = ref('home');
const activeGuideTab = ref<'siswa' | 'guru'>('siswa');

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    activeSection.value = entry.target.id;
                }
            });
        },
        { rootMargin: '-100px 0px -60% 0px' },
    );

    ['home', 'stages', 'features', 'guide', 'about'].forEach((id) => {
        const el = document.getElementById(id);

        if (el) {
            observer.observe(el);
        }
    });

    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
    });
});

// Refresh AOS when component updates (useful for SPAs)
onUpdated(() => {
    AOS.refresh();
});

const scrollToSection = (id: string) => {
    const element = document.getElementById(id);

    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const navItems = [
    { id: 'home', label: 'Beranda' },
    { id: 'stages', label: 'Tahapan Belajar' },
    { id: 'features', label: 'Fasilitas' },
    { id: 'guide', label: 'Panduan' },
    { id: 'about', label: 'Tentang ElementVerse' },
];

const siswaGuideSteps = [
    {
        step: '01',
        title: 'Registrasi & Masuk Kelas',
        desc: 'Daftar akun sebagai Siswa, lalu masukkan 6 Digit Kode Kelas yang diberikan oleh Guru untuk bergabung ke ruang kelas virtual.',
        icon: markRaw(GraduationCap),
        badge: 'Akses Kelas',
        color: 'from-[#d2ff00]/20 to-[#d2ff00]/5 text-[#d2ff00] border-[#d2ff00]/30',
        highlights: [
            'Registrasi Akun Mandiri',
            'Input Kode Kelas 6 Karakter',
            'Akses Topik Pembelajaran Aktif',
        ],
    },
    {
        step: '02',
        title: 'Ikuti Sesi Pembelajaran & Instruksi Guru',
        desc: 'Pelajari materi melalui alur Sesi Pembelajaran yang disusun Guru (Apersepsi, Video Interaktif, Worksheet POE, Rangkuman, & Latihan Soal). Siswa wajib mengikuti secara penuh seluruh instruksi dari Guru Kelas.',
        icon: markRaw(BookOpen),
        badge: 'Sesi Pembelajaran',
        color: 'from-[#00ffff]/20 to-[#00ffff]/5 text-[#00ffff] border-[#00ffff]/30',
        highlights: [
            'Alur Sesi 1-5 (Apersepsi s/d Latihan)',
            'Patuhi Penuh Instruksi Guru',
            'Kerjakan Worksheet Predict-Observe-Explain',
        ],
    },
    {
        step: '03',
        title: 'Konsultasi AI Tutor 24/7',
        desc: 'Jika mengalami kesulitan konsep saat mengerjakan sesi, manfaatkan AI Tutor Chatbot untuk membimbing pemikiranmu tanpa membocorkan jawaban langsung.',
        icon: markRaw(Bot),
        badge: 'Bantuan AI',
        color: 'from-purple-500/20 to-purple-500/5 text-purple-400 border-purple-500/30',
        highlights: [
            'Respon Cepat 24/7',
            'Metode Pembimbingan Sokratik',
            'Fokus Relevan Materi Kimia',
        ],
    },
    {
        step: '04',
        title: 'Feedback AI & Evaluasi Guru',
        desc: 'Dapatkan umpan balik otomatis dari AI saat mengisi jawaban esai/worksheet, ikuti forum diskusi kelas, dan dapatkan nilai final resmi dari Guru.',
        icon: markRaw(CheckCircle2),
        badge: 'Penilaian & Nilai',
        color: 'from-amber-500/20 to-amber-500/5 text-amber-400 border-amber-500/30',
        highlights: [
            'Evaluasi Esai Otomatis',
            'Diskusi Forum Per Sesi',
            'Nilai & Catatan Final Guru',
        ],
    },
];

const guruGuideSteps = [
    {
        step: '01',
        title: 'Pembuatan Kelas Virtual',
        desc: 'Buat ruang kelas baru untuk mata pelajaran kimia. Sistem secara otomatis menerbitkan Kode Kelas unik 6-digit untuk dibagikan kepada siswa.',
        icon: markRaw(UserCheck),
        badge: 'Manajemen Kelas',
        color: 'from-[#d2ff00]/20 to-[#d2ff00]/5 text-[#d2ff00] border-[#d2ff00]/30',
        highlights: [
            'Buat Ruang Kelas Baru',
            'Kode Kelas Unik (6 Digit)',
            'Pantau Anggota & Kemajuan',
        ],
    },
    {
        step: '02',
        title: 'Content Builder Berbasis POE',
        desc: 'Rancang alur Sesi Pembelajaran (seperti Apersepsi, Video Interaktif, Worksheet POE, Rangkuman & Peta Konsep, hingga Latihan Soal) menggunakan beragam jenis blok konten interaktif dinamis.',
        icon: markRaw(Layers),
        badge: 'Sesi Pembelajaran',
        color: 'from-[#00ffff]/20 to-[#00ffff]/5 text-[#00ffff] border-[#00ffff]/30',
        highlights: [
            'Kelola Alur Sesi Pembelajaran',
            'Blok Teks, Video & Pilihan Ganda',
            'Soal Esai & Status Draft / Publish',
        ],
    },
    {
        step: '03',
        title: 'Kustomisasi Prompt Agen AI',
        desc: 'Atur kriteria evaluasi otomatis esai (AI Feedback) dan instruksi batasan perilaku AI Chatbot Tutor per sesi sesuai target pembelajaran.',
        icon: markRaw(Sliders),
        badge: 'Pengaturan AI',
        color: 'from-purple-500/20 to-purple-500/5 text-purple-400 border-purple-500/30',
        highlights: [
            'Prompt Evaluasi Penilaian Esai',
            'Instruksi Perilaku Chatbot Tutor',
            'Koreksi Objektif & Konsisten',
        ],
    },
    {
        step: '04',
        title: 'Penilaian, Validasi & Publikasi',
        desc: 'Tinjau draf nilai dan umpan balik yang dihasilkan AI pada rekap jawaban kelas. Sesuaikan nilai secara manual bila perlu, lalu kirim ke siswa.',
        icon: markRaw(Award),
        badge: 'Review & Grading',
        color: 'from-emerald-500/20 to-emerald-500/5 text-emerald-400 border-emerald-500/30',
        highlights: [
            'Review Draf Penilaian AI',
            'Koreksi Skor/Feedback Manual',
            'Kirim Nilai Final ke Siswa',
        ],
    },
    {
        step: '05',
        title: 'Ekspor Rekap & Log Chat AI',
        desc: 'Unduh rekapitulasi nilai seluruh siswa ke format Excel/CSV dan ekspor riwayat percakapan AI Tutor siswa ke PDF untuk analisis kendala belajar.',
        icon: markRaw(Download),
        badge: 'Laporan & Analytics',
        color: 'from-rose-500/20 to-rose-500/5 text-rose-400 border-rose-500/30',
        highlights: [
            'Ekspor Nilai Excel / CSV',
            'Cetak Log Chat PDF',
            'Analisis Perilaku Belajar',
        ],
    },
];

const stages = [
    {
        title: 'Predict (Prediksi)',
        desc: 'Buatlah dugaan atau perkiraan awalmu terhadap suatu fenomena kimia sebelum melakukan observasi.',
        icon: 'pi-lightbulb',
        bg: 'bg-[#d2ff00]/10',
        text: 'text-[#d2ff00]',
        color: 'lime',
        extra: () =>
            h(
                'div',
                {
                    class: 'h-1.5 w-full overflow-hidden rounded-full bg-white/10',
                },
                [
                    h('div', {
                        class: 'h-full w-3/4 bg-[#d2ff00] rounded-full shadow-[0_0_8px_#d2ff00]',
                    }),
                ],
            ),
    },
    {
        title: 'Observe (Observasi)',
        desc: 'Amati fenomena yang disajikan melalui video, eksperimen, atau data untuk menguji prediksi awalmu.',
        icon: 'pi-compass',
        bg: 'bg-[#00ffff]/10',
        text: 'text-[#00ffff]',
        color: 'cyan',
        extra: () =>
            h('div', { class: 'flex gap-1.5' }, [
                h(
                    'div',
                    {
                        class: 'flex h-8 flex-1 items-center justify-center rounded-lg border border-dashed border-white/10 bg-white/5 text-[#00ffff]/60',
                    },
                    [h('i', { class: 'pi pi-chart-bar text-xs' })],
                ),
                h(
                    'div',
                    {
                        class: 'flex h-8 flex-1 items-center justify-center rounded-lg border border-[#00ffff]/20 bg-[#00ffff]/5 text-[#00ffff]',
                    },
                    [h('i', { class: 'pi pi-chart-line text-xs' })],
                ),
            ]),
    },
    {
        title: 'Explain (Penjelasan)',
        desc: 'Berikan penjelasan ilmiah mengapa fenomena tersebut terjadi dan bandingkan dengan dugaan awalmu.',
        icon: 'pi-comments',
        bg: 'bg-purple-500/10',
        text: 'text-purple-400',
        color: 'purple',
        extra: () =>
            h('div', { class: 'space-y-1.5' }, [
                h('div', { class: 'h-1 w-full rounded-full bg-white/10' }),
                h('div', {
                    class: 'h-1 w-4/5 rounded-full bg-[#00ffff]/30 shadow-[0_0_4px_#00ffff]',
                }),
            ]),
    },
];

const features = [
    {
        icon: markRaw(Brain),
        bg: 'bg-[#d2ff00]/10',
        text: 'text-[#d2ff00]',
        border: 'border-[#d2ff00]/20',
        title: 'AI Tutor Interaktif',
        desc: 'Dapatkan bimbingan langsung dari AI saat menjelaskan konsep kimia dengan bahasa kamu sendiri.',
    },
    {
        icon: markRaw(FlaskConical),
        bg: 'bg-[#00ffff]/10',
        text: 'text-[#00ffff]',
        border: 'border-[#00ffff]/20',
        title: 'Simulasi & Eksperimen',
        desc: 'Eksplorasi tren sifat periodik unsur melalui tabel periodik interaktif dan visualisasi data.',
    },
    {
        icon: markRaw(ListChecks),
        bg: 'bg-purple-500/10',
        text: 'text-purple-400',
        border: 'border-purple-500/20',
        title: 'Evaluasi Adaptif',
        desc: 'Soal evaluasi yang menyesuaikan tingkat pemahaman kamu secara real-time.',
    },
    {
        icon: markRaw(BookOpen),
        bg: 'bg-amber-500/10',
        text: 'text-amber-400',
        border: 'border-amber-500/20',
        title: 'Metode Pembelajaran POE',
        desc: 'Pembelajaran terstruktur mengikuti alur Predict, Observe, dan Explain untuk pemahaman lebih mendalam.',
    },
    {
        icon: markRaw(TrendingUp),
        bg: 'bg-blue-500/10',
        text: 'text-blue-400',
        border: 'border-blue-500/20',
        title: 'Pelacakan Progres',
        desc: 'Pantau kemajuan belajar di setiap tahap dan lihat perkembanganmu dari waktu ke waktu.',
    },
    {
        icon: markRaw(Smartphone),
        bg: 'bg-rose-500/10',
        text: 'text-rose-400',
        border: 'border-rose-500/20',
        title: 'Responsif & Ringan',
        desc: 'Akses kapan saja dari perangkat apapun — laptop, tablet, maupun smartphone.',
    },
];
</script>

<template>
    <Head title="Selamat Datang di ElementVerse" />

    <div
        class="min-h-screen overflow-x-hidden bg-[#070814] font-sans text-slate-100 selection:bg-[#d2ff00]/30 selection:text-white"
    >
        <!-- ===== NAVBAR ===== -->
        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-[#070814]/80 shadow-[0_4px_30px_rgba(0,0,0,0.5)] backdrop-blur-md transition-all duration-300"
        >
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-12"
                aria-label="Global"
            >
                <!-- Logo -->
                <div class="flex lg:flex-1">
                    <Link
                        href="#home"
                        @click.prevent="scrollToSection('home')"
                        class="-m-1.5 flex items-center gap-2.5 p-1.5"
                    >
                        <div class="flex items-center">
                            <img
                                src="/assets/images/logo_elementverse.png"
                                alt="ElementVerse Logo"
                                class="h-8 w-auto object-contain select-none"
                            />
                        </div>
                    </Link>
                </div>

                <!-- Nav Links -->
                <div class="hidden items-center gap-8 md:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.id"
                        :href="`#${item.id}`"
                        @click.prevent="scrollToSection(item.id)"
                        :class="[
                            'relative cursor-pointer pb-1 text-sm font-semibold tracking-wide transition-colors',
                            activeSection === item.id
                                ? 'font-bold text-[#d2ff00] drop-shadow-[0_0_8px_rgba(210,255,0,0.3)]'
                                : 'text-slate-400 hover:text-white',
                        ]"
                    >
                        {{ item.label }}
                        <span
                            v-if="activeSection === item.id"
                            class="absolute right-0 bottom-0 left-0 h-0.5 rounded-full bg-[#d2ff00] shadow-[0_0_8px_#d2ff00]"
                        ></span>
                    </Link>
                </div>

                <!-- Auth Buttons -->
                <div class="flex flex-1 items-center justify-end gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="text-sm font-semibold text-slate-300 transition-colors hover:text-[#d2ff00]"
                        >
                            Dashboard <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-slate-300 transition-colors hover:text-white"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-5 py-2 text-sm font-extrabold text-[#070814] shadow-[0_0_15px_rgba(210,255,0,0.25)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95"
                        >
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- ===== HERO ===== -->
        <section
            id="home"
            class="relative overflow-hidden bg-gradient-to-b from-[#0e0f2d]/40 via-[#070814] to-[#070814] pt-32 pb-16 sm:pt-40 sm:pb-24 lg:pb-32"
        >
            <!-- Glow Effect & Diagonal Lines -->
            <div
                class="pointer-events-none absolute inset-0 z-0 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="absolute top-[10%] right-[-10%] h-[1px] w-[800px] rotate-[-35deg] bg-gradient-to-l from-transparent via-[#d2ff00]/40 to-transparent blur-[1px]"
                ></div>
                <div
                    class="absolute bottom-[20%] left-[-15%] h-[1.5px] w-[900px] rotate-[-35deg] bg-gradient-to-r from-transparent via-[#00ffff]/30 to-transparent blur-[1px]"
                ></div>
                <div
                    class="absolute top-1/4 left-[-10%] h-[400px] w-[400px] rounded-full bg-violet-600/10 blur-[120px]"
                ></div>
                <div
                    class="absolute right-[-10%] bottom-1/3 h-[450px] w-[450px] rounded-full bg-indigo-500/10 blur-[150px]"
                ></div>
            </div>

            <!-- Floating SVGs -->
            <div
                class="animate-float pointer-events-none absolute top-24 right-12 z-0 opacity-20 lg:right-24"
            >
                <svg
                    class="h-16 w-16"
                    viewBox="0 0 64 64"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M22 36 L12 52 C10 55 12 58 16 58 H48 C52 58 54 55 52 52 L42 36 Z"
                        fill="#00ffff"
                        opacity="0.3"
                    />
                    <path
                        d="M28 8 H36 V18 L48.5 42 L52 52 C53.5 55 51.5 58 48 58 H16 C12.5 58 10.5 55 12 52 L15.5 42 L28 18 V8 Z"
                        stroke="#00ffff"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M26 8 H38"
                        stroke="#00ffff"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                    <circle
                        cx="28"
                        cy="46"
                        r="2"
                        fill="#d2ff00"
                        opacity="0.8"
                    />
                    <circle
                        cx="36"
                        cy="42"
                        r="3"
                        fill="#d2ff00"
                        opacity="0.8"
                    />
                </svg>
            </div>
            <div
                class="animate-float-delayed pointer-events-none absolute bottom-20 left-10 z-0 opacity-15 lg:left-24"
            >
                <svg
                    class="h-12 w-12"
                    viewBox="0 0 64 64"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <line
                        x1="16"
                        y1="48"
                        x2="32"
                        y2="32"
                        stroke="#d2ff00"
                        stroke-width="2"
                    />
                    <line
                        x1="48"
                        y1="48"
                        x2="32"
                        y2="32"
                        stroke="#d2ff00"
                        stroke-width="2"
                    />
                    <line
                        x1="32"
                        y1="16"
                        x2="32"
                        y2="32"
                        stroke="#d2ff00"
                        stroke-width="2"
                    />
                    <circle
                        cx="32"
                        cy="32"
                        r="7"
                        fill="#00ffff"
                        opacity="0.3"
                        stroke="#00ffff"
                        stroke-width="2"
                    />
                    <circle
                        cx="16"
                        cy="48"
                        r="5"
                        fill="#d2ff00"
                        opacity="0.5"
                    />
                    <circle
                        cx="48"
                        cy="48"
                        r="5"
                        fill="#d2ff00"
                        opacity="0.5"
                    />
                    <circle
                        cx="32"
                        cy="16"
                        r="4"
                        fill="#00ffff"
                        opacity="0.5"
                    />
                </svg>
            </div>

            <!-- Content -->
            <div
                class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 lg:grid-cols-12 lg:px-12"
            >
                <!-- Left Column: Copy & Actions -->
                <div
                    class="flex flex-col items-center text-center lg:col-span-7 lg:items-start lg:text-left"
                >
                    <div
                        data-aos="fade-up"
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#d2ff00]/20 bg-[#d2ff00]/5 px-4 py-1.5 text-xs font-semibold text-[#d2ff00] shadow-[0_0_15px_rgba(210,255,0,0.1)] backdrop-blur-sm"
                    >
                        <span
                            class="flex h-2 w-2 animate-pulse rounded-full bg-[#d2ff00]"
                        ></span>
                        Platform LMS Interaktif & Adaptif
                    </div>

                    <h1
                        class="mb-6 text-4xl leading-tight font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl"
                        data-aos="fade-up"
                        data-aos-delay="100"
                    >
                        Selamat Datang di
                        <span
                            class="animate-text-glow mt-2 block bg-gradient-to-r from-[#d2ff00] via-white to-[#00ffff] bg-clip-text pb-2 text-transparent"
                        >
                            ElementVerse
                            <Sparkles
                                class="relative -top-1 ml-1 inline-block h-8 w-8 animate-pulse align-middle text-[#00ffff]"
                            />
                        </span>
                    </h1>

                    <p
                        class="mb-10 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg lg:mx-0"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        Tingkatkan pemahaman konsep
                        <strong class="font-bold text-[#d2ff00]"
                            >Sistem Periodik Unsur</strong
                        >
                        melalui pendekatan
                        <strong class="font-bold text-[#00ffff]"
                            >Predict, Observe, Explain (POE)</strong
                        >. Belajar lebih interaktif, mendalam, dan menyenangkan
                        bersama AI Tutor.
                    </p>

                    <div
                        class="flex w-full flex-col items-center justify-center gap-4 sm:w-auto sm:flex-row lg:justify-start"
                        data-aos="fade-up"
                        data-aos-delay="300"
                    >
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3.5 text-center text-sm font-extrabold text-[#070814] shadow-[0_0_20px_rgba(210,255,0,0.3)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                        >
                            Lanjutkan Belajar &rarr;
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('register')"
                                class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3.5 text-center text-sm font-extrabold text-[#070814] shadow-[0_0_20px_rgba(210,255,0,0.3)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                            >
                                Mulai Sekarang &rarr;
                            </Link>
                            <Link
                                :href="route('login')"
                                class="w-full rounded-full border border-white/10 bg-white/5 px-8 py-3.5 text-center text-sm font-bold text-slate-200 backdrop-blur-sm transition-all duration-200 hover:border-white/40 hover:bg-white/10 hover:text-white focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:bg-white/5 sm:w-auto"
                            >
                                Sudah punya akun? Masuk
                            </Link>
                        </template>
                    </div>
                </div>
                <!-- Right Column: Animated Logomark without Card -->
                <div
                    class="flex w-full justify-center lg:col-span-5 lg:justify-end"
                    data-aos="fade-left"
                    data-aos-delay="200"
                >
                    <div
                        class="relative flex aspect-square w-full max-w-[380px] items-center justify-center select-none"
                    >
                        <!-- SVG Orbit Animation -->
                        <svg
                            class="animate-spin-slow pointer-events-none absolute h-full w-full opacity-70"
                            viewBox="0 0 200 200"
                        >
                            <ellipse
                                cx="100"
                                cy="100"
                                rx="95"
                                ry="35"
                                fill="none"
                                stroke="#00ffff"
                                stroke-width="1"
                                stroke-dasharray="6 6"
                                class="origin-center rotate-45"
                            />
                            <ellipse
                                cx="100"
                                cy="100"
                                rx="95"
                                ry="35"
                                fill="none"
                                stroke="#d2ff00"
                                stroke-width="1"
                                stroke-dasharray="6 6"
                                class="origin-center -rotate-45"
                            />
                            <circle
                                cx="5"
                                cy="100"
                                r="5"
                                fill="#00ffff"
                                class="animate-pulse"
                            />
                            <circle
                                cx="195"
                                cy="100"
                                r="5"
                                fill="#d2ff00"
                                class="animate-pulse"
                            />
                        </svg>

                        <!-- Logomark Core -->
                        <img
                            src="/assets/images/logomark_elementverse.png"
                            alt="ElementVerse Logomark"
                            class="animate-float pointer-events-none relative z-10 h-52 w-52 object-contain drop-shadow-[0_0_35px_rgba(0,255,255,0.45)] transition-all duration-500 hover:scale-110"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== STAGES ===== -->
        <section
            id="stages"
            class="border-t border-white/5 bg-[#070814]/90 py-16 sm:py-24"
        >
            <div class="mx-auto max-w-5xl px-6 lg:px-12">
                <div class="mb-14 text-center" data-aos="fade-up">
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#00ffff]/20 bg-[#00ffff]/5 px-3 py-1 text-xs font-semibold text-[#00ffff] shadow-[0_0_10px_rgba(0,255,255,0.1)]"
                    >
                        <i class="pi pi-compass text-xs"></i>
                        Pendekatan Saintifik
                    </div>
                    <h2
                        class="mb-4 text-3xl font-extrabold text-white sm:text-4xl"
                    >
                        Siklus Belajar POE
                    </h2>
                    <p
                        class="mx-auto max-w-xl text-sm leading-relaxed text-slate-400 sm:text-base"
                    >
                        Model pembelajaran
                        <strong class="font-bold text-white"
                            >Predict, Observe, Explain (POE)</strong
                        >
                        yang terbukti mampu melatih nalar kritis, kemampuan
                        analisis, dan pemahaman konsep sains siswa.
                    </p>
                </div>

                <div class="relative">
                    <!-- Garis timeline -->
                    <div
                        class="absolute top-2 bottom-2 left-5 w-0.5 -translate-x-1/2 rounded-full shadow-[0_0_8px_rgba(0,255,255,0.3)] md:left-1/2"
                        style="
                            background: linear-gradient(
                                to bottom,
                                #d2ff00 0%,
                                #00ffff 50%,
                                #a78bfa 100%
                            );
                        "
                    ></div>

                    <div class="space-y-6">
                        <template v-for="(stage, i) in stages" :key="i">
                            <!-- Mobile -->
                            <div
                                class="flex items-start gap-5 md:hidden"
                                data-aos="fade-up"
                                :data-aos-delay="i * 100"
                            >
                                <div class="relative z-10 mt-0.5">
                                    <div
                                        :class="`flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-white/10 bg-[#0e1026]/90 shadow-lg ${stage.text}`"
                                    >
                                        <i
                                            :class="`pi ${stage.icon} text-sm`"
                                        ></i>
                                    </div>
                                </div>
                                <div
                                    class="flex-1 rounded-2xl border border-white/5 bg-white/[0.02] p-5 shadow-[0_8px_32px_rgba(0,0,0,0.37)] backdrop-blur-md"
                                >
                                    <span
                                        :class="`inline-block text-[10px] font-bold tracking-widest uppercase ${stage.text} mb-2`"
                                        >Tahap {{ i + 1 }}</span
                                    >
                                    <h3
                                        class="mb-1 text-base font-extrabold text-white"
                                    >
                                        {{ stage.title }}
                                    </h3>
                                    <p
                                        class="mb-3 text-xs leading-relaxed text-slate-300"
                                    >
                                        {{ stage.desc }}
                                    </p>
                                    <component :is="stage.extra" />
                                </div>
                            </div>

                            <!-- Desktop zigzag -->
                            <div
                                class="hidden items-center md:grid md:grid-cols-[1fr_80px_1fr]"
                            >
                                <!-- Kolom kiri (tahap genap: 2, 4) -->
                                <div class="flex justify-end pr-8">
                                    <div
                                        v-if="i % 2 !== 0"
                                        data-aos="fade-right"
                                        :data-aos-delay="i * 100"
                                        class="group w-full max-w-xs rounded-2xl border border-white/5 bg-white/[0.02] p-5 shadow-[0_8px_32px_rgba(0,0,0,0.37)] backdrop-blur-md transition-all duration-300 hover:border-white/10 hover:bg-white/[0.04]"
                                    >
                                        <span
                                            :class="`inline-block text-[10px] font-bold tracking-widest uppercase ${stage.text} mb-3`"
                                            >Tahap {{ i + 1 }}</span
                                        >
                                        <div class="flex items-start gap-3">
                                            <div
                                                :class="`flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ${stage.text} border border-white/5 transition-transform group-hover:scale-105`"
                                            >
                                                <i
                                                    :class="`pi ${stage.icon} text-base`"
                                                ></i>
                                            </div>
                                            <div>
                                                <h3
                                                    class="mb-1 text-base font-extrabold text-white"
                                                >
                                                    {{ stage.title }}
                                                </h3>
                                                <p
                                                    class="text-xs leading-relaxed text-slate-300"
                                                >
                                                    {{ stage.desc }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <component :is="stage.extra" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Dot tengah -->
                                <div
                                    class="relative z-10 flex justify-center"
                                    data-aos="zoom-in"
                                    :data-aos-delay="i * 100"
                                >
                                    <div
                                        :class="`flex h-12 w-12 items-center justify-center rounded-full border-2 border-white/10 bg-[#0e1026]/90 shadow-lg ${stage.text} ring-4 ring-[#070814]`"
                                    >
                                        <i
                                            :class="`pi ${stage.icon} text-base`"
                                        ></i>
                                    </div>
                                </div>

                                <!-- Kolom kanan (tahap ganjil: 1, 3, 5) -->
                                <div class="flex justify-start pl-8">
                                    <div
                                        v-if="i % 2 === 0"
                                        data-aos="fade-left"
                                        :data-aos-delay="i * 100"
                                        class="group w-full max-w-xs rounded-2xl border border-white/5 bg-white/[0.02] p-5 shadow-[0_8px_32px_rgba(0,0,0,0.37)] backdrop-blur-md transition-all duration-300 hover:border-white/10 hover:bg-white/[0.04]"
                                    >
                                        <span
                                            :class="`inline-block text-[10px] font-bold tracking-widest uppercase ${stage.text} mb-3`"
                                            >Tahap {{ i + 1 }}</span
                                        >
                                        <div class="flex items-start gap-3">
                                            <div
                                                :class="`flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ${stage.text} border border-white/5 transition-transform group-hover:scale-105`"
                                            >
                                                <i
                                                    :class="`pi ${stage.icon} text-base`"
                                                ></i>
                                            </div>
                                            <div>
                                                <h3
                                                    class="mb-1 text-base font-extrabold text-white"
                                                >
                                                    {{ stage.title }}
                                                </h3>
                                                <p
                                                    class="text-xs leading-relaxed text-slate-300"
                                                >
                                                    {{ stage.desc }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <component :is="stage.extra" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FEATURES ===== -->
        <section
            id="features"
            class="border-t border-white/5 bg-[#070814]/80 py-16 sm:py-24"
        >
            <div class="mx-auto max-w-7xl px-6 lg:px-12">
                <div class="mb-16 text-center" data-aos="fade-up">
                    <p
                        class="mb-2 text-sm font-semibold tracking-wider text-[#d2ff00] uppercase drop-shadow-[0_0_6px_rgba(210,255,0,0.25)]"
                    >
                        Kenapa ElementVerse?
                    </p>
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                        Fasilitas Penunjang Belajarmu
                    </h2>
                    <p class="mx-auto mt-4 max-w-xl text-base text-slate-400">
                        Kombinasi metode pembelajaran terbukti dan kecerdasan
                        buatan untuk pengalaman belajar terbaik.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="(feat, index) in features"
                        :key="feat.title"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                        class="group rounded-2xl border border-white/5 bg-white/[0.02] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.25)] backdrop-blur-md transition-all duration-300 hover:border-[#d2ff00]/20 hover:bg-white/[0.04] hover:shadow-[0_8px_30px_rgba(210,255,0,0.05)]"
                    >
                        <div
                            :class="`mb-5 flex h-12 w-12 items-center justify-center rounded-xl ${feat.bg} ${feat.text} border transition-transform group-hover:scale-110 ${feat.border}`"
                        >
                            <component
                                :is="feat.icon"
                                class="h-6 w-6"
                                :stroke-width="2"
                            />
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-white">
                            {{ feat.title }}
                        </h3>
                        <p class="text-sm leading-relaxed text-slate-400">
                            {{ feat.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== GUIDE (PANDUAN PENGGUNAAN) ===== -->
        <section
            id="guide"
            class="relative overflow-hidden border-t border-white/5 bg-[#070814]/95 py-16 sm:py-24"
        >
            <!-- Glow background effect -->
            <div
                class="pointer-events-none absolute -top-40 right-1/4 h-96 w-96 rounded-full bg-[#d2ff00]/5 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-40 left-1/4 h-96 w-96 rounded-full bg-[#00ffff]/5 blur-3xl"
            ></div>

            <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-12">
                <!-- Section Header -->
                <div class="mb-14 text-center" data-aos="fade-up">
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#d2ff00]/20 bg-[#d2ff00]/5 px-3 py-1 text-xs font-semibold text-[#d2ff00] shadow-[0_0_10px_rgba(210,255,0,0.1)]"
                    >
                        <Zap class="h-3.5 w-3.5" />
                        Panduan Lengkap Platform
                    </div>
                    <h2
                        class="mb-4 text-3xl font-extrabold text-white sm:text-4xl"
                    >
                        Panduan Penggunaan LMS
                    </h2>
                    <p
                        class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-400 sm:text-base"
                    >
                        Petunjuk praktis langkah demi langkah untuk
                        memaksimalkan pengalaman belajar interaktif dan
                        pengelolaan kelas berbasis POE dan AI.
                    </p>

                    <!-- Tab Switcher (Siswa / Guru) -->
                    <div class="mt-8 flex justify-center">
                        <div
                            class="inline-flex rounded-full border border-white/10 bg-[#0e1026]/80 p-1.5 backdrop-blur-md"
                        >
                            <button
                                @click="activeGuideTab = 'siswa'"
                                :class="[
                                    'flex items-center gap-2.5 rounded-full px-6 py-2.5 text-xs font-bold transition-all duration-300 sm:text-sm',
                                    activeGuideTab === 'siswa'
                                        ? 'bg-gradient-to-r from-[#d2ff00] to-[#00ffff] text-[#070814] shadow-[0_0_20px_rgba(210,255,0,0.3)]'
                                        : 'text-slate-400 hover:text-white',
                                ]"
                            >
                                <GraduationCap class="h-4 w-4" />
                                Panduan Siswa
                            </button>
                            <button
                                @click="activeGuideTab = 'guru'"
                                :class="[
                                    'flex items-center gap-2.5 rounded-full px-6 py-2.5 text-xs font-bold transition-all duration-300 sm:text-sm',
                                    activeGuideTab === 'guru'
                                        ? 'bg-gradient-to-r from-[#00ffff] to-purple-400 text-[#070814] shadow-[0_0_20px_rgba(0,255,255,0.3)]'
                                        : 'text-slate-400 hover:text-white',
                                ]"
                            >
                                <UserCheck class="h-4 w-4" />
                                Panduan Guru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: SISWA GUIDE -->
                <div v-if="activeGuideTab === 'siswa'" class="space-y-8">
                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            v-for="(item, idx) in siswaGuideSteps"
                            :key="item.step"
                            data-aos="fade-up"
                            :data-aos-delay="idx * 100"
                            class="group relative flex flex-col justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.37)] backdrop-blur-md transition-all duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.04]"
                        >
                            <div>
                                <!-- Step Header -->
                                <div
                                    class="mb-5 flex items-center justify-between"
                                >
                                    <span
                                        :class="`flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br ${item.color} border text-sm font-black shadow-inner`"
                                    >
                                        {{ item.step }}
                                    </span>
                                    <span
                                        class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-[10px] font-semibold text-slate-300"
                                    >
                                        {{ item.badge }}
                                    </span>
                                </div>

                                <!-- Icon & Title -->
                                <div class="mb-3 flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-[#00ffff]"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="h-4 w-4"
                                        />
                                    </div>
                                    <h3
                                        class="text-base font-extrabold text-white"
                                    >
                                        {{ item.title }}
                                    </h3>
                                </div>

                                <p
                                    class="mb-5 text-xs leading-relaxed text-slate-300"
                                >
                                    {{ item.desc }}
                                </p>
                            </div>

                            <!-- Highlights -->
                            <div class="border-t border-white/5 pt-4">
                                <ul class="space-y-2">
                                    <li
                                        v-for="(point, pIdx) in item.highlights"
                                        :key="pIdx"
                                        class="flex items-center gap-2 text-[11px] text-slate-400"
                                    >
                                        <CheckCircle2
                                            class="h-3.5 w-3.5 shrink-0 text-[#d2ff00]"
                                        />
                                        <span>{{ point }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Note Penekanan Instruksi Guru -->
                    <div
                        data-aos="fade-up"
                        class="rounded-xl border border-[#00ffff]/20 bg-[#00ffff]/5 p-4 shadow-lg backdrop-blur-md"
                    >
                        <div
                            class="flex flex-col items-center gap-3 text-center md:flex-row md:text-left"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#00ffff]/30 bg-[#00ffff]/10 text-[#00ffff]"
                            >
                                <BookOpen class="h-5 w-5" />
                            </div>
                            <p class="text-xs leading-relaxed text-slate-300">
                                <strong class="font-extrabold text-[#00ffff]"
                                    >Penting untuk Siswa:</strong
                                >
                                Setiap topik pembelajaran kimia disusun dalam
                                urutan
                                <span class="font-semibold text-white"
                                    >Sesi Pembelajaran</span
                                >
                                (Apersepsi, Video Interaktif, Worksheet POE,
                                Rangkuman & Peta Konsep, hingga Latihan Soal).
                                Siswa
                                <strong
                                    class="text-white underline decoration-[#d2ff00] underline-offset-4"
                                    >wajib mengikuti secara penuh seluruh
                                    instruksi dari Guru Kelas</strong
                                >
                                di setiap sesi!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: GURU GUIDE -->
                <div v-else class="space-y-8">
                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="(item, idx) in guruGuideSteps"
                            :key="item.step"
                            data-aos="fade-up"
                            :data-aos-delay="idx * 100"
                            class="group relative flex flex-col justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.37)] backdrop-blur-md transition-all duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.04]"
                        >
                            <div>
                                <!-- Step Header -->
                                <div
                                    class="mb-5 flex items-center justify-between"
                                >
                                    <span
                                        :class="`flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br ${item.color} border text-sm font-black shadow-inner`"
                                    >
                                        {{ item.step }}
                                    </span>
                                    <span
                                        class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-[10px] font-semibold text-slate-300"
                                    >
                                        {{ item.badge }}
                                    </span>
                                </div>

                                <!-- Icon & Title -->
                                <div class="mb-3 flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-[#d2ff00]"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="h-4 w-4"
                                        />
                                    </div>
                                    <h3
                                        class="text-base font-extrabold text-white"
                                    >
                                        {{ item.title }}
                                    </h3>
                                </div>

                                <p
                                    class="mb-5 text-xs leading-relaxed text-slate-300"
                                >
                                    {{ item.desc }}
                                </p>
                            </div>

                            <!-- Highlights -->
                            <div class="border-t border-white/5 pt-4">
                                <ul class="space-y-2">
                                    <li
                                        v-for="(point, pIdx) in item.highlights"
                                        :key="pIdx"
                                        class="flex items-center gap-2 text-[11px] text-slate-400"
                                    >
                                        <CheckCircle2
                                            class="h-3.5 w-3.5 shrink-0 text-[#00ffff]"
                                        />
                                        <span>{{ point }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Password & Help Info Banner -->
                <div
                    data-aos="fade-up"
                    class="mt-12 rounded-2xl border border-white/10 bg-gradient-to-r from-[#0c0e29] via-[#10133a] to-[#0c0e29] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.4)] backdrop-blur-md"
                >
                    <div
                        class="flex flex-col items-center justify-between gap-4 md:flex-row"
                    >
                        <div
                            class="flex items-center gap-4 text-center md:text-left"
                        >
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-amber-500/30 bg-amber-500/10 text-amber-400"
                            >
                                <ShieldCheck class="h-6 w-6" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">
                                    Lupa Kata Sandi atau Kendala Akses Akun?
                                </h4>
                                <p class="mt-0.5 text-xs text-slate-300">
                                    Siswa atau Guru dapat mengajukan permohonan
                                    reset password di halaman
                                    <strong class="text-white"
                                        >Lupa Password</strong
                                    >. Permohonan diproses langsung oleh Admin
                                    Sekolah.
                                </p>
                            </div>
                        </div>
                        <Link
                            :href="route('password.request')"
                            class="shrink-0 rounded-full border border-amber-500/30 bg-amber-500/10 px-5 py-2 text-xs font-bold text-amber-400 transition-all hover:bg-amber-500/20 hover:text-white"
                        >
                            Reset Password &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== ABOUT ===== -->
        <section
            id="about"
            class="border-t border-white/5 bg-[#070814]/90 py-16 sm:py-24"
        >
            <div class="mx-auto max-w-7xl px-6 lg:px-12">
                <div class="items-center gap-12 lg:grid lg:grid-cols-2">
                    <div class="mb-10 lg:mb-0" data-aos="fade-right">
                        <h2 class="mb-5 text-3xl font-extrabold text-white">
                            Tentang ElementVerse
                        </h2>
                        <p
                            class="mb-4 text-justify leading-relaxed text-slate-300"
                        >
                            <strong class="font-bold text-white"
                                >ElementVerse</strong
                            >
                            adalah inovasi platform Learning Management System
                            (LMS) yang dirancang khusus untuk memecahkan
                            kesulitan siswa SMA dalam memahami materi kimia yang
                            abstrak, khususnya pada topik Sistem Periodik Unsur.
                        </p>
                        <p class="text-justify leading-relaxed text-slate-300">
                            Dengan mengintegrasikan Kecerdasan Buatan (AI)
                            sebagai tutor personal 24/7, platform ini membimbing
                            kamu mulai dari proses eksplorasi masalah,
                            penyusunan eksperimen virtual, hingga evaluasi akhir
                            — memastikan konsep kimia benar-benar dipahami,
                            bukan sekadar dihafal.
                        </p>
                    </div>

                    <div
                        data-aos="fade-left"
                        class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-[#0c0d26] to-[#0e0f2d] p-8 text-center shadow-[0_20px_50px_rgba(0,0,0,0.5)] sm:p-12"
                    >
                        <div class="absolute inset-0 opacity-5">
                            <svg
                                class="h-full w-full"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <defs>
                                    <pattern
                                        id="about-pattern"
                                        x="0"
                                        y="0"
                                        width="40"
                                        height="40"
                                        patternUnits="userSpaceOnUse"
                                    >
                                        <circle
                                            cx="20"
                                            cy="20"
                                            r="1.5"
                                            fill="white"
                                        />
                                    </pattern>
                                </defs>
                                <rect
                                    width="100%"
                                    height="100%"
                                    fill="url(#about-pattern)"
                                />
                            </svg>
                        </div>
                        <div
                            class="pointer-events-none absolute -top-8 -right-8 h-32 w-32 rounded-full bg-[#00ffff]/10 blur-3xl"
                        ></div>
                        <h3
                            class="relative z-10 mb-3 text-2xl font-bold text-white"
                        >
                            Siap Belajar Cara Baru?
                        </h3>
                        <p
                            class="relative z-10 mb-8 text-sm text-slate-300 sm:text-base"
                        >
                            Dapatkan akses penuh ke materi POE, AI Tutor, dan
                            evaluasi adaptif sekarang juga.
                        </p>
                        <div
                            class="relative z-10 flex flex-col items-center justify-center gap-3 sm:flex-row"
                        >
                            <Link
                                v-if="$page.props.auth?.user"
                                :href="route('dashboard')"
                                class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3 text-sm font-extrabold text-[#070814] shadow-[0_0_15px_rgba(210,255,0,0.25)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                            >
                                Buka Dashboard &rarr;
                            </Link>
                            <template v-else>
                                <Link
                                    :href="route('register')"
                                    class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3 text-sm font-extrabold text-[#070814] shadow-[0_0_15px_rgba(210,255,0,0.25)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                                >
                                    Daftar Gratis
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="w-full rounded-full border border-white/10 bg-white/5 px-8 py-3 text-sm font-bold text-slate-200 transition-all duration-200 hover:border-white/40 hover:bg-white/10 hover:text-white focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:bg-white/5 sm:w-auto"
                                >
                                    Masuk
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FOOTER ===== -->
        <footer
            class="border-t border-white/5 bg-[#05050f] py-8 text-slate-400"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 sm:flex-row lg:px-12"
            >
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center">
                        <img
                            src="/assets/images/logo_elementverse.png"
                            alt="ElementVerse Logo"
                            class="h-6 w-auto object-contain select-none"
                        />
                    </div>
                </div>
                <p class="text-center text-xs">
                    © {{ new Date().getFullYear() }} ElementVerse. Platform
                    Pembelajaran Kimia SMA Berbasis AI Tutor.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
    scroll-padding-top: 80px;
}

@keyframes spin-slow {
    to {
        transform: rotate(360deg);
    }
}
.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

@keyframes shine {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}
.animate-text-glow {
    background-size: 200% auto;
    animation: shine 6s linear infinite;
}

@keyframes float-gentle {
    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-12px) rotate(6deg);
    }
}
@keyframes float-reverse {
    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-10px) rotate(-8deg);
    }
}
.animate-float {
    animation: float-gentle 6s ease-in-out infinite;
}
.animate-float-delayed {
    animation: float-reverse 8s ease-in-out infinite 2s;
}
</style>
