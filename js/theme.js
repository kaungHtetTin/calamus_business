(function () {
    var root = document.querySelector('.app-root');
    if (!root) return;

    var storageKey = 'calamus_portal_theme';
    var savedTheme = localStorage.getItem(storageKey);
    var preferredDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    root.dataset.theme = savedTheme || (preferredDark ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = nextTheme;
            localStorage.setItem(storageKey, nextTheme);
            button.setAttribute('aria-label', nextTheme === 'dark' ? 'Use light theme' : 'Use dark theme');
        });
    });
})();
