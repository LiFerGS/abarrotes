<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$products = $data['products'];

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

// Insertar datos del cliente
$stmt = $conn->prepare("INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $email, $phone);
$stmt->execute();
$customer_id = $stmt->insert_id;
$stmt->close();

// Insertar datos del pedido
$stmt = $conn->prepare("INSERT INTO orders (customer_id) VALUES (?)");
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insertar detalles del pedido
$stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)");

foreach ($products as $product) {
    $product_id = $product['productId'];
    $quantity = $product['quantity'];
    
    if (!empty($product_id) && !empty($quantity)) {
        $stmt->bind_param('iii', $order_id, $product_id, $quantity);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'orderId' => $order_id]);
?>

