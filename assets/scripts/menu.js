document.addEventListener('DOMContentLoaded', () => {
    const $navbarBurger = document.querySelector('.navbar-burger');
    const $sidebarMenu = document.getElementById('sidebarMenu');

    if ($navbarBurger && $sidebarMenu) {
        $navbarBurger.addEventListener('click', () => {
            $navbarBurger.classList.toggle('is-active');
            $sidebarMenu.classList.toggle('is-active');
        });
    }
});