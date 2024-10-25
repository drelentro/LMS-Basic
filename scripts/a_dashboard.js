document.addEventListener('DOMContentLoaded', function () {


    function handleFormSubmission() {
        const submitButtons = document.querySelectorAll('.submit');
        submitButtons.forEach(button => {
            button.addEventListener('click', function () {
                const confirmation = confirm('Are you sure you want to add this book?');
                if (!confirmation) {
                    event.preventDefault();
                }
            });
        });
    }

    handleFormSubmission();



});
