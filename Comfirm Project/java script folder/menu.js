function openProductModal(id, name, price, desc, img) {
    document.getElementById('modal_product_id').value = id;
    document.getElementById('modal_name').innerText = name;
    document.getElementById('modal_price').innerText = price;
    document.getElementById('modal_desc').innerText = desc;
    document.getElementById('modal_img').src = img;

    document.getElementById('modal_qty').value = 1;

    var myModal = new bootstrap.Modal(document.getElementById('productModal'));
    myModal.show();
}

function changeQty(change) {
    var qtyInput = document.getElementById('modal_qty');
    var currentQty = parseInt(qtyInput.value);
    var newQty = currentQty + change;
    if (newQty >= 1) {
        qtyInput.value = newQty;
    }
}

function confirmAddToCart() {
    var productId = document.getElementById('modal_product_id').value;
    var quantity = document.getElementById('modal_qty').value;

    const formData = new FormData();
    formData.append('add_to_cart', '1');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('menu.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'not_logged_in') {
                alert("Please Login First to order food!");
                window.location.href = "login.php";
            }
            else if (data.trim() === 'success') {
                var modalEl = document.getElementById('productModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                alert("Added to cart!");
                location.reload(); 
            }
            else {
                alert('Error: ' + data);
            }
        })
        .catch(error => console.error('Error:', error));
}