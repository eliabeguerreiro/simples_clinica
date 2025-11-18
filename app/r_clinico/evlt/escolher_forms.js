document.addEventListener('DOMContentLoaded', function () {
    const headers = document.querySelectorAll('.accordion-header');
    headers.forEach(header => {
        header.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            const icon = this.querySelector('.accordion-icon');

            if (content.classList.contains('show')) {
                content.classList.remove('show');
                icon.classList.remove('rotated');
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('show');
                icon.classList.add('rotated');
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
});