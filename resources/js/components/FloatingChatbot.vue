<script setup lang="ts">
// Import ikon SVG premium dari Lucide
import axios from 'axios';
import katex from 'katex';
import { X, Bot, Send, BotIcon } from 'lucide-vue-next';
import { marked } from 'marked';
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import 'katex/dist/katex.min.css';

// Menerima judul materi untuk konteks AI dan id fase
const props = defineProps<{
    topicTitle?: string;
    phaseId?: number;
}>();

// State untuk mengatur jendela chat terbuka/tertutup
const isOpen = ref(false);

// State untuk input teks dan animasi loading AI
const newMessage = ref('');
const isTyping = ref(false);

// Referensi ke elemen kotak pesan untuk auto-scroll
const messagesContainer = ref<HTMLElement | null>(null);

// Format array: { id, sender, text }
const messages = ref<Array<any>>([]);
let pollingInterval: any = null;

/**
 * REFACTOR FINAL: Fungsi render dengan token '%%' yang kebal dari manipulasi Markdown Parser.
 * Menggunakan metode Split-Join untuk menjamin akurasi penggantian string secara global.
 */
const renderMarkdown = (text: string) => {
    if (!text) {
        return '';
    }

    const mathBlocks: string[] = [];

    // 1. Amankan Block Math ($$ rumus baris baru $$)
    let processedText = text.replace(/\$\$(.+?)\$\$/gs, (match, math) => {
        try {
            const rendered = katex.renderToString(math, { displayMode: true });
            mathBlocks.push(
                `<div class="my-3 overflow-x-auto">${rendered}</div>`,
            );

            return `%%MATH_BLOCK_TOKEN_${mathBlocks.length - 1}%%`;
        } catch {
            return match;
        }
    });

    // 2. Amankan Inline Math (Rumus kimia di dalam baris kalimat seperti $H_2O$)
    processedText = processedText.replace(/\$(.+?)\$/g, (match, math) => {
        try {
            const rendered = katex.renderToString(math, { displayMode: false });
            mathBlocks.push(rendered);

            return `%%MATH_BLOCK_TOKEN_${mathBlocks.length - 1}%%`;
        } catch {
            return match;
        }
    });

    // 3. Render teks narasi utama ke format HTML via marked
    let finalHtml = marked.parse(processedText, { breaks: true }) as string;

    // 4. Kembalikan kode HTML KaTeX yang sudah matang ke posisinya masing-masing
    mathBlocks.forEach((renderedMath, index) => {
        finalHtml = finalHtml
            .split(`%%MATH_BLOCK_TOKEN_${index}%%`)
            .join(renderedMath);
    });

    return finalHtml;
};

// Fungsi mengambil riwayat chat dari database
const fetchChats = async () => {
    try {
        const response = await axios.get(route('siswa.chatbot.index'));
        const logs = response.data;

        const newMessages: Array<any> = [
            {
                id: 'welcome',
                sender: 'ai',
                text: 'Halo! 👋 Aku adalah AI Tutor pendamping belajarmu. Ada yang bikin kamu bingung?',
            },
        ];

        let isWaiting = false;

        logs.forEach((log: any) => {
            // Masukkan pesan siswa
            newMessages.push({
                id: `user_${log.id}`,
                sender: 'user',
                text: log.prompt,
            });

            // Masukkan pesan AI (jika sudah dijawab oleh background worker)
            if (log.response) {
                newMessages.push({
                    id: `ai_${log.id}`,
                    sender: 'ai',
                    text: log.response,
                });
            } else {
                // Cek apakah chat log sudah terlalu lama (misal > 45 detik) tapi belum ada respon.
                // Jika iya, berarti job AI di backend telah gagal/limit.
                const createdTimeStr = log.created_at;
                const utcTimeStr =
                    createdTimeStr.endsWith('Z') || createdTimeStr.includes('+')
                        ? createdTimeStr
                        : createdTimeStr.replace(' ', 'T') + 'Z';

                const createdTime = new Date(utcTimeStr).getTime();
                const nowTime = new Date().getTime();
                const diffSeconds = (nowTime - createdTime) / 1000;

                if (diffSeconds > 45) {
                    newMessages.push({
                        id: `ai_failed_${log.id}`,
                        sender: 'ai',
                        text: 'Maaf, sepertinya terjadi gangguan koneksi atau batas kuota API terlampaui di server AI. Silakan kirim pesan baru atau coba sesaat lagi.',
                    });
                } else {
                    isWaiting = true; // Menandakan ada pesan yang masih antre di antrean server
                }
            }
        });

        messages.value = newMessages;
        isTyping.value = isWaiting;

        // Manajemen Polling otomatis jika masih menunggu jawaban AI
        if (isWaiting && !pollingInterval) {
            startPolling();
        } else if (!isWaiting && pollingInterval) {
            stopPolling();
        }
    } catch (error) {
        console.error('Gagal mengambil riwayat chat:', error);
    }
};

const startPolling = () => {
    if (pollingInterval) {
        return;
    }

    pollingInterval = setInterval(async () => {
        await fetchChats();
        scrollToBottom();
    }, 3000); // Sinkronisasi data tiap 3 detik
};

const stopPolling = () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
};

const toggleChat = async () => {
    isOpen.value = !isOpen.value;

    if (isOpen.value) {
        await fetchChats();
        scrollToBottom();
    }
};

const scrollToBottom = async () => {
    await nextTick();

    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isTyping.value) {
        return;
    }

    const userText = newMessage.value;
    newMessage.value = '';

    // Tampilkan langsung di layar secara instan agar terasa responsif
    messages.value.push({ id: Date.now(), sender: 'user', text: userText });
    isTyping.value = true;
    scrollToBottom();

    try {
        // Kirim post request menuju backend controller rute siswa
        await axios.post(route('siswa.chatbot.store'), {
            prompt: userText,
            topic_context: props.topicTitle || 'Materi Umum',
            phase_id: props.phaseId,
        });

        // Jalankan pemantauan latar belakang
        startPolling();
    } catch {
        isTyping.value = false;
        messages.value.push({
            id: Date.now(),
            sender: 'ai',
            text: 'Maaf, terjadi gangguan jaringan saat mengirim pesan.',
        });
        scrollToBottom();
    }
};

onMounted(() => {
    fetchChats();
});

onUnmounted(() => {
    stopPolling();
});
</script>

<template>
    <div class="fixed right-6 bottom-6 z-50 flex flex-col items-end">
        <transition
            enter-active-class="transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] origin-bottom-right"
            enter-from-class="opacity-0 translate-y-10 scale-50"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in origin-bottom-right"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-10 scale-50"
        >
            <div
                v-show="isOpen"
                class="mb-4 flex w-80 flex-col overflow-hidden rounded-3xl border border-white/10 bg-[#0d0e25]/95 shadow-2xl backdrop-blur-md sm:w-[380px]"
            >
                <div
                    class="animate-gradient relative z-10 flex items-center justify-between overflow-hidden border-b border-white/10 bg-gradient-to-r from-[#0a0f26] via-[#1a0f3c] to-[#0a0f26] p-4 shadow-md"
                >
                    <div
                        class="pointer-events-none absolute inset-0 bg-white/5 opacity-0 transition-opacity duration-700 hover:opacity-100"
                    ></div>

                    <div class="relative z-10 flex items-center gap-3">
                        <div
                            class="rounded-full bg-[#d2ff00]/10 p-2.5 shadow-inner backdrop-blur-sm transition-transform duration-300 group-hover:rotate-12"
                        >
                            <bot-icon
                                class="h-5 w-5 animate-pulse text-[#d2ff00]"
                            />
                        </div>
                        <div>
                            <h3
                                class="text-[15px] font-bold tracking-wide text-slate-100"
                            >
                                AI Tutor Pendamping
                            </h3>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <span
                                    class="h-2 w-2 animate-pulse rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"
                                ></span>
                                <span
                                    class="text-[11px] font-medium tracking-wider text-slate-400"
                                    >Online & Siap Membantu</span
                                >
                            </div>
                        </div>
                    </div>
                    <button
                        @click="toggleChat"
                        class="relative z-10 rounded-xl bg-white/5 p-2 text-slate-400 transition-all duration-300 hover:rotate-90 hover:bg-white/10 hover:text-white"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div
                    ref="messagesContainer"
                    class="flex max-h-[450px] min-h-[350px] flex-1 flex-col gap-5 overflow-y-auto scroll-smooth bg-[#08091a] p-5"
                >
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="flex w-full animate-in duration-500 ease-out fade-in slide-in-from-bottom-4"
                        :class="
                            msg.sender === 'user'
                                ? 'justify-end'
                                : 'justify-start'
                        "
                    >
                        <div
                            v-if="msg.sender === 'ai'"
                            class="flex max-w-[85%] gap-2.5"
                        >
                            <div
                                class="mt-1 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border border-[#d2ff00]/20 bg-[#d2ff00]/10 shadow-sm"
                            >
                                <Bot class="h-4 w-4 text-[#d2ff00]" />
                            </div>
                            <div
                                class="rounded-2xl rounded-tl-sm border border-white/10 bg-[#0d0e25] p-4 text-[13.5px] text-slate-200 shadow-sm transition-shadow hover:border-white/20"
                            >
                                <div
                                    v-html="renderMarkdown(msg.text)"
                                    class="prose prose-sm prose-slate prose-invert max-w-none break-words"
                                ></div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="max-w-[85%] transform rounded-2xl rounded-tr-sm bg-gradient-to-br from-[#d2ff00] to-[#00ffff] p-3.5 text-[13.5px] leading-relaxed font-semibold whitespace-pre-wrap text-[#070814] shadow-md transition-shadow duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                        >
                            {{ msg.text }}
                        </div>
                    </div>

                    <div
                        v-if="isTyping"
                        class="flex max-w-[85%] animate-in gap-2.5 duration-300 fade-in slide-in-from-bottom-2"
                    >
                        <div
                            class="mt-1 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border border-[#d2ff00]/20 bg-[#d2ff00]/10 shadow-sm"
                        >
                            <Bot class="h-4 w-4 text-[#d2ff00]" />
                        </div>
                        <div
                            class="flex items-center gap-1.5 rounded-2xl rounded-tl-sm border border-white/10 bg-[#0d0e25] px-4 py-3 shadow-sm"
                        >
                            <span
                                class="h-2 w-2 animate-bounce rounded-full bg-[#d2ff00]"
                                style="animation-delay: 0ms"
                            ></span>
                            <span
                                class="h-2 w-2 animate-bounce rounded-full bg-[#d2ff00]"
                                style="animation-delay: 150ms"
                            ></span>
                            <span
                                class="h-2 w-2 animate-bounce rounded-full bg-[#d2ff00]"
                                style="animation-delay: 300ms"
                            ></span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 bg-[#0d0e25] p-3.5">
                    <form
                        @submit.prevent="sendMessage"
                        class="group relative flex items-center gap-2"
                    >
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Ketik pertanyaanmu di sini..."
                            class="w-full rounded-2xl border border-white/10 bg-white/5 py-3.5 pr-12 pl-4 text-[13.5px] text-white placeholder-slate-500 shadow-inner transition-all duration-300 focus:border-[var(--theme-accent)] focus:bg-[#08091a]/40 focus:ring-2 focus:ring-[var(--theme-accent)]/20"
                            :disabled="isTyping"
                        />
                        <button
                            type="submit"
                            class="absolute top-1/2 right-2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl bg-gradient-to-r from-[#d2ff00] to-[#00ffff] text-[#070814] shadow-md transition-all duration-200 hover:shadow-[0_0_20px_rgba(0,255,255,0.4)] hover:brightness-110 focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-40 disabled:shadow-none"
                            :disabled="!newMessage.trim() || isTyping"
                        >
                            <Send
                                class="ml-0.5 h-4 w-4"
                                :class="{ 'animate-pulse': newMessage.trim() }"
                            />
                        </button>
                    </form>
                    <div class="mt-2.5 text-center">
                        <span
                            class="text-[9px] font-semibold tracking-wider text-slate-500"
                            >AI DAPAT MELAKUKAN KESALAHAN. PERIKSA
                            KEMBALI.</span
                        >
                    </div>
                </div>
            </div>
        </transition>

        <div class="group relative">
            <span
                v-if="!isOpen"
                class="absolute inset-0 animate-ping rounded-full bg-[#00ffff] opacity-30"
                style="animation-duration: 2s"
            ></span>

            <button
                @click="toggleChat"
                class="relative flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-tr from-[#d2ff00] to-[#00ffff] text-[#070814] shadow-[0_8px_20px_rgba(0,255,255,0.25)] transition-all duration-200 hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(0,255,255,0.4)] focus-visible:ring-2 focus-visible:ring-[#d2ff00] focus-visible:ring-offset-2 focus-visible:ring-offset-[#08091a] focus-visible:outline-none active:scale-[0.97] active:brightness-95"
            >
                <X
                    v-show="isOpen"
                    class="h-6 w-6 rotate-90 transition-transform duration-500"
                />
                <bot-icon
                    v-show="!isOpen"
                    class="h-6 w-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12"
                />

                <span
                    v-if="!isOpen"
                    class="absolute top-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-slate-900 bg-rose-500"
                ></span>
            </button>
        </div>
    </div>
</template>

<style scoped>
@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}
.animate-gradient {
    animation: gradient 6s ease infinite;
}

/* Merapikan margin pembungkus bawaan parser markdown agar simetris di balon chat */
:deep(.prose p) {
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
    line-height: 1.6;
}
:deep(.prose p:last-child) {
    margin-bottom: 0 !important;
}

/* Memastikan output KaTeX tidak merusak layout bubble chat */
:deep(.katex-display) {
    margin: 0.5em 0 !important;
    overflow-x: auto;
    overflow-y: hidden;
}
</style>
