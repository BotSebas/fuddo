<?php
// Procesar Registro Demo - Homologado con restaurantes/procesar.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

header('Content-Type: application/json; charset=utf-8');

// Crear directorio de logs si no existe
if (!is_dir(__DIR__ . '/logs')) {
    @mkdir(__DIR__ . '/logs', 0755, true);
}

// Variables de respuesta
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Incluir conexión a base de datos master
    $conexionPath = __DIR__ . '/includes/conexion_master.php';
    if (!file_exists($conexionPath)) {
        throw new Exception('Archivo de conexión no encontrado');
    }
    require_once $conexionPath;
    
    if (!isset($conexion_master) || !$conexion_master || $conexion_master->connect_error) {
        throw new Exception('Error de conexión a BD master');
    }

    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método HTTP no permitido');
    }
    
    // Obtener y validar datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $nombreNegocio = isset($_POST['nombreNegocio']) ? trim($_POST['nombreNegocio']) : '';
    $tipoNegocio = isset($_POST['tipoNegocio']) ? trim($_POST['tipoNegocio']) : '';
    $tipoNegocioOtro = isset($_POST['tipoNegocioOtro']) ? trim($_POST['tipoNegocioOtro']) : '';

    // Validar datos requeridos
    if (empty($nombre) || empty($email) || empty($telefono) || empty($nombreNegocio) || empty($tipoNegocio)) {
        throw new Exception('Faltan datos requeridos');
    }

    // Si es "otro", usar tipoNegocioOtro
    if ($tipoNegocio === 'otro') {
        if (empty($tipoNegocioOtro)) {
            throw new Exception('Debe especificar el tipo de negocio');
        }
        $tipoNegocio = $tipoNegocioOtro;
    }

    // Validar que el email no exista en usuarios_master
    $sqlCheckEmail = "SELECT id FROM usuarios_master WHERE email = ?";
    $stmtCheck = $conexion_master->prepare($sqlCheckEmail);
    if (!$stmtCheck) {
        throw new Exception('Error preparando query: ' . $conexion_master->error);
    }
    
    $stmtCheck->bind_param('s', $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        throw new Exception('El correo electrónico ya está registrado');
    }
    $stmtCheck->close();

    // ============================================
    // 1. GENERAR IDENTIFICADOR PARA DEMO
    // ============================================
    // Formato: demo_{nombreNegocio sin espacios}
    // Sin el prefijo 'fuddo_' porque conexion.php lo agrega automáticamente
    $nombreLimpio = strtolower(preg_replace('/[^a-z0-9]/i', '', $nombreNegocio));
    $identificador = 'demo_' . $nombreLimpio;
    
    // Limitar a 60 caracteres (límite de MySQL para nombres)
    $identificador = substr($identificador, 0, 60);
    
    // Verificar que el identificador sea único
    $contador = 1;
    $identificadorOriginal = $identificador;
    $sqlCheckId = "SELECT id FROM restaurantes WHERE identificador = ?";
    $stmtCheckId = $conexion_master->prepare($sqlCheckId);
    
    while (true) {
        $stmtCheckId->bind_param('s', $identificador);
        $stmtCheckId->execute();
        $resultCheckId = $stmtCheckId->get_result();
        
        if ($resultCheckId->num_rows === 0) {
            break; // Identificador es único
        }
        
        // Si no es único, agregar sufijo numérico
        $identificador = substr($identificadorOriginal . '_' . $contador, 0, 60);
        $contador++;
    }
    $stmtCheckId->close();

    // ============================================
    // 2. GENERAR CONTRASEÑA Y USUARIO
    // ============================================
    $password = generarPassword(8);
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Generar usuario único - Para demo el usuario es el email completo
    $usuarioBase = strtolower($email);
    $usuario = $usuarioBase;
    $contador = 1;
    
    while (usuarioExiste($usuario, $conexion_master)) {
        $usuario = $usuarioBase . $contador;
        $contador++;
    }

    // ============================================
    // 3. PREFIJO DE TABLAS EN mgacgdnjkg (HOMOLOGADO CON restaurantes/procesar.php)
    // ============================================
    // Las tablas se crean DENTRO de mgacgdnjkg con prefijo, no en una BD separada
    // Formato: fuddo_demo_{nombreNegocio sin espacios}_
    $table_prefix = 'fuddo_demo_' . $nombreLimpio . '_';
    
    // El nombre_bd guardará el prefijo (como lo hace restaurantes/procesar.php)
    $nombreBd = $table_prefix;
    
    error_log("[DEMO REGISTRO] Prefijo de tablas: $table_prefix");

    // ============================================
    // 4. LEER TEMPLATE Y CREAR TABLAS EN mgacgdnjkg
    // ============================================
    // Leer template de restaurante (usa {PREFIX})
    $sqlTemplatePath = __DIR__ . '/sql/template_restaurante.sql';
    if (!file_exists($sqlTemplatePath)) {
        throw new Exception('No se pudo encontrar el template SQL');
    }
    
    $sql_template = file_get_contents($sqlTemplatePath);
    if ($sql_template === false) {
        throw new Exception('No se pudo leer el template SQL');
    }

    // Reemplazar {PREFIX} con el prefijo del demo
    $sql_schema = str_replace('{PREFIX}', $table_prefix, $sql_template);
    
    // Limpiar comentarios de SQL (-- comentarios en líneas) para evitar problemas con multi_query
    // Pero preservar comentarios dentro de COMMENT='...'
    $lineas = explode("\n", $sql_schema);
    $sql_limpio = [];
    foreach ($lineas as $linea) {
        // Si la línea tiene -- pero NO está dentro de COMMENT='...'
        if (strpos($linea, '--') !== false && strpos($linea, "COMMENT='") === false) {
            $linea = substr($linea, 0, strpos($linea, '--'));
        }
        $sql_limpio[] = trim($linea);
    }
    $sql_schema = implode("\n", $sql_limpio);
    
    error_log("[DEMO REGISTRO] SQL limpio generado para ejecutar");
    
    // Ejecutar en mgacgdnjkg usando conexion_master (ya está conectada a mgacgdnjkg)
    if (!$conexion_master->multi_query($sql_schema)) {
        error_log("[DEMO REGISTRO ERROR] Error al crear tablas: " . $conexion_master->error);
        throw new Exception('Error al crear tablas: ' . $conexion_master->error);
    }

    // Esperar a que terminen todas las consultas y contar éxitos/errores
    $tablasCreadas = 0;
    $tablasError = 0;
    do {
        $resultado = $conexion_master->store_result();
        if ($resultado) {
            $resultado->free();
            $tablasCreadas++;
        } elseif ($conexion_master->error) {
            $tablasError++;
            error_log("[DEMO REGISTRO ERROR] Error en query: " . $conexion_master->error);
        }
    } while ($conexion_master->more_results() && $conexion_master->next_result());
    
    error_log("[DEMO REGISTRO] Se crearon $tablasCreadas tablas, $tablasError errores en mgacgdnjkg con prefijo: $table_prefix");
    
    // Verificar que existan las tablas críticas
    $tablasRequeridas = ['mesas', 'productos', 'servicios', 'servicios_total', 'comandas', 'comandas_total', 'menu_digital'];
    foreach ($tablasRequeridas as $tabla) {
        $tabla_nombre = $table_prefix . $tabla;
        $verificar = $conexion_master->query("SHOW TABLES LIKE '$tabla_nombre'");
        if (!$verificar || $verificar->num_rows === 0) {
            error_log("[DEMO REGISTRO WARNING] Tabla no creada: $tabla_nombre");
        } else {
            error_log("[DEMO REGISTRO OK] Tabla verificada: $tabla_nombre");
        }
    }

    // ============================================
    // 5. GENERAR TOKEN DE ACTIVACIÓN
    // ============================================
    $token_activacion = bin2hex(random_bytes(32));
    
    // ============================================
    // 6. CREAR USUARIO EN usuarios_master (INACTIVO)
    // ============================================
    // nombre_bd guarda el PREFIJO (como lo hace restaurantes/procesar.php)
    // Estado: INACTIVO hasta que active por correo
    $sqlInsertUser = "INSERT INTO usuarios_master (usuario, password, nombre, email, rol, estado, nombre_bd, token_activacion, fecha_creacion) 
                      VALUES (?, ?, ?, ?, 'admin-restaurante', 'inactivo', ?, ?, NOW())";
    
    $stmtInsert = $conexion_master->prepare($sqlInsertUser);
    if (!$stmtInsert) {
        throw new Exception('Error preparando insert user: ' . $conexion_master->error);
    }

    $stmtInsert->bind_param('ssssss', $usuario, $passwordHash, $nombre, $email, $table_prefix, $token_activacion);
    
    if (!$stmtInsert->execute()) {
        throw new Exception('Error creando usuario: ' . $stmtInsert->error);
    }

    $userId = $stmtInsert->insert_id;
    $stmtInsert->close();
    error_log("[DEMO REGISTRO] Usuario creado: $usuario (ID: $userId)");

    // ============================================
    // 7. CREAR RESTAURANTE EN restaurantes (INACTIVO)
    // ============================================
    $plan = 'demo'; // Identificar como demo
    $fecha_expiracion = date('Y-m-d', strtotime('+7 days')); // 7 días para demo
    
    $sqlCreateRestaurante = "INSERT INTO restaurantes (nombre, identificador, nombre_bd, contacto, telefono, email, estado, plan, fecha_expiracion, fecha_creacion) 
                             VALUES (?, ?, ?, ?, ?, ?, 'inactivo', ?, ?, NOW())";
    
    $stmtRest = $conexion_master->prepare($sqlCreateRestaurante);
    if (!$stmtRest) {
        throw new Exception('Error preparando insert restaurante: ' . $conexion_master->error);
    }

    // nombre_bd guarda el PREFIJO (igual que restaurantes/procesar.php)
    $stmtRest->bind_param('ssssssss', $nombreNegocio, $identificador, $table_prefix, $nombre, $telefono, $email, $plan, $fecha_expiracion);
    
    if (!$stmtRest->execute()) {
        throw new Exception('Error creando restaurante: ' . $stmtRest->error);
    }

    $restauranteId = $stmtRest->insert_id;
    $stmtRest->close();
    error_log("[DEMO REGISTRO] Restaurante creado: $nombreNegocio (ID: $restauranteId, Identificador: $identificador, Prefijo: $table_prefix, Expira: $fecha_expiracion)");

    // ============================================
    // 8. ACTUALIZAR usuario_master CON id_restaurante
    // ============================================
    $sqlUpdateUser = "UPDATE usuarios_master SET id_restaurante = ? WHERE id = ?";
    $stmtUpdate = $conexion_master->prepare($sqlUpdateUser);
    if ($stmtUpdate) {
        $stmtUpdate->bind_param('ii', $restauranteId, $userId);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }

    // ============================================
    // 8.5 ASIGNAR PERMISOS (APLICACIONES) SEGÚN TIPO DE NEGOCIO
    // ============================================
    // 9. ASIGNAR APLICACIONES AUTOMÁTICAMENTE SEGÚN TIPO DE NEGOCIO
    // ============================================
    $aplicacionesPorTipo = [
        'restaurante' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
        'bar' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
        'restaurante-bar' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
        'minimarket' => ['comandas', 'productos', 'menu_digital', 'usuarios'],
    ];

    // Obtener aplicaciones según el tipo (normalizar el formato)
    $tipoNegocioLower = strtolower(trim($tipoNegocio));
    $aplicacionesAsignadas = $aplicacionesPorTipo[$tipoNegocioLower] ?? [];

    if (!empty($aplicacionesAsignadas)) {
        // Obtener IDs de aplicaciones que coincidan con las claves requeridas
        $placeholders = implode(',', array_fill(0, count($aplicacionesAsignadas), '?'));
        $sqlApps = "SELECT id, clave FROM aplicaciones WHERE clave IN ($placeholders) AND estado = 'activo'";
        $stmtApps = $conexion_master->prepare($sqlApps);
        
        if ($stmtApps) {
            $stmtApps->bind_param(str_repeat('s', count($aplicacionesAsignadas)), ...$aplicacionesAsignadas);
            $stmtApps->execute();
            $resultApps = $stmtApps->get_result();

            // Insertar asignaciones de aplicaciones
            $sqlInsertPermiso = "INSERT INTO restaurante_aplicaciones (id_restaurante, id_aplicacion, fecha_asignacion) VALUES (?, ?, NOW())";
            $stmtPermiso = $conexion_master->prepare($sqlInsertPermiso);
            $aplicacionesInsertadas = 0;

            if ($stmtPermiso) {
                while ($app = $resultApps->fetch_assoc()) {
                    $stmtPermiso->bind_param('ii', $restauranteId, $app['id']);
                    if ($stmtPermiso->execute()) {
                        $aplicacionesInsertadas++;
                        error_log("[DEMO REGISTRO] Aplicación asignada: {$app['clave']} (ID: {$app['id']}) al restaurante ID: $restauranteId");
                    } else {
                        error_log("[DEMO REGISTRO WARNING] Error al asignar aplicación {$app['clave']}: " . $stmtPermiso->error);
                    }
                }
                $stmtPermiso->close();
                error_log("[DEMO REGISTRO] Total de aplicaciones asignadas: $aplicacionesInsertadas para tipo: $tipoNegocio");
            }
            $stmtApps->close();
        } else {
            error_log("[DEMO REGISTRO ERROR] Error preparando query de aplicaciones: " . $conexion_master->error);
        }
    } else {
        error_log("[DEMO REGISTRO WARNING] No se encontraron aplicaciones para el tipo: $tipoNegocio");
    }

    // ============================================
    // 10. ENVIAR CORREO DE BIENVENIDA
    // ============================================
    require_once __DIR__ . '/includes/enviar_correo.php';
    
    // Generar link de activación
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $linkActivacion = $baseUrl . "/fuddo/confirmar_registro.php?token=" . urlencode($token_activacion) . "&email=" . urlencode($email);
    
    // Generar HTML del correo de bienvenida
    $asuntoCorreo = "Bienvenido a FUDDO";
    $cuerpoHTML = generarHTMLBienvenida($nombreNegocio, $usuario, $email, $password, $linkActivacion);
    
    // Enviar correo
    $resultadoCorreo = enviarCorreo($email, $asuntoCorreo, $cuerpoHTML, $nombre);
    
    if ($resultadoCorreo['success']) {
        error_log("[DEMO CORREO BIENVENIDA ENVIADO] A: $email");
    } else {
        error_log("[DEMO CORREO BIENVENIDA ERROR] A: $email | Error: " . $resultadoCorreo['error']);
    }

    // ✅ RESPUESTA EXITOSA
    $response['success'] = true;
    $response['message'] = 'Registro exitoso. Revisa tu correo para activar la cuenta.';
    $response['data'] = [
        'userId' => $userId,
        'restauranteId' => $restauranteId,
        'email' => $email,
        'nombreBd' => $table_prefix,  // Prefijo de tablas en mgacgdnjkg
        'identificador' => $identificador,
        'nombreNegocio' => $nombreNegocio,
        'usuario' => $usuario,
        'plan' => 'demo',
        'estado' => 'inactivo',
        'activacion' => 'Revisa tu correo para activar'
    ];

    error_log("[DEMO REGISTRO EXITOSO] Email: $email | Prefijo: $table_prefix | Identificador: $identificador | Token enviado");


} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('[DEMO REGISTRO ERROR] ' . $e->getMessage());
    
    http_response_code(400);

} catch (Throwable $e) {
    $response['success'] = false;
    $response['message'] = 'Error del servidor';
    error_log('[DEMO REGISTRO FATAL] ' . $e->getMessage());
    http_response_code(500);
}

// Cerrar conexión
if (isset($conexion_master)) {
    @$conexion_master->close();
}

// Devolver JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

/**
 * Función para generar contraseña aleatoria
 */
function generarPassword($longitud = 8) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    
    return $password;
}

/**
 * Función para verificar si usuario existe
 */
function usuarioExiste($usuario, $conexion) {
    $sql = "SELECT id FROM usuarios_master WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->num_rows > 0;
    $stmt->close();
    return $existe;
}
?>
