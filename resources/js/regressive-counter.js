document.addEventListener('DOMContentLoaded', function() {
    updateCounters();
    setInterval(updateCounters, 1000);
});

function updateCounters() {
    document.querySelectorAll('.regressive-counter').forEach(counter => {

        const endDate = counter.getAttribute('data-end');

        if (!endDate) {
            return;
        }

        const timeRemaining = new Date(endDate) - new Date();

        if (isNaN(timeRemaining)) {
            return;
        }

        if (timeRemaining <= 0) {
            counter.textContent = "Finished";
            return;
        }

        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        const daysText = days > 0 ? `${days}d ` : '';
        counter.textContent = `${daysText}${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    });
}
