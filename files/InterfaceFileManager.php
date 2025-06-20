<?php

interface InterfaceFileManager {

    /**
     * Guardar los comprobantes en el servidor.
     * 
     * @param string $employeeDocument Número de documento del empleado.
     * @param int $permissionId ID del permiso.
     * @param array $images Array con las imágenes a subir.
     * @param string|null $nombreFile Nombre opcional para el archivo a guardar. Si es null, se usará un nombre por defecto.
     * 
     * @return array Resultados de la subida de archivos.
     */
    public function saveImages($employeeDocument, $permissionId, $images, $nombreFile = null);

    /**
     * Obtener los comprobantes de un permiso específico.
     * 
     * @param string $employeeDocument Número de documento del empleado.
     * @param int $permissionId ID del permiso.
     * 
     * @return array Lista de archivos de los comprobantes del permiso.
     */
    public function getProofs($employeeDocument, $permissionId);
}
