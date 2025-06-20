<?php
require_once __DIR__ . "/../models/Department.php";

class DepartmentController {
    private $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository) {
        $this->departmentRepository = $departmentRepository;
    }

    public function saveDepartment($data) {
        $data_filter = [];
        $data_filter = array_filter($data, function($value) {
            return !empty($value);
        });

        $result = $this->departmentRepository->save($data_filter);

        if ($result) {
            return ['success' => true, 'message' => 'Departamento creado correctamente', 'id' => $result];
        } else {
            return ['success' => false, 'message' => 'Error en crear el departamento'];
        }
    }

    public function updateDepartment($id, $data) {
        $department = $this->departmentRepository->getById($id);
        $data_filter = [];

        foreach($data as $key => $value) {
            if ($value !== $department[0]->$key) {
                $data_filter[$key] = $value;
            }
        }

        if (empty($data_filter)) {
            return ['success' => false, 'message' => 'No se realizaron cambios',  'type' => 'info'];
        }

        $result = $this->departmentRepository->update($id, $data_filter);

        if ($result) {
            return ['success' => true, 'message' => 'Departamento actualizado correctamente', 'id' => $result];
        } else {
            return ['success' => false, 'message' => 'Error en actualizar el departamento'];
        }
    }

    public function getDepartments() {
        return $this->departmentRepository->getDepartments();
    }

    public function getByIdDepartment($id) {
        return $this->departmentRepository->getById($id);
    }

    public function deleteDepartment($id) {
        $result = $this->departmentRepository->delete($id);
        if ($result) {
            return ['message' => 'Departamento eliminado conrrecamente'];
        } else {
            return ['error' => 'No se pudo eliminar el departamento'];
        }
    }
}