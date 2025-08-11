// Array para almacenar los productos del carrito
let cartItems = [];

// Función para actualizar la interfaz del carrito y los totales
function updateCartUI() {
    const cartList = document.getElementById('sale-cart-list');
    const emptyMessage = document.getElementById('empty-cart-message');
    cartList.innerHTML = ''; // Limpia la lista actual

    if (cartItems.length === 0) {
        emptyMessage.style.display = 'block';
    } else {
        emptyMessage.style.display = 'none';
        cartItems.forEach(item => {
            const li = document.createElement('li');
            li.className = 'cart-item';
            li.dataset.id = item.id;
            li.dataset.price = item.price;
            li.dataset.quantity = item.quantity;
            li.dataset.stock = item.stock;
            li.innerHTML = `
                <div class="item-details">
                    <span>${item.name}</span>
                    <span class="item-price">$${item.price.toFixed(2)}</span>
                </div>
                <div class="item-controls">
                    <input type="number" class="item-quantity-input" value="${item.quantity}" min="1" max="${item.stock}">
                    <button class="btn remove-from-cart-btn"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            cartList.appendChild(li);
        });
    }
    updateTotals();
}

// Función para añadir un producto al carrito
function addProductToCart(product) {
    const existingItem = cartItems.find(item => item.id === product.id);
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity += 1;
        } else {
            alert('No se puede añadir más stock de este producto.');
        }
    } else {
        cartItems.push({ ...product, quantity: 1 });
    }
    updateCartUI();
}

// Función para eliminar un producto del carrito
function removeProductFromCart(productId) {
    cartItems = cartItems.filter(item => item.id !== productId);
    updateCartUI();
}

// Función para actualizar los totales (subtotal, descuento, total)
function updateTotals() {
    let subtotal = cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    // Lógica de descuento si aplica
    const total = subtotal;

    document.getElementById('cart-subtotal').innerText = `$${subtotal.toFixed(2)}`;
    document.getElementById('cart-grand-total').innerText = `$${total.toFixed(2)}`;
    updateChange();
}

// Función para calcular el cambio
function updateChange() {
    const total = parseFloat(document.getElementById('cart-grand-total').innerText.replace('$', ''));
    const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
    const change = amountReceived - total;
    document.getElementById('change-due').innerText = `$${change.toFixed(2)}`;
}

// Event listener para manejar el clic en el botón de añadir al carrito
document.getElementById('product-display-grid').addEventListener('click', function(event) {
    const button = event.target.closest('.add-to-cart-btn');
    if (button) {
        const productCard = button.closest('.product-card');
        const product = {
            id: parseInt(productCard.dataset.id),
            name: productCard.dataset.name,
            price: parseFloat(productCard.dataset.price),
            stock: parseInt(productCard.dataset.stock)
        };
        addProductToCart(product);
    }
});

// Event listener para manejar el clic en el botón de eliminar del carrito
document.getElementById('sale-cart-list').addEventListener('click', function(event) {
    if (event.target.closest('.remove-from-cart-btn')) {
        const cartItem = event.target.closest('.cart-item');
        const productId = parseInt(cartItem.dataset.id);
        removeProductFromCart(productId);
    }
});

// Event listener para manejar los cambios en la cantidad de productos en el carrito
document.getElementById('sale-cart-list').addEventListener('input', function(event) {
    if (event.target.classList.contains('item-quantity-input')) {
        const input = event.target;
        const cartItem = input.closest('.cart-item');
        const productId = parseInt(cartItem.dataset.id);
        const newQuantity = parseInt(input.value);
        const productStock = parseInt(cartItem.dataset.stock);

        if (newQuantity > productStock) {
            alert(`No hay suficiente stock para este producto. Stock disponible: ${productStock}`);
            input.value = productStock;
        } else if (newQuantity < 1 || isNaN(newQuantity)) {
            input.value = 1;
        }

        const productInCart = cartItems.find(item => item.id === productId);
        if (productInCart) {
            productInCart.quantity = parseInt(input.value);
            updateTotals();
        }
    }
});

// Event listener para actualizar el cambio cuando se ingresa el monto recibido
document.getElementById('amount-received').addEventListener('input', updateChange);

// --- Funcionalidad de Búsqueda ---
const allProductCards = document.querySelectorAll('.product-card');

function filterProducts(searchTerm = '') {
    const lowerCaseSearchTerm = searchTerm.toLowerCase();

    allProductCards.forEach(card => {
        const productName = card.dataset.name.toLowerCase();
        const productId = card.dataset.id;

        if (productName.includes(lowerCaseSearchTerm) || productId.includes(lowerCaseSearchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

document.getElementById('sale-product-search-input').addEventListener('input', (e) => {
    filterProducts(e.target.value);
});


// --- Lógica de Completar Venta ---
document.getElementById('complete-sale-btn').addEventListener('click', () => {
    if (cartItems.length === 0) {
        alert('El carrito está vacío. Añade productos para completar la venta.');
        return;
    }

    const total = parseFloat(document.getElementById('cart-grand-total').innerText.replace('$', ''));
    if (total <= 0) {
        alert('El total de la venta debe ser mayor a cero.');
        return;
    }

    const saleData = {
        cartItems: cartItems.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        total: total,
        metodo_pago: document.getElementById('payment-method').value,
        id_empleado: 1, // Reemplazar con el ID del empleado logueado
        id_cliente: 1,  // Reemplazar con el ID del cliente
    };

    fetch('procesar_venta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(saleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venta completada exitosamente. ID: ' + data.id_venta);
            cartItems = [];
            updateCartUI();
            // Opcional: Redirigir a otra página o limpiar el formulario
            location.reload(); 
        } else {
            alert('Error al completar la venta: ' + data.message);
            console.error('Detalle del error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al conectar con el servidor.');
    });
});

document.getElementById('cancel-sale-btn').addEventListener('click', () => {
    if (confirm('¿Estás seguro de que quieres cancelar la venta actual?')) {
        cartItems = [];
        updateCartUI();
        // Opcional: Limpiar otros campos del formulario si es necesario
    }
});

// Cargar la interfaz del carrito al inicio
updateCartUI();