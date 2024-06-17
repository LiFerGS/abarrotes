<?php
header('Content-Type: application/json');

if (!isset($_GET['orderId'])) {
    die(json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado.']));
}

$orderId = $_GET['orderId'];

// Configuración de la conexión a la base de datos
$host = 'localhost'; // Puede variar dependiendo de tu configuración
$db = 'abarrotes24'; // Nombre de la base de datos que creaste
$user = 'root'; // Usuario de tu base de datos en XAMPP (por defecto es 'root')
$pass = ''; // Contraseña de tu base de datos en XAMPP (por defecto es vacía)

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Consulta de pedido
$sql = "SELECT customers.name, customers.email, customers.phone, products.name AS product_name, order_details.quantity
        FROM orders
        JOIN customers ON orders.customer_id = customers.id
        JOIN order_details ON orders.id = order_details.order_id
        JOIN products ON order_details.product_id = products.id
        WHERE orders.id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = [
        'customer' => [],
        'products' => []
    ];

    // Obtener datos del cliente
    $row = $result->fetch_assoc();
    $order['customer']['name'] = $row['name'];
    $order['customer']['email'] = $row['email'];
    $order['customer']['phone'] = $row['phone'];

    // Obtener detalles de productos
    do {
        $order['products'][] = [
            'name' => $row['product_name'],
            'quantity' => $row['quantity']
        ];
    } while ($row = $result->fetch_assoc());

    echo json_encode(['success' => true, 'order' => $order]);
} else {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado.']);
}

$stmt->close();
$conn->close();
?>

