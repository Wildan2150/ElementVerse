<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useTheme } from '@/composables/useTheme';

const stripHtml = (html: string | null | undefined): string => {
    if (!html) {
        return '';
    }

    return html
        .replace(/<\/?[^>]+(>|$)/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
};

const { theme } = useTheme();

const props = defineProps<{
    classes: Array<{
        id: number;
        class_name: string;
        class_code: string;
        description: string | null;
        created_at: string;
    }>;
    stats: {
        total_students: number;
        pending_reviews: number;
    };
}>();

// --- LOGIKA PEMOTONGAN DATA KELAS ---
// Ambil maksimal 5 kelas terbaru untuk ditampilkan di Dashboard
const recentClasses = computed(() => {
    return props.classes.slice(0, 5);
});

// Hitung sisa kelas (jika ada lebih dari 5)
const remainingClassesCount = computed(() => {
    return props.classes.length > 5 ? props.classes.length - 5 : 0;
});

const form = useForm({
    class_name: '',
    description: '',
});

const submitClass = () => {
    const createdClassName = form.class_name;

    form.post(route('guru.classes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success('Kelas Aktif!', {
                description: `Kelas "${createdClassName}" berhasil dibuat. Kode unik siap dibagikan ke siswa Anda.`,
                icon: '🚀',
                duration: 3000,
                action: {
                    label: 'Siap!',
                    onClick: () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                },
            });
        },
        onError: () => {
            toast.error('Gagal Membuat Kelas', {
                description:
                    'Mohon periksa kembali isian form Anda. Nama kelas wajib diisi.',
                icon: '⚠️',
                duration: 5000,
            });
        },
    });
};

const stats = computed(
    () => props.stats || { total_students: 0, pending_reviews: 0 },
);
</script>

<template>
    <Head title="Workspace Guru" />

    <div
        :class="[theme === 'classic' ? 'font-serif' : 'font-sans']"
        class="min-h-screen px-4 py-6 md:px-8 lg:px-10"
    >
        <div
            class="mx-auto mb-8 flex max-w-7xl flex-col items-start justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h1
                    class="text-[26px] font-bold tracking-tight text-slate-900 dark:text-slate-100"
                >
                    Workspace Guru
                </h1>
                <p class="mt-1 text-[14px] font-medium text-slate-400">
                    Kelola kelas, pantau progress metode POE
                    (Predict-Observe-Explain), dan evaluasi siswa Anda.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <Button
                    variant="outline"
                    size="icon"
                    class="h-9 w-9 rounded-lg border-slate-200 bg-white shadow-sm transition-colors hover:bg-[var(--theme-primary)]/10 hover:text-[var(--theme-primary)] dark:bg-slate-900"
                >
                    <i class="pi pi-bell text-[14px]"></i>
                </Button>

                <div
                    class="flex cursor-pointer items-center gap-3 rounded-full border border-border/40 bg-card/60 py-1.5 pr-4 pl-1.5 shadow-sm backdrop-blur-md transition-colors hover:bg-white/5"
                >
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500 text-[11px] font-bold text-white shadow-inner"
                    >
                        <i class="pi pi-user text-[10px]"></i>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-[13px] leading-none font-bold text-slate-800 dark:text-slate-200"
                            >Panel Guru</span
                        >
                        <span
                            class="dark:text-emerald-450 mt-0.5 text-[9px] font-bold tracking-wider text-emerald-600"
                            >ROLE: GURU</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <div
            class="mx-auto mb-8 grid max-w-7xl grid-cols-1 gap-5 md:grid-cols-3"
        >
            <Card
                class="rounded-xl border-border/40 bg-card/60 text-card-foreground shadow-sm backdrop-blur-md"
            >
                <CardContent class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="mb-2 text-[11px] font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                Total Kelas Anda
                            </p>
                            <h2
                                class="text-3xl font-extrabold text-slate-900 dark:text-slate-100"
                            >
                                {{ classes.length }}
                            </h2>
                        </div>
                        <div
                            class="flex h-10 w-10 animate-pulse items-center justify-center rounded-xl bg-[var(--theme-primary)]/10 text-[var(--theme-primary)]"
                        >
                            <i class="pi pi-book text-lg"></i>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card
                class="rounded-xl border-border/40 bg-card/60 text-card-foreground shadow-sm backdrop-blur-md"
            >
                <CardContent class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="mb-2 text-[11px] font-bold tracking-wider text-slate-500 uppercase dark:text-slate-400"
                            >
                                Total Siswa Aktif
                            </p>
                            <h2
                                class="text-3xl font-extrabold text-slate-900 dark:text-slate-100"
                            >
                                {{ stats.total_students }}
                            </h2>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-[#00ffff] dark:text-blue-400"
                        >
                            <i class="pi pi-users text-lg"></i>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card
                class="relative overflow-hidden rounded-xl border-none bg-gradient-to-br from-[#10B981] to-[#047857] shadow-sm"
            >
                <CardContent class="p-6">
                    <i
                        class="pi pi-check-circle absolute -right-3 -bottom-3 text-7xl text-white opacity-10"
                    ></i>
                    <div class="relative z-10 flex items-start justify-between">
                        <div>
                            <p
                                class="mb-2 text-[11px] font-bold tracking-wider text-emerald-100 uppercase"
                            >
                                Perlu Evaluasi (POE)
                            </p>
                            <h2 class="text-3xl font-extrabold text-white">
                                {{ stats.pending_reviews }}
                            </h2>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-sm"
                        >
                            <i class="pi pi-clipboard text-lg"></i>
                        </div>
                    </div>
                    <div
                        class="relative z-10 mt-4 flex items-center gap-2 text-sm"
                    >
                        <span
                            class="flex items-center gap-1.5 text-[12px] font-medium text-emerald-50"
                        >
                            Menunggu penilaian Anda
                        </span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="flex flex-col gap-5 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3
                        class="text-[17px] font-bold text-slate-800 dark:text-slate-200"
                    >
                        Kelas Terbaru
                    </h3>
                    <Link
                        v-if="classes.length > 0"
                        :href="route('guru.classes.index')"
                        class="flex items-center gap-1.5 text-[13px] font-bold text-[var(--theme-primary)] transition-colors hover:text-[var(--theme-primary-hover)] hover:underline"
                    >
                        Lihat Semua Kelas
                        <i class="pi pi-arrow-right text-[10px]"></i>
                    </Link>
                </div>

                <div
                    v-if="recentClasses.length > 0"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2"
                >
                    <Card
                        v-for="cls in recentClasses"
                        :key="cls.id"
                        class="group flex flex-col rounded-xl border-border/40 bg-card/60 text-card-foreground shadow-sm backdrop-blur-md transition-all hover:border-[var(--theme-primary)]/50 hover:shadow-[0_0_20px_rgba(210,255,0,0.08)]"
                    >
                        <CardContent class="flex flex-1 flex-col p-5">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="pr-2">
                                    <h4
                                        class="line-clamp-1 text-[16px] font-bold text-slate-900 transition-colors group-hover:text-[var(--theme-primary)] dark:text-slate-100"
                                        :title="cls.class_name"
                                    >
                                        {{ cls.class_name }}
                                    </h4>
                                    <p
                                        class="mt-1 flex items-center gap-1.5 text-[12px] text-slate-500"
                                    >
                                        <i
                                            class="pi pi-calendar text-[10px]"
                                        ></i>
                                        Dibuat:
                                        {{
                                            new Date(
                                                cls.created_at,
                                            ).toLocaleDateString('id-ID')
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="shrink-0 rounded-md border border-[var(--theme-primary)]/20 bg-[var(--theme-primary)]/5 px-2.5 py-1 font-mono text-[12px] font-bold tracking-widest text-[var(--theme-primary)]"
                                    title="Bagikan kode ini ke siswa"
                                >
                                    {{ cls.class_code }}
                                </div>
                            </div>

                            <p
                                class="mb-5 line-clamp-2 flex-1 text-[13px] text-slate-600 dark:text-slate-300"
                            >
                                {{
                                    stripHtml(cls.description) ||
                                    'Belum ada deskripsi untuk kelas ini.'
                                }}
                            </p>

                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800"
                            >
                                <div class="flex -space-x-2 overflow-hidden">
                                    <div
                                        class="inline-block h-6 w-6 rounded-full bg-slate-200 ring-2 ring-white dark:bg-slate-700 dark:ring-slate-900"
                                    ></div>
                                    <div
                                        class="inline-block h-6 w-6 rounded-full bg-slate-300 ring-2 ring-white dark:bg-slate-600 dark:ring-slate-900"
                                    ></div>
                                    <div
                                        class="inline-block h-6 w-6 rounded-full bg-slate-400 ring-2 ring-white dark:bg-slate-500 dark:ring-slate-900"
                                    ></div>
                                </div>

                                <Link
                                    :href="route('guru.classes.show', cls.id)"
                                    class="flex items-center text-[12px] font-semibold text-[var(--theme-primary)] group-hover:text-[var(--theme-primary-hover)]"
                                >
                                    Masuk Kelas
                                    <i
                                        class="pi pi-arrow-right ml-1.5 text-[10px]"
                                    ></i>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>

                    <Link
                        v-if="remainingClassesCount > 0"
                        :href="route('guru.classes.index')"
                        class="group flex min-h-[180px] cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-border/40 bg-card/40 backdrop-blur-md transition-all hover:bg-white/5"
                    >
                        <div
                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[var(--theme-primary)]/20 transition-colors group-hover:bg-[var(--theme-primary)]/30"
                        >
                            <span class="font-bold text-[var(--theme-primary)]"
                                >+{{ remainingClassesCount }}</span
                            >
                        </div>
                        <h4
                            class="text-[14px] font-bold text-slate-700 dark:text-slate-300"
                        >
                            Kelas Lainnya
                        </h4>
                        <p
                            class="mt-1 text-[12px] font-medium text-slate-500 transition-colors group-hover:text-[var(--theme-primary)]"
                        >
                            Lihat di Manajemen Kelas
                        </p>
                    </Link>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border/40 bg-card/60 py-20 text-slate-400 backdrop-blur-md"
                >
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5"
                    >
                        <i
                            class="pi pi-folder-open text-3xl text-slate-300"
                        ></i>
                    </div>
                    <h4 class="mb-1 text-[16px] font-bold text-slate-700">
                        Belum Ada Kelas
                    </h4>
                    <p
                        class="max-w-[250px] text-center text-[13px] font-medium text-slate-500"
                    >
                        Anda belum membuat kelas. Silakan buat kelas pertama
                        Anda melalui panel di samping.
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-6 lg:col-span-1">
                <Card
                    class="rounded-xl border-border/40 bg-card/60 shadow-sm backdrop-blur-md"
                >
                    <CardHeader class="border-b border-border/40 pb-4">
                        <CardTitle
                            class="flex items-center gap-2 text-[16px] font-bold text-slate-100"
                        >
                            <i
                                class="pi pi-plus-circle text-[var(--theme-accent)]"
                            ></i>
                            Buat Kelas Baru
                        </CardTitle>
                        <CardDescription
                            class="mt-1 text-[12px] text-slate-400"
                        >
                            Sistem otomatis men-generate kode unik 6 digit.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="pt-5">
                        <form @submit.prevent="submitClass" class="space-y-4">
                            <div>
                                <label
                                    class="mb-2 block text-[12px] font-bold tracking-wider text-slate-300 uppercase"
                                >
                                    Nama Kelas
                                    <span class="text-rose-500">*</span>
                                </label>
                                <Input
                                    v-model="form.class_name"
                                    type="text"
                                    required
                                    placeholder="Contoh: Kimia X IPA 1"
                                    class="h-10 rounded-xl border-border/40 bg-white/5 text-[13px] text-white focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]/20 focus:outline-none focus-visible:border-[var(--theme-primary)] focus-visible:ring-[var(--theme-primary)]/20"
                                />
                                <span
                                    v-if="form.errors.class_name"
                                    class="mt-1.5 block text-[11px] font-semibold text-rose-500"
                                >
                                    {{ form.errors.class_name }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-[12px] font-bold tracking-wider text-slate-300 uppercase"
                                >
                                    Deskripsi Singkat
                                </label>
                                <RichTextEditor
                                    v-model="form.description"
                                    placeholder="Opsional: Tujuan pembelajaran..."
                                />
                            </div>

                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="mt-2 h-10 w-full rounded-xl text-[13px] font-bold"
                            >
                                <i
                                    v-if="!form.processing"
                                    class="pi pi-check mr-2 text-[12px]"
                                ></i>
                                <i
                                    v-else
                                    class="pi pi-spinner pi-spin mr-2 text-[12px]"
                                ></i>
                                Generate Kelas
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
