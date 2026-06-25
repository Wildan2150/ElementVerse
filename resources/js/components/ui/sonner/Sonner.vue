<script lang="ts" setup>
import { computed } from "vue"
import type { ToasterProps } from "vue-sonner"
import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon, XIcon } from "lucide-vue-next";
import { Toaster as Sonner } from "vue-sonner"
import 'vue-sonner/style.css'; 
import { cn } from "@/lib/utils"
import { useAppearance } from "@/composables/useAppearance"

const props = defineProps<ToasterProps>()
const { resolvedAppearance } = useAppearance()

const toasterStyle = computed(() => {
  const isDark = resolvedAppearance.value === "dark"
  return {
    "--normal-bg": "var(--popover)",
    "--normal-text": "var(--popover-foreground)",
    "--normal-border": "var(--border)",
    "--border-radius": "var(--radius)",
    "--success-bg": "var(--popover)",
    "--success-text": isDark ? "#4ade80" : "#15803d",
    "--success-border": "var(--border)",
    "--error-bg": "var(--popover)",
    "--error-text": isDark ? "#f87171" : "#b91c1c",
    "--error-border": "var(--border)",
    "--info-bg": "var(--popover)",
    "--info-text": isDark ? "#38bdf8" : "#0369a1",
    "--info-border": "var(--border)",
    "--warning-bg": "var(--popover)",
    "--warning-text": isDark ? "#fbbf24" : "#b45309",
    "--warning-border": "var(--border)",
    "--description-color": "#cbd5e1",
  }
})
</script>

<template>
  <Sonner
    :class="cn('toaster group', props.class)"
    :theme="resolvedAppearance"
    :style="toasterStyle"
    v-bind="props"
  >
    <template #success-icon>
      <CircleCheckIcon class="size-4" />
    </template>
    <template #info-icon>
      <InfoIcon class="size-4" />
    </template>
    <template #warning-icon>
      <TriangleAlertIcon class="size-4" />
    </template>
    <template #error-icon>
      <OctagonXIcon class="size-4" />
    </template>
    <template #loading-icon>
      <div>
        <Loader2Icon class="size-4 animate-spin" />
      </div>
    </template>
    <template #close-icon>
      <XIcon class="size-4" />
    </template>
  </Sonner>
</template>
