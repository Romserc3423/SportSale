document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.sidebar-nav ul li a');
    const contentSections = document.querySelectorAll('.content-section');

    const inventoryTableBody = document.querySelector('#inventory-table tbody');
    const addProductBtn = document.getElementById('add-product-btn');
    const productModal = document.getElementById('product-modal');
    const closeModalBtn = productModal.querySelector('.close-button');
    const productForm = document.getElementById('product-form');
    const modalTitle = document.getElementById('modal-title');
    const productIdInput = document.getElementById('product-id');
    const productNameInput = document.getElementById('product-name');
    const productPriceInput = document.getElementById('product-price');
    const productStockInput = document.getElementById('product-stock');
    const productDescriptionInput = document.getElementById('product-description');

    const saleSearchInput = document.getElementById('sale-search-input');
    const addToSaleBtn = document.getElementById('add-to-sale-btn');
    const saleCartList = document.getElementById('sale-cart-list');
    const saleTotalSpan = document.getElementById('sale-total');
    const completeSaleBtn = document.getElementById('complete-sale-btn');
    const saleProductDisplay = document.getElementById('sale-product-display');

    const advisorsTableBody = document.querySelector('#advisors-table tbody');

    const dailySalesValue = document.getElementById('daily-sales-value');
    const productsSoldValue = document.getElementById('products-sold-value');
    const transactionsValue = document.getElementById('transactions-value');
    const downloadDailyReportBtn = document.getElementById('download-daily-report-btn');
    const advisorSelect = document.getElementById('advisor-select');
    const downloadAdvisorReportBtn = document.getElementById('download-advisor-report-btn');

    let products = JSON.parse(localStorage.getItem('products')) || [
        { id: 1, name: 'Zapatillas Ultraboost Pro', price: 120.00, stock: 15, description: 'Comodidad y rendimiento para tus carreras diarias.' },
        { id: 2, name: 'Camiseta Dry-Fit', price: 35.00, stock: 50, description: 'Tecnología transpirable para tus entrenamientos.' },
        { id: 3, name: 'Short Deportivo Ligero', price: 25.00, stock: 20, description: 'Ideal para el gimnasio y actividades al aire libre.' },
        { id: 4, name: 'Mochila SportPack', price: 50.00, stock: 10, description: 'Amplia y resistente para llevar todo tu equipo.' },
        { id: 5, name: 'Balón de Fútbol Pro', price: 45.00, stock: 30, description: 'El balón favorito de nuestros clientes, alta durabilidad.' }
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

    let currentSaleCart = [];

    const saveToLocalStorage = () => {
        localStorage.setItem('products', JSON.stringify(products));
        localStorage.setItem('advisors', JSON.stringify(advisors));
        localStorage.setItem('sales', JSON.stringify(sales));
    };

    const formatCurrency = (amount) => {
        return `$ ${parseFloat(amount).toFixed(2)}`;
    };

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
                renderInventory();
            } else if (targetId === 'sell') {
                renderSaleProducts();
                renderSaleCart();
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

    const renderInventory = () => {
        inventoryTableBody.innerHTML = '';
        products.forEach(product => {
            const row = inventoryTableBody.insertRow();
            row.innerHTML = `
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${formatCurrency(product.price)}</td>
                <td>${product.stock}</td>
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

    const openProductModal = (productId = null) => {
        productForm.reset();
        productIdInput.value = '';

        if (productId) {
            modalTitle.textContent = 'Editar Producto';
            const product = products.find(p => p.id == productId);
            if (product) {
                productIdInput.value = product.id;
                productNameInput.value = product.name;
                productPriceInput.value = product.price;
                productStockInput.value = product.stock;
                productDescriptionInput.value = product.description || '';
            }
        } else {
            modalTitle.textContent = 'Añadir Nuevo Producto';
        }
        productModal.style.display = 'flex';
    };

    const closeProductModal = () => {
        productModal.style.display = 'none';
    };

    const saveProduct = (e) => {
        e.preventDefault();
        const id = productIdInput.value;
        const name = productNameInput.value.trim();
        const price = parseFloat(productPriceInput.value);
        const stock = parseInt(productStockInput.value);
        const description = productDescriptionInput.value.trim();

        if (!name || isNaN(price) || isNaN(stock) || price < 0 || stock < 0) {
            alert('Por favor, rellena todos los campos correctamente. Precio y Stock deben ser números positivos.');
            return;
        }

        if (id) {
            const productIndex = products.findIndex(p => p.id == id);
            if (productIndex > -1) {
                products[productIndex] = { id: parseInt(id), name, price, stock, description };
            }
            alert('Producto actualizado exitosamente.');
        } else {
            const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
            products.push({ id: newId, name, price, stock, description });
            alert('Producto añadido exitosamente.');
        }
        saveToLocalStorage();
        renderInventory();
        closeProductModal();
    };

    const deleteProduct = (productId) => {
        if (confirm(`¿Estás seguro de que quieres eliminar el producto con ID ${productId}?`)) {
            products = products.filter(p => p.id != productId);
            saveToLocalStorage();
            renderInventory();
            alert('Producto eliminado.');
        }
    };

    const renderSaleProducts = () => {
        saleProductDisplay.innerHTML = '';
        const searchTerm = saleSearchInput.value.toLowerCase().trim();

        const filteredProducts = products.filter(p =>
            p.stock > 0 && (p.name.toLowerCase().includes(searchTerm) || searchTerm === '')
        );

        if (filteredProducts.length === 0 && searchTerm !== '') {
            saleProductDisplay.innerHTML = '<p style="text-align: center; width: 100%; color: #666;">No se encontraron productos disponibles con ese nombre.</p>';
        } else if (filteredProducts.length === 0) {
            saleProductDisplay.innerHTML = '<p style="text-align: center; width: 100%; color: #666;">No hay productos en stock.</p>';
        } else {
            filteredProducts.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card small-card';
                card.dataset.id = product.id;
                card.innerHTML = `
                    <img src="https://via.placeholder.com/150x100?text=${encodeURIComponent(product.name.split(' ')[0])}" alt="${product.name}">
                    <h4>${product.name}</h4>
                    <p>${formatCurrency(product.price)} | Stock: ${product.stock}</p>
                    <button class="add-to-sale-quick-btn" data-id="${product.id}">Añadir</button>
                `;
                saleProductDisplay.appendChild(card);
            });

            document.querySelectorAll('.add-to-sale-quick-btn').forEach(button => {
                button.addEventListener('click', (e) => addProductToSaleCart(parseInt(e.currentTarget.dataset.id)));
            });
        }
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
                alert('No hay más stock de este producto.');
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
        renderSaleCart();
    };

    const renderSaleCart = () => {
        saleCartList.innerHTML = '';
        let total = 0;
        if (currentSaleCart.length === 0) {
            saleCartList.innerHTML = '<li style="text-align: center; color: #999;">El carrito está vacío.</li>';
        } else {
            currentSaleCart.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span>${item.name} x ${item.quantity}</span>
                    <span>${formatCurrency(item.price * item.quantity)}</span>
                `;
                saleCartList.appendChild(li);
                total += item.price * item.quantity;
            });
        }
        saleTotalSpan.textContent = formatCurrency(total);
    };

    const completeSale = () => {
        if (currentSaleCart.length === 0) {
            alert('El carrito de venta está vacío.');
            return;
        }

        if (confirm(`Confirmar venta por un total de ${saleTotalSpan.textContent}?`)) {
            currentSaleCart.forEach(cartItem => {
                const productIndex = products.findIndex(p => p.id === cartItem.id);
                if (productIndex > -1) {
                    products[productIndex].stock -= cartItem.quantity;
                }
            });

            const salesRecord = sales.find(s => s.date === today);
            if (salesRecord) {
                salesRecord.transactions.push({
                    id: Date.now(),
                    date: new Date().toISOString(),
                    items: currentSaleCart,
                    total: parseFloat(saleTotalSpan.textContent.replace('$', '').trim()),
                    advisorId: 'gerente'
                });
            } else {
                sales.push({
                    date: today,
                    transactions: [{
                        id: Date.now(),
                        date: new Date().toISOString(),
                        items: currentSaleCart,
                        total: parseFloat(saleTotalSpan.textContent.replace('$', '').trim()),
                        advisorId: 'gerente'
                    }]
                });
            }

            const currentAdvisor = advisors.find(a => a.username === 'gerente');
            if (currentAdvisor) {
                currentAdvisor.salesCount++;
            }

            saveToLocalStorage();
            alert('Venta completada exitosamente!');
            currentSaleCart = [];
            renderSaleCart();
            renderSaleProducts();
            renderDashboard();
        }
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
        if (dailySalesData.length > 0) {
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
                    if (trans.advisorId === selectedAdvisorId || trans.advisorId === 'gerente') {
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
    renderInventory();
    renderSaleProducts();
    renderSaleCart();
});