document.addEventListener('DOMContentLoaded', function () {

    const addForm = document.getElementById('addProductForm');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('addBtn');
            btn.disabled = true;
            btn.innerText = "Adding...";
            fetch(window.location.href, {
                method: 'POST',
                body: new FormData(this)
            })
                .then(res => res.text()).then(data => {
                    if (data.trim() === 'success') {
                        location.reload();
                    } else {
                        alert(data);
                    }
                }).finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Add Product";
                });
        });
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_category').value = this.dataset.cat;
            document.getElementById('edit_desc').value = this.dataset.desc;
            document.getElementById('edit_img_preview').src = this.dataset.img;
        });
    });

    const editForm = document.getElementById('editProductForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('updateBtn');
            btn.disabled = true;
            btn.innerText = "Saving...";
            fetch(window.location.href, {
                method: 'POST',
                body: new FormData(this)
            })
                .then(res => res.text()).then(data => {
                    if (data.trim() === 'success') {
                        location.reload();
                    } else {
                        alert(data);
                    }
                }).finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Save Changes";
                });
        });
    }

    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const fd = new FormData();
            fd.append('ajax_update_status', '1');
            fd.append('product_id', this.dataset.id);
            fd.append('current_status', this.dataset.status);

            this.disabled = true;
            const originalText = this.innerText;
            this.innerText = "...";

            fetch(window.location.href, {
                method: 'POST',
                body: fd
            })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        const newStatus = this.dataset.status === 'Active' ? 'Inactive' : 'Active';
                        this.dataset.status = newStatus;
                        this.innerText = newStatus;

                        if (newStatus === 'Active') {
                            this.classList.remove('btn-secondary');
                            this.classList.add('btn-success');
                        } else {
                            this.classList.remove('btn-success');
                            this.classList.add('btn-secondary');
                        }
                    } else {
                        alert(data);
                        this.innerText = originalText;
                    }
                })
                .finally(() => {
                    this.disabled = false;
                });
        });
    });
});