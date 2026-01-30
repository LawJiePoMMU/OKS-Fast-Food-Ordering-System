document.addEventListener('DOMContentLoaded', function() {
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            userMenu.classList.remove('show');
        });
    }
});