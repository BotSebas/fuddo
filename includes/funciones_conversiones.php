<?php
/**
 * Funciones de conversión de unidades para el sistema de costeo
 * Convierte todas las unidades a unidades mínimas estándar:
 * - Peso: kg, lb → gramos (g)
 * - Volumen: l → mililitros (ml)
 * - Unidades: und → und (sin conversión)
 */

/**
 * Convierte una cantidad de una unidad a su unidad mínima estándar
 * 
 * @param float $cantidad Cantidad a convertir
 * @param string $unidad_original kg, g, lb, l, ml, und
 * @return array ['cantidad_convertida' => float, 'unidad_minima' => string]
 */
function convertirAUnidadMinima($cantidad, $unidad_original) {
    $unidad_original = strtolower(trim($unidad_original));
    
    switch ($unidad_original) {
        // ===== PESO EN GRAMOS =====
        case 'kg':
            // 1 kg = 1000 g
            return [
                'cantidad_convertida' => $cantidad * 1000,
                'unidad_minima' => 'g'
            ];
        case 'g':
            // Ya está en la unidad mínima
            return [
                'cantidad_convertida' => $cantidad,
                'unidad_minima' => 'g'
            ];
        case 'lb':
            // 1 lb = 453.592 g (conversión estándar internacional)
            return [
                'cantidad_convertida' => $cantidad * 453.592,
                'unidad_minima' => 'g'
            ];
        
        // ===== VOLUMEN EN MILILITROS =====
        case 'l':
            // 1 l = 1000 ml
            return [
                'cantidad_convertida' => $cantidad * 1000,
                'unidad_minima' => 'ml'
            ];
        case 'ml':
            // Ya está en la unidad mínima
            return [
                'cantidad_convertida' => $cantidad,
                'unidad_minima' => 'ml'
            ];
        
        // ===== UNIDADES (sin conversión) =====
        case 'und':
            return [
                'cantidad_convertida' => $cantidad,
                'unidad_minima' => 'und'
            ];
        
        default:
            throw new Exception("Unidad de medida no válida: $unidad_original");
    }
}

/**
 * Obtiene la unidad mínima según la unidad original
 * 
 * @param string $unidad_original kg, g, lb, l, ml, und
 * @return string Unidad mínima (g, ml, und)
 */
function obtenerUnidadMinima($unidad_original) {
    $unidad_original = strtolower(trim($unidad_original));
    
    // Unidades de peso
    if (in_array($unidad_original, ['kg', 'g', 'lb'])) {
        return 'g';
    }
    
    // Unidades de volumen
    if (in_array($unidad_original, ['l', 'ml'])) {
        return 'ml';
    }
    
    // Unidades sinconversión
    if ($unidad_original === 'und') {
        return 'und';
    }
    
    throw new Exception("Unidad de medida no válida: $unidad_original");
}

/**
 * Calcula el costo por unidad mínima
 * 
 * @param float $costo_total Costo total de la cantidad comprada
 * @param float $cantidad_base Cantidad en la unidad original
 * @param string $unidad_original kg, g, lb, l, ml, und
 * @return float Costo por unidad mínima (6 decimales)
 */
function calcularCostoUnitarioMinimo($costo_total, $cantidad_base, $unidad_original) {
    $conversion = convertirAUnidadMinima($cantidad_base, $unidad_original);
    $cantidad_convertida = $conversion['cantidad_convertida'];
    
    if ($cantidad_convertida <= 0) {
        throw new Exception("Cantidad debe ser mayor a 0");
    }
    
    // Retorna el costo por unidad mínima con 6 decimales
    return round($costo_total / $cantidad_convertida, 6);
}

/**
 * Obtiene el texto descriptivo de una unidad
 * 
 * @param string $unidad kg, g, lb, l, ml, und
 * @return string Descripción de la unidad
 */
function obtenerDescripcionUnidad($unidad) {
    $unidad = strtolower(trim($unidad));
    
    $unidades = [
        'kg' => 'Kilogramo (kg)',
        'g' => 'Gramo (g)',
        'lb' => 'Libra (lb)',
        'l' => 'Litro (l)',
        'ml' => 'Mililitro (ml)',
        'und' => 'Unidad (und)'
    ];
    
    return $unidades[$unidad] ?? 'Desconocida';
}

/**
 * Valida si una unidad es válida
 * 
 * @param string $unidad kg, g, lb, l, ml, und
 * @return bool
 */
function esUnidadValida($unidad) {
    $unidad = strtolower(trim($unidad));
    return in_array($unidad, ['kg', 'g', 'lb', 'l', 'ml', 'und']);
}

/**
 * Obtiene todas las unidades disponibles
 * 
 * @return array
 */
function obtenerUnidadesDisponibles() {
    return [
        'kg' => 'Kilogramo (kg)',
        'g' => 'Gramo (g)',
        'lb' => 'Libra (lb)',
        'l' => 'Litro (l)',
        'ml' => 'Mililitro (ml)',
        'und' => 'Unidad (und)'
    ];
}

/**
 * Agrupa unidades por tipo
 * 
 * @return array Unidades agrupadas
 */
function agruparUnidadesPorTipo() {
    return [
        'peso' => [
            'kg' => 'Kilogramo (kg)',
            'g' => 'Gramo (g)',
            'lb' => 'Libra (lb)'
        ],
        'volumen' => [
            'l' => 'Litro (l)',
            'ml' => 'Mililitro (ml)'
        ],
        'unidad' => [
            'und' => 'Unidad (und)'
        ]
    ];
}

/**
 * Formatea un número decimal con decimales específicos
 * Usado para mostrar costos en la interfaz
 * 
 * @param float $numero
 * @param int $decimales
 * @return string Número formateado
 */
function formatearCosto($numero, $decimales = 2) {
    return number_format($numero, $decimales, '.', ',');
}

?>
