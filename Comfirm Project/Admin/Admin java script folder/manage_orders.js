document.addEventListener('DOMContentLoaded', function() {

    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.id;
            const contentDiv = document.getElementById('order_details_content');
            contentDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-danger"></div></div>';

            const fd = new FormData();
            fd.append('ajax_view_details', '1');
            fd.append('order_id', orderId);

            fetch('manage_orders.php', { method: 'POST', body: fd })
            .then(res => res.text())
            .then(html => {
                contentDiv.innerHTML = html;
            });
        });
    });

    const statusButtons = document.querySelectorAll('.status-btn');
    statusButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.id;
            const currentStatus = this.dataset.status;

            if (currentStatus === 'Pending') {
                document.getElementById('status_order_id').value = orderId;
                const select = document.getElementById('new_status');
                select.innerHTML = '';
                select.innerHTML += '<option value="Preparing">Preparing (Accept)</option>';
                select.innerHTML += '<option value="Cancelled">Cancelled (Reject)</option>';
                const myModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                myModal.show();
            } 

            else if (currentStatus === 'Preparing') {
                if(confirm('Are you sure you want to mark Order #' + orderId + ' as Out for Delivery?')) {
                    document.getElementById('status_order_id').value = orderId;
                    const dummyInput = document.createElement('input');
                    dummyInput.value = 'Out for Delivery';
                    dummyInput.id = 'new_status_direct';
                    updateDirectly(orderId, 'Out for Delivery');
                }
            } 
        });
    });
});

function updateDirectly(orderId, newStatus) {
    const fd = new FormData();
    fd.append('ajax_update_status', '1');
    fd.append('order_id', orderId);
    fd.append('status', newStatus);

    fetch('manage_orders.php', { method: 'POST', body: fd })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === 'success') {
            location.reload();
        } else {
            alert(data);
        }
    });
}

function saveStatus() {
    const orderId = document.getElementById('status_order_id').value;
    const newStatus = document.getElementById('new_status').value;
    const btn = document.querySelector(`button[onclick="saveStatus()"]`);

    if(!newStatus) return;

    btn.disabled = true;
    btn.innerText = "Updating...";

    const fd = new FormData();
    fd.append('ajax_update_status', '1');
    fd.append('order_id', orderId);
    fd.append('status', newStatus);

    fetch('manage_orders.php', { method: 'POST', body: fd })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === 'success') {
            location.reload();
        } else {
            alert(data);
            btn.disabled = false;
            btn.innerText = "Update Status";
        }
    });
}