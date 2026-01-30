document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const alertBox = document.getElementById('profileAlert');

    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            const mobileInput = document.querySelector('input[name="mobile"]');
            const mobileValue = mobileInput.value.trim();
            if (mobileValue.length < 9 || mobileValue.length > 10) {
                showAlert('danger', 'Mobile number must be 9 or 10 digits (excluding +60).');
                mobileInput.focus();
                return;
            }

            const saveBtn = document.querySelector('.btn-save');
            const originalBtnText = saveBtn.innerHTML;
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

            const formData = new FormData(this);
            formData.append('update_profile', '1'); 

            fetch('profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnText;

                if (data.trim() === 'success') {
                    showAlert('success', 'Profile updated successfully!');
                    setTimeout(() => {
                        location.reload(); 
                    }, 1500);
                } else {
                    showAlert('danger', 'Error updating profile: ' + data);
                }
            })
            .catch(error => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnText;
                showAlert('danger', 'Something went wrong. Please try again.');
                console.error('Error:', error);
            });
        });
    }

    function showAlert(type, message) {
        alertBox.className = `alert alert-${type} alert-custom`;
        alertBox.innerHTML = message;
        alertBox.style.display = 'block';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});