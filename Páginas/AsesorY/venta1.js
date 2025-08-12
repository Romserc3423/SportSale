const productosDiv = document.getElementById("productos");
const carritoBody = document.getElementById("carrito");
const totalSpan = document.getElementById("total");
const pagoCliente = document.getElementById("pagoCliente");
const cambioSpan = document.getElementById("cambio");

let carrito = [];
let total = 0;

function renderProductos() {
  productosDiv.innerHTML = "";
  inventario.forEach(p => {
    const div = document.createElement("div");
    div.className = "producto";
    div.innerHTML = `
      <h4>${p.nombre}</h4>
      <p>$${p.precio} MXN</p>
      <p>Stock: ${p.stock}</p>
      <button onclick="agregarAlCarrito(${p.id})">Agregar</button>
    `;
    productosDiv.appendChild(div);
  });
}

function agregarAlCarrito(id) {
  const producto = inventario.find(p => p.id === id);
  if (producto && producto.stock > 0) {
    producto.stock--;
    carrito.push({ id: producto.id, nombre: producto.nombre, precio: producto.precio });
    total += producto.precio;
    actualizarVista();
  } else {
    alert("Sin stock disponible");
  }
}

function quitarDelCarrito(index) {
  const item = carrito[index];
  const prodInventario = inventario.find(p => p.id === item.id);
  if (prodInventario) {
    prodInventario.stock++;
  }

  total -= item.precio;
  carrito.splice(index, 1);
  actualizarVista();
}

function actualizarVista() {
  carritoBody.innerHTML = "";
  carrito.forEach((item, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.nombre}</td>
      <td>1</td>
      <td>$${item.precio}</td>
      <td><button onclick="quitarDelCarrito(${index})"> eliminar </button></td>
    `;
    carritoBody.appendChild(row);
  });
  totalSpan.textContent = total.toFixed(2);
  calcularCambio();
  renderProductos();
}

function calcularCambio() {
  const pago = parseFloat(pagoCliente.value);
  const mensaje = document.getElementById("mensajePago");

  if (!isNaN(pago) && pago >= total) {
    cambioSpan.textContent = (pago - total).toFixed(2);
    mensaje.style.display = "none";
  } else {
    cambioSpan.textContent = "0.00";
    mensaje.style.display = "block"; 
  }
}


pagoCliente.addEventListener("input", calcularCambio);

function procesarPago() {
  const pago = parseFloat(pagoCliente.value);
  if (isNaN(pago) || pago < total) {
    alert("El pago es insuficiente");
    return;
  }

  const cambio = pago - total;
  cambioSpan.textContent = cambio.toFixed(2);
  alert("Pago procesado correctamente. Cambio: $" + cambio.toFixed(2));
}

function finalizarVenta() {
  if (carrito.length === 0) {
    alert("Carrito vacío");
    return;
  }
  alert("Venta realizada. Gracias por su compra.");
  carrito = [];
  total = 0;
  pagoCliente.value = "";
  cambioSpan.textContent = "0.00";
  actualizarVista();
}

renderProductos();

function buscarProducto() {
  const filtro = document.getElementById('busqueda').value.toLowerCase();
  const filas = document.querySelectorAll('#tablaProductos tbody tr');

  filas.forEach(fila => {
    const textoFila = fila.innerText.toLowerCase();
    fila.style.display = textoFila.includes(filtro) ? '' : 'none';
  });
}


