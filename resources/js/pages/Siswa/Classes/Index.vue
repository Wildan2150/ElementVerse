<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

const stripHtml = (html: string | null | undefined): string => {
    if (!html) {
        return '';
    }

    return html
        .replace(/<\/?[^>]+(>|$)/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
};

const props = defineProps<{
    classrooms: Array<{
        id: number;
        class_name: string;
        class_code: string;
        description: string | null;
        topics_count: number;
    }>;
}>();

// Form Inertia untuk fitur Gabung Kelas
const form = useForm({
    class_code: '',
});

const submitJoinClass = () => {
    form.post(route('siswa.classes.join'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('class_code');
            alert('Berhasil bergabung ke kelas!');
        },
    });
};
</script>

<template>
    <Head title="Kelas Saya - ElementVerse" />

    <main
        class="relative flex min-h-screen w-full flex-1 flex-col bg-transparent font-sans"
    >
        <div
            class="border-b border-border/40 bg-card/60 px-4 py-6 text-card-foreground backdrop-blur-md md:px-8 md:py-8"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col justify-between gap-6 md:flex-row md:items-center"
            >
                <div>
                    <h1
                        class="text-[28px] font-black tracking-tight text-slate-100"
                    >
                        Kelas Saya
                    </h1>
                    <p class="mt-1 text-[14px] font-medium text-slate-400">
                        Daftar kelas kimia yang sedang Anda ikuti.
                    </p>
                </div>

                <form
                    @submit.prevent="submitJoinClass"
                    class="flex w-full flex-col items-stretch gap-2 rounded-2xl border border-border/40 bg-card/60 p-2.5 shadow-sm sm:flex-row sm:items-center sm:p-1.5 md:w-auto"
                >
                    <div class="relative w-full sm:w-56">
                        <i
                            class="pi pi-key absolute top-1/2 left-3 -translate-y-1/2 text-[13px] text-slate-400"
                        ></i>
                        <Input
                            v-model="form.class_code"
                            placeholder="Kode Kelas (6 Digit)"
                            maxlength="6"
                            class="h-10 w-full border-none bg-transparent pr-3 pl-9 text-[13px] font-bold tracking-widest text-slate-100 uppercase placeholder-slate-500 focus-visible:ring-0"
                        />
                    </div>
                    <Button
                        type="submit"
                        :disabled="
                            form.processing || form.class_code.length !== 6
                        "
                        class="h-10 w-full rounded-xl px-6 text-[13px] font-bold sm:w-auto"
                    >
                        <span v-if="form.processing"
                            ><i class="pi pi-spin pi-spinner"></i
                        ></span>
                        <span v-else>Gabung Kelas</span>
                    </Button>
                </form>
            </div>
            <p
                v-if="form.errors.class_code"
                class="mx-auto mt-2 max-w-7xl text-right text-[12px] font-bold text-rose-500"
            >
                * {{ form.errors.class_code }}
            </p>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="mx-auto max-w-7xl">
                <div
                    v-if="classrooms && classrooms.length > 0"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="kelas in classrooms"
                        :key="kelas.id"
                        class="group flex cursor-pointer flex-col overflow-hidden rounded-2xl border border-border/40 bg-card/60 text-card-foreground shadow-sm backdrop-blur-md transition-all duration-200 hover:border-[var(--theme-primary)]/50 hover:shadow-[0_0_20px_rgba(210,255,0,0.08)]"
                    >
                        <div
                            class="relative h-24 overflow-hidden border-b border-border/40 bg-gradient-to-r from-[#0c0d26]/80 to-[#10112b] p-5"
                        >
                            <div class="absolute -top-4 -right-4 opacity-10">
                                <i
                                    class="pi pi-share-alt text-8xl text-white"
                                ></i>
                            </div>
                            <h3
                                class="relative z-10 truncate text-lg font-bold text-slate-100 transition-colors group-hover:text-[var(--theme-primary)]"
                            >
                                {{ kelas.class_name }}
                            </h3>
                            <p
                                class="relative z-10 mt-1 text-[11px] font-semibold tracking-wider text-[var(--theme-primary)] uppercase"
                            >
                                Kode: {{ kelas.class_code }}
                            </p>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <p
                                class="text-slate-350 mb-4 line-clamp-2 flex-1 text-[13px]"
                            >
                                {{
                                    stripHtml(kelas.description) ||
                                    'Tidak ada deskripsi untuk kelas ini.'
                                }}
                            </p>

                            <div class="mt-auto space-y-2">
                                <div
                                    class="flex justify-between text-[11px] font-bold text-slate-400"
                                >
                                    <span>Progress Pembelajaran</span>
                                    <span class="text-[var(--theme-primary)]"
                                        >0%</span
                                    >
                                </div>
                                <div
                                    class="h-2 w-full overflow-hidden rounded-full bg-white/10"
                                >
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff]"
                                        style="width: 0%"
                                    ></div>
                                </div>
                                <p
                                    class="pt-2 text-[11px] font-medium text-slate-500"
                                >
                                    <i class="pi pi-book mr-1"></i>
                                    {{ kelas.topics_count }} Topik Materi
                                    tersedia
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-white/5 bg-white/5 p-4">
                            <Link
                                :href="route('siswa.classes.show', kelas.id)"
                                class="flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-border/40 bg-white/5 text-[13px] font-bold text-slate-200 shadow-sm transition-all hover:bg-white/10 hover:text-white"
                            >
                                Masuk ke Kelas
                                <i class="pi pi-arrow-right text-[11px]"></i>
                            </Link>
                        </div>
                    </Card>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-border/40 bg-card/45 py-20 text-center"
                >
                    <div
                        class="mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-border/40 bg-white/5 text-slate-400"
                    >
                        <i
                            class="pi pi-folder-open text-4xl text-slate-400"
                        ></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-200">
                        Belum Ada Kelas
                    </h2>
                    <p class="mt-2 max-w-md text-[14px] text-slate-400">
                        Anda belum bergabung ke kelas mana pun. Silakan masukkan
                        6 digit kode kelas yang diberikan oleh Guru Anda pada
                        kolom di atas.
                    </p>
                </div>
            </div>
        </div>
    </main>
</template>
