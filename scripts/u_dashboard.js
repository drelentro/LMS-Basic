document.addEventListener('DOMContentLoaded', function () {

    function handleBorrowReturnActions() {
        const borrowButtons = document.querySelectorAll('input[name="borrow"]');
        const returnButtons = document.querySelectorAll('input[name="return"]');

        borrowButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const confirmation = confirm('Are you sure you want to borrow this book?');
                if (!confirmation) {
                    event.stopPropagation();
                } else {
                    alert('Book borrowed successfully!');
                }
            });
        });

        returnButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const confirmation = confirm('Are you sure you want to return this book?');
                if (!confirmation) {
                    event.stopPropagation();
                } else {
                    alert('Book returned successfully!');
                }
            });
        });
    }

    handleBorrowReturnActions();


});
