const lista = document.getElementById("lista-inventario");

function mostrarInventario() {
  lista.innerHTML = "";
  inventario.forEach(p => {
    const div = document.createElement("div");
    div.classList.add("item");
    div.innerHTML = `
      <h3>${p.nombre}</h3>
      <p>Precio: $${p.precio} MXN</p>
      <p>Stock disponible: ${p.stock}</p>
    `;
    lista.appendChild(div);
  });
}

mostrarInventario();
