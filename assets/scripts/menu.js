document.addEventListener('DOMContentLoaded', () => {
    const navbarBurger = document.querySelector('.navbar-burger');
    const sidebar = document.getElementById('sidebarMenu');

    navbarBurger.addEventListener('click', () => {
        navbarBurger.classList.toggle('is-active');
        sidebar.classList.toggle('is-active');
    });
});
