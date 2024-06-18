document.querySelectorAll('input[name="has_users"]').forEach(function(elem) {
    elem.addEventListener('change', function() {
        let userTypeOptions = document.getElementById('user-type-options');
        if (this.value === 'yes') {
            userTypeOptions.style.display = 'block';
        } else {
            userTypeOptions.style.display = 'none';
        }
    });
});