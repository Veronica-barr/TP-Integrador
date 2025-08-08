<?php
require_once '../includes/conexion.php';

header('Content-Type: application/json');

try {
    // Validar entrada
    if (!isset($_GET['provincia_id'])) {
        throw new Exception('Parámetro provincia_id faltante');
    }
    
    $provincia_id = filter_var($_GET['provincia_id'], FILTER_VALIDATE_INT);
    if ($provincia_id === false || $provincia_id <= 0) {
        throw new Exception('ID de provincia no válido');
    }

    // Consulta preparada con solo los campos necesarios
    $query = "SELECT localidad_id, nombre 
              FROM localidades 
              WHERE provincia_id = ? 
              ORDER BY nombre";
    
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->bind_param("i", $provincia_id);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $localidades = [];
    
    while ($row = $result->fetch_assoc()) {
        $localidades[] = [
            'localidad_id' => (int)$row['localidad_id'],
            'nombre' => htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8')
        ];
    }
    
    // Cabecera para evitar caché en desarrollo
    header('Cache-Control: no-cache, must-revalidate');
    
    echo json_encode([
        'success' => true,
        'data' => $localidades
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>