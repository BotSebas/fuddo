<?php
session_start();
include 'includes/conexion_master.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if (empty($usuario) || empty($password)) {
        header("Location: login.php?error=campos_vacios");
        exit();
    }

    // Buscar usuario en BD maestra (LEFT JOIN para permitir super-admin sin restaurante)
    $stmt = $conexion_master->prepare("
        SELECT um.id, um.usuario, um.password, um.nombre, um.email, um.rol, um.estado,
               um.id_restaurante, r.nombre_bd, r.identificador, r.nombre as nombre_restaurante, r.estado as estado_restaurante
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

            // Solo validar restaurante si NO es super-admin
            if ($user['rol'] !== 'super-admin') {
                // Verificar que el restaurante esté activo
                if ($user['estado_restaurante'] !== 'activo') {
                    header("Location: login.php?error=restaurante_inactivo");
                    exit();
                }

                // Verificar que las tablas del restaurante existan (en modo Cloudways con prefijos)
                // Si nombre_bd termina en _ es un prefijo, si no es nombre de BD
                if (substr($user['nombre_bd'], -1) === '_') {
                    // Modo Cloudways: verificar que exista al menos una tabla con el prefijo
                    $tabla_test = $user['nombre_bd'] . 'mesas';
                    $check_table = $conexion_master->query("SHOW TABLES LIKE '$tabla_test'");
                    if ($check_table->num_rows === 0) {
                        header("Location: login.php?error=tablas_no_existen");
                        exit();
                    }
                } else {
                    // Modo legacy: verificar que la BD exista
                    $check_db = $conexion_master->query("SHOW DATABASES LIKE '{$user['nombre_bd']}'");
                    if ($check_db->num_rows === 0) {
                        header("Location: login.php?error=bd_no_existe");
                        exit();
                    }
                }
            }

            // Establecer variables de sesión (usando solo datos de usuarios_master)
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id']; // ID de usuarios_master
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['rol_master'] = $user['rol'];
            $_SESSION['id_restaurante'] = $user['id_restaurante'] ?? null;
            $_SESSION['identificador'] = $user['identificador'] ?? null; // CRÍTICO: Para generar TABLE_PREFIX
            $_SESSION['nombre_bd'] = $user['nombre_bd'] ?? null;
            $_SESSION['nombre_restaurante'] = $user['nombre_restaurante'] ?? null;
            $_SESSION['foto'] = null; // Se puede actualizar después desde perfil

            // Registrar último acceso (comentado hasta agregar la columna)
            // $conexion_master->query("UPDATE usuarios_master SET ultimo_acceso = NOW() WHERE id = {$user['id']}");

            header("Location: home.php");
            exit();

        } else {
            header("Location: login.php?error=credenciales_invalidas");
            exit();
        }
    } else {
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
