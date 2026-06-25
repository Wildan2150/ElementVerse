<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import { useTheme } from '@/composables/useTheme';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { theme } = useTheme();
</script>

<template>
    <AppShell
        variant="sidebar"
        :class="[theme === 'classic' ? 'font-serif' : '']"
    >
        <AppSidebar />
        <AppContent
            variant="sidebar"
            class="relative min-h-screen overflow-x-clip"
        >
            <!-- BACKGROUND DECORATIONS -->

            <!-- ElementVerse Dark-Tech Theme Decorations -->
            <div
                class="pointer-events-none absolute inset-0 -z-10 overflow-hidden bg-[var(--theme-bg-gradient)] transition-colors duration-500"
            >
                <!-- Diagonal Neon Glowing Lines -->
                <div
                    class="absolute top-[10%] right-[-10%] h-[1px] w-[800px] rotate-[-35deg] bg-gradient-to-l from-transparent via-[#d2ff00]/40 to-transparent blur-[1px]"
                ></div>
                <div
                    class="absolute bottom-[20%] left-[-15%] h-[1.5px] w-[900px] rotate-[-35deg] bg-gradient-to-r from-transparent via-[#00ffff]/30 to-transparent blur-[1px]"
                ></div>
                <div
                    class="absolute top-[40%] left-[10%] h-[1px] w-[600px] rotate-[-35deg] bg-gradient-to-r from-transparent via-[#d2ff00]/25 to-transparent"
                ></div>

                <!-- Glowing Orbs (Atmosphere) -->
                <div
                    class="pointer-events-none absolute top-1/4 left-[-10%] h-[400px] w-[400px] rounded-full bg-violet-600/10 blur-[120px] dark:bg-violet-900/5"
                ></div>
                <div
                    class="pointer-events-none absolute right-[-10%] bottom-1/3 h-[450px] w-[450px] rounded-full bg-indigo-500/10 blur-[150px] dark:bg-indigo-950/5"
                ></div>
            </div>

            <!-- MAIN CONTENT WRAPPER -->
            <div class="relative z-10 flex min-h-screen flex-col">
                <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                <div class="flex-1">
                    <slot />
                </div>
            </div>
        </AppContent>
        <Toaster />
    </AppShell>
</template>

<style scoped>
@keyframes floatUp {
    0% {
        transform: translateY(110vh) scale(0.6);
        opacity: 0;
    }
    15% {
        opacity: 0.35;
    }
    85% {
        opacity: 0.35;
    }
    100% {
        transform: translateY(-10vh) scale(1.1);
        opacity: 0;
    }
}

@keyframes floatSlow {
    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-15px) rotate(8deg);
    }
}

.animate-float-slow {
    animation: floatSlow 6s infinite ease-in-out;
}

.chem-bubble {
    position: absolute;
    bottom: -40px;
    border-radius: 50%;
    opacity: 0;
    animation: floatUp 12s infinite linear;
}

.bubble-1 {
    left: 15%;
    width: 14px;
    height: 14px;
    background: rgba(217, 70, 239, 0.25);
    box-shadow: 0 0 8px rgba(217, 70, 239, 0.4);
    animation-duration: 9s;
    animation-delay: 0s;
}

.bubble-2 {
    left: 45%;
    width: 22px;
    height: 22px;
    background: rgba(6, 182, 212, 0.2);
    box-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
    animation-duration: 13s;
    animation-delay: 2s;
}

.bubble-3 {
    left: 70%;
    width: 12px;
    height: 12px;
    background: rgba(217, 70, 239, 0.3);
    box-shadow: 0 0 6px rgba(217, 70, 239, 0.4);
    animation-duration: 8s;
    animation-delay: 4s;
}

.bubble-4 {
    left: 32%;
    width: 18px;
    height: 18px;
    background: rgba(6, 182, 212, 0.25);
    box-shadow: 0 0 8px rgba(6, 182, 212, 0.4);
    animation-duration: 11s;
    animation-delay: 6s;
}

.bubble-5 {
    left: 88%;
    width: 16px;
    height: 16px;
    background: rgba(234, 179, 8, 0.2);
    box-shadow: 0 0 8px rgba(234, 179, 8, 0.3);
    animation-duration: 15s;
    animation-delay: 1s;
}
</style>
