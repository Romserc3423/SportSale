// comprar.js
document.addEventListener('DOMContentLoaded', () => {
  const addButtons = Array.from(document.querySelectorAll('.add-to-cart-btn'));
  const cartList = document.getElementById('cart-items-list');
  const emptyCartMsg = document.getElementById('empty-cart');
  const subtotalEl = document.getElementById('subtotal');
  const discountEl = document.getElementById('discount');
  const totalEl = document.getElementById('total');
  const paymentMethod = document.getElementById('payment-method');
  const amountReceived = document.getElementById('amount-received');
  const changeEl = document.getElementById('change');
  const checkoutBtn = document.getElementById('checkout-btn');
  const clearCartBtn = document.getElementById('clear-cart-btn');
  const searchInput = document.getElementById('search-input');

  let cart = []; // {id, name, price, quantity, stock, image}

  // helper
  const currency = (v) => '$' + Number(v).toFixed(2);

  function updateCartUI() {
    cartList.innerHTML = '';
    if (cart.length === 0) {
      emptyCartMsg.style.display = 'block';
      checkoutBtn.disabled = true;
    } else {
      emptyCartMsg.style.display = 'none';
      checkoutBtn.disabled = false;
    }

    let subtotal = 0;
    cart.forEach(item => {
      subtotal += item.price * item.quantity;
      const li = document.createElement('li');
      li.className = 'cart-item';
      li.innerHTML = `
        <div class="cart-item-left">
          <img src="${item.image}" alt="${item.name}">
        </div>
        <div class="cart-item-body">
          <strong>${item.name}</strong>
          <div class="cart-item-meta">${currency(item.price)} x 
            <input type="number" class="cart-qty" min="1" max="${item.stock}" value="${item.quantity}" data-id="${item.id}">
            <button class="remove-item" data-id="${item.id}">Eliminar</button>
          </div>
        </div>
        <div class="cart-item-right">${currency(item.price * item.quantity)}</div>
      `;
      cartList.appendChild(li);
    });

    // simple discount logic placeholder (0)
    const discount = 0;
    const total = subtotal - discount;

    subtotalEl.textContent = currency(subtotal);
    discountEl.textContent = currency(discount);
    totalEl.textContent = currency(total);

    // update change
    const received = parseFloat(amountReceived.value) || 0;
    changeEl.textContent = currency(Math.max(0, received - total));
  }

  // add to cart
  addButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const card = e.target.closest('.product-card');
      const id = parseInt(card.dataset.id);
      const name = card.dataset.name;
      const price = parseFloat(card.dataset.price);
      const stock = parseInt(card.dataset.stock);
      const image = card.dataset.image;
      const qtyInput = card.querySelector('.qty-input');
      const qty = Math.max(1, Math.min(stock, parseInt(qtyInput.value) || 1));

      const existing = cart.find(c => c.id === id);
      if (existing) {
        existing.quantity = Math.min(existing.stock, existing.quantity + qty);
      } else {
        cart.push({id, name, price, quantity: qty, stock, image});
      }
      updateCartUI();
    });
  });

  // delegate quantity change and remove
  cartList.addEventListener('input', (e) => {
    if (e.target.matches('.cart-qty')) {
      const id = parseInt(e.target.dataset.id);
      const qty = Math.max(1, Math.min(parseInt(e.target.max), parseInt(e.target.value) || 1));
      const item = cart.find(x => x.id === id);
      if (item) {
        item.quantity = qty;
        updateCartUI();
      }
    }
  });

  cartList.addEventListener('click', (e) => {
    if (e.target.matches('.remove-item')) {
      const id = parseInt(e.target.dataset.id);
      cart = cart.filter(x => x.id !== id);
      updateCartUI();
    }
  });

  // clear cart
  clearCartBtn.addEventListener('click', () => {
    if (confirm('¿Vaciar carrito?')) {
      cart = [];
      updateCartUI();
    }
  });

  // amount received change -> change calc
  amountReceived.addEventListener('input', () => updateCartUI());

  // checkout
  checkoutBtn.addEventListener('click', async () => {
    if (cart.length === 0) {
      alert('El carrito está vacío.');
      return;
    }
    const subtotal = cart.reduce((s,i)=> s + i.price*i.quantity, 0);
    const discount = 0;
    const total = subtotal - discount;

    const payload = {
      cartItems: cart.map(i=>({id:i.id, name:i.name, price:i.price, quantity:i.quantity})),
      total,
      metodo_pago: paymentMethod.value,
      id_cliente: (typeof CLIENTE_ID === 'number') ? CLIENTE_ID : null
    };

    checkoutBtn.disabled = true;
    checkoutBtn.textContent = 'Procesando...';

    try {
      const res = await fetch('procesar_compra.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      const result = await res.json();
      if (result.success) {
        alert('Compra exitosa. ID venta: ' + (result.id_venta || result.idVenta || 'N/A'));
        cart = [];
        updateCartUI();
      } else {
        alert('Error: ' + (result.message || 'Error desconocido'));
      }
    } catch (err) {
      console.error(err);
      alert('Error de conexión al procesar la compra.');
    } finally {
      checkoutBtn.disabled = false;
      checkoutBtn.textContent = 'Comprar ahora';
    }
  });

  // simple search filter
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.trim().toLowerCase();
      const cards = document.querySelectorAll('.product-card');
      cards.forEach(c => {
        const name = c.dataset.name.toLowerCase();
        const id = c.dataset.id;
        if (name.includes(q) || id.includes(q) || q === '') {
          c.style.display = 'block';
        } else c.style.display = 'none';
      });
    });
  }

  // initial UI update
  updateCartUI();
});
