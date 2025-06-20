<?php
require_once __DIR__ . "/InterfaceFileManager.php";

class FileManager implements InterfaceFileManager {

    private $uploadDir;

    public function __construct() {
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/GoPermisos/files/';
    }

    public function saveImages($employeeDocument, $permissionId, $images, $nombreFile = null) {
        if (empty($images)) {
            return ['error' => 'No se proporcionaron archivos.'];
        }

        $uploadResults = [];

        $employeeDir = $this->uploadDir . $employeeDocument;
        if (!file_exists($employeeDir)) {
            mkdir($employeeDir, 0777, true);
        }

        $permissionDir = $employeeDir . '/' . $permissionId;
        if (!file_exists($permissionDir)) {
            mkdir($permissionDir, 0777, true);
        }

        foreach ($images['tmp_name'] as $index => $tmpName) {
            $fileName = $images['name'][$index];
            $fileTmpPath = $images['tmp_name'][$index];
            $fileSize = $images['size'][$index];
            $fileError = $images['error'][$index];

            if ($fileError !== UPLOAD_ERR_OK) {
                $uploadResults[] = ['file' => $fileName, 'error' => 'Error al cargar el archivo.'];
                continue;
            }

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, ['jpg', 'jpeg', 'png', 'pdf'])) {
                $uploadResults[] = ['file' => $fileName, 'error' => 'Extensión de archivo no válida. Solo se permiten JPG, JPEG y PNG.'];
                continue;
            }

            if ($nombreFile) {
                $newFileName = $nombreFile . '.' . $fileExtension;
            } else {
                $newFileName = uniqid('', true) . '.' . $fileExtension;
            }

            $destinationPath = $permissionDir . '/' . $newFileName;

            if (file_exists($destinationPath)) {
                $uploadResults[] = ['file' => $fileName, 'error' => 'El archivo ya existe.'];
                continue;
            }

            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                $uploadResults[] = ['file' => $fileName, 'success' => 'Archivo cargado exitosamente.'];
            } else {
                $uploadResults[] = ['file' => $fileName, 'error' => 'Error al mover el archivo cargado.'];
            }
        }

        return $uploadResults;
    }

    public function getProofs($employeeDocument, $permissionId) {
        $employeeDir = $this->uploadDir . $employeeDocument;
        $permissionDir = $employeeDir . '/' . $permissionId;

        if (!file_exists($permissionDir)) {
            return ['error' => 'No se encontraron archivos para el permiso especificado.'];
        }

        $files = array_diff(scandir($permissionDir), ['.', '..']);

        if (empty($files)) {
            return ['error' => 'No se encontraron archivos para el permiso especificado.'];
        }
        
        $baseURL = 'files/' . $employeeDocument . '/' . $permissionId;
        $fileURLs = array_map(fn($file) => $baseURL . '/' . $file, $files);

        return ['files' => $fileURLs];
    }
}
?>
