document.addEventListener("DOMContentLoaded", function() {
    // --- Lógica de Filtrado y Búsqueda (sin cambios) ---
    const productsTableBody = document.getElementById('inventory-table').querySelector('tbody');
    const allProductRows = Array.from(productsTableBody.querySelectorAll('tr'));
    const categoryTabs = document.querySelectorAll('#category-tabs .category-tab');
    const stockTabs = document.querySelectorAll('#stock-filter-tabs .stock-tab');
    const searchInput = document.getElementById('inventory-search-input');
    let activeCategory = 'ALL';
    let activeStockStatus = 'ALL';
    
    function filterAndSearchProducts() {
        const searchText = searchInput.value.toLowerCase().trim();
        let anyProductFound = false;

        allProductRows.forEach(row => {
            const productCategory = row.querySelector('td:nth-child(3)').textContent.toUpperCase();
            const productStockText = row.querySelector('td:nth-child(7) .status-badge').textContent.toUpperCase();
            const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const productDescription = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            
            const categoryMatch = activeCategory === 'ALL' || productCategory.includes(activeCategory);
            
            let stockMatch = false;
            if (activeStockStatus === 'ALL') {
                stockMatch = true;
            } else if (activeStockStatus === 'AVAILABLE' && productStockText === 'DISPONIBLE') {
                stockMatch = true;
            } else if (activeStockStatus === 'LOW_STOCK' && productStockText === 'BAJO STOCK') {
                stockMatch = true;
            } else if (activeStockStatus === 'OUT_OF_STOCK' && productStockText === 'SIN STOCK') {
                stockMatch = true;
            }

            const searchMatch = productName.includes(searchText) || productDescription.includes(searchText);

            if (categoryMatch && stockMatch && searchMatch) {
                row.style.display = '';
                anyProductFound = true;
            } else {
                row.style.display = 'none';
            }
        });

        const noProductsMessage = document.getElementById('no-products-message');
        if (noProductsMessage) {
            noProductsMessage.style.display = anyProductFound ? 'none' : 'block';
        }
    }

    categoryTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            categoryTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeCategory = tab.dataset.category;
            filterAndSearchProducts();
        });
    });

    stockTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            stockTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeStockStatus = tab.dataset.stockStatus;
            filterAndSearchProducts();
        });
    });

    searchInput.addEventListener('input', filterAndSearchProducts);

    // --- Lógica para Editar y Eliminar ---

    const productModal = document.getElementById('product-modal');
    const modalTitle = document.getElementById('modal-title');
    const productForm = document.getElementById('product-form');
    const closeButton = productModal.querySelector('.close-button');
    const tableBody = document.getElementById('inventory-table').querySelector('tbody');

    // Manejar clics en los botones de la tabla
    tableBody.addEventListener('click', function(event) {
        if (event.target.closest('.btn-edit')) {
            const row = event.target.closest('tr');
            const productId = row.querySelector('.btn-edit').dataset.id;
            editProduct(productId, row);
        }

        if (event.target.closest('.btn-delete')) {
            const productId = event.target.closest('.btn-delete').dataset.id;
            deleteProduct(productId);
        }
    });

    // Función para llenar el modal y editar un producto
    function editProduct(productId, row) {
        modalTitle.textContent = 'Editar Producto';
        document.getElementById('product-id').value = productId;
        document.getElementById('product-name').value = row.cells[1].textContent;
        const categoryValue = row.cells[2].textContent.toUpperCase();
        document.getElementById('product-category').value = categoryValue;
        document.getElementById('product-price').value = parseFloat(row.cells[3].textContent.replace('$', '').replace(',', ''));
        document.getElementById('product-stock').value = parseInt(row.cells[4].textContent);
        document.getElementById('product-description').value = row.cells[5].textContent;
        productModal.style.display = 'block';
    }

    // --- Lógica de envío de datos del formulario (MODIFICADA) ---
    productForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // **Crear un objeto FormData para manejar los archivos**
        const formData = new FormData(this);
        
        // **Añadir la acción y el ID al FormData**
        const productId = document.getElementById('product-id').value;
        formData.append('action', 'update');
        formData.append('id', productId);
        
        // **La categoría y otros datos ya se añaden automáticamente por FormData**

        fetch('acciones_inventario.php', {
            method: 'POST',
            body: formData  // **Enviar el objeto FormData directamente, sin JSON.stringify**
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                productModal.style.display = 'none';
                location.reload(); // Recargar la página para ver los cambios
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Función para eliminar un producto (sin cambios, ya que no envía archivos)
    function deleteProduct(productId) {
        if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            fetch('acciones_inventario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload(); // Recargar la página para ver los cambios
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Cerrar el modal
    closeButton.addEventListener('click', () => {
        productModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === productModal) {
            productModal.style.display = 'none';
        }
    });
});