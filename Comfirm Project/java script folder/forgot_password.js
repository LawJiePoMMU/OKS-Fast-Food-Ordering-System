document.addEventListener('DOMContentLoaded', function () {
    const errorMsg = document.getElementById('php_error').value;
    const successMsg = document.getElementById('php_success').value;

    if (errorMsg) {
        alert("Verification Failed: " + errorMsg);
    }

    if (successMsg) {
        alert("Success: " + successMsg);
        window.location.href = "login.php";
    }

    const mobileInput = document.querySelector('.mobile-input');
    if (mobileInput) {
        mobileInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }
});