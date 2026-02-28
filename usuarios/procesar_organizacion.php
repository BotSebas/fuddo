<?php
include '../includes/auth.php';
include '../includes/conexion_master.php';

// Verificar que sea admin de la organización o super-admin
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'super-admin' && $_SESSION['rol'] !== 'admin-restaurante')) {
    header("Location: ../login.php");
    exit();
}

// Verificar que tenga id_restaurante
if (!isset($_SESSION['id_restaurante'])) {
    header("Location: ../home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: usuarios_organizacion.php");
    exit();
}

$accion = $_POST['accion'] ?? '';
$id_restaurante = $_SESSION['id_restaurante'];

// Crear usuario en usuarios_master
if ($accion === 'crear') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'admin-restaurante';
    $estado = $_POST['estado'] ?? 'activo';

    // Validar campos
    if (empty($usuario) || empty($password) || empty($nombre) || empty($rol)) {
        header("Location: usuarios_organizacion.php?error=campos_vacios");
        exit();
    }

    // Validar rol (roles de usuarios_master)
    $roles_validos = ['super-admin', 'admin-restaurante', 'admin', 'mesero', 'cocinero', 'vendedor', 'mesero_vendedor'];
    if (!in_array($rol, $roles_validos)) {
        header("Location: usuarios_organizacion.php?error=rol_invalido");
        exit();
    }

    // Validar email si no está vacío
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: usuarios_organizacion.php?error=email_invalido");
        exit();
    }

    // Validar longitud de contraseña
    if (strlen($password) < 6) {
        header("Location: usuarios_organizacion.php?error=password_corta");
        exit();
    }

    // Verificar si el usuario ya existe en usuarios_master
    $stmt = $conexion_master->prepare("SELECT id FROM usuarios_master WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: usuarios_organizacion.php?error=usuario_existe");
        exit();
    }

    // Hash de contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar usuario en usuarios_master con id_restaurante
    $stmt = $conexion_master->prepare(
        "INSERT INTO usuarios_master (usuario, password, nombre, email, rol, estado, id_restaurante) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssssi", $usuario, $password_hash, $nombre, $email, $rol, $estado, $id_restaurante);

    if ($stmt->execute()) {
        header("Location: usuarios_organizacion.php?exito=creado");
        exit();
    } else {
        header("Location: usuarios_organizacion.php?error=db_error");
        exit();
    }
    $stmt->close();
}

// Actualizar usuario
elseif ($accion === 'actualizar') {
    $id_usuario = intval($_POST['id_usuario'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'admin-restaurante';
    $estado = $_POST['estado'] ?? 'activo';
    $password = $_POST['password'] ?? '';

    // Validar campos
    if (empty($id_usuario) || empty($nombre) || empty($rol)) {
        header("Location: usuarios_organizacion.php?error=campos_vacios");
        exit();
    }

    // Validar rol
    $roles_validos = ['super-admin', 'admin-restaurante', 'admin', 'mesero', 'cocinero', 'vendedor', 'mesero_vendedor'];
    if (!in_array($rol, $roles_validos)) {
        header("Location: usuarios_organizacion.php?error=rol_invalido");
        exit();
    }

    // Validar email si no está vacío
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: usuarios_organizacion.php?error=email_invalido");
        exit();
    }

    // Verificar que el usuario pertenece a este restaurante
    $stmt = $conexion_master->prepare("SELECT id_restaurante FROM usuarios_master WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0 || $result->fetch_assoc()['id_restaurante'] != $id_restaurante) {
        header("Location: usuarios_organizacion.php?error=usuario_no_encontrado");
        exit();
    }

    // Validar contraseña si se proporciona
    if (!empty($password)) {
        if (strlen($password) < 6) {
            header("Location: usuarios_organizacion.php?error=password_corta");
            exit();
        }
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Actualizar con contraseña
        $stmt = $conexion_master->prepare(
            "UPDATE usuarios_master SET nombre = ?, email = ?, rol = ?, estado = ?, password = ? 
             WHERE id = ? AND id_restaurante = ?"
        );
        $stmt->bind_param("ssssii", $nombre, $email, $rol, $estado, $password_hash, $id_usuario, $id_restaurante);
    } else {
        // Actualizar sin contraseña
        $stmt = $conexion_master->prepare(
            "UPDATE usuarios_master SET nombre = ?, email = ?, rol = ?, estado = ? WHERE id = ? AND id_restaurante = ?"
        );
        $stmt->bind_param("ssssii", $nombre, $email, $rol, $estado, $id_usuario, $id_restaurante);
    }

    if ($stmt->execute()) {
        header("Location: usuarios_organizacion.php?exito=actualizado");
        exit();
    } else {
        header("Location: usuarios_organizacion.php?error=db_error");
        exit();
    }
    $stmt->close();
}

// Cambiar estado
elseif ($accion === 'cambiar_estado') {
    $id_usuario = intval($_POST['id_usuario'] ?? 0);
    $nuevo_estado = $_POST['estado'] == 'activo' ? 'activo' : 'inactivo';

    if ($id_usuario > 0) {
        $stmt = $conexion_master->prepare(
            "UPDATE usuarios_master SET estado = ? WHERE id = ? AND id_restaurante = ?"
        );
        $stmt->bind_param("sii", $nuevo_estado, $id_usuario, $id_restaurante);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: usuarios_organizacion.php?exito=actualizado");
    exit();
}

// Acción no reconocida
else {
    header("Location: usuarios_organizacion.php");
    exit();
}

$conexion_master->close();
?>
