const activePage = window.location.pathname.split('/').pop(); // Get the current page file name
const navLinks = document.querySelectorAll('.sidebar .nav-link');

navLinks.forEach(link => {
    const linkPage = link.getAttribute('href').split('/').pop(); // Get the link's file name
    if (activePage === linkPage) {
        link.classList.add('active');
        // Optionally, ensure the parent submenu is expanded if necessary
        const parentMenu = link.closest('ul.collapse');
        if (parentMenu) {
            parentMenu.classList.add('show');
        }
    } else {
        link.classList.remove('active');
    }
});