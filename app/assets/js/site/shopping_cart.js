function saveQuantity(productId, quantity) {
    productId = parseInt(productId);
    quantity = parseInt(quantity);

    if (isNaN(productId) || productId <= 0 || isNaN(quantity)) {
        return;
    }

    return fetch(WEBROOT + `ajax/shopping_cart/save_quantity/${productId}/${quantity}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(response.statusText)
            }

            return response.json()
        })
        .then(feedback => {
            let shoppingCartProductsNumber = Object.keys(feedback.data).length;

            updateMiniShoppingCartQuantity(Object.keys(feedback.data).length);

            if (!shoppingCartProductsNumber) {
                $('#order-form').remove();
            }

            document.getElementById('shopping-cart').innerHTML = feedback.content;

            toastr.success(trans.cart_updated);
        })
        .catch(error => {
            console.log(error);
        });
}

// Update header shopping cart quantity
function updateMiniShoppingCartQuantity(quantity) {
    quantity = parseInt(quantity);

    if (isNaN(quantity)) {
        return;
    }

    document.getElementById('mini-shopping-cart-quantity').innerHTML = quantity;
}

window.saveQuantity = saveQuantity;

$(document).ready(function () {
    let timer;

    $(document).on('change', '#shopping-cart .quantity-input', function () {
        let input = $(this);

        clearTimeout(timer);

        timer = setTimeout(function () {
            let quantity = parseInt(input.val());
            let productId = parseInt(input.closest('.quantity-input-wrapper').find('input[name="product_id"]').val());
            if (!isNaN(quantity) && !isNaN(productId)) {
                saveQuantity(productId, quantity);
            }

        }, 500);
     });
});