document.getElementById('hamburgerToggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('show');
    document.querySelector('.content').classList.toggle('expanded'); // Shift content when sidebar is open
});

