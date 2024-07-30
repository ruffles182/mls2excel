<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión a la base de datos
$host = 'localhost';
$db = 'nombre_de_tu_base_de_datos';
$user = 'tu_usuario';
$pass = 'tu_contraseña';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta a la tabla properties
$sql = "SELECT * FROM properties";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados de las columnas
    $columns = array_keys($result->fetch_assoc());
    $result->data_seek(0); // Reiniciar el puntero de resultados

    $columnIndex = 'A';
    foreach ($columns as $column) {
        $sheet->setCellValue($columnIndex . '1', $column);
        $columnIndex++;
    }

    // Datos de la tabla
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $columnIndex = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($columnIndex . $rowIndex, $cell);
            $columnIndex++;
        }
        $rowIndex++;
    }

    // Nombre del archivo
    $filename = date('Y-m-d_H-i-s') . ' - propiedades.xlsx';

    // Descargar el archivo
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
} else {
    echo "No se encontraron registros.";
}

$conn->close();
?>
