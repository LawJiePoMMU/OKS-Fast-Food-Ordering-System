function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('modal_user_id').value = this.getAttribute('data-id');
            document.getElementById('modal_username').value = this.getAttribute('data-username');
            document.getElementById('modal_email').value = this.getAttribute('data-email');

            let mobile = this.getAttribute('data-mobile');

            mobile = mobile.replace(/\D/g, '');

            while (mobile.startsWith('60')) {
                mobile = mobile.substring(2);
            }

            document.getElementById('modal_mobile').value = mobile;
            document.getElementById('modal_address').value = this.getAttribute('data-address');
            document.getElementById('modal_role').value = this.getAttribute('data-role');
        });
    });

    document.getElementById('editUserForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.innerText = 'Saving...'; saveBtn.disabled = true;

        fetch(window.location.href, { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') { location.reload(); }
                else { alert('Error: ' + data); }
            })
            .finally(() => { saveBtn.innerText = 'Save Changes'; saveBtn.disabled = false; });
    });

    document.getElementById('addAdminForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const addBtn = document.getElementById('addAdminBtn');
        addBtn.innerText = 'Creating...'; addBtn.disabled = true;

        fetch(window.location.href, { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('New Admin Created!');
                    location.reload();
                } else {
                    alert('Error: ' + data);
                }
            })
            .finally(() => { addBtn.innerText = 'Create Admin'; addBtn.disabled = false; });
    });

    const statusButtons = document.querySelectorAll('.toggle-status-btn');
    statusButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');

            const formData = new FormData();
            formData.append('ajax_toggle_status', '1');
            formData.append('user_id', id);
            formData.append('current_status', currentStatus);

            fetch(window.location.href, { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === 'success') { location.reload(); }
                    else { alert('Error: ' + data); }
                });
        });
    });
});