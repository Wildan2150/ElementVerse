<script setup lang="ts">
import { QuillEditor, loadQuill } from '@vueup/vue-quill';
import { ref, computed, onBeforeMount, onBeforeUnmount } from 'vue';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
    variant?: 'full' | 'student';
    disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

const quillReady = ref(false);
let activeOverlay: HTMLElement | null = null;
let currentQuillInstance: any = null;

// Modal Crop state
const isCropModalOpen = ref(false);
const cropCanvas = ref<HTMLCanvasElement | null>(null);
let targetImageElement: HTMLImageElement | null = null;
let originalImageInstance: HTMLImageElement | null = null;

const selection = ref({
    startX: 0,
    startY: 0,
    currentX: 0,
    currentY: 0,
    active: false,
    isDragging: false,
});

let scaleFactorX = 1;
let scaleFactorY = 1;

onBeforeMount(async () => {
    if (typeof window !== 'undefined') {
        const Quill = await loadQuill();

        // Mendaftarkan Attributor jenis font berbasis style agar menghasilkan output inline CSS font-family
        const Font = Quill.import('attributors/style/font');
        Font.whitelist = [
            'sans-serif',
            'serif',
            'monospace',
            'cursive',
            'Arial',
            'Times New Roman',
        ];
        Quill.register(Font, true);

        // Mendaftarkan Custom Image Format agar style="width: ..." dan width="..." tidak dihapus oleh parser Quill
        const BaseImageFormat = Quill.import('formats/image');
        const ImageFormatAttributesList = ['alt', 'height', 'width', 'style'];
        class CustomImageFormat extends BaseImageFormat {
            static formats(domNode: any) {
                return ImageFormatAttributesList.reduce(
                    (formats: any, attribute) => {
                        if (domNode.hasAttribute(attribute)) {
                            formats[attribute] =
                                domNode.getAttribute(attribute);
                        }

                        return formats;
                    },
                    {},
                );
            }
            format(name: string, value: any) {
                if (ImageFormatAttributesList.indexOf(name) > -1) {
                    if (value) {
                        this.domNode.setAttribute(name, value);
                    } else {
                        this.domNode.removeAttribute(name);
                    }
                } else {
                    super.format(name, value);
                }
            }
        }
        Quill.register(CustomImageFormat, true);

        quillReady.value = true;
    }
});

// Konfigurasi Toolbar
const fullToolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],
    [
        {
            font: [
                false,
                'serif',
                'monospace',
                'cursive',
                'Arial',
                'Times New Roman',
            ],
        },
    ], // false = default sans-serif
    [{ header: [1, 2, 3, false] }], // Menampilkan label "Normal" saat false
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ script: 'sub' }, { script: 'super' }],
    [{ align: [] }],
    ['image', 'link', 'clean'],
];

const studentToolbarOptions = [
    ['bold', 'italic'],
    [{ script: 'sub' }, { script: 'super' }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ align: [] }],
    ['clean'],
];

const toolbarOptions = computed(() => {
    return props.variant === 'student'
        ? studentToolbarOptions
        : fullToolbarOptions;
});

const hideImageResizeOverlay = () => {
    if (activeOverlay) {
        activeOverlay.remove();
        activeOverlay = null;
    }
};

const showImageResizeOverlay = (img: HTMLImageElement, quill: any) => {
    hideImageResizeOverlay();

    const overlay = document.createElement('div');
    overlay.className = 'custom-image-resize-overlay';

    // Inline styling for the overlay box
    overlay.style.position = 'absolute';
    overlay.style.zIndex = '1000';
    overlay.style.backgroundColor = '#0c0d26';
    overlay.style.border = '1px solid rgba(255, 255, 255, 0.15)';
    overlay.style.borderRadius = '0.5rem';
    overlay.style.boxShadow =
        '0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3)';
    overlay.style.padding = '6px';
    overlay.style.display = 'flex';
    overlay.style.gap = '4px';

    // Size buttons
    const sizes = ['25%', '50%', '75%', '100%'];
    sizes.forEach((size) => {
        const btn = document.createElement('button');
        btn.innerText = size;
        btn.style.fontSize = '12px';
        btn.style.fontWeight = '600';
        btn.style.padding = '4px 8px';
        btn.style.borderRadius = '0.25rem';
        btn.style.border = '1px solid rgba(255, 255, 255, 0.1)';
        btn.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
        btn.style.color = '#cbd5e1';
        btn.style.cursor = 'pointer';
        btn.style.transition = 'all 0.15s ease';

        const currentWidth = img.style.width || img.getAttribute('width');

        if (currentWidth === size || (size === '100%' && !currentWidth)) {
            btn.style.backgroundColor = 'rgba(210, 255, 0, 0.15)';
            btn.style.color = '#d2ff00';
            btn.style.borderColor = '#d2ff00';
        }

        btn.addEventListener('mouseenter', () => {
            const freshWidth = img.style.width || img.getAttribute('width');

            if (!(freshWidth === size || (size === '100%' && !freshWidth))) {
                btn.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
            }
        });
        btn.addEventListener('mouseleave', () => {
            const freshWidth = img.style.width || img.getAttribute('width');

            if (!(freshWidth === size || (size === '100%' && !freshWidth))) {
                btn.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
            }
        });

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            img.style.width = size;
            img.setAttribute('width', size);

            quill.update();
            emit('update:modelValue', quill.root.innerHTML);
            hideImageResizeOverlay();
        });

        overlay.appendChild(btn);
    });

    // Crop button
    const cropBtn = document.createElement('button');
    cropBtn.innerText = 'Crop';
    cropBtn.style.fontSize = '12px';
    cropBtn.style.fontWeight = '600';
    cropBtn.style.padding = '4px 8px';
    cropBtn.style.borderRadius = '0.25rem';
    cropBtn.style.border = 'none';
    cropBtn.style.backgroundColor = '#00ffff';
    cropBtn.style.color = '#070814';
    cropBtn.style.cursor = 'pointer';
    cropBtn.style.transition = 'all 0.15s ease';
    cropBtn.style.marginLeft = '4px';

    cropBtn.addEventListener('mouseenter', () => {
        cropBtn.style.backgroundColor = '#00cccc';
    });
    cropBtn.addEventListener('mouseleave', () => {
        cropBtn.style.backgroundColor = '#00ffff';
    });

    cropBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        hideImageResizeOverlay();
        openCropModal(img);
    });

    overlay.appendChild(cropBtn);

    // Posisikan overlay di atas gambar di dalam wrapper editor
    const wrapper = quill.root.closest('.quill-custom-wrapper');

    if (wrapper) {
        wrapper.appendChild(overlay);
        const wrapperRect = wrapper.getBoundingClientRect();
        const imgRect = img.getBoundingClientRect();

        // Posisikan horizontal di tengah gambar, vertikal di atas gambar
        const top = imgRect.top - wrapperRect.top - 42;
        const left =
            imgRect.left -
            wrapperRect.left +
            (imgRect.width - overlay.offsetWidth) / 2;

        overlay.style.top = `${Math.max(0, top)}px`;
        overlay.style.left = `${Math.max(0, left)}px`;
        activeOverlay = overlay;
    }
};

const globalClickHandler = (e: MouseEvent) => {
    const target = e.target as HTMLElement;

    if (
        target &&
        !target.closest('.custom-image-resize-overlay') &&
        target.tagName !== 'IMG'
    ) {
        hideImageResizeOverlay();
    }
};

if (typeof window !== 'undefined') {
    document.addEventListener('click', globalClickHandler);
}

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        document.removeEventListener('click', globalClickHandler);
    }

    hideImageResizeOverlay();
});

const onEditorReady = (quill: any) => {
    currentQuillInstance = quill;

    // Listener klik gambar di dalam editor
    quill.root.addEventListener('click', (event: MouseEvent) => {
        if (props.disabled) {
            return;
        }

        const target = event.target as HTMLElement;

        if (target && target.tagName === 'IMG' && props.variant !== 'student') {
            showImageResizeOverlay(target as HTMLImageElement, quill);
        } else {
            hideImageResizeOverlay();
        }
    });

    quill.on('selection-change', () => {
        hideImageResizeOverlay();
    });
};

// Logic modal crop
const openCropModal = (img: HTMLImageElement) => {
    targetImageElement = img;
    isCropModalOpen.value = true;

    originalImageInstance = new Image();
    originalImageInstance.crossOrigin = 'anonymous';
    originalImageInstance.src = img.src;
    originalImageInstance.onload = () => {
        initCropCanvas();
    };
};

const initCropCanvas = () => {
    const canvas = cropCanvas.value;
    const img = originalImageInstance;

    if (!canvas || !img) {
        return;
    }

    const ctx = canvas.getContext('2d');

    if (!ctx) {
        return;
    }

    // Scale canvas agar proporsional di area maksimal 600x400
    const maxWidth = 600;
    const maxHeight = 400;
    let width = img.naturalWidth;
    let height = img.naturalHeight;

    if (width > maxWidth) {
        height = (maxWidth / width) * height;
        width = maxWidth;
    }

    if (height > maxHeight) {
        width = (maxHeight / height) * width;
        height = maxHeight;
    }

    canvas.width = width;
    canvas.height = height;

    scaleFactorX = img.naturalWidth / width;
    scaleFactorY = img.naturalHeight / height;

    // Draw initial image
    ctx.drawImage(img, 0, 0, width, height);

    // Reset selection state
    selection.value = {
        startX: 0,
        startY: 0,
        currentX: 0,
        currentY: 0,
        active: false,
        isDragging: false,
    };
};

const getMousePos = (e: MouseEvent) => {
    const canvas = cropCanvas.value;

    if (!canvas) {
        return { x: 0, y: 0 };
    }

    const rect = canvas.getBoundingClientRect();

    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top,
    };
};

const onCropMouseDown = (e: MouseEvent) => {
    const pos = getMousePos(e);
    selection.value.startX = pos.x;
    selection.value.startY = pos.y;
    selection.value.currentX = pos.x;
    selection.value.currentY = pos.y;
    selection.value.active = true;
    selection.value.isDragging = true;
    drawCropOverlay();
};

const onCropMouseMove = (e: MouseEvent) => {
    if (!selection.value.isDragging) {
        return;
    }

    const pos = getMousePos(e);
    selection.value.currentX = pos.x;
    selection.value.currentY = pos.y;
    drawCropOverlay();
};

const onCropMouseUp = (e: MouseEvent) => {
    if (!selection.value.isDragging) {
        return;
    }

    const pos = getMousePos(e);
    selection.value.currentX = pos.x;
    selection.value.currentY = pos.y;
    selection.value.isDragging = false;
    drawCropOverlay();
};

const drawCropOverlay = () => {
    const canvas = cropCanvas.value;
    const img = originalImageInstance;

    if (!canvas || !img) {
        return;
    }

    const ctx = canvas.getContext('2d');

    if (!ctx) {
        return;
    }

    // Bersihkan canvas dan gambar ulang gambar asli
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

    const sel = selection.value;

    if (!sel.active) {
        return;
    }

    // Menggambar latar belakang gelap semi transparan
    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const x = Math.min(sel.startX, sel.currentX);
    const y = Math.min(sel.startY, sel.currentY);
    const w = Math.abs(sel.startX - sel.currentX);
    const h = Math.abs(sel.startY - sel.currentY);

    if (w > 0 && h > 0) {
        // Gambar bagian yang diseleksi agar tetap terang benderang
        ctx.save();
        ctx.beginPath();
        ctx.rect(x, y, w, h);
        ctx.clip();
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        ctx.restore();

        // Garis batas dashed di area potong
        ctx.strokeStyle = '#4f46e5';
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 4]);
        ctx.strokeRect(x, y, w, h);
    }
};

const executeCrop = () => {
    const sel = selection.value;
    const img = originalImageInstance;
    const target = targetImageElement;

    if (!img || !target) {
        return;
    }

    const x = Math.min(sel.startX, sel.currentX);
    const y = Math.min(sel.startY, sel.currentY);
    const w = Math.abs(sel.startX - sel.currentX);
    const h = Math.abs(sel.startY - sel.currentY);

    // Abaikan jika area seleksi tidak valid atau terlalu kecil
    if (w < 5 || h < 5) {
        return;
    }

    const sx = x * scaleFactorX;
    const sy = y * scaleFactorY;
    const sw = w * scaleFactorX;
    const sh = h * scaleFactorY;

    // Potong dengan resolusi asli gambar menggunakan canvas offscreen
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = sw;
    tempCanvas.height = sh;
    const tempCtx = tempCanvas.getContext('2d');

    if (tempCtx) {
        tempCtx.drawImage(img, sx, sy, sw, sh, 0, 0, sw, sh);
        const croppedDataUrl = tempCanvas.toDataURL('image/png');
        target.src = croppedDataUrl;

        if (currentQuillInstance) {
            currentQuillInstance.update();
            emit('update:modelValue', currentQuillInstance.root.innerHTML);
        }
    }

    closeCropModal();
};

const closeCropModal = () => {
    isCropModalOpen.value = false;
    targetImageElement = null;
    originalImageInstance = null;
};
</script>

<template>
    <div class="quill-custom-wrapper" :class="{ 'quill-disabled': disabled }">
        <QuillEditor
            v-if="quillReady"
            :content="modelValue"
            contentType="html"
            @update:content="$emit('update:modelValue', $event)"
            @ready="onEditorReady"
            :placeholder="
                placeholder || 'Ketik materi atau pertanyaan di sini...'
            "
            theme="snow"
            :toolbar="toolbarOptions"
            :readOnly="disabled"
        />

        <!-- Modal Pemotong Gambar (Crop Modal Overlay) -->
        <div
            v-if="isCropModalOpen"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="flex w-full max-w-3xl flex-col overflow-hidden rounded-xl border border-white/10 bg-[#0d0e25] shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between border-b border-white/10 px-6 py-4"
                >
                    <h3 class="text-lg font-bold text-slate-100">
                        Potong Gambar (Crop Image)
                    </h3>
                    <button
                        @click="closeCropModal"
                        class="rounded-lg p-1 text-slate-400 transition hover:bg-white/10 hover:text-slate-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Area Gambar & Canvas -->
                <div
                    class="flex max-h-[70vh] flex-col items-center justify-center overflow-auto bg-[#08091a] p-6"
                >
                    <p
                        class="mb-4 text-center text-sm font-medium text-slate-400"
                    >
                        Seret mouse di atas gambar untuk menentukan bagian yang
                        ingin dipotong.
                    </p>
                    <div
                        class="relative overflow-hidden rounded-lg border border-white/10 bg-[#08091a] shadow-inner"
                    >
                        <canvas
                            ref="cropCanvas"
                            @mousedown="onCropMouseDown"
                            @mousemove="onCropMouseMove"
                            @mouseup="onCropMouseUp"
                            class="block cursor-crosshair"
                        ></canvas>
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div
                    class="flex items-center justify-end gap-3 border-t border-white/10 bg-[#08091a]/50 px-6 py-4"
                >
                    <button
                        @click="closeCropModal"
                        class="text-slate-350 rounded-lg border border-white/10 px-4 py-2 text-sm font-semibold transition hover:bg-white/10"
                    >
                        Batal
                    </button>
                    <button
                        @click="executeCrop"
                        class="flex h-10 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-5 text-sm font-bold text-[#070814] shadow-[0_0_15px_rgba(210,255,0,0.25)] transition-all hover:brightness-110 active:scale-98"
                    >
                        Potong & Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* 
 * CENTRALIZED RICH TEXT EDITOR DESIGN SYSTEM
 * Menyelaraskan penuh Quill Editor dengan standar desain modern Tailwind UI (SaaS Premium).
 */

/* 1. Wrapper Card & Border Fungsionalitas Fokus */
.quill-custom-wrapper {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem; /* rounded-xl */
    background-color: rgba(16, 17, 43, 0.6);
    overflow: visible; /* Diubah agar popover/tooltip link tidak terpotong */
    position: relative;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.3);
}

/* Mengubah warna border menjadi neon aksen saat area editor fokus */
.quill-custom-wrapper:focus-within {
    border-color: var(--theme-accent, #00ffff);
    box-shadow: 0 0 0 3px rgba(0, 255, 255, 0.15); /* Ring fokus cyan */
}

/* 2. Desain Kustom Toolbar */
.quill-custom-wrapper .ql-toolbar.ql-snow {
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(16, 17, 43, 0.85);
    padding: 10px 14px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}

/* Pembatas grup tombol */
.quill-custom-wrapper .ql-formats {
    margin-right: 12px !important;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

/* 3. Desain Kustom Tombol Toolbar & Status Aktif/Hover */
.quill-custom-wrapper .ql-snow.ql-toolbar button {
    width: 28px;
    height: 28px;
    padding: 4px;
    border-radius: 0.375rem; /* rounded-md */
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    outline: none;
}

/* Hover tombol */
.quill-custom-wrapper .ql-snow.ql-toolbar button:hover {
    background-color: rgba(255, 255, 255, 0.08);
}

/* Ikon Default (Abu-abu / Slate-400) */
.quill-custom-wrapper .ql-snow.ql-toolbar button .ql-stroke {
    stroke: #94a3b8 !important;
    stroke-width: 2px;
    transition: stroke 0.15s ease;
}
.quill-custom-wrapper .ql-snow.ql-toolbar button .ql-fill {
    fill: #94a3b8 !important;
    transition: fill 0.15s ease;
}

/* Hover warna ikon (Highlight Neon Green / Primary) */
.quill-custom-wrapper .ql-snow.ql-toolbar button:hover .ql-stroke {
    stroke: var(--theme-primary, #d2ff00) !important;
}
.quill-custom-wrapper .ql-snow.ql-toolbar button:hover .ql-fill {
    fill: var(--theme-primary, #d2ff00) !important;
}

/* Highlight Tombol Aktif (Neon Green / Primary) */
.quill-custom-wrapper .ql-snow.ql-toolbar button.ql-active {
    background-color: rgba(210, 255, 0, 0.15) !important;
}
.quill-custom-wrapper .ql-snow.ql-toolbar button.ql-active .ql-stroke {
    stroke: var(--theme-primary, #d2ff00) !important;
}
.quill-custom-wrapper .ql-snow.ql-toolbar button.ql-active .ql-fill {
    fill: var(--theme-primary, #d2ff00) !important;
}

/* 4. Dropdown Format Paragraf "Normal" */
.quill-custom-wrapper .ql-snow.ql-toolbar .ql-picker.ql-header {
    width: 115px;
    height: 28px;
    margin-right: 4px;
}

/* Label Select Dropdown */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-label {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.375rem;
    padding: 0 24px 0 10px;
    font-size: 12px;
    font-weight: 600;
    color: #94a3b8;
    background-color: rgba(16, 17, 43, 0.8);
    display: flex;
    align-items: center;
    position: relative;
    height: 100%;
    transition: all 0.15s ease;
    border-width: 1px !important;
}

/* Hover label */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-label:hover {
    border-color: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.05);
}

/* Dropdown Aktif */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header.ql-active
    .ql-picker-label {
    border-color: var(--theme-primary, #d2ff00);
    color: var(--theme-primary, #d2ff00);
    background-color: rgba(210, 255, 0, 0.15);
}

/* Ikon Chevron di Samping Dropdown */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-label
    svg {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
}
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-label
    svg
    .ql-stroke {
    stroke: #94a3b8 !important;
}
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header.ql-active
    .ql-picker-label
    svg
    .ql-stroke {
    stroke: var(--theme-primary, #d2ff00) !important;
}

/* Panel Pilihan Dropdown Panel */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-options {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    padding: 4px;
    background-color: #0c0d26;
    z-index: 50;
    margin-top: 4px;
}
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-options
    .ql-picker-item {
    padding: 6px 8px;
    border-radius: 0.25rem;
    font-size: 12px;
    color: #94a3b8;
    transition: all 0.15s ease;
}
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-options
    .ql-picker-item:hover,
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-header
    .ql-picker-options
    .ql-picker-item.ql-selected {
    background-color: rgba(255, 255, 255, 0.08);
    color: var(--theme-primary, #d2ff00);
}

/* 5. Kontainer Area Editor Teks */
.quill-custom-wrapper .ql-container.ql-snow {
    border: none;
    background-color: transparent;
    font-family: inherit;
    font-size: 14px;
    color: #f8fafc; /* text-slate-100 */
    border-bottom-left-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
}

.quill-custom-wrapper .ql-editor {
    min-height: 160px;
    padding: 18px;
    line-height: 1.6;
}

/* Mengubah warna placeholder editor */
.quill-custom-wrapper .ql-editor.ql-blank::before {
    color: #555877; /* dark slate placeholder */
    font-style: normal;
    left: 18px;
}

/* 6. Formatting Konten Editor & Image Responsive */
.quill-custom-wrapper .ql-editor p {
    margin-bottom: 0.5rem;
}

/* Penyisipan Gambar Inline Responsive */
.quill-custom-wrapper .ql-editor img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem; /* rounded corner tipis */
    display: inline-block;
    margin: 8px 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Layout List & Nested Indentation di Editor */
.quill-custom-wrapper .ql-editor ol,
.quill-custom-wrapper .ql-editor ul {
    padding-left: 1.5rem;
    margin-bottom: 0.5rem;
}

/* 7. Quill Tooltip (Popup Link Insertion) */
.quill-custom-wrapper .ql-snow .ql-tooltip {
    background-color: #0c0d26 !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
    padding: 8px 12px !important;
    z-index: 50 !important;
    color: #f8fafc !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip input[type='text'] {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 0.375rem !important;
    padding: 4px 8px !important;
    font-size: 13px !important;
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
    outline: none !important;
    transition: border-color 0.15s ease !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip input[type='text']:focus {
    border-color: var(--theme-primary, #d2ff00) !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip a.ql-action {
    color: var(--theme-accent, #00ffff) !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    margin-left: 8px !important;
    text-decoration: none !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip a.ql-action:hover {
    color: var(--theme-primary, #d2ff00) !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip a.ql-remove {
    color: #ef4444 !important;
    font-size: 12px !important;
    margin-left: 8px !important;
    text-decoration: none !important;
}

.quill-custom-wrapper .ql-snow .ql-tooltip a.ql-remove:hover {
    color: #f87171 !important;
}

/* ==========================================
   DROPDOWN JENIS FONT (FONT FAMILY)
   ========================================== */
.quill-custom-wrapper .ql-snow.ql-toolbar .ql-picker.ql-font {
    width: 135px;
    height: 28px;
    margin-right: 4px;
}

.quill-custom-wrapper .ql-snow.ql-toolbar .ql-picker.ql-font .ql-picker-label {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.375rem;
    padding: 0 24px 0 10px;
    font-size: 12px;
    font-weight: 600;
    color: #94a3b8;
    background-color: rgba(16, 17, 43, 0.8);
    display: flex;
    align-items: center;
    position: relative;
    height: 100%;
    transition: all 0.15s ease;
    border-width: 1px !important;
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-label:hover {
    border-color: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.05);
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font.ql-active
    .ql-picker-label {
    border-color: var(--theme-primary, #d2ff00);
    color: var(--theme-primary, #d2ff00);
    background-color: rgba(210, 255, 0, 0.15);
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-label
    svg {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-label
    svg
    .ql-stroke {
    stroke: #94a3b8 !important;
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font.ql-active
    .ql-picker-label
    svg
    .ql-stroke {
    stroke: var(--theme-primary, #d2ff00) !important;
}

/* Panel Pilihan Dropdown Font */
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-options {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    padding: 4px;
    background-color: #0c0d26;
    z-index: 50;
    margin-top: 4px;
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-options
    .ql-picker-item {
    padding: 6px 8px;
    border-radius: 0.25rem;
    font-size: 12px;
    color: #94a3b8;
    transition: all 0.15s ease;
}

.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-options
    .ql-picker-item:hover,
.quill-custom-wrapper
    .ql-snow.ql-toolbar
    .ql-picker.ql-font
    .ql-picker-options
    .ql-picker-item.ql-selected {
    background-color: rgba(255, 255, 255, 0.08);
    color: var(--theme-primary, #d2ff00);
}

/* Label Teks Pilihan Font */
.quill-custom-wrapper .ql-snow .ql-picker.ql-font .ql-picker-label::before,
.quill-custom-wrapper .ql-snow .ql-picker.ql-font .ql-picker-item::before {
    content: 'Sans-Serif' !important;
    font-family: sans-serif !important;
}
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-label[data-value='serif']::before,
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-item[data-value='serif']::before {
    content: 'Serif' !important;
    font-family: serif !important;
}
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-label[data-value='monospace']::before,
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-item[data-value='monospace']::before {
    content: 'Monospace' !important;
    font-family: monospace !important;
}
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-label[data-value='cursive']::before,
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-item[data-value='cursive']::before {
    content: 'Cursive' !important;
    font-family: cursive !important;
}
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-label[data-value='Arial']::before,
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-item[data-value='Arial']::before {
    content: 'Arial' !important;
    font-family: Arial, sans-serif !important;
}
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-label[data-value='Times New Roman']::before,
.quill-custom-wrapper
    .ql-snow
    .ql-picker.ql-font
    .ql-picker-item[data-value='Times New Roman']::before {
    content: 'Times New Roman' !important;
    font-family: 'Times New Roman', serif !important;
}

/* ==========================================
   STATE DISABLED / READ-ONLY
   ========================================== */
.quill-custom-wrapper.quill-disabled {
    opacity: 0.6;
    pointer-events: none;
    cursor: not-allowed;
}

.quill-custom-wrapper.quill-disabled .ql-toolbar.ql-snow {
    background-color: rgba(255, 255, 255, 0.02);
}

.quill-custom-wrapper.quill-disabled .ql-editor {
    background-color: rgba(255, 255, 255, 0.02);
    cursor: not-allowed;
}

/* PERBAIKAN: Sembunyikan placeholder saat editor dalam mode read-only/disabled.
   Ini mencegah teks placeholder ("Ketik jawaban Anda di sini...") 
   bertumpuk di atas jawaban siswa yang sudah dikirim. */
.quill-custom-wrapper.quill-disabled .ql-editor.ql-blank::before {
    display: none !important;
    content: none !important;
}

/* Juga sembunyikan placeholder jika editor berisi konten apapun (termasuk list) */
.quill-custom-wrapper .ql-editor:not(.ql-blank)::before {
    display: none !important;
}
</style>
