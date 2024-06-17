<?php
header('Content-Type: application/json');

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

// Consulta de productos
$sql = "SELECT id, name, price FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>

