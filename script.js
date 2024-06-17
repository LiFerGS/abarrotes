document.addEventListener('DOMContentLoaded', () => {
    // Cargar productos al inicio
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            const productContainer = document.getElementById('products');
            addProductField(productContainer, products); // Agregar campos al cargar la página
        })
        .catch(error => console.error('Error al obtener productos:', error));

    // Manejar el evento de agregar más productos
    document.getElementById('addProduct').addEventListener('click', () => {
        fetch('get_products.php')
            .then(response => response.json())
            .then(products => {
                const productContainer = document.getElementById('products');
                addProductField(productContainer, products); // Agregar más campos al hacer clic en "Agregar Producto"
            })
            .catch(error => console.error('Error al obtener productos:', error));
    });

    // Manejar el evento de enviar el pedido
    document.getElementById('submitOrder').addEventListener('click', function(event) {
        event.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const productSelects = document.querySelectorAll('select[name="products[]"]');
        const quantities = document.querySelectorAll('input[name="quantities[]"]');

        // Validar que al menos un producto se haya seleccionado
        if (productSelects.length === 0 || productSelects[0].value === '') {
            alert('Debe seleccionar al menos un producto.');
            return;
        }
        const products = [];
        for (let i = 0; i < productSelects.length; i++) {
            const productId = productSelects[i].value; // Aquí productId debe coincidir con el 'id' de products
            const quantity = quantities[i].value;
            products.push({ productId, quantity });
        }
        

        const orderData = {
            name: name,
            email: email,
            phone: phone,
            products: products
        };

        // Enviar los datos del pedido al servidor
        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `resumen.html?orderId=${data.orderId}`;
            } else {
                alert('Hubo un error al realizar el pedido.');
            }
        })
        .catch(error => console.error('Error al procesar pedido:', error));
    });
});

function addProductField(container, products) {
    const productDiv = document.createElement('div');
    productDiv.className = 'product';

    const productSelect = document.createElement('select');
    productSelect.name = 'products[]';
    productSelect.required = true;

    // Agregar una opción vacía inicial
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Seleccione un producto';
    productSelect.appendChild(defaultOption);

    // Agregar opciones de productos
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id; // Utilizando el campo 'id' de la tabla products
        option.textContent = `${product.name} - $${product.price}`;
        productSelect.appendChild(option);
    });
    
    const quantityInput = document.createElement('input');
    quantityInput.type = 'number';
    quantityInput.name = 'quantities[]';
    quantityInput.required = true;
    quantityInput.placeholder = 'Cantidad';
    quantityInput.style.marginLeft = '10px';

    productDiv.appendChild(productSelect);
    productDiv.appendChild(quantityInput);
    container.appendChild(productDiv);
}
