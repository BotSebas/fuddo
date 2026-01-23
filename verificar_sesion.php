<?php
session_start();

echo "<h2>Estado de la Sesión Actual</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";
echo "<h3>Datos del Usuario en BD:</h3>";

$conn = new mysqli('localhost', 'root', '', 'fuddo_master');
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$result = $conn->query("SELECT nombre, email, rol FROM usuarios WHERE email = 'admin@fuddo.co'");
if ($row = $result->fetch_assoc()) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
}

$conn->close();

echo "<hr>";
echo "<a href='logout.php'>Cerrar Sesión</a> | <a href='login.php'>Ir a Login</a>";
?>
