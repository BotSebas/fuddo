<?php
// Archivo para proteger páginas que requieren autenticación
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?sesion=1");
    exit();
}

// Verificar que el restaurante siga activo (solo para usuarios no super-admin)
if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'super-admin' && isset($_SESSION['id_restaurante'])) {
    include_once __DIR__ . '/conexion_master.php';
    
    $stmt = $conexion_master->prepare("SELECT estado FROM restaurantes WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id_restaurante']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $restaurante = $resultado->fetch_assoc();
        
        if ($restaurante['estado'] !== 'activo') {
            // Cerrar sesión y redirigir con mensaje
            session_destroy();
                header("Location: /login.php?error=restaurante_inactivo");
            exit();
        }
    }
    
    $stmt->close();
}

/**
 * Función para verificar permisos de módulos basados en roles de restaurante
 * 
 * Roles y sus permisos:
 * - admin: Acceso a todo
 * - mesero: mesas, comandas
 * - cocinero: cocina
 * - vendedor: mesas, comandas
 * - mesero_vendedor: mesas, comandas
 */
function tienePermisoRestaurante($modulo) {
    // Si es usuario master (super-admin o admin-restaurante), tiene acceso a todo
    if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] !== null) {
        return true;
    }
    
    // Si no está definida usuario_tipo, asumir que es usuario master por compatibilidad
    if (!isset($_SESSION['usuario_tipo'])) {
        return true;
    }
    
    // Si es usuario de restaurante
    if ($_SESSION['usuario_tipo'] !== 'restaurant') {
        return false;
    }
    
    $rol = $_SESSION['rol'] ?? null;
    
    if (!$rol) {
        return false;
    }
    
    // Admin tiene acceso a todo
    if ($rol === 'admin') {
        return true;
    }
    
    // Definir qué módulos puede acceder cada rol
    $permisos = [
        'mesas' => ['mesero', 'vendedor', 'mesero_vendedor', 'admin'],
        'comandas' => ['mesero', 'vendedor', 'mesero_vendedor', 'admin'],
        'cocina' => ['cocinero', 'admin'],
        'productos' => ['admin'],
        'reportes' => ['admin'],
        'usuarios' => ['admin'],
        'menu_digital' => ['admin'],
    ];
    
    // Si el módulo existe en permisos, verificar si el rol tiene acceso
    if (isset($permisos[$modulo])) {
        return in_array($rol, $permisos[$modulo]);
    }
    
    // Por defecto, denegar acceso
    return false;
}

/**
 * Función para verificar si el usuario es admin del restaurante
 */
function esAdminRestaurante() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin' && (isset($_SESSION['usuario_tipo']) ? $_SESSION['usuario_tipo'] === 'restaurant' : false);
}

/**
 * Función para verificar si el usuario es master (super-admin o admin-restaurante)
 */
function esMasterUser() {
    return isset($_SESSION['rol_master']) && $_SESSION['rol_master'] !== null;
}

/**
 * Función para verificar permisos de módulo por rol
 * Retorna true si el usuario tiene permiso para acceder al módulo
 */
function tienePermisoModulo($modulo) {
    // Super-admin tiene acceso a todo
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin') {
        return true;
    }
    
    $rol = $_SESSION['rol'] ?? null;
    
    // Mapeo de módulos permitidos por rol
    $modulos_por_rol = [
        'admin-restaurante' => ['mesas', 'comandas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios', 'pedidos', 'materias_primas', 'recetas'],
        'admin' => ['mesas', 'comandas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios', 'pedidos', 'materias_primas', 'recetas'],
        'mesero' => ['mesas', 'comandas'],
        'cocinero' => ['cocina'],
        'vendedor' => ['mesas', 'comandas'],
        'mesero_vendedor' => ['mesas', 'comandas'],
    ];
    
    if (isset($modulos_por_rol[$rol]) && in_array($modulo, $modulos_por_rol[$rol])) {
        return true;
    }
    
    return false;
}
?>
