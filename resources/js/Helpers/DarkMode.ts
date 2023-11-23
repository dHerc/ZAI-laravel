export function loadDarkMode(): boolean {
    return window.localStorage.getItem('darkMode') === 'true';
}

export function saveDarkMode(value: boolean) {
    window.localStorage.setItem('darkMode', value ? 'true' : 'false');
}
