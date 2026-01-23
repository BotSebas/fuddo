<?php
session_start();
header('Content-Type: application/json');

include 'includes/conexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No estás autenticado'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['user_id'];
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Validar que las contraseñas coincidan
    if ($password_nueva !== $password_confirmar) {
        echo json_encode([
            'success' => false,
            'message' => 'Las contraseñas no coinciden'
        ]);
        exit;
    }
    
    // Validar longitud mínima
    if (strlen($password_nueva) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
        exit;
    }
    
    // Obtener la contraseña actual del usuario
    $sql = "SELECT password FROM usuarios WHERE id = $usuario_id";
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // Verificar la contraseña actual
        if (password_verify($password_actual, $usuario['password'])) {
            // Hash de la nueva contraseña
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña
            $sqlUpdate = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sqlUpdate);
            $stmt->bind_param('si', $password_hash, $usuario_id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contraseña cambiada exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña'
                ]);
            }
            
            $stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
