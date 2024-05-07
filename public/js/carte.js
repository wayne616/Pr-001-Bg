$(document).ready(function() {
    $('[data-bs-toggle="modal"]').click(function() {
        var articleId = $(this).data('article-id');
        var articleName = $(this).data('article-name');
        var articleDescription = $(this).data('article-description');
        $('#articleModalLabel').text(articleId);
        $('#articleModalLabel').text(articleName);
        $('#articleDescription').text(articleDescription);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    renderCart();


    document.querySelectorAll('.add-to-cart-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var articleId = this.getAttribute('data-article-id');
            addToCart(articleId);
            renderCart();
        });
    });

});

function addToCart(articleId) {
    var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
    var existingItemIndex = cartItems.findIndex(item => item.articleId === articleId);
    if (existingItemIndex !== -1) {
        cartItems[existingItemIndex].quantity++;
    } else {
        cartItems.push({ articleId: articleId, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cartItems));
}

function renderCart() {
    var cartDropdown = document.getElementById('cartDropdown');
    const test = cartDropdown.innerHTML;
    test = ''; // Clear the cart before rendering
    
    var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
    cartItems.forEach(function(item) {
        var cartItem = document.createElement('a');
        cartItem.classList.add('dropdown-item');
        cartItem.innerHTML = 'Article ID: ' + item.articleId + ' - Quantity: ' + item.quantity;
        
        var deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.classList.add('btn', 'btn-sm', 'btn-danger', 'ml-2');
        deleteButton.addEventListener('click', function(event) {
            event.stopPropagation();
            removeFromCart(item.articleId);
            renderCart();
        });
        
        var quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.value = item.quantity;
        quantityInput.classList.add('form-control', 'form-control-sm', 'w-auto', 'd-inline');
        
        var updateButton = document.createElement('button');
        updateButton.textContent = 'Update';
        updateButton.classList.add('btn', 'btn-sm', 'btn-primary', 'ml-2');
        updateButton.addEventListener('click', function(event) {
            event.stopPropagation();
            updateQuantity(item.articleId, parseInt(quantityInput.value));
            renderCart();
        });
        
        var itemContainer = document.createElement('div');
        itemContainer.appendChild(quantityInput);
        itemContainer.appendChild(updateButton);
        itemContainer.appendChild(deleteButton);
        cartItem.appendChild(itemContainer);
        cartDropdown.appendChild(cartItem);
    });

    // Ajoute le bouton pour envoyer le panier vers la base de données à la fin du dropdown menu
    var sendToDatabaseBtn = document.createElement('button');
    sendToDatabaseBtn.textContent = 'Payment';
    sendToDatabaseBtn.classList.add('btn', 'btn-primary', 'dropdown-item');
    sendToDatabaseBtn.addEventListener('click', function() {
        sendCartToDatabase();
    });
    cartDropdown.appendChild(sendToDatabaseBtn);
    
    var cartItemCount = document.getElementById('cartItemCount');
    cartItemCount.textContent = cartItems.length;
}

function removeFromCart(articleId) {
    var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
    var updatedCartItems = cartItems.filter(item => item.articleId !== articleId);
    localStorage.setItem('cart', JSON.stringify(updatedCartItems));
}

function updateQuantity(articleId, newQuantity) {
    var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
    var existingItemIndex = cartItems.findIndex(item => item.articleId === articleId);
    if (existingItemIndex !== -1) {
        cartItems[existingItemIndex].quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cartItems));
    }
}

function sendCartToDatabase() {
    var cartItems = JSON.parse(localStorage.getItem('cart')) || [];

    // Envoyer les données du panier au backend via une requête POST
    fetch('/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(cartItems)
    })
    .then(response => {
        if (response.ok) {
            console.log('Le panier a été envoyé avec succès à la base de données !');
            // Optionnel : Réinitialisez le panier local ou effectuez d'autres actions nécessaires après l'envoi
            localStorage.removeItem('cart');
            renderCart(); // Réaffichez le panier pour refléter les changements
        } else {
            console.error('Erreur lors de l\'envoi du panier à la base de données.');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi du panier à la base de données :', error);
    });
}
