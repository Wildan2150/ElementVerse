<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    classroom: {
        id: number;
        class_name: string;
    };
    topic: {
        id: number;
        title: string;
        description: string | null;
        pivot: { is_published: boolean; is_open: boolean };
        phases: Array<{
            id: number;
            name: string;
            is_ai_enabled: boolean;
            is_chatbot_enabled: boolean;
            order?: number;
        }>;
    };
}>();

// ==========================================
// 1. LOGIKA PUBLISH / UNPUBLISH (Optimistic)
// ==========================================
const isToggling = ref(false);
const localIsPublished = ref(!!props.topic.pivot?.is_published);

watch(
    () => props.topic.pivot?.is_published,
    (newVal) => {
        localIsPublished.value = !!newVal;
    },
);

const togglePublish = () => {
    if (isToggling.value) {
        return;
    }

    isToggling.value = true;

    // Optimistic Update
    localIsPublished.value = !localIsPublished.value;

    router.post(
        route('guru.classes.topics.toggle-publish', {
            classroom: props.classroom.id,
            topic: props.topic.id,
        }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            only: ['topic'],
            onSuccess: () => {
                toast.success(
                    localIsPublished.value
                        ? 'Materi Dipublikasikan'
                        : 'Materi Disembunyikan (Draft)',
                    {
                        description:
                            'Status publikasi topik berhasil diperbarui ke siswa.',
                        icon: localIsPublished.value ? '🚀' : '🔒',
                    },
                );
            },
            onError: () => {
                // Revert jika gagal
                localIsPublished.value = !localIsPublished.value;
                toast.error('Gagal mengubah status publikasi!');
            },
            onFinish: () => {
                isToggling.value = false;
            },
        },
    );
};

// ==========================================
// 2. LOGIKA EDIT TOPIK
// ==========================================
const isEditModalOpen = ref(false);
const editForm = useForm({
    title: props.topic.title,
    description: props.topic.description || '',
});

const openEditModal = () => {
    editForm.title = props.topic.title;
    editForm.description = props.topic.description || '';
    isEditModalOpen.value = true;
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    setTimeout(() => editForm.clearErrors(), 200);
};

const submitEdit = () => {
    editForm.put(
        route('guru.classes.topics.update', {
            classroom: props.classroom.id,
            topic: props.topic.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeEditModal();
                toast.success('Topik Diperbarui', {
                    description: 'Informasi topik berhasil disimpan.',
                });
            },
        },
    );
};

// ==========================================
// 3. LOGIKA HAPUS TOPIK
// ==========================================
const isDeleteModalOpen = ref(false);
const isDeleting = ref(false);

const openDeleteModal = () => (isDeleteModalOpen.value = true);
const closeDeleteModal = () => (isDeleteModalOpen.value = false);

const executeDelete = () => {
    isDeleting.value = true;
    router.delete(
        route('guru.classes.topics.destroy', {
            classroom: props.classroom.id,
            topic: props.topic.id,
        }),
        {
            onSuccess: () => {
                // Note: Redirect ditangani oleh controller (kembali ke detail kelas)
                toast.success('Topik Dihapus', {
                    description: `Topik "${props.topic.title}" berhasil dihapus permanen.`,
                });
            },
            onError: () => {
                isDeleting.value = false;
                toast.error('Gagal Menghapus', {
                    description: 'Terjadi kesalahan saat menghapus topik.',
                });
            },
        },
    );
};

// ==========================================
// 4. LOGIKA TAMBAH FASE (MODAL)
// ==========================================
const isCreatePhaseModalOpen = ref(false);
const createPhaseForm = useForm({
    name: '',
});

const openCreatePhaseModal = () => (isCreatePhaseModalOpen.value = true);
const closeCreatePhaseModal = () => {
    isCreatePhaseModalOpen.value = false;
    setTimeout(() => {
        createPhaseForm.reset();
        createPhaseForm.clearErrors();
    }, 200);
};

const submitCreatePhase = () => {
    createPhaseForm.post(
        route('guru.phases.store', {
            classroom: props.classroom.id,
            topic: props.topic.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeCreatePhaseModal();
                toast.success('Sesi Berhasil Dibuat', {
                    description: 'Sesi baru telah ditambahkan ke dalam topik.',
                    icon: '✨',
                });
            },
        },
    );
};

// ==========================================
// 5. LOGIKA UPDATE & HAPUS FASE
// ==========================================
const localPhases = ref([...props.topic.phases]);

watch(
    () => props.topic.phases,
    (newPhases) => {
        localPhases.value = [...newPhases];
    },
    { deep: true },
);

const movePhase = (index: number, direction: 'up' | 'down') => {
    if (direction === 'up' && index === 0) {
        return;
    }

    if (direction === 'down' && index === localPhases.value.length - 1) {
        return;
    }

    const targetIndex = direction === 'up' ? index - 1 : index + 1;

    const updated = [...localPhases.value];
    const temp = updated[index];
    updated[index] = updated[targetIndex];
    updated[targetIndex] = temp;
    localPhases.value = updated;

    const phaseIds = localPhases.value.map((p: any) => p.id);
    router.post(
        route('guru.phases.reorder', {
            classroom: props.classroom.id,
            topic: props.topic.id,
        }),
        { phase_ids: phaseIds },
        {
            preserveScroll: true,
            onSuccess: () =>
                toast.success('Urutan sesi diperbarui', {
                    icon: '🔄',
                }),
        },
    );
};

const updatePhase = (phase: any) => {
    // Dipicu saat input field fase kehilangan fokus (blur)
    router.put(
        route('guru.phases.update', { phase: phase.id }),
        {
            name: phase.name,
            is_ai_enabled: !!phase.is_ai_enabled, // Paksa jadi boolean
            is_chatbot_enabled: !!phase.is_chatbot_enabled, // Paksa jadi boolean
        },
        {
            preserveScroll: true,
            onSuccess: () =>
                toast.success('Nama sesi diperbarui', {
                    description: `Perubahan tersimpan otomatis.`,
                }),
        },
    );
};

const isDeletePhaseModalOpen = ref(false);
const phaseIdToDelete = ref<number | null>(null);

const deletePhase = (id: number) => {
    phaseIdToDelete.value = id;
    isDeletePhaseModalOpen.value = true;
};

const closeDeletePhaseModal = () => {
    isDeletePhaseModalOpen.value = false;
    phaseIdToDelete.value = null;
};

const executeDeletePhase = () => {
    if (phaseIdToDelete.value) {
        router.delete(
            route('guru.phases.destroy', { phase: phaseIdToDelete.value }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeDeletePhaseModal();
                    toast.success('Sesi Dihapus');
                },
                onError: () => {
                    closeDeletePhaseModal();
                    toast.error('Gagal Menghapus', {
                        description: 'Terjadi kesalahan saat menghapus sesi.',
                    });
                },
            },
        );
    }
};
</script>

<template>
    <Head :title="`Kelola Topik: ${topic.title}`" />

    <div
        class="min-h-screen bg-transparent px-4 py-6 font-sans md:px-8 lg:px-10"
    >
        <div class="mx-auto max-w-5xl">
            <div
                class="mb-6 flex items-center gap-2 text-[12px] font-bold text-slate-400"
            >
                <Link
                    :href="route('guru.dashboard')"
                    class="transition-colors hover:text-[var(--theme-primary)]"
                    >Dashboard</Link
                >
                <i class="pi pi-chevron-right text-[8px] text-slate-600"></i>
                <Link
                    :href="route('guru.classes.show', classroom.id)"
                    class="transition-colors hover:text-[var(--theme-primary)]"
                    >{{ classroom.class_name }}</Link
                >
                <i class="pi pi-chevron-right text-[8px] text-slate-600"></i>
                <span class="text-[var(--theme-primary)]">Kelola Topik</span>
            </div>

            <Card
                class="mb-8 overflow-hidden border-border/40 bg-card/60 p-4 text-card-foreground shadow-sm backdrop-blur-md md:p-6"
            >
                <div
                    class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between"
                >
                    <div class="flex flex-1 items-start gap-5">
                        <div
                            class="mt-1 flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-border/40 bg-[var(--theme-primary)]/10 text-[var(--theme-primary)] md:mt-0"
                        >
                            <i class="pi pi-folder-open text-2xl"></i>
                        </div>
                        <div>
                            <div class="mb-1 flex items-center gap-3">
                                <h1
                                    class="text-2xl font-extrabold tracking-tight text-slate-100"
                                >
                                    {{ topic.title }}
                                </h1>
                                <!-- Badge: 3 state — loading, draft, published -->
                                <span
                                    v-if="isToggling"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-800 px-2.5 py-0.5 text-[11px] font-medium text-slate-400"
                                >
                                    <span
                                        class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-slate-400"
                                    ></span>
                                    Menyimpan...
                                </span>
                                <span
                                    v-else-if="!localIsPublished"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-2.5 py-0.5 text-[11px] font-medium text-amber-400"
                                >
                                    <span
                                        class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"
                                    ></span>
                                    Draft
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-medium text-emerald-400"
                                >
                                    <span
                                        class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"
                                    ></span>
                                    Published
                                </span>
                            </div>
                            <div
                                v-if="topic.description"
                                v-html="topic.description"
                                class="rich-text-content max-w-2xl text-[14px] leading-relaxed text-slate-300"
                            ></div>
                            <p
                                v-else
                                class="max-w-2xl text-[14px] leading-relaxed text-slate-400"
                            >
                                Tidak ada deskripsi khusus untuk topik ini.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center"
                    >
                        <div
                            class="flex items-center justify-between gap-3 rounded-lg border px-4 py-2.5 transition-colors sm:justify-start"
                            :class="
                                isToggling
                                    ? 'cursor-not-allowed border-white/5 bg-white/5 opacity-70'
                                    : localIsPublished
                                      ? 'cursor-pointer border-emerald-500/20 bg-emerald-500/10'
                                      : 'cursor-pointer border-white/5 bg-white/5'
                            "
                        >
                            <div class="flex flex-col gap-0.5">
                                <span
                                    class="text-[12px] font-medium"
                                    :class="
                                        localIsPublished
                                            ? 'text-emerald-400'
                                            : 'text-slate-300'
                                    "
                                    >Publikasi ke siswa</span
                                >
                                <span
                                    class="text-[11px]"
                                    :class="
                                        isToggling
                                            ? 'text-slate-500'
                                            : localIsPublished
                                              ? 'text-emerald-550'
                                              : 'text-slate-400'
                                    "
                                >
                                    {{
                                        isToggling
                                            ? 'Menyimpan perubahan...'
                                            : localIsPublished
                                              ? 'Materi sudah terlihat oleh siswa'
                                              : 'Materi belum terlihat oleh siswa'
                                    }}
                                </span>
                            </div>
                            <button
                                type="button"
                                :disabled="isToggling"
                                @click="togglePublish"
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                :class="
                                    isToggling
                                        ? 'bg-white/10'
                                        : localIsPublished
                                          ? 'bg-emerald-500'
                                          : 'bg-white/20'
                                "
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                                    :class="
                                        localIsPublished
                                            ? 'translate-x-6'
                                            : 'translate-x-1'
                                    "
                                />
                            </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button
                                @click="openEditModal"
                                variant="outline"
                                class="h-10"
                            >
                                <i class="pi pi-pencil mr-2"></i> Edit
                            </Button>
                            <Button
                                @click="openDeleteModal"
                                variant="outline"
                                class="hover:text-rose-350 h-10 border-rose-500/20 bg-rose-500/5 text-rose-400 hover:bg-rose-500/10"
                            >
                                <i class="pi pi-trash"></i>
                            </Button>
                        </div>
                    </div>
                </div>
            </Card>

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-100">
                        Daftar sesi pembelajaran.
                    </h2>
                    <p class="text-[13px] text-slate-400">
                        Buat alur tahapan pembelajaran.
                    </p>
                </div>
                <Button @click="openCreatePhaseModal" class="h-10 font-bold">
                    <i class="pi pi-plus mr-2"></i> Tambah Sesi Baru
                </Button>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(phase, pIdx) in localPhases"
                    :key="phase.id"
                    class="relative"
                >
                    <Card
                        class="flex flex-col justify-between gap-4 overflow-hidden rounded-2xl border-border/40 bg-card/60 p-4 shadow-sm backdrop-blur-md transition-all hover:border-[var(--theme-primary)]/50 hover:shadow-[0_0_20px_rgba(210,255,0,0.08)] md:flex-row md:items-center md:p-5"
                    >
                        <div class="flex flex-1 items-center gap-4">
                            <!-- Tombol Reorder Sesi -->
                            <div
                                class="flex shrink-0 flex-col items-center gap-1.5"
                            >
                                <button
                                    type="button"
                                    @click="movePhase(pIdx, 'up')"
                                    :disabled="pIdx === 0"
                                    class="text-slate-400 transition-colors hover:text-[var(--theme-primary)] disabled:cursor-not-allowed disabled:opacity-20"
                                    title="Pindahkan Ke Atas"
                                >
                                    <i class="pi pi-chevron-up text-xs"></i>
                                </button>
                                <button
                                    type="button"
                                    @click="movePhase(pIdx, 'down')"
                                    :disabled="pIdx === localPhases.length - 1"
                                    class="text-slate-400 transition-colors hover:text-[var(--theme-primary)] disabled:cursor-not-allowed disabled:opacity-20"
                                    title="Pindahkan Ke Bawah"
                                >
                                    <i class="pi pi-chevron-down text-xs"></i>
                                </button>
                            </div>

                            <span
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-border/40 bg-white/5 text-[16px] font-black text-[var(--theme-primary)]"
                            >
                                {{ pIdx + 1 }}
                            </span>
                            <div class="flex-1">
                                <input
                                    v-model="phase.name"
                                    @blur="updatePhase(phase)"
                                    @keyup.enter="$event.target.blur()"
                                    class="w-full border-none bg-transparent p-0 text-[16px] font-extrabold text-slate-100 placeholder:text-slate-500 focus:ring-0 focus:outline-none"
                                    placeholder="Ketik nama sesi pembelajaran..."
                                />
                                <div
                                    class="mt-1 flex items-center gap-2 text-[11px] font-bold text-slate-400"
                                >
                                    <span
                                        v-if="phase.is_ai_enabled"
                                        class="text-emerald-450"
                                        ><i class="pi pi-sparkles"></i> AI
                                        Feedback Aktif</span
                                    >
                                    <span v-else class="text-slate-500"
                                        ><i class="pi pi-minus-circle"></i> AI
                                        Feedback Nonaktif</span
                                    >
                                    <span class="text-slate-600">•</span>
                                    <span
                                        v-if="phase.is_chatbot_enabled"
                                        class="text-sky-400"
                                        ><i
                                            class="pi pi-comments text-[10px]"
                                        ></i>
                                        Chatbot Aktif</span
                                    >
                                    <span v-else class="text-slate-500"
                                        ><i
                                            class="pi pi-minus-circle text-[10px]"
                                        ></i>
                                        Chatbot Nonaktif</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex w-full flex-wrap items-center justify-end gap-2.5 self-end border-t border-white/5 pt-3 md:w-auto md:self-auto md:border-none md:pt-0"
                        >
                            <Link
                                :href="
                                    route('guru.student-answers.index', {
                                        classroom: classroom.id,
                                        topic: topic.id,
                                        phase: phase.id,
                                    })
                                "
                            >
                                <Button
                                    variant="outline"
                                    class="hover:text-emerald-355 h-10 border-emerald-500/20 bg-emerald-500/5 px-4 text-[13px] text-emerald-400 shadow-sm hover:bg-emerald-500/10"
                                >
                                    <i
                                        class="pi pi-clipboard mr-2 text-[10px]"
                                    ></i>
                                    Lihat Jawaban
                                </Button>
                            </Link>
                            <Link
                                :href="
                                    route('guru.phases.show', {
                                        classroom: classroom.id,
                                        topic: topic.id,
                                        phase: phase.id,
                                    })
                                "
                            >
                                <Button variant="outline" class="h-10 px-5">
                                    Kelola Isi Konten
                                    <i
                                        class="pi pi-arrow-right ml-2 text-[10px]"
                                    ></i>
                                </Button>
                            </Link>

                            <button
                                @click="deletePhase(phase.id)"
                                class="text-rose-455 hover:text-rose-350 flex h-10 w-10 items-center justify-center rounded-lg border border-rose-500/20 bg-rose-500/5 transition-colors hover:bg-rose-500/10"
                                title="Hapus Sesi"
                            >
                                <i class="pi pi-trash text-sm"></i>
                            </button>
                        </div>
                    </Card>
                </div>
            </div>

            <div
                v-if="localPhases.length === 0"
                class="mt-8 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-border/40 bg-card/40 py-24 text-center shadow-sm"
            >
                <div
                    class="text-slate-450 mb-5 flex h-20 w-20 items-center justify-center rounded-full border border-border/40 bg-white/5"
                >
                    <i class="pi pi-sitemap text-4xl text-slate-400"></i>
                </div>
                <h4 class="mb-2 text-[18px] font-extrabold text-slate-200">
                    Belum Ada Sesi Pembelajaran
                </h4>
                <p
                    class="mb-6 max-w-[420px] text-[14px] leading-relaxed font-medium text-slate-400"
                >
                    Mulai bangun alur belajar siswa Anda. Tambahkan sesi secara
                    fleksibel.
                </p>
                <Button
                    @click="openCreatePhaseModal"
                    class="h-11 px-6 text-[13px] font-bold"
                >
                    <i class="pi pi-plus mr-2"></i> Buat Sesi Pertama
                </Button>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div
            v-if="isCreatePhaseModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 px-4 backdrop-blur-[6px] transition-all"
        >
            <div
                class="w-full max-w-[450px] animate-in overflow-hidden rounded-3xl border border-slate-800/50 bg-slate-950 shadow-[0_20px_50px_rgba(0,0,0,0.3)] duration-200 zoom-in-95 fade-in"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-800 bg-gradient-to-r from-slate-900/50 via-slate-900/30 to-slate-900/40 px-6 py-4.5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="text-slate-350 flex h-9 w-9 items-center justify-center rounded-xl border border-slate-800 bg-white/5"
                        >
                            <i class="pi pi-layer-group text-[15px]"></i>
                        </div>
                        <span class="text-base font-extrabold text-slate-100"
                            >Buat Sesi Pembelajaran</span
                        >
                    </div>
                    <button
                        @click="closeCreatePhaseModal"
                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-900 hover:text-slate-200"
                    >
                        <i class="pi pi-times text-sm"></i>
                    </button>
                </div>
                <form @submit.prevent="submitCreatePhase" class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label
                                class="mb-2 block text-[12px] font-bold tracking-wider text-slate-300 uppercase"
                                >Sesi Pembelajaran
                                <span class="text-rose-500">*</span></label
                            >
                            <Input
                                v-model="createPhaseForm.name"
                                type="text"
                                required
                                placeholder=""
                                class="h-11 rounded-xl border-border/40 bg-white/5 text-[14px] text-slate-100 shadow-sm focus:border-[var(--theme-primary)] focus:outline-none focus-visible:border-[var(--theme-primary)] focus-visible:ring-[var(--theme-primary)]/20"
                            />
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeCreatePhaseModal"
                            class="h-10 rounded-xl border border-border/40 bg-white/5 px-5 text-[13px] font-bold text-slate-300 hover:bg-white/10 hover:text-white"
                            >Batal</Button
                        >
                        <Button
                            type="submit"
                            :disabled="createPhaseForm.processing"
                            class="h-10 rounded-xl px-6 text-[13px] font-bold"
                        >
                            <i
                                v-if="createPhaseForm.processing"
                                class="pi pi-spinner pi-spin mr-2"
                            ></i>
                            <span v-else>Simpan Sesi</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <Teleport to="body">
        <div
            v-if="isEditModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 px-4 backdrop-blur-[6px] transition-all"
        >
            <div
                class="w-full max-w-[450px] animate-in overflow-hidden rounded-3xl border border-slate-800/50 bg-slate-950 shadow-[0_20px_50px_rgba(0,0,0,0.3)] duration-200 zoom-in-95 fade-in"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-800 bg-gradient-to-r from-slate-900/50 via-slate-900/30 to-slate-900/40 px-6 py-4.5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="text-slate-350 flex h-9 w-9 items-center justify-center rounded-xl border border-slate-800 bg-white/5"
                        >
                            <i class="pi pi-pencil text-[15px]"></i>
                        </div>
                        <span class="text-base font-extrabold text-slate-100"
                            >Edit Topik</span
                        >
                    </div>
                    <button
                        @click="closeEditModal"
                        class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-900 hover:text-slate-200"
                    >
                        <i class="pi pi-times text-sm"></i>
                    </button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label
                                class="mb-2 block text-[12px] font-bold tracking-wider text-slate-300 uppercase"
                                >Judul Topik
                                <span class="text-rose-500">*</span></label
                            >
                            <Input
                                v-model="editForm.title"
                                type="text"
                                required
                                class="h-11 rounded-xl border-border/40 bg-white/5 text-[14px] text-slate-100 shadow-sm focus:border-[var(--theme-primary)] focus:outline-none focus-visible:border-[var(--theme-primary)] focus-visible:ring-[var(--theme-primary)]/20"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-2 block text-[12px] font-bold tracking-wider text-slate-300 uppercase"
                                >Deskripsi</label
                            >
                            <RichTextEditor
                                v-model="editForm.description"
                                placeholder="Tujuan pembelajaran, deskripsi modul, dll..."
                            />
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeEditModal"
                            class="h-10 rounded-xl border border-border/40 bg-white/5 px-5 text-[13px] font-bold text-slate-300 hover:bg-white/10 hover:text-white"
                            >Batal</Button
                        >
                        <Button
                            type="submit"
                            :disabled="editForm.processing"
                            class="h-10 rounded-xl px-6 text-[13px] font-bold"
                        >
                            <i
                                v-if="editForm.processing"
                                class="pi pi-spinner pi-spin mr-2"
                            ></i>
                            <span v-else>Simpan Perubahan</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <Teleport to="body">
        <div
            v-if="isDeleteModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 px-4 backdrop-blur-[6px] transition-all"
        >
            <div
                class="w-full max-w-[400px] animate-in overflow-hidden rounded-3xl border border-slate-800/50 bg-slate-950 p-6 text-center shadow-[0_20px_50px_rgba(0,0,0,0.3)] duration-200 zoom-in-95 fade-in"
            >
                <div
                    class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full border border-amber-900/30 bg-amber-950/30 text-amber-400 shadow-inner"
                >
                    <i class="pi pi-exclamation-triangle text-2xl"></i>
                </div>
                <h3
                    class="text-xl font-extrabold tracking-tight text-slate-100"
                >
                    Hapus Topik Ini?
                </h3>
                <p
                    class="mt-2 text-[14px] leading-relaxed font-medium text-slate-400"
                >
                    Anda yakin ingin menghapus topik ini secara permanen?
                    Seluruh Sesi & Konten di dalamnya akan ikut terhapus dan
                    tidak bisa dikembalikan.
                </p>
                <div
                    class="mt-8 flex flex-col-reverse justify-center gap-3 sm:flex-row"
                >
                    <Button
                        type="button"
                        variant="outline"
                        @click="closeDeleteModal"
                        :disabled="isDeleting"
                        class="h-11 w-full rounded-xl border border-border/40 bg-white/5 px-6 text-[13px] font-bold text-slate-300 hover:bg-white/10 hover:text-white sm:w-auto"
                    >
                        Batalkan
                    </Button>
                    <Button
                        type="button"
                        @click="executeDelete"
                        :disabled="isDeleting"
                        class="h-11 w-full rounded-xl border-none bg-gradient-to-r from-rose-500 to-red-600 px-6 text-[13px] font-bold text-white shadow-[0_0_15px_rgba(239,68,68,0.2)] sm:w-auto"
                    >
                        <i
                            v-if="isDeleting"
                            class="pi pi-spinner pi-spin mr-2"
                        ></i>
                        <span v-else>Ya, Hapus Topik</span>
                    </Button>
                </div>
            </div>
        </div>
    </Teleport>

    <Teleport to="body">
        <div
            v-if="isDeletePhaseModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 px-4 backdrop-blur-[6px] transition-all"
        >
            <div
                class="w-full max-w-[400px] animate-in overflow-hidden rounded-3xl border border-slate-800/50 bg-slate-950 p-6 text-center shadow-[0_20px_50px_rgba(0,0,0,0.3)] duration-200 zoom-in-95 fade-in"
            >
                <div
                    class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full border border-amber-900/30 bg-amber-950/30 text-amber-400 shadow-inner"
                >
                    <i class="pi pi-exclamation-triangle text-2xl"></i>
                </div>
                <h3
                    class="text-xl font-extrabold tracking-tight text-slate-100"
                >
                    Hapus Sesi Ini?
                </h3>
                <p
                    class="mt-2 text-[14px] leading-relaxed font-medium text-slate-400"
                >
                    Hapus sesi ini? Seluruh isi materi dan pertanyaan di
                    dalamnya akan ikut terhapus permanen.
                </p>
                <div
                    class="mt-8 flex flex-col-reverse justify-center gap-3 sm:flex-row"
                >
                    <Button
                        type="button"
                        variant="outline"
                        @click="closeDeletePhaseModal"
                        class="text-slate-350 h-11 w-full rounded-xl border border-border/40 bg-white/5 px-6 text-[13px] font-bold hover:bg-white/10 hover:text-white sm:w-auto"
                    >
                        Batalkan
                    </Button>
                    <Button
                        type="button"
                        @click="executeDeletePhase"
                        class="to-red-650 h-11 w-full rounded-xl border-none bg-gradient-to-r from-rose-500 px-6 text-[13px] font-bold text-white shadow-[0_0_15px_rgba(239,68,68,0.2)] sm:w-auto"
                    >
                        Ya, Hapus Sesi
                    </Button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
