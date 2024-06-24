document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.grid-item input[type="checkbox"]');

    checkboxes.forEach(checkbox => {
        // Initialize the correct image based on the initial state of the checkbox
        const label = checkbox.closest('label');
        const img = label.querySelector('img');
        if (checkbox.checked) {
            img.src = 'assets/icons/black/checkbox-true.svg'; // Path to checked image
        } else {
            img.src = 'assets/icons/black/checkbox-false.svg'; // Path to unchecked image
        }

        // Add event listener for change event
        checkbox.addEventListener('change', (event) => {
            if (event.target.checked) {
                img.src = 'assets/icons/orange/checkbox-true.svg'; // Path to checked image
            } else {
                img.src = 'assets/icons/black/checkbox-false.svg'; // Path to unchecked image
            }
        });
    });
});
