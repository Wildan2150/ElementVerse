<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import katex from 'katex';
import { marked } from 'marked';
import { Card } from '@/components/ui/card';
import 'katex/dist/katex.min.css';

const props = defineProps<{
    classroom: {
        id: number;
        class_name: string;
        class_code: string;
        description: string | null;
        teacher: {
            name: string;
        };
    };
    topics: Array<{
        id: number;
        title: string;
        phases: Array<{ id: number; name: string }>;
    }>;
    answers: Array<{
        id: number;
        phase_id: number;
        answer_data: any;
        evaluation: 'benar' | 'setengah_benar' | 'salah' | null;
        content: {
            id: number;
            type: string;
            content_data: any;
            correct_answers?: any[];
        };
    }>;
    isEvaluationSent: boolean;
}>();

const renderMarkdown = (text: string | null) => {
    if (!text) {
        return '';
    }

    const mathBlocks: string[] = [];
    let processedText = text.replace(/\$\$(.+?)\$\$/gs, (match, math) => {
        try {
            const rendered = katex.renderToString(math, { displayMode: true });
            mathBlocks.push(
                `<div class="my-3 overflow-x-auto">${rendered}</div>`,
            );

            return `%%MATH_BLOCK_TOKEN_${mathBlocks.length - 1}%%`;
        } catch (e) {
            return match;
        }
    });
    processedText = processedText.replace(/\$(.+?)\$/g, (match, math) => {
        try {
            const rendered = katex.renderToString(math, { displayMode: false });
            mathBlocks.push(rendered);

            return `%%MATH_BLOCK_TOKEN_${mathBlocks.length - 1}%%`;
        } catch (e) {
            return match;
        }
    });
    let finalHtml = marked.parse(processedText, { breaks: true }) as string;
    mathBlocks.forEach((renderedMath, index) => {
        finalHtml = finalHtml
            .split(`%%MATH_BLOCK_TOKEN_${index}%%`)
            .join(renderedMath);
    });

    return finalHtml;
};

// Fungsi helper untuk status warna evaluasi
const getEvaluationColor = (evaluation: string | null) => {
    switch (evaluation) {
        case 'benar':
            return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
        case 'setengah_benar':
            return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
        case 'salah':
            return 'bg-rose-500/10 text-rose-455 border-rose-500/20';
        default:
            return 'bg-white/5 text-slate-400 border-white/5';
    }
};

const getEvaluationLabel = (evaluation: string | null) => {
    switch (evaluation) {
        case 'benar':
            return 'Jawaban Benar';
        case 'setengah_benar':
            return 'Kurang Tepat';
        case 'salah':
            return 'Jawaban Salah';
        default:
            return 'Belum Dinilai';
    }
};

// Filter answers per phase
const getPhaseAnswers = (phaseId: number) => {
    return props.answers.filter((a) => a.phase_id === phaseId);
};

// Mengecek kebenaran auto-grading untuk PG dan PG Kompleks
const checkAutoGrade = (answer: any) => {
    const content = answer.content;

    if (!content) {
        return null;
    }

    const correctAnswers = content.correct_answers || [];
    const studentAns = answer.answer_data;

    if (content.type === 'eval_mcq') {
        return correctAnswers.includes(String(studentAns));
    } else if (content.type === 'eval_cmcq') {
        if (!Array.isArray(studentAns)) {
            return false;
        }

        const isSameLength = studentAns.length === correctAnswers.length;
        const hasAllCorrect = correctAnswers.every((c: any) =>
            studentAns.map(String).includes(String(c)),
        );

        return isSameLength && hasAllCorrect;
    }

    return null;
};

const isImage = (url: string | null) => {
    if (!url) {
        return false;
    }

    return /\.(jpeg|jpg|gif|png|webp)/i.test(url);
};
</script>

<template>
    <Head :title="classroom.class_name + ' - Hasil Penilaian'" />

    <main
        class="relative flex min-h-screen w-full flex-1 flex-col bg-transparent font-sans"
    >
        <div
            class="relative overflow-hidden border-b border-white/5 bg-gradient-to-r from-[#070814]/90 to-[#0e1026]/90 px-4 py-8 md:px-8 md:py-12"
        >
            <div
                class="absolute inset-0 opacity-10"
                style="
                    background-image: radial-gradient(
                        circle at 2px 2px,
                        white 1px,
                        transparent 0
                    );
                    background-size: 32px 32px;
                "
            ></div>
            <div
                class="relative z-10 mx-auto flex max-w-5xl flex-col justify-between gap-6 md:flex-row md:items-end"
            >
                <div class="text-white">
                    <Link
                        :href="route('siswa.classes.index')"
                        class="mb-4 flex items-center gap-2 text-[13px] font-bold text-[var(--theme-primary)] transition-colors hover:text-white"
                    >
                        <i class="pi pi-arrow-left"></i> Kembali ke Kelas Saya
                    </Link>
                    <div
                        class="mb-3 inline-block rounded-full border border-[var(--theme-primary)]/20 bg-[var(--theme-primary)]/10 px-3 py-1 text-[11px] font-bold tracking-wider text-[var(--theme-primary)] uppercase"
                    >
                        KODE: {{ classroom.class_code }}
                    </div>
                    <h1
                        class="mb-2 text-3xl font-black tracking-tight text-slate-100 md:text-4xl"
                    >
                        {{ classroom.class_name }}
                    </h1>
                    <div
                        v-if="classroom.description"
                        v-html="classroom.description"
                        class="text-slate-355 rich-text-content max-w-2xl text-[14px] leading-relaxed"
                    ></div>
                    <p
                        v-else
                        class="max-w-2xl text-[14px] leading-relaxed text-slate-400"
                    >
                        Selamat datang di kelas ini. Mari belajar kimia dengan
                        pendekatan POE!
                    </p>
                </div>
                <div
                    class="flex min-w-[200px] items-center gap-4 rounded-2xl border border-border/40 bg-card/60 p-4 text-white backdrop-blur-md"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full border border-border/40 bg-[var(--theme-primary)] text-lg font-bold text-[#070814]"
                    >
                        {{ classroom.teacher.name.charAt(0) }}
                    </div>
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                        >
                            Pengajar
                        </p>
                        <p class="text-[14px] font-bold text-slate-200">
                            {{ classroom.teacher.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex border-b border-white/5 bg-[#0b0c20]/60 px-4 backdrop-blur-md md:px-8"
        >
            <div class="mx-auto flex w-full max-w-5xl gap-6">
                <Link
                    :href="route('siswa.classes.show', classroom.id)"
                    class="relative pt-4 pb-3 text-[14px] font-bold text-slate-400 transition-colors hover:text-slate-200"
                >
                    <i class="pi pi-book mr-1.5 text-[12px]"></i> Modul Materi
                </Link>
                <Link
                    :href="
                        route('siswa.classes.evaluation-result', classroom.id)
                    "
                    class="relative pt-4 pb-3 text-[14px] font-bold text-[var(--theme-primary)]"
                >
                    <i class="pi pi-chart-line mr-1.5 text-[12px]"></i> Hasil
                    Penilaian
                    <div
                        class="absolute bottom-0 left-0 h-0.5 w-full rounded-t-full bg-[var(--theme-primary)]"
                    ></div>
                </Link>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <div v-if="!isEvaluationSent">
                    <div
                        class="rounded-3xl border border-dashed border-border/40 bg-card/60 py-16 text-center shadow-sm backdrop-blur-md"
                    >
                        <div
                            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-border/40 bg-white/5 text-[var(--theme-primary)]"
                        >
                            <i class="pi pi-lock text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-200">
                            Hasil Belum Tersedia
                        </h3>
                        <p class="text-slate-455 mt-1 text-[13px]">
                            Guru belum mengirimkan hasil penilaian evaluasi
                            untuk Anda. Harap tunggu info selanjutnya dari guru
                            Anda.
                        </p>
                    </div>
                </div>

                <div v-else>
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-extrabold text-slate-100">
                            Hasil Evaluasi Guru
                        </h2>
                    </div>

                    <div v-if="topics.length > 0" class="space-y-6">
                        <Card
                            v-for="(topic, index) in topics"
                            :key="topic.id"
                            class="flex flex-col gap-5 rounded-2xl border-border/40 bg-card/60 p-6 shadow-sm backdrop-blur-md transition-all"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-border/40 bg-white/5 text-xl font-black text-[var(--theme-primary)]"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-bold text-slate-100"
                                    >
                                        {{ topic.title }}
                                    </h3>
                                    <p class="text-slate-455 mt-1 text-[13px]">
                                        Berikut adalah hasil pengerjaan Anda
                                        untuk topik ini.
                                    </p>
                                </div>
                            </div>

                            <div class="h-px w-full bg-white/5"></div>

                            <div class="space-y-6">
                                <template
                                    v-for="phase in topic.phases"
                                    :key="phase.id"
                                >
                                    <div
                                        v-if="
                                            getPhaseAnswers(phase.id).length > 0
                                        "
                                        class="rounded-xl border border-border/40 bg-slate-950/40 p-5"
                                    >
                                        <!-- Judul Fase -->
                                        <div
                                            class="mb-6 border-b border-white/5 pb-4 text-center"
                                        >
                                            <h4
                                                class="text-[16px] font-extrabold text-slate-100"
                                            >
                                                {{ phase.name }}
                                            </h4>
                                        </div>

                                        <!-- Jawaban di fase ini -->
                                        <div class="space-y-4">
                                            <div
                                                v-for="answer in getPhaseAnswers(
                                                    phase.id,
                                                )"
                                                :key="answer.id"
                                                class="relative overflow-hidden rounded-xl border border-border/40 bg-card/60 p-5 shadow-sm backdrop-blur-md"
                                            >
                                                <!-- Evaluasi Badge (Auto-grade / Manual) -->
                                                <div
                                                    class="z-10 mb-3 sm:absolute sm:top-5 sm:right-5 sm:mb-0"
                                                >
                                                    <template
                                                        v-if="
                                                            [
                                                                'eval_mcq',
                                                                'eval_cmcq',
                                                            ].includes(
                                                                answer.content
                                                                    .type,
                                                            )
                                                        "
                                                    >
                                                        <span
                                                            v-if="
                                                                checkAutoGrade(
                                                                    answer,
                                                                )
                                                            "
                                                            class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-[11px] font-bold text-emerald-400"
                                                        >
                                                            <i
                                                                class="pi pi-check-circle"
                                                            ></i>
                                                            Jawaban Benar
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="text-rose-455 inline-flex items-center gap-1 rounded-full border border-rose-500/20 bg-rose-500/10 px-3 py-1 text-[11px] font-bold"
                                                        >
                                                            <i
                                                                class="pi pi-times-circle"
                                                            ></i>
                                                            Jawaban Salah
                                                        </span>
                                                    </template>
                                                    <template
                                                        v-else-if="
                                                            answer.evaluation
                                                        "
                                                    >
                                                        <span
                                                            class="rounded-full border px-3 py-1 text-[11px] font-bold"
                                                            :class="
                                                                getEvaluationColor(
                                                                    answer.evaluation,
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                getEvaluationLabel(
                                                                    answer.evaluation,
                                                                )
                                                            }}
                                                        </span>
                                                    </template>
                                                </div>

                                                <div class="pr-0 sm:pr-36">
                                                    <!-- Pertanyaan/Konten -->
                                                    <div class="mb-4">
                                                        <h5
                                                            class="mb-1 text-[11px] font-bold tracking-wider text-[var(--theme-primary)] uppercase"
                                                        >
                                                            Pertanyaan / Soal
                                                        </h5>
                                                        <div
                                                            class="text-[14px] leading-relaxed font-medium text-slate-200"
                                                            v-html="
                                                                renderMarkdown(
                                                                    answer
                                                                        .content
                                                                        .content_data
                                                                        ?.question ||
                                                                        answer
                                                                            .content
                                                                            .content_data
                                                                            ?.label ||
                                                                        answer
                                                                            .content
                                                                            .content_data
                                                                            ?.content ||
                                                                        '',
                                                                )
                                                            "
                                                        ></div>
                                                    </div>

                                                    <div
                                                        class="my-4 h-px w-full bg-white/5"
                                                    ></div>

                                                    <!-- Jawaban Siswa -->
                                                    <div>
                                                        <h5
                                                            class="text-slate-455 mb-1 text-[11px] font-bold tracking-wider uppercase"
                                                        >
                                                            Jawaban Anda
                                                        </h5>
                                                        <div
                                                            class="rounded-xl border border-border/40 bg-white/5 p-4 text-[14px] leading-relaxed text-slate-300"
                                                        >
                                                            <template
                                                                v-if="
                                                                    answer
                                                                        .content
                                                                        .type ===
                                                                    'eval_mcq'
                                                                "
                                                            >
                                                                {{
                                                                    Array.isArray(
                                                                        answer
                                                                            .content
                                                                            .content_data
                                                                            ?.options,
                                                                    )
                                                                        ? answer
                                                                              .content
                                                                              .content_data
                                                                              .options[
                                                                              answer
                                                                                  .answer_data
                                                                          ]
                                                                        : answer.answer_data
                                                                }}
                                                            </template>
                                                            <template
                                                                v-else-if="
                                                                    answer
                                                                        .content
                                                                        .type ===
                                                                    'eval_cmcq'
                                                                "
                                                            >
                                                                <ul
                                                                    class="list-disc pl-5"
                                                                >
                                                                    <li
                                                                        v-for="ans in Array.isArray(
                                                                            answer.answer_data,
                                                                        )
                                                                            ? answer.answer_data
                                                                            : []"
                                                                        :key="
                                                                            ans
                                                                        "
                                                                    >
                                                                        {{
                                                                            Array.isArray(
                                                                                answer
                                                                                    .content
                                                                                    .content_data
                                                                                    ?.options,
                                                                            )
                                                                                ? answer
                                                                                      .content
                                                                                      .content_data
                                                                                      .options[
                                                                                      ans
                                                                                  ]
                                                                                : ans
                                                                        }}
                                                                    </li>
                                                                </ul>
                                                            </template>
                                                            <template
                                                                v-else-if="
                                                                    answer
                                                                        .content
                                                                        .type ===
                                                                    'eval_file'
                                                                "
                                                            >
                                                                <div
                                                                    v-if="
                                                                        isImage(
                                                                            answer.answer_data,
                                                                        )
                                                                    "
                                                                    class="mt-2"
                                                                >
                                                                    <img
                                                                        :src="
                                                                            answer.answer_data
                                                                        "
                                                                        alt="Uploaded Image"
                                                                        class="mb-2 max-h-48 rounded-lg border border-border/40 shadow-sm"
                                                                    />
                                                                    <a
                                                                        :href="
                                                                            answer.answer_data
                                                                        "
                                                                        target="_blank"
                                                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--theme-primary)] hover:brightness-110"
                                                                    >
                                                                        <i
                                                                            class="pi pi-external-link"
                                                                        ></i>
                                                                        Lihat
                                                                        Gambar
                                                                        Ukuran
                                                                        Penuh
                                                                    </a>
                                                                </div>
                                                                <div
                                                                    v-else
                                                                    class="flex items-center gap-2"
                                                                >
                                                                    <i
                                                                        class="pi pi-file text-lg text-slate-400"
                                                                    ></i>
                                                                    <a
                                                                        :href="
                                                                            answer.answer_data
                                                                        "
                                                                        target="_blank"
                                                                        class="font-bold text-[var(--theme-primary)] hover:underline"
                                                                    >
                                                                        Lihat
                                                                        File
                                                                        Terunggah
                                                                    </a>
                                                                </div>
                                                            </template>
                                                            <template v-else>
                                                                <div
                                                                    class="rich-text-content break-words"
                                                                    v-html="
                                                                        answer.answer_data
                                                                    "
                                                                ></div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </Card>
                    </div>

                    <div
                        v-else
                        class="rounded-3xl border border-dashed border-border/40 bg-card/60 py-16 text-center shadow-sm backdrop-blur-md"
                    >
                        <div
                            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-border/40 bg-white/5 text-[var(--theme-primary)]"
                        >
                            <i class="pi pi-inbox text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-200">
                            Belum Ada Data Topik
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
