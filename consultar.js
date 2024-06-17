document.getElementById('consultarPedido').addEventListener('click', function(event) {
    event.preventDefault();

    const orderId = document.getElementById('orderId').value;

    fetch(`get_order.php?orderId=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showOrderDetails(data.order);
            } else {
                alert('Pedido no encontrado.');
            }
        })
        .catch(error => console.error('Error:', error));
});

function showOrderDetails(order) {
    const consultaContent = document.getElementById('consultaContent');
    consultaContent.innerHTML = `
        <p><strong>Nombre:</strong> ${order.customer.name}</p>
        <p><strong>Email:</strong> ${order.customer.email}</p>
        <p><strong>Teléfono:</strong> ${order.customer.phone}</p>
        <h3>Productos:</h3>
        <ul>
            ${order.products.map(product => `<li>${product.name} - ${product.quantity} unidades</li>`).join('')}
        </ul>
    `;

    document.getElementById('consultarForm').style.display = 'none';
    document.getElementById('consultaResultado').style.display = 'block';
}
