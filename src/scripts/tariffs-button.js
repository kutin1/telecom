document.addEventListener('DOMContentLoaded', function() {
    const tariffButtons = document.querySelectorAll('.tariff-button');
    const connectModal = document.getElementById('connectModal');

    tariffButtons.forEach(button => {
        button.addEventListener('click', function() {
            showModal(connectModal);
        });
    });

    function showModal(modal) {
        modal.style.display = 'block';
    }
});