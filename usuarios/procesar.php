<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

try {
    $id = $_POST['id'] ?? null;
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $password = $_POST['password'] ?? '';
    $id_restaurante = $_POST['id_restaurante'] ?? null;
    
    // Validaciones básicas
    if (empty($usuario) || empty($nombre) || empty($rol)) {
        throw new Exception('Por favor completa todos los campos obligatorios');
    }
    
    // Validar rol primero
    if (!in_array($rol, ['admin-restaurante', 'super-admin'])) {
        throw new Exception('Rol no válido');
    }
    
    // Solo requerir restaurante si NO es super-admin
    if ($rol !== 'super-admin' && empty($id_restaurante)) {
        throw new Exception('Debes seleccionar un restaurante para este rol');
    }
    
    // Si es nuevo usuario, password es obligatorio
    if (empty($id) && empty($password)) {
        throw new Exception('La contraseña es obligatoria para usuarios nuevos');
    }
    
    if (!empty($password) && strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Manejo de foto
    $rutaFoto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = '../assets/img/users/';
        
        // Crear directorio si no existe
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }
        
        // Validar tipo de archivo
        $tipoArchivo = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            throw new Exception('Solo se permiten archivos JPG, PNG o GIF');
        }
        
        // Validar tamaño (2MB máximo)
        if ($_FILES['foto']['size'] > 2097152) {
            throw new Exception('La imagen no debe superar los 2MB');
        }
        
        // Generar nombre único basado en usuario
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9]/', '_', $usuario) . '_' . time() . '.' . $tipoArchivo;
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            $rutaFoto = 'assets/img/users/' . $nombreArchivo;
        } else {
            throw new Exception('Error al subir la foto');
        }
    }
    
    if (empty($id)) {
        // CREAR NUEVO USUARIO EN USUARIOS_MASTER
        
        // Verificar que el usuario no exista
        $stmt = $conexion_master->prepare("SELECT id FROM usuarios_master WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('El usuario ya existe');
        }
        
        // Hashear contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar en usuarios_master
        $sql = "INSERT INTO usuarios_master (usuario, password, nombre, email, id_restaurante, rol, estado, foto) VALUES (?, ?, ?, ?, ?, ?, 'activo', ?)";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param("sssssss", $usuario, $passwordHash, $nombre, $email, $id_restaurante, $rol, $rutaFoto);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado exitosamente'
            ]);
        } else {
            throw new Exception('Error al crear el usuario');
        }
        
    } else {
        // ACTUALIZAR USUARIO EXISTENTE EN USUARIOS_MASTER
        
        // Verificar que el usuario no esté duplicado (excepto el mismo)
        $stmt = $conexion_master->prepare("SELECT id FROM usuarios_master WHERE usuario = ? AND id != ?");
        $stmt->bind_param("si", $usuario, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('El usuario ya existe');
        }
        
        // Construir query dinámicamente
        $campos = [];
        $tipos = "";
        $valores = [];
        
        $campos[] = "usuario = ?";
        $tipos .= "s";
        $valores[] = $usuario;
        
        $campos[] = "nombre = ?";
        $tipos .= "s";
        $valores[] = $nombre;
        
        $campos[] = "email = ?";
        $tipos .= "s";
        $valores[] = $email;
        
        $campos[] = "rol = ?";
        $tipos .= "s";
        $valores[] = $rol;
        
        // Solo actualizar id_restaurante según el rol
        if ($rol === 'super-admin') {
            $campos[] = "id_restaurante = NULL";
        } else {
            $campos[] = "id_restaurante = ?";
            $tipos .= "i";
            $valores[] = $id_restaurante;
        }
        
        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $campos[] = "password = ?";
            $tipos .= "s";
            $valores[] = $passwordHash;
        }
        
        // Solo actualizar foto si se subió una nueva
        if ($rutaFoto !== null) {
            $campos[] = "foto = ?";
            $tipos .= "s";
            $valores[] = $rutaFoto;
        }
        
        $tipos .= "i";
        $valores[] = $id;
        
        $sql = "UPDATE usuarios_master SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param($tipos, ...$valores);
        
        if ($stmt->execute()) {
            // Si el usuario editado es el mismo que está logueado, actualizar la sesión
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                $_SESSION['nombre'] = $nombre;
                $_SESSION['email'] = $email;
                if ($rutaFoto !== null) {
                    $_SESSION['foto'] = $rutaFoto;
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar el usuario');
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
