document.addEventListener('DOMContentLoaded', function () {
    let deleteButtons = document.querySelectorAll('.notification .delete');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            let notification = button.parentElement;
            notification.remove();
        });
    });
});
