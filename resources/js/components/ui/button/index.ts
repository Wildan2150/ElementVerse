import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Button } from "./Button.vue"

export const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-bold transition-all duration-200 shrink-0 outline-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none active:scale-[0.97] [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 [&_svg]:shrink-0",
  {
    variants: {
      variant: {
        default:
          "bg-gradient-to-r from-primary to-accent text-primary-foreground shadow-md shadow-primary/25 dark:shadow-[0_0_15px_rgba(210,255,0,0.25)] hover:brightness-110 hover:shadow-[0_0_28px_rgba(79,70,229,0.45)] dark:hover:shadow-[0_0_28px_rgba(210,255,0,0.45)] active:brightness-95 focus-visible:ring-primary focus-visible:ring-offset-background dark:from-[#d2ff00] dark:to-[#00ffff] dark:text-[#070814] dark:focus-visible:ring-[#d2ff00] dark:focus-visible:ring-offset-[#08091a]",
        destructive:
          "border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 hover:text-red-800 active:bg-red-50 focus-visible:ring-red-500 focus-visible:ring-offset-white dark:border-red-500/40 dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 dark:hover:border-red-500/70 dark:hover:text-red-300 dark:active:bg-red-500/20 dark:focus-visible:ring-red-500 dark:focus-visible:ring-offset-[#08091a]",
        outline:
          "border border-neutral-200 bg-white text-neutral-800 hover:bg-neutral-50 hover:text-neutral-900 focus-visible:ring-slate-400 focus-visible:ring-offset-white dark:border-white/20 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:border-white/40 dark:hover:text-white dark:active:bg-white/5 dark:focus-visible:ring-slate-400 dark:focus-visible:ring-offset-[#08091a]",
        secondary:
          "border border-neutral-200 bg-white text-neutral-800 hover:bg-neutral-50 hover:text-neutral-900 focus-visible:ring-slate-400 focus-visible:ring-offset-white dark:border-white/20 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10 dark:hover:border-white/40 dark:hover:text-white dark:active:bg-white/5 dark:focus-visible:ring-slate-400 dark:focus-visible:ring-offset-[#08091a]",
        ghost:
          "bg-transparent text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 active:bg-neutral-50 focus-visible:ring-slate-400 focus-visible:ring-offset-white dark:text-slate-400 dark:hover:bg-white/8 dark:hover:text-slate-100 dark:active:bg-white/5 dark:focus-visible:ring-slate-500 dark:focus-visible:ring-offset-[#08091a]",
        link: "text-primary underline-offset-4 hover:underline",
      },
      size: {
        default: "h-10 px-5",
        sm: "h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5",
        lg: "h-10 rounded-md px-6 has-[>svg]:px-4",
        icon: "size-9",
        "icon-sm": "size-8",
        "icon-lg": "size-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)
export type ButtonVariants = VariantProps<typeof buttonVariants>
