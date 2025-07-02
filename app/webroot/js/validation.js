document.addEventListener('DOMContentLoaded', function() {
    let form = document.getElementById('crudForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let name = document.getElementById('name').value.trim();
            let email = document.getElementById('email').value.trim();

            if (name === '') {
                alert('Name is required!');
                e.preventDefault();
            } else if (!email.match(/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/)) {
                alert('Enter a valid email.');
                e.preventDefault();
            }
        });
    }
});
