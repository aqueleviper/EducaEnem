document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (!hamburger || !sidebar || !overlay) return;

    function fechar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    hamburger.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', fechar);

    sidebar.querySelectorAll('nav a').forEach(function (link) {
        link.addEventListener('click', fechar);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) fechar();
    });
});