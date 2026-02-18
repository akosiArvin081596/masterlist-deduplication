import { onMounted, ref } from 'vue';

export function useDarkMode() {
    const isDark = ref(false);

    function applyTheme(dark: boolean): void {
        isDark.value = dark;
        document.documentElement.classList.toggle('dark', dark);
    }

    function toggle(): void {
        const newDark = !isDark.value;
        localStorage.setItem('theme', newDark ? 'dark' : 'light');
        applyTheme(newDark);
    }

    onMounted(() => {
        const stored = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(stored === 'dark' || (!stored && prefersDark));
    });

    return { isDark, toggle };
}
