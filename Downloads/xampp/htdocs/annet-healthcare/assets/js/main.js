// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('.bg-green-50.border-green-200, .bg-red-50.border-red-200');
    flashes.forEach(el => {
        if (el.textContent.includes('success') || el.textContent.length < 200) {
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 6000);
        }
    });
});