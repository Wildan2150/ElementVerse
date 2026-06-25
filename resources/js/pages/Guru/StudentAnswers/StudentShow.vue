<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

const props = defineProps<{
    classroom: any;
    student: any;
    topics: any[];
    answers: any[];
    isEvaluationSent: boolean;
    isEvaluationFinished: boolean;
}>();

const activeTopicId = ref<number | null>(
    props.topics.length > 0 ? props.topics[0].id : null,
);

const activeTopic = computed(() => {
    return props.topics.find((t) => t.id === activeTopicId.value) || null;
});

// Helper untuk mendapatkan jawaban per fase
const getPhaseAnswers = (phaseId: number) => {
    return props.answers.filter((a) => a.phase_id === phaseId);
};

// Mengecek kebenaran auto-grading untuk PG dan PG Kompleks
const checkAutoGrade = (answer: any) => {
    const content = answer.content;
    const correctAnswers = content.correct_answers || [];
    const studentAns = answer.answer_data;

    if (content.type === 'eval_mcq') {
        // PG biasa (1 jawaban), studentAns bisa berupa string/int, correctAnswers adalah array of index
        // asumsi studentAns adalah index dari radio button
        return correctAnswers.includes(String(studentAns));
    } else if (content.type === 'eval_cmcq') {
        // PG Kompleks (multiple jawaban), studentAns adalah array
        if (!Array.isArray(studentAns)) {
            return false;
        }

        // Memastikan semua correct answers ada di student answers, dan jumlahnya sama
        const isSameLength = studentAns.length === correctAnswers.length;
        const hasAllCorrect = correctAnswers.every((c: any) =>
            studentAns.map(String).includes(String(c)),
        );

        return isSameLength && hasAllCorrect;
    }

    return null;
};

// Menilai soal uraian
const evaluateAnswer = (
    answerId: number,
    evaluation: 'benar' | 'setengah_benar' | 'salah',
) => {
    router.post(
        route('guru.answers.evaluate', answerId),
        {
            evaluation,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Berhasil', {
                    description: 'Penilaian berhasil disimpan.',
                });
            },
            onError: () => {
                toast.error('Gagal', {
                    description: 'Terjadi kesalahan saat menyimpan penilaian.',
                });
            },
        },
    );
};

const finishEvaluation = () => {
    router.post(
        route('guru.classes.students.finish-evaluation', {
            classroom: props.classroom.id,
            student: props.student.id,
        }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Berhasil', {
                    description: 'Evaluasi telah ditandai selesai.',
                });
            },
            onError: () => {
                toast.error('Gagal', {
                    description:
                        'Terjadi kesalahan saat menyelesaikan evaluasi.',
                });
            },
        },
    );
};

const editEvaluation = () => {
    router.post(
        route('guru.classes.students.edit-evaluation', {
            classroom: props.classroom.id,
            student: props.student.id,
        }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Berhasil', {
                    description:
                        'Kunci evaluasi dibuka. Anda dapat mengedit kembali.',
                });
            },
            onError: () => {
                toast.error('Gagal', {
                    description:
                        'Terjadi kesalahan saat membuka kunci evaluasi.',
                });
            },
        },
    );
};

const sendEvaluation = () => {
    router.post(
        route('guru.classes.students.send-evaluation', {
            classroom: props.classroom.id,
            student: props.student.id,
        }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Berhasil Dikirim', {
                    description: 'Hasil evaluasi berhasil dikirimkan ke siswa.',
                });
            },
            onError: () => {
                toast.error('Gagal', {
                    description: 'Terjadi kesalahan saat mengirimkan evaluasi.',
                });
            },
        },
    );
};

// Hitung progress
const totalEvaluated = computed(() => {
    return props.answers.filter((a) => {
        if (['eval_mcq', 'eval_cmcq'].includes(a.content.type)) {
            return true;
        }

        return a.evaluation !== null;
    }).length;
});
const totalQuestions = computed(() => props.answers.length);
const progressPercent = computed(() =>
    totalQuestions.value > 0
        ? Math.round((totalEvaluated.value / totalQuestions.value) * 100)
        : 0,
);

const isImage = (url: string | null) => {
    if (!url) {
        return false;
    }

    return /\.(jpeg|jpg|gif|png|webp)/i.test(url);
};
</script>

<template>
    <Head :title="`Evaluasi Siswa: ${student.name}`" />

    <div
        class="min-h-screen bg-transparent px-4 py-6 font-sans md:px-8 lg:px-10"
    >
        <div class="mx-auto max-w-5xl">
            <!-- Header -->
            <div
                class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
            >
                <div>
                    <div
                        class="mb-3 flex items-center gap-2 text-[12px] font-bold text-slate-400"
                    >
                        <Link
                            :href="
                                route('guru.classes.show', {
                                    class: classroom.id,
                                    tab: 'siswa',
                                })
                            "
                            class="transition-colors hover:text-[var(--theme-primary)]"
                            >Kelas {{ classroom.class_name }}</Link
                        >
                        <i
                            class="pi pi-chevron-right text-[8px] text-slate-600"
                        ></i>
                        <span class="text-[var(--theme-primary)]"
                            >Evaluasi Siswa</span
                        >
                    </div>
                    <h1
                        class="flex items-center gap-3 text-[24px] font-extrabold text-slate-100"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-indigo-500/20 bg-indigo-500/10 text-[14px] font-bold text-indigo-400 uppercase"
                        >
                            {{ student.name.substring(0, 2) }}
                        </div>
                        {{ student.name }}
                    </h1>
                    <p class="mt-1 text-[13px] text-slate-400">
                        {{ student.email }}
                    </p>
                </div>

                <div
                    class="flex flex-col items-start gap-3 md:items-end md:gap-1.5"
                >
                    <div
                        class="flex w-full flex-wrap items-center justify-start gap-3 md:justify-end"
                    >
                        <!-- Status Badge -->
                        <div class="mr-1 flex items-center gap-2">
                            <span
                                class="text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                                >Status:</span
                            >
                            <span
                                v-if="!isEvaluationFinished"
                                class="inline-flex items-center gap-1 rounded-md border border-amber-500/20 bg-amber-500/10 px-2.5 py-1 text-[12px] font-bold text-amber-400"
                            >
                                <span
                                    class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"
                                ></span>
                                Sedang Dinilai
                            </span>
                            <span
                                v-else-if="
                                    isEvaluationFinished && !isEvaluationSent
                                "
                                class="inline-flex items-center gap-1 rounded-md border border-sky-500/20 bg-sky-500/10 px-2.5 py-1 text-[12px] font-bold text-sky-400"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-sky-500"
                                ></span>
                                Belum Dikirim
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[12px] font-bold text-emerald-400"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                ></span>
                                Telah Dikirim
                            </span>
                        </div>

                        <!-- Tombol Selesai / Edit Evaluasi -->
                        <Button
                            v-if="!isEvaluationFinished"
                            @click="finishEvaluation"
                            :disabled="progressPercent < 100"
                            class="rounded-xl bg-indigo-600 px-4 py-2.5 font-bold text-white transition-all hover:bg-indigo-700 disabled:border disabled:border-white/10 disabled:bg-white/5 disabled:text-slate-500"
                        >
                            <i class="pi pi-lock mr-2 text-[12px]"></i> Selesai
                            Evaluasi
                        </Button>

                        <Button
                            v-else
                            @click="editEvaluation"
                            class="text-rose-455 hover:text-rose-350 rounded-xl border border-rose-500/25 bg-white/5 px-4 py-2.5 font-bold transition-all hover:bg-rose-500/10"
                        >
                            <i class="pi pi-unlock mr-2 text-[12px]"></i> Edit
                            Evaluasi
                        </Button>

                        <!-- Tombol Kirim / Kirim Ulang Hasil -->
                        <Button
                            @click="sendEvaluation"
                            :disabled="!isEvaluationFinished"
                            class="rounded-xl border-none px-4 py-2.5 font-bold shadow-sm transition-all"
                            :class="[
                                !isEvaluationFinished
                                    ? 'cursor-not-allowed border border-white/10 bg-white/5 text-slate-500 opacity-70'
                                    : isEvaluationSent
                                      ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                      : 'bg-indigo-600 text-white hover:bg-indigo-700',
                            ]"
                        >
                            <i
                                :class="[
                                    'pi mr-2',
                                    isEvaluationSent ? 'pi-sync' : 'pi-send',
                                ]"
                            ></i>
                            {{
                                isEvaluationSent
                                    ? 'Perbarui & Kirim Ulang'
                                    : 'Kirim Hasil ke Siswa'
                            }}
                        </Button>

                        <!-- Tombol Cetak Evaluasi (PDF) -->
                        <a
                            :href="
                                route('guru.classes.students.print', {
                                    classroom: classroom.id,
                                    student: student.id,
                                })
                            "
                            target="_blank"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-bold text-slate-200 shadow-sm transition-all hover:bg-white/10 hover:text-white"
                        >
                            <i
                                class="pi pi-print mr-2 text-[12px] text-slate-400"
                            ></i>
                            Cetak Evaluasi (PDF)
                        </a>
                    </div>

                    <div
                        class="text-slate-455 max-w-[350px] text-left text-[11px] font-medium md:text-right"
                    >
                        <p
                            v-if="
                                progressPercent < 100 && !isEvaluationFinished
                            "
                        >
                            Selesaikan evaluasi seluruh soal ({{
                                progressPercent
                            }}%) terlebih dahulu.
                        </p>
                        <p v-else-if="!isEvaluationFinished">
                            Semua soal telah dinilai. Klik "Selesai Evaluasi"
                            untuk mengunci & mengirim hasil.
                        </p>
                        <p
                            v-else-if="
                                isEvaluationFinished && !isEvaluationSent
                            "
                        >
                            Hasil evaluasi telah dikunci. Anda sekarang dapat
                            mengirimkannya ke siswa.
                        </p>
                        <p v-else>
                            Hasil telah terkirim. Klik "Edit" untuk mengubah
                            kembali, atau "Kirim Ulang" untuk memperbarui.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Progress Box -->
            <Card
                class="mb-8 border-border/40 bg-card/60 p-5 text-card-foreground backdrop-blur-md"
            >
                <div class="mb-2 flex items-end justify-between">
                    <div>
                        <h3 class="text-[14px] font-bold text-slate-200">
                            Progress Evaluasi Keseluruhan
                        </h3>
                        <p class="text-[12px] text-slate-400">
                            {{ totalEvaluated }} dari {{ totalQuestions }} soal
                            telah dievaluasi
                        </p>
                    </div>
                    <span
                        class="text-[20px] font-extrabold text-[var(--theme-primary)]"
                        >{{ progressPercent }}%</span
                    >
                </div>
                <div
                    class="h-2 w-full overflow-hidden rounded-full bg-white/10"
                >
                    <div
                        class="h-full bg-gradient-to-r from-[#d2ff00] to-[#00ffff] transition-all duration-500"
                        :style="{ width: progressPercent + '%' }"
                    ></div>
                </div>
            </Card>

            <!-- Pemilihan Topik -->
            <div v-if="topics.length > 0" class="mb-8 flex flex-wrap gap-3">
                <button
                    v-for="topic in topics"
                    :key="topic.id"
                    @click="activeTopicId = topic.id"
                    :class="[
                        'rounded-full px-5 py-2.5 text-[14px] font-bold shadow-sm transition-all',
                        activeTopicId === topic.id
                            ? 'border-transparent bg-indigo-600 text-white ring-2 ring-indigo-600 ring-offset-2'
                            : 'text-slate-250 border border-white/10 bg-white/5 hover:bg-white/10 hover:text-white',
                    ]"
                >
                    {{ topic.title }}
                </button>
            </div>

            <!-- Konten Evaluasi (Berdasarkan Topik yang Dipilih) -->
            <div v-if="activeTopic" class="flex flex-col gap-10">
                <div
                    v-for="phase in activeTopic.phases"
                    :key="phase.id"
                    class="relative overflow-hidden rounded-2xl border border-border/40 bg-card/60 p-4 shadow-sm backdrop-blur-md md:p-8"
                >
                    <div
                        class="absolute top-0 left-0 h-1 w-full bg-[var(--theme-primary)]"
                    ></div>

                    <!-- Judul Fase -->
                    <div class="mb-8 border-b border-white/5 pb-4 text-center">
                        <h2 class="text-[20px] font-extrabold text-slate-100">
                            {{ phase.name }}
                        </h2>
                    </div>

                    <div
                        v-if="getPhaseAnswers(phase.id).length === 0"
                        class="rounded-xl border border-dashed border-white/10 bg-white/5 py-10 text-center"
                    >
                        <i
                            class="pi pi-file-excel mb-3 block text-3xl text-slate-500"
                        ></i>
                        <p class="text-[14px] font-medium text-slate-400">
                            Siswa belum menjawab soal di fase ini.
                        </p>
                    </div>

                    <div v-else class="flex flex-col gap-5">
                        <Card
                            v-for="(answer, index) in getPhaseAnswers(phase.id)"
                            :key="answer.id"
                            class="border-border/40 bg-white/5 p-4 text-slate-100 shadow-sm transition-shadow hover:shadow-md md:p-5"
                        >
                            <!-- Pertanyaan -->
                            <div class="mb-4">
                                <span
                                    class="text-slate-450 mb-2 block text-[11px] font-bold tracking-wider uppercase"
                                    >Pertanyaan {{ index + 1 }}</span
                                >
                                <div
                                    class="text-[14px] font-medium text-slate-200"
                                    v-html="
                                        answer.content.content_data?.question ||
                                        answer.content.content_data?.label ||
                                        answer.content.content_data?.content
                                    "
                                ></div>
                            </div>

                            <!-- Jawaban PG/PG Kompleks -->
                            <div
                                v-if="
                                    ['eval_mcq', 'eval_cmcq'].includes(
                                        answer.content.type,
                                    )
                                "
                                class="mb-4 rounded-lg border border-white/5 bg-slate-950/80 p-4"
                            >
                                <div
                                    class="mb-2 text-[12px] font-bold text-slate-400"
                                >
                                    Jawaban Siswa:
                                </div>
                                <ul
                                    class="text-slate-350 list-disc pl-5 text-[13px]"
                                >
                                    <template
                                        v-if="
                                            answer.content.type === 'eval_mcq'
                                        "
                                    >
                                        <li>
                                            {{
                                                Array.isArray(
                                                    answer.content.content_data
                                                        ?.options,
                                                )
                                                    ? answer.content
                                                          .content_data.options[
                                                          answer.answer_data
                                                      ]
                                                    : answer.answer_data
                                            }}
                                        </li>
                                    </template>
                                    <template v-else>
                                        <li
                                            v-for="ans in Array.isArray(
                                                answer.answer_data,
                                            )
                                                ? answer.answer_data
                                                : []"
                                            :key="ans"
                                        >
                                            {{
                                                Array.isArray(
                                                    answer.content.content_data
                                                        ?.options,
                                                )
                                                    ? answer.content
                                                          .content_data.options[
                                                          ans
                                                      ]
                                                    : ans
                                            }}
                                        </li>
                                    </template>
                                </ul>

                                <div
                                    class="mt-4 flex items-center justify-between border-t border-white/5 pt-3"
                                >
                                    <span
                                        class="text-[12px] font-bold text-slate-400"
                                        >Auto-grading:</span
                                    >
                                    <span
                                        v-if="checkAutoGrade(answer)"
                                        class="inline-flex items-center gap-1.5 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[12px] font-bold text-emerald-400"
                                    >
                                        <i class="pi pi-check-circle"></i> Benar
                                    </span>
                                    <span
                                        v-else
                                        class="text-rose-455 inline-flex items-center gap-1.5 rounded-md border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-[12px] font-bold"
                                    >
                                        <i class="pi pi-times-circle"></i> Salah
                                    </span>
                                </div>
                            </div>

                            <!-- Jawaban Upload File -->
                            <div
                                v-else-if="answer.content.type === 'eval_file'"
                                class="mb-4 rounded-lg border border-white/5 bg-slate-950/80 p-4"
                            >
                                <div
                                    class="mb-2 text-[12px] font-bold text-slate-400"
                                >
                                    Jawaban Siswa (File/Gambar):
                                </div>
                                <div
                                    v-if="isImage(answer.answer_data)"
                                    class="mt-2"
                                >
                                    <img
                                        :src="answer.answer_data"
                                        alt="Uploaded Image"
                                        class="mb-2 max-h-64 rounded-lg border border-white/10 shadow-sm"
                                    />
                                    <a
                                        :href="answer.answer_data"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-400 hover:text-indigo-300"
                                    >
                                        <i class="pi pi-external-link"></i>
                                        Lihat Gambar Ukuran Penuh
                                    </a>
                                </div>
                                <div
                                    v-else-if="answer.answer_data"
                                    class="mt-2 flex items-center gap-2"
                                >
                                    <i
                                        class="pi pi-file text-lg text-slate-500"
                                    ></i>
                                    <a
                                        :href="answer.answer_data"
                                        target="_blank"
                                        class="font-bold text-indigo-400 hover:underline"
                                    >
                                        Lihat File Terunggah
                                    </a>
                                </div>
                                <div
                                    v-else
                                    class="text-[14px] text-slate-500 italic"
                                >
                                    Siswa belum mengunggah file.
                                </div>
                            </div>

                            <!-- Jawaban Uraian -->
                            <div
                                v-else
                                class="mb-4 rounded-lg border border-white/5 bg-slate-950/80 p-4"
                            >
                                <div
                                    class="mb-2 text-[12px] font-bold text-slate-400"
                                >
                                    Jawaban Siswa:
                                </div>
                                <div
                                    v-if="
                                        answer.content.type === 'eval_essay' ||
                                        answer.content.type === 'input_text'
                                    "
                                    class="rich-text-content prose prose-invert prose-sm text-[14px] break-words text-slate-200"
                                    v-html="answer.answer_data || ''"
                                ></div>
                                <div
                                    v-else
                                    class="text-[14px] break-words whitespace-pre-wrap text-slate-200"
                                >
                                    {{ answer.answer_data }}
                                </div>
                            </div>

                            <!-- Form Evaluasi Manual (Hanya untuk uraian) -->
                            <div
                                v-if="
                                    !['eval_mcq', 'eval_cmcq'].includes(
                                        answer.content.type,
                                    )
                                "
                                class="flex flex-wrap items-center gap-2.5 pt-2"
                            >
                                <span
                                    class="mr-2 text-[12px] font-bold text-slate-400"
                                    >Evaluasi:</span
                                >
                                <button
                                    @click="evaluateAnswer(answer.id, 'benar')"
                                    :disabled="isEvaluationFinished"
                                    :class="[
                                        'rounded-lg border px-3 py-1.5 text-[12px] font-bold transition-all',
                                        isEvaluationFinished
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        answer.evaluation === 'benar'
                                            ? 'border-emerald-700 bg-emerald-600 text-white shadow-sm'
                                            : 'border-emerald-500/20 bg-white/5 text-emerald-400 hover:bg-emerald-500/10',
                                    ]"
                                >
                                    <i class="pi pi-check mr-1 text-[10px]"></i>
                                    Benar
                                </button>
                                <button
                                    @click="
                                        evaluateAnswer(
                                            answer.id,
                                            'setengah_benar',
                                        )
                                    "
                                    :disabled="isEvaluationFinished"
                                    :class="[
                                        'rounded-lg border px-3 py-1.5 text-[12px] font-bold transition-all',
                                        isEvaluationFinished
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        answer.evaluation === 'setengah_benar'
                                            ? 'border-amber-700 bg-amber-600 text-white shadow-sm'
                                            : 'border-amber-500/20 bg-white/5 text-amber-400 hover:bg-amber-500/10',
                                    ]"
                                >
                                    <i class="pi pi-minus mr-1 text-[10px]"></i>
                                    Setengah Benar
                                </button>
                                <button
                                    @click="evaluateAnswer(answer.id, 'salah')"
                                    :disabled="isEvaluationFinished"
                                    :class="[
                                        'rounded-lg border px-3 py-1.5 text-[12px] font-bold transition-all',
                                        isEvaluationFinished
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        answer.evaluation === 'salah'
                                            ? 'border-rose-700 bg-rose-600 text-white shadow-sm'
                                            : 'text-rose-455 border-rose-500/20 bg-white/5 hover:bg-rose-500/10',
                                    ]"
                                >
                                    <i class="pi pi-times mr-1 text-[10px]"></i>
                                    Salah
                                </button>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

export default {
    layout: AppLayout,
};
</script>
