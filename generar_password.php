<?php
// Script para generar hash de contraseña
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña: $password\n";
echo "Hash generado: $hash\n\n";

// SQL para insertar
echo "SQL para insertar usuario:\n";
echo "INSERT INTO usuarios (usuario, password, nombre, rol) VALUES ('admin', '$hash', 'Administrador', 'admin');\n\n";

// Verificar que el hash funciona
if (password_verify($password, $hash)) {
    echo "✓ Verificación exitosa - El hash es correcto\n";
} else {
    echo "✗ Error en la verificación\n";
}
?>
