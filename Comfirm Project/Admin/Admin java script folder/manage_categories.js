document.addEventListener('DOMContentLoaded', function () {
    const addForm = document.getElementById('addCategoryForm');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('addBtn');
            btn.disabled = true; btn.innerText = "Adding...";
            fetch(window.location.href, { method: 'POST', body: new FormData(this) })
                .then(res => res.text()).then(data => {
                    if (data.trim() === 'success') { location.reload(); } else { alert('Error: ' + data); }
                }).finally(() => { btn.disabled = false; btn.innerText = "Add Category"; });
        });
    }

    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            this.style.opacity = "0.5";
            const fd = new FormData();
            fd.append('ajax_toggle_status', '1');
            fd.append('category_id', this.dataset.id);
            fd.append('current_status', this.dataset.status);
            fetch(window.location.href, { method: 'POST', body: fd })
                .then(res => res.text()).then(data => {
                    if (data.trim() === 'success') { location.reload(); } else { alert('Error: ' + data); this.style.opacity = "1"; }
                });
        });
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
        });
    });

    const editForm = document.getElementById('editCategoryForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('updateBtn');
            btn.disabled = true; btn.innerText = "Saving...";
            fetch(window.location.href, { method: 'POST', body: new FormData(this) })
                .then(res => res.text()).then(data => {
                    if (data.trim() === 'success') { location.reload(); } else { alert('Error: ' + data); }
                }).finally(() => { btn.disabled = false; btn.innerText = "Save Changes"; });
        });
    }
});