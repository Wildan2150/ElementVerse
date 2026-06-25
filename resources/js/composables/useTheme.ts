import { ref, onMounted } from 'vue';

export type Theme = 'elementverse';

const theme = ref<Theme>('elementverse');

export function useTheme() {
    onMounted(() => {
        document.documentElement.setAttribute('data-theme', 'elementverse');
    });

    const updateTheme = (newTheme: Theme) => {
        theme.value = 'elementverse';
        document.documentElement.setAttribute('data-theme', 'elementverse');
    };

    return {
        theme,
        updateTheme,
    };
}
