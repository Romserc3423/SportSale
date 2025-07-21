document.addEventListener('DOMContentLoaded', () => {

    
    const productModal = document.getElementById('product-modal');
    const closeModalBtn = productModal ? productModal.querySelector('.close-button') : null;
    const productForm = document.getElementById('product-form');
    const modalTitle = document.getElementById('modal-title');
    const productIdInput = document.getElementById('product-id');
    const productNameInput = document.getElementById('product-name');
    const productCategoryInput = document.getElementById('product-category');
    const productPriceInput = document.getElementById('product-price');
    const productStockInput = document.getElementById('product-stock');
    const productDescriptionInput = document.getElementById('product-description');

    let products = JSON.parse(localStorage.getItem('products')) || [
        { id: 1, name: 'Zapatillas Ultraboost Pro', category: 'CALZADO', price: 120.00, stock: 15, description: 'Comodidad y rendimiento para tus carreras diarias.' },
        { id: 2, name: 'Camiseta Dry-Fit', category: 'ROPA', price: 35.00, stock: 50, description: 'Tecnología transpirable para tus entrenamientos.' },
        { id: 3, name: 'Short Deportivo Ligero', category: 'ROPA', price: 25.00, stock: 20, description: 'Ideal para el gimnasio y actividades al aire libre.' },
        { id: 4, name: 'Mochila SportPack', category: 'ACCESORIOS', price: 50.00, stock: 10, description: 'Amplia y resistente para llevar todo tu equipo.' },
        { id: 5, name: 'Balón de Fútbol Pro', category: 'DEPORTES', price: 45.00, stock: 30, description: 'El balón favorito de nuestros clientes, alta durabilidad.' },
        { id: 6, name: 'Guantes de Levantamiento', category: 'ACCESORIOS', price: 22.00, stock: 5, description: 'Agarre y protección para tus manos.' },
        { id: 7, name: 'Proteina Whey 2kg', category: 'SUPLEMENTOS', price: 60.00, stock: 8, description: 'Suplemento de proteína para recuperación muscular.' },
        { id: 8, name: 'Smartwatch Deportivo', category: 'TECNOLOGIA', price: 200.00, stock: 3, description: 'Monitoriza tu actividad y rendimiento.' },
        { id: 9, name: 'Raqueta de Tenis Avanzada', category: 'EQUIPOS', price: 90.00, stock: 2, description: 'Control y potencia para jugadores exigentes.' },
        { id: 10, name: 'Calcetines Transpirables (3 pares)', category: 'ROPA', price: 15.00, stock: 100, description: 'Máxima comodidad para cualquier deporte.' },
        { id: 11, name: 'Botella de Agua Deportiva', category: 'ACCESORIOS', price: 10.00, stock: 0, description: 'Mantente hidratado en todo momento.' }
    ];

    let advisors = JSON.parse(localStorage.getItem('advisors')) || [
        { id: 101, name: 'Ana García', username: 'ana.g', salesCount: 0, lastLogin: '2025-07-13' },
        { id: 102, name: 'Carlos Ruiz', username: 'carlos.r', salesCount: 0, lastLogin: '2025-07-12' }
    ];

    const today = new Date().toISOString().slice(0, 10);
    let sales = JSON.parse(localStorage.getItem('sales')) || [];
    if (!sales.find(s => s.date === today)) {
        sales.push({ date: today, transactions: [] });
        localStorage.setItem('sales', JSON.stringify(sales));
    }

    let currentSaleCart = JSON.parse(sessionStorage.getItem('currentSaleCart')) || [];

    const saveToLocalStorage = () => {
        localStorage.setItem('products', JSON.stringify(products));
        localStorage.setItem('advisors', JSON.stringify(advisors));
        localStorage.setItem('sales', JSON.stringify(sales));
    };

    const saveSaleCartToSessionStorage = () => {
        sessionStorage.setItem('currentSaleCart', JSON.stringify(currentSaleCart));
    };

    const formatCurrency = (amount) => {
        return `$ ${parseFloat(amount).toFixed(2)}`;
    };

    const getStockStatus = (stock) => {
        if (stock === 0) return 'Sin Stock';
        if (stock <= 5) return 'Bajo Stock';
        return 'Disponible';
    };

    if (productModal && closeModalBtn && productForm) {
        closeModalBtn.addEventListener('click', () => {
            productModal.classList.remove('active');
        });

        window.addEventListener('click', (event) => {
            if (event.target == productModal) {
                productModal.classList.remove('active');
            }
        });

        productForm.addEventListener('submit', (e) => saveProduct(e));
    }

    const openProductModal = (productId = null) => {
        productForm.reset();
        productIdInput.value = '';

        if (productId) {
            modalTitle.textContent = 'Editar Producto';
            const product = products.find(p => p.id == productId);
            if (product) {
                productIdInput.value = product.id;
                productNameInput.value = product.name;
                productCategoryInput.value = product.category || '';
                productPriceInput.value = product.price;
                productStockInput.value = product.stock;
                productDescriptionInput.value = product.description || '';
            }
        } else {
            modalTitle.textContent = 'Añadir Nuevo Producto';
        }
        if (productModal) {
            productModal.classList.add('active');
        }
    };

    const saveProduct = (e) => {
        e.preventDefault();
        const id = productIdInput.value;
        const name = productNameInput.value.trim();
        const category = productCategoryInput.value;
        const price = parseFloat(productPriceInput.value);
        const stock = parseInt(productStockInput.value);
        const description = productDescriptionInput.value.trim();

        if (!name || !category || isNaN(price) || isNaN(stock) || price < 0 || stock < 0) {
            alert('Por favor, rellena todos los campos correctamente. Categoría, Precio y Stock son requeridos y deben ser válidos.');
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

        if (window.location.pathname.includes('inventario.html')) {
            renderInventory();
        } else if (window.location.pathname.includes('manager.html')) {
            const inventoryView = document.getElementById('inventory-view');
            if (inventoryView && inventoryView.classList.contains('active')) {
                renderInventoryTableForManager(document.querySelector('#inventory-view #inventory-table tbody'));
            }
        } else if (window.location.pathname.includes('generar-venta.html')) {
            renderSaleProducts();
        }

        if (productModal) {
            productModal.classList.remove('active');
        }
    };

    const deleteProduct = (productId) => {
        if (confirm(`¿Estás seguro de que quieres eliminar el producto con ID ${productId}?`)) {
            products = products.filter(p => p.id != productId);
            saveToLocalStorage();
            if (window.location.pathname.includes('inventario.html')) {
                renderInventory();
            } else if (window.location.pathname.includes('manager.html')) {
                const inventoryView = document.getElementById('inventory-view');
                if (inventoryView && inventoryView.classList.contains('active')) {
                    renderInventoryTableForManager(document.querySelector('#inventory-view #inventory-table tbody'));
                }
            } else if (window.location.pathname.includes('generar-venta.html')) {
                renderSaleProducts();
            }
            alert('Producto eliminado.');
        }
    };

    if (window.location.pathname.includes('manager.html')) {
        const navLinks = document.querySelectorAll('.sidebar-nav ul li a');
        const contentSections = document.querySelectorAll('.content-section');

        const managerAddProductBtn = document.getElementById('add-product-btn');
        if (managerAddProductBtn) {
            managerAddProductBtn.addEventListener('click', () => openProductModal());
        }
        const inventoryTableBodyManager = document.querySelector('#inventory-view #inventory-table tbody');

        const dailySalesValue = document.getElementById('daily-sales-value');
        const productsSoldValue = document.getElementById('products-sold-value');
        const transactionsValue = document.getElementById('transactions-value');

        const advisorsTableBody = document.querySelector('#advisors-table tbody');

        const downloadDailyReportBtn = document.getElementById('download-daily-report-btn');
        const advisorSelect = document.getElementById('advisor-select');
        const downloadAdvisorReportBtn = document.getElementById('download-advisor-report-btn');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                navLinks.forEach(nav => nav.classList.remove('active'));
                link.classList.add('active');

                const targetId = link.id.replace('nav-', '');
                contentSections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === `${targetId}-view`) {
                        section.classList.add('active');
                    }
                });

                if (targetId === 'dashboard') {
                    renderDashboard();
                } else if (targetId === 'inventory') {
                    window.location.href = 'inventario.html';
                } else if (targetId === 'sell') {
                    window.location.href = 'generar-venta.html';
                } else if (targetId === 'advisors') {
                    renderAdvisors();
                } else if (targetId === 'reports') {
                    populateAdvisorSelect();
                } else if (targetId === 'logout') {
                    alert('Cerrando sesión... (simulado)');
                    window.location.href = 'index.html';
                }
            });
        });

        const renderDashboard = () => {
            const todaySales = sales.find(s => s.date === today);
            let totalDailySales = 0;
            let totalProductsSold = 0;
            let totalTransactions = 0;

            if (todaySales) {
                totalTransactions = todaySales.transactions.length;
                todaySales.transactions.forEach(transaction => {
                    totalDailySales += transaction.total;
                    transaction.items.forEach(item => {
                        totalProductsSold += item.quantity;
                    });
                });
            }
            dailySalesValue.textContent = formatCurrency(totalDailySales);
            productsSoldValue.textContent = totalProductsSold;
            transactionsValue.textContent = totalTransactions;
        };

        const renderInventoryTableForManager = (tableBodyElement) => {
            if (!tableBodyElement) return;
            tableBodyElement.innerHTML = '';
            products.forEach(product => {
                const row = tableBodyElement.insertRow();
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.category || 'N/A'}</td>
                    <td>${formatCurrency(product.price)}</td>
                    <td>${product.stock}</td>
                    <td class="action-buttons">
                        <button class="edit-btn" data-id="${product.id}"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn" data-id="${product.id}"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
            });
            tableBodyElement.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', (e) => openProductModal(e.currentTarget.dataset.id));
            });
            tableBodyElement.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', (e) => deleteProduct(e.currentTarget.dataset.id));
            });
        };

        const renderAdvisors = () => {
            advisorsTableBody.innerHTML = '';
            advisors.forEach(advisor => {
                const row = advisorsTableBody.insertRow();
                row.innerHTML = `
                    <td>${advisor.id}</td>
                    <td>${advisor.name}</td>
                    <td>${advisor.username}</td>
                    <td>${advisor.salesCount}</td>
                    <td>${advisor.lastLogin}</td>
                `;
            });
        };

        const populateAdvisorSelect = () => {
            advisorSelect.innerHTML = '<option value="">Seleccionar Asesor</option>';
            advisors.forEach(advisor => {
                const option = document.createElement('option');
                option.value = advisor.id;
                option.textContent = advisor.name;
                advisorSelect.appendChild(option);
            });
            downloadAdvisorReportBtn.disabled = !advisorSelect.value;
        };

        advisorSelect.addEventListener('change', () => {
            downloadAdvisorReportBtn.disabled = !advisorSelect.value;
        });

        const downloadReport = (reportType, data, filename) => {
            let csvContent = '';
            if (reportType === 'daily') {
                csvContent += 'Fecha,ID Transaccion,Items,Cantidad,Precio Unitario,Total\n';
                data.forEach(saleDay => {
                    saleDay.transactions.forEach(trans => {
                        trans.items.forEach(item => {
                            csvContent += `${saleDay.date},${trans.id},"${item.name}",${item.quantity},${item.price},${item.quantity * item.price}\n`;
                        });
                    });
                });
            } else if (reportType === 'advisor') {
                csvContent += 'Fecha,ID Transaccion,Items,Cantidad,Precio Unitario,Total\n';
                data.transactions.forEach(trans => {
                    trans.items.forEach(item => {
                        csvContent += `${trans.date},${trans.id},"${item.name}",${item.quantity},${item.price},${item.quantity * item.price}\n`;
                    });
                });
            }

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                alert('Reporte descargado!');
            }
        };

        downloadDailyReportBtn.addEventListener('click', () => {
            const dailySalesData = sales.filter(s => s.date === today);
            if (dailySalesData.length > 0 && dailySalesData[0].transactions.length > 0) {
                downloadReport('daily', dailySalesData, `reporte_ventas_dia_${today}.csv`);
            } else {
                alert('No hay ventas registradas para hoy.');
            }
        });

        downloadAdvisorReportBtn.addEventListener('click', () => {
            const selectedAdvisorId = parseInt(advisorSelect.value);
            if (selectedAdvisorId) {
                const advisorSales = sales.reduce((acc, saleDay) => {
                    saleDay.transactions.forEach(trans => {
                        if (trans.advisorId == selectedAdvisorId) {
                            acc.transactions.push(trans);
                        }
                    });
                    return acc;
                }, { transactions: [] });

                if (advisorSales.transactions.length > 0) {
                    const advisorName = advisors.find(a => a.id === selectedAdvisorId)?.name || 'Desconocido';
                    downloadReport('advisor', advisorSales, `reporte_ventas_${advisorName.replace(' ', '_')}.csv`);
                } else {
                    alert('No hay ventas registradas para este asesor.');
                }
            } else {
                alert('Por favor, selecciona un asesor.');
            }
        });

        renderDashboard();
    }

    if (window.location.pathname.includes('inventario.html')) {
        const inventoryTableBody = document.querySelector('#inventory-table tbody');
        const addProductBtn = document.getElementById('add-product-btn');
        const categoryTabs = document.querySelectorAll('.category-tab');
        const stockTabs = document.querySelectorAll('.stock-tab');
        const inventorySearchInput = document.getElementById('inventory-search-input');
        const inventorySearchButton = document.getElementById('inventory-search-button');
        const noProductsMessage = document.getElementById('no-products-message');

        let activeCategory = 'ALL';
        let activeStockStatus = 'ALL';

        categoryTabs.forEach(button => {
            button.addEventListener('click', (e) => {
                categoryTabs.forEach(btn => btn.classList.remove('active'));
                e.currentTarget.classList.add('active');
                activeCategory = e.currentTarget.dataset.category;
                renderInventory();
            });
        });

        stockTabs.forEach(button => {
            button.addEventListener('click', (e) => {
                stockTabs.forEach(btn => btn.classList.remove('active'));
                e.currentTarget.classList.add('active');
                activeStockStatus = e.currentTarget.dataset.stockStatus;
                renderInventory();
            });
        });

        inventorySearchButton.addEventListener('click', () => renderInventory());
        inventorySearchInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter' || inventorySearchInput.value.trim() === '') {
                renderInventory();
            }
        });

        if (addProductBtn) {
            addProductBtn.addEventListener('click', () => openProductModal());
        }

        const renderInventory = () => {
            inventoryTableBody.innerHTML = '';
            noProductsMessage.classList.add('hidden');

            const searchTerm = inventorySearchInput.value.toLowerCase().trim();

            const filteredProducts = products.filter(product => {
                const matchesCategory = (activeCategory === 'ALL' || product.category === activeCategory);
                const matchesStockStatus = (activeStockStatus === 'ALL' || getStockStatus(product.stock) === activeStockStatus || (activeStockStatus === 'AVAILABLE' && product.stock > 0));
                const matchesSearchTerm = (searchTerm === '' ||
                                            product.name.toLowerCase().includes(searchTerm) ||
                                            (product.description && product.description.toLowerCase().includes(searchTerm)) ||
                                            product.category.toLowerCase().includes(searchTerm));

                return matchesCategory && matchesStockStatus && matchesSearchTerm;
            });

            if (filteredProducts.length === 0) {
                noProductsMessage.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach(product => {
                const row = inventoryTableBody.insertRow();
                const stockStatusText = getStockStatus(product.stock);
                let stockStatusClass = '';
                if (stockStatusText === 'Sin Stock') stockStatusClass = 'status-out-of-stock';
                else if (stockStatusText === 'Bajo Stock') stockStatusClass = 'status-low-stock';
                else stockStatusClass = 'status-available';

                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.category || 'N/A'}</td>
                    <td>${formatCurrency(product.price)}</td>
                    <td>${product.stock}</td>
                    <td>${product.description || ''}</td>
                    <td class="${stockStatusClass}">${stockStatusText}</td>
                    <td class="action-buttons">
                        <button class="edit-btn" data-id="${product.id}"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn" data-id="${product.id}"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', (e) => openProductModal(e.currentTarget.dataset.id));
            });
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', (e) => deleteProduct(e.currentTarget.dataset.id));
            });
        };

        renderInventory();
    }

    if (window.location.pathname.includes('generar-venta.html')) {
        const saleProductSearchInput = document.getElementById('sale-product-search-input');
        const saleSearchBtn = document.getElementById('sale-search-btn');
        const categorySaleButtons = document.querySelectorAll('.category-sale-btn');
        const productDisplayGrid = document.getElementById('product-display-grid');
        const noProductsMessageSale = document.getElementById('no-products-message-sale');

        const saleCartList = document.getElementById('sale-cart-list');
        const cartSubtotalSpan = document.getElementById('cart-subtotal');
        const cartDiscountSpan = document.getElementById('cart-discount');
        const cartGrandTotalSpan = document.getElementById('cart-grand-total');
        const paymentMethodSelect = document.getElementById('payment-method');
        const amountReceivedInput = document.getElementById('amount-received');
        const changeDueSpan = document.getElementById('change-due');
        const completeSaleBtn = document.getElementById('complete-sale-btn');
        const cancelSaleBtn = document.getElementById('cancel-sale-btn');

        let activeSaleCategory = 'ALL';

        saleSearchBtn.addEventListener('click', () => renderSaleProducts());
        saleProductSearchInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter' || saleProductSearchInput.value.trim() === '') {
                renderSaleProducts();
            }
        });

        categorySaleButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                categorySaleButtons.forEach(btn => btn.classList.remove('active'));
                e.currentTarget.classList.add('active');
                activeSaleCategory = e.currentTarget.dataset.category;
                renderSaleProducts();
            });
        });

        const renderSaleProducts = () => {
            productDisplayGrid.innerHTML = '';
            noProductsMessageSale.classList.add('hidden');
            const searchTerm = saleProductSearchInput.value.toLowerCase().trim();

            const filteredProducts = products.filter(p =>
                p.stock > 0 &&
                (activeSaleCategory === 'ALL' || p.category === activeSaleCategory) &&
                (p.name.toLowerCase().includes(searchTerm) || (p.description && p.description.toLowerCase().includes(searchTerm)) || p.id.toString().includes(searchTerm))
            );

            if (filteredProducts.length === 0) {
                noProductsMessageSale.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card-sale';
                card.dataset.id = product.id;
                card.innerHTML = `
                    <img src="https://via.placeholder.com/120x80?text=${encodeURIComponent(product.name.split(' ')[0])}" alt="${product.name}">
                    <h4>${product.name}</h4>
                    <p>${formatCurrency(product.price)} | Stock: ${product.stock}</p>
                    <button class="add-to-cart-btn" data-id="${product.id}" ${product.stock === 0 ? 'disabled' : ''}>Añadir al Carrito</button>
                `;
                productDisplayGrid.appendChild(card);
            });

            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', (e) => addProductToSaleCart(parseInt(e.currentTarget.dataset.id)));
            });
        };

        const addProductToSaleCart = (productId) => {
            const product = products.find(p => p.id === productId);
            if (!product || product.stock <= 0) {
                alert('Producto no disponible o sin stock.');
                return;
            }

            const existingItem = currentSaleCart.find(item => item.id === productId);
            if (existingItem) {
                if (existingItem.quantity < product.stock) {
                    existingItem.quantity++;
                } else {
                    alert('No hay más stock de este producto disponible.');
                    return;
                }
            } else {
                currentSaleCart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            }
            saveSaleCartToSessionStorage();
            renderSaleCart();
            renderSaleProducts();
        };

        const updateCartItemQuantity = (productId, change) => {
            const itemIndex = currentSaleCart.findIndex(item => item.id === productId);
            if (itemIndex > -1) {
                const product = products.find(p => p.id === productId);
                if (!product) return;

                currentSaleCart[itemIndex].quantity += change;

                if (currentSaleCart[itemIndex].quantity <= 0) {
                    currentSaleCart.splice(itemIndex, 1);
                } else if (currentSaleCart[itemIndex].quantity > product.stock) {
                    currentSaleCart[itemIndex].quantity = product.stock;
                    alert('No hay más stock de este producto.');
                }
            }
            saveSaleCartToSessionStorage();
            renderSaleCart();
            renderSaleProducts();
        };

        const removeCartItem = (productId) => {
            if (confirm('¿Estás seguro de que quieres quitar este producto del carrito?')) {
                currentSaleCart = currentSaleCart.filter(item => item.id !== productId);
                saveSaleCartToSessionStorage();
                renderSaleCart();
                renderSaleProducts();
            }
        };

        const renderSaleCart = () => {
            saleCartList.innerHTML = '';
            let subtotal = 0;
            const discount = 0;
            let grandTotal = 0;

            if (currentSaleCart.length === 0) {
                saleCartList.innerHTML = '<li class="empty-cart-message">El carrito está vacío.</li>';
            } else {
                currentSaleCart.forEach(item => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <div class="cart-item-details">
                            <span class="item-name">${item.name}</span>
                            <span class="item-qty-price">${item.quantity} x ${formatCurrency(item.price)}</span>
                        </div>
                        <div class="cart-item-controls">
                            <button data-id="${item.id}" class="qty-minus-btn">-</button>
                            <span>${item.quantity}</span>
                            <button data-id="${item.id}" class="qty-plus-btn">+</button>
                            <button data-id="${item.id}" class="remove-item-btn"><i class="fas fa-trash-alt"></i></button>
                        </div>
                        <span class="item-subtotal">${formatCurrency(item.price * item.quantity)}</span>
                    `;
                    saleCartList.appendChild(li);
                    subtotal += item.price * item.quantity;
                });
            }

            grandTotal = subtotal - discount;

            cartSubtotalSpan.textContent = formatCurrency(subtotal);
            cartDiscountSpan.textContent = formatCurrency(discount);
            cartGrandTotalSpan.textContent = formatCurrency(grandTotal);

            calculateChange();

            saleCartList.querySelectorAll('.qty-minus-btn').forEach(button => {
                button.addEventListener('click', (e) => updateCartItemQuantity(parseInt(e.currentTarget.dataset.id), -1));
            });
            saleCartList.querySelectorAll('.qty-plus-btn').forEach(button => {
                button.addEventListener('click', (e) => updateCartItemQuantity(parseInt(e.currentTarget.dataset.id), 1));
            });
            saleCartList.querySelectorAll('.remove-item-btn').forEach(button => {
                button.addEventListener('click', (e) => removeCartItem(parseInt(e.currentTarget.dataset.id)));
            });
        };

        const calculateChange = () => {
            const grandTotal = parseFloat(cartGrandTotalSpan.textContent.replace('$', ''));
            const amountReceived = parseFloat(amountReceivedInput.value) || 0;
            const change = amountReceived - grandTotal;
            changeDueSpan.textContent = formatCurrency(change);
            changeDueSpan.style.color = change >= 0 ? 'green' : 'red';
            completeSaleBtn.disabled = change < 0 || currentSaleCart.length === 0;
        };

        amountReceivedInput.addEventListener('input', calculateChange);

        completeSaleBtn.addEventListener('click', () => {
            if (currentSaleCart.length === 0) {
                alert('El carrito de venta está vacío.');
                return;
            }

            const grandTotal = parseFloat(cartGrandTotalSpan.textContent.replace('$', ''));
            const amountReceived = parseFloat(amountReceivedInput.value) || 0;

            if (amountReceived < grandTotal) {
                alert('El monto recibido es insuficiente.');
                return;
            }

            const paymentMethod = paymentMethodSelect.value;
            if (!paymentMethod) {
                alert('Por favor, selecciona un método de pago.');
                return;
            }

            const currentSalesDay = sales.find(s => s.date === today);
            if (!currentSalesDay) {
                sales.push({ date: today, transactions: [] });
            }

            const transactionId = currentSalesDay.transactions.length > 0 ? Math.max(...currentSalesDay.transactions.map(t => t.id)) + 1 : 1;
            const newTransaction = {
                id: transactionId,
                date: today,
                items: currentSaleCart,
                total: grandTotal,
                paymentMethod: paymentMethod,
                amountReceived: amountReceived,
                change: amountReceived - grandTotal,
                advisorId: 101 // Esto debería ser dinámico basado en el asesor logueado
            };

            currentSalesDay.transactions.push(newTransaction);

            currentSaleCart.forEach(cartItem => {
                const product = products.find(p => p.id === cartItem.id);
                if (product) {
                    product.stock -= cartItem.quantity;
                }
            });

            const currentAdvisor = advisors.find(a => a.id === newTransaction.advisorId);
            if (currentAdvisor) {
                currentAdvisor.salesCount++;
            }

            saveToLocalStorage();
            sessionStorage.removeItem('currentSaleCart');
            currentSaleCart = [];

            alert('¡Venta completada exitosamente!');
            renderSaleCart();
            renderSaleProducts();
            amountReceivedInput.value = '';
            paymentMethodSelect.value = '';
            calculateChange();
        });

        cancelSaleBtn.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que quieres cancelar la venta actual? Se perderán todos los productos en el carrito.')) {
                sessionStorage.removeItem('currentSaleCart');
                currentSaleCart = [];
                renderSaleCart();
                renderSaleProducts();
                amountReceivedInput.value = '';
                paymentMethodSelect.value = '';
                calculateChange();
                alert('Venta cancelada.');
            }
        });

        renderSaleProducts();
        renderSaleCart();
    }
});