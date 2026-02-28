<?php
session_start();
include 'includes/conexion_master.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $id_restaurante_login = intval($_POST['id_restaurante'] ?? 0); // ID del restaurante intentado

    if (empty($usuario) || empty($password)) {
        header("Location: login.php?error=campos_vacios");
        exit();
    }

    // ÚNICA FUENTE DE VERDAD: Buscar en usuarios_master
    $stmt = $conexion_master->prepare("
        SELECT um.id, um.usuario, um.password, um.nombre, um.email, um.rol, um.estado, um.id_restaurante,
               r.nombre_bd, r.identificador, r.nombre as nombre_restaurante, r.estado as estado_restaurante
        FROM usuarios_master um
        LEFT JOIN restaurantes r ON um.id_restaurante = r.id
        WHERE um.usuario = ?
    ");
    
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();

        // Verificar password
        if (password_verify($password, $user['password'])) {
            
            // Verificar que el usuario esté activo
            if ($user['estado'] !== 'activo') {
                header("Location: login.php?error=usuario_inactivo");
                exit();
            }

            // REGLA: Si NO es super-admin, verificar que intente entrar a SU restaurante
            if ($user['rol'] !== 'super-admin') {
                // El usuario debe tener id_restaurante asignado
                if (!isset($user['id_restaurante']) || $user['id_restaurante'] === null) {
                    header("Location: login.php?error=sin_restaurante_asignado");
                    exit();
                }

                // Verificar que el restaurante esté activo
                if ($user['estado_restaurante'] !== 'activo') {
                    header("Location: login.php?error=restaurante_inactivo");
                    exit();
                }

                // Verificar que el restaurante del usuario coincida si se envió id_restaurante en login
                if ($id_restaurante_login > 0 && $user['id_restaurante'] != $id_restaurante_login) {
                    header("Location: login.php?error=restaurante_no_permitido");
                    exit();
                }
            }

            // Establecer variables de sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['rol_master'] = $user['rol'];
            $_SESSION['id_restaurante'] = $user['id_restaurante'] ?? null;
            $_SESSION['identificador'] = $user['identificador'] ?? null;
            $_SESSION['nombre_bd'] = $user['nombre_bd'] ?? null;
            $_SESSION['nombre_restaurante'] = $user['nombre_restaurante'] ?? null;
            $_SESSION['foto'] = null;
            $_SESSION['usuario_tipo'] = 'master';

            // Registrar último acceso
            $conexion_master->query("UPDATE usuarios_master SET ultimo_acceso = NOW() WHERE id = {$user['id']}");

            header("Location: home.php");
            exit();

        } else {
            // Usuario encontrado pero contraseña incorrecta
            header("Location: login.php?error=credenciales_invalidas");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: login.php?error=usuario_no_existe");
        exit();
    }

    $stmt->close();
    $conexion_master->close();
} else {
    header("Location: login.php");
    exit();
}
?>
