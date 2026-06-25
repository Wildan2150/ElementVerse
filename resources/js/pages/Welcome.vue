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
} from 'lucide-vue-next';
import { h, markRaw, ref, onMounted, onUpdated } from 'vue';

defineOptions({ layout: null });

const activeSection = ref('home');

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

    ['home', 'stages', 'features', 'about'].forEach((id) => {
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
    { id: 'about', label: 'Tentang ElementVerse' },
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
                class="relative z-10 mx-auto max-w-4xl px-6 text-center lg:px-12"
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
                    class="mx-auto mb-10 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg"
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
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                    data-aos="fade-up"
                    data-aos-delay="300"
                >
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3.5 text-sm font-extrabold text-[#070814] shadow-[0_0_20px_rgba(210,255,0,0.3)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                    >
                        Lanjutkan Belajar &rarr;
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('register')"
                            class="w-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-8 py-3.5 text-sm font-extrabold text-[#070814] shadow-[0_0_20px_rgba(210,255,0,0.3)] transition-all duration-200 hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 sm:w-auto"
                        >
                            Mulai Sekarang &rarr;
                        </Link>
                        <Link
                            :href="route('login')"
                            class="w-full rounded-full border border-white/10 bg-white/5 px-8 py-3.5 text-sm font-bold text-slate-200 backdrop-blur-sm transition-all duration-200 hover:border-white/40 hover:bg-white/10 hover:text-white focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:bg-white/5 sm:w-auto"
                        >
                            Sudah punya akun? Masuk
                        </Link>
                    </template>
                </div>

                <div
                    class="mt-14 flex justify-center gap-10 border-t border-white/5 pt-8 sm:gap-16"
                    data-aos="fade-up"
                    data-aos-delay="400"
                >
                    <div class="text-center">
                        <p class="text-3xl font-extrabold text-white">3</p>
                        <p
                            class="mt-1 text-xs font-medium text-slate-400 sm:text-sm"
                        >
                            Tahapan Belajar
                        </p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-3xl font-extrabold text-[#d2ff00] drop-shadow-[0_0_8px_rgba(210,255,0,0.3)]"
                        >
                            AI
                        </p>
                        <p
                            class="mt-1 text-xs font-medium text-slate-400 sm:text-sm"
                        >
                            Tutor Interaktif
                        </p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-3xl font-extrabold text-[#00ffff] drop-shadow-[0_0_8px_rgba(0,255,255,0.3)]"
                        >
                            SMA
                        </p>
                        <p
                            class="mt-1 text-xs font-medium text-slate-400 sm:text-sm"
                        >
                            Sistem Periodik Unsur
                        </p>
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
                <div>
                    <p class="text-center text-xs">
                        Developed by
                        <a
                            href="https://instagram.com/scalenix.studio"
                            target="_blank"
                            class="text-[#00ffff] transition-colors hover:text-[#d2ff00]"
                            >Scalenix Studio</a
                        >
                    </p>
                </div>
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
