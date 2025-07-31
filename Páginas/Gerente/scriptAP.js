document.addEventListener('DOMContentLoaded', () => {
    // ... (todas tus declaraciones de variables y datos iniciales) ...

    // Reasegúrate de que estas variables estén declaradas globalmente o al principio del DOMContentLoaded
    // para que sean accesibles en todas las ramas de 'if (window.location.pathname.includes(...))'
    const productModal = document.getElementById('product-modal'); // Puede ser null en AgregarProd.html
    const closeModalBtn = productModal ? productModal.querySelector('.close-button') : null;
    const productForm = document.getElementById('product-form'); // Este es el formulario del MODAL

    // Nuevas referencias para AgregarProd.html
    const addProductFormPage = document.getElementById('add-product-form'); // El formulario de la nueva página
    const cancelAddBtn = document.getElementById('cancel-add-btn');

    // Las referencias a los inputs las vas a necesitar en ambas páginas
    const productIdInput = document.getElementById('product-id');
    const productNameInput = document.getElementById('product-name');
    const productCategoryInput = document.getElementById('product-category');
    const productPriceInput = document.getElementById('product-price');
    const productStockInput = document.getElementById('product-stock');
    const productDescriptionInput = document.getElementById('product-description');
    const modalTitle = document.getElementById('modal-title'); // Solo si el modal todavía se usa

    // ... (Tu let products, advisors, sales, etc.) ...
    // ... (Tus funciones saveToLocalStorage, formatCurrency, getStockStatus) ...

    // Lógica para el modal existente (si se usa para edición en inventario.html o manager.html)
    if (productModal && closeModalBtn && productForm) {
        closeModalBtn.addEventListener('click', () => {
            productModal.classList.remove('active');
        });
        window.addEventListener('click', (event) => {
            if (event.target == productModal) {
                productModal.classList.remove('active');
            }
        });
        // Si la función saveProduct se va a llamar desde el modal de inventario/manager para editar
        productForm.addEventListener('submit', (e) => saveProduct(e, productModal)); // Pasar el modal para cerrarlo
    }

    const openProductModal = (productId = null) => {
        // ... (Tu función openProductModal existente) ...
    };

    // Modifica saveProduct para que pueda cerrar el modal si viene de allí
    // o redirigir si viene de la página AgregarProd.html
    const saveProduct = (e, modalToClose = null) => { // Añadir un argumento para el modal
        e.preventDefault();
        const id = productIdInput.value;
        const name = productNameInput.value.trim();
        const category = productCategoryInput.value;
        const price = parseFloat(productPriceInput.value);
        const stock = parseInt(productStockInput.value);
        const description = productDescriptionInput.value.trim();

        if (!name || !category || isNaN(price) || isNaN(stock) || price < 0 || stock < 0) {
            alert('Por favor, rellena todos los campos correctamente. Nombre, Categoría, Precio y Stock son requeridos y deben ser válidos.');
            return;
        }

        if (id) {
            const productIndex = products.findIndex(p => p.id == id);
            if (productIndex > -1) {
                products[productIndex] = { id: parseInt(id), name, category, price, stock, description };
            }
            alert('Producto actualizado exitosamente.');
        } else {
            const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
            products.push({ id: newId, name, category, price, stock, description });
            alert('Producto añadido exitosamente.');
        }
        saveToLocalStorage();

        // Lógica de redirección o cierre de modal después de guardar
        if (modalToClose) { // Si viene del modal (editar)
            modalToClose.classList.remove('active');
        } else if (window.location.pathname.includes('AgregarProd.html')) {
            // Si viene de la página de añadir, redirigir de vuelta al inventario
            window.location.href = 'inventario.html';
        } else if (window.location.pathname.includes('manager.html')) {
            // Si estás en el panel de gerente y actualizaste/añadiste desde su modal
            const inventoryView = document.getElementById('inventory-view');
            if (inventoryView && inventoryView.classList.contains('active')) {
                renderInventoryTableForManager(document.querySelector('#inventory-view #inventory-table tbody'));
            }
            if (productModal) productModal.classList.remove('active'); // Cerrar el modal del gerente
        } else if (window.location.pathname.includes('inventario.html')) {
            renderInventory(); // Para la página de inventario, si aún se usa el modal allí
        }
    };

    // ... (Tu función deleteProduct) ...

    if (window.location.pathname.includes('manager.html')) {
        // ... (Tu lógica existente para manager.html) ...

        const managerAddProductBtn = document.getElementById('add-product-btn'); // Asumo que también hay uno en manager.html
        if (managerAddProductBtn) {
            managerAddProductBtn.addEventListener('click', () => openProductModal()); // El gerente seguirá usando el modal
        }
        // ... (el resto de manager.html) ...
    }

    if (window.location.pathname.includes('inventario.html')) {
        const inventoryTableBody = document.querySelector('#inventory-table tbody');
        const addProductBtn = document.getElementById('add-product-btn');

        // *** CAMBIO CLAVE: Redirigir en lugar de abrir modal ***
        if (addProductBtn) {
            addProductBtn.addEventListener('click', () => {
                window.location.href = 'AgregarProd.html';
            });
        }
        // ... (El resto de la lógica de inventario.html, como los filtros, búsqueda, y renderInventory) ...
        renderInventory();
    }

    // Lógica específica para la nueva página AgregarProd.html
    if (window.location.pathname.includes('AgregarProd.html')) {
        if (addProductFormPage) {
            addProductFormPage.addEventListener('submit', saveProduct); // Llama a saveProduct para guardar
        }
        if (cancelAddBtn) {
            cancelAddBtn.addEventListener('click', () => {
                window.location.href = 'inventario.html'; // Volver sin guardar
            });
        }
    }

    // ... (Tu lógica para generar-venta.html) ...

});