function removeItem(cartId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('cart_id', cartId);

    fetch('cart.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                location.reload(); 
            } else {
                console.error('Error removing item');
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateQty(cartId, change, currentQty) {
    let newQty = currentQty + change;
    
    if (newQty < 1) return;

    const formData = new FormData();
    formData.append('action', 'update_qty');
    formData.append('cart_id', cartId);
    formData.append('quantity', newQty);

    fetch('cart.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                location.reload();
            } else {
                console.error('Error updating quantity');
            }
        })
        .catch(error => console.error('Error:', error));
}