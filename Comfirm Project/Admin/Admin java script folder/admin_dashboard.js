document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.id;
            const contentDiv = document.getElementById('order_details_content');

            contentDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-danger"></div></div>';
            
            const formData = new FormData();
            formData.append('ajax_view_details', '1');
            formData.append('order_id', orderId);

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.text())
            .then(html => { contentDiv.innerHTML = html; })
            .catch(err => {
                contentDiv.innerHTML = '<p class="text-danger text-center">Error loading details.</p>';
                console.error(err);
            });
        });
    });

});