<?php

use function PHPSTORM_META\type;

class SedeController {
    private $sedeRepository;
    private $departmentsSedeRepo;

    public function __construct($sedeRepository, $departmentsSedeRepo) {
        $this->sedeRepository = $sedeRepository;
        $this->departmentsSedeRepo = $departmentsSedeRepo;
    }

    public function save($data, $departmentsSede) {
        $this->sedeRepository->pdo->beginTransaction();
        $id_sede = $this->sedeRepository->save($data);
        
        if (!$id_sede) {
            $this->sedeRepository->pdo->rollBack();
            return ['success' => false, 'message' => 'Error en crear la sede'];
        }

        if (!empty($departmentsSede)) {
            foreach($departmentsSede as $department_id) {
                $data = [
                    'sede_id' => $id_sede,
                    'department_id' => $department_id
                ];
                $request2 = $this->departmentsSedeRepo->save($data);
            }
        }

        if (isset($request2) && !$request2) {
             $this->sedeRepository->pdo->rollBack();
            return ['success' => false, 'message' => 'Error en asignar los departamentos'];
        }

        $this->sedeRepository->pdo->commit();
        return ['success' => true, 'message' => 'Sede creada exitosamente'];
    }

    public function update($id_sede, $data) {
        try {
            $this->sedeRepository->pdo->beginTransaction();

            $current_linked_departments = $this->departmentsSedeRepo->getDepartmentsIdBySede($id_sede);
            $newDepartments = $data['departments'];

            $departmentsToUnlink = array_diff($current_linked_departments, $newDepartments);
            $departmentsToLink = array_diff($newDepartments, $current_linked_departments);

            $data_sede = $this->sedeRepository->getById($id_sede);
            $sede_name = $data_sede[0]->sede;
            $sede_filter = $data['sede'] !== $sede_name ? $data['sede'] : '';

            if (isset($sede_filter) && !empty($sede_filter)) {
                $resultSede = $this->sedeRepository->update($id_sede, ['sede' => $sede_filter]);
                if (!$resultSede) {
                    $this->sedeRepository->pdo->rollBack();
                    return ['success' => false, 'message' => 'Error al actualizar la sede'];
                }
            }

            if (!empty($departmentsToUnlink)) {
                $resultUnlink = $this->departmentsSedeRepo->unlinkDepartmentsFromSede($id_sede, $departmentsToUnlink);
                if (!$resultUnlink) {
                    $this->sedeRepository->pdo->rollBack();
                    return ['success' => false, 'message' => 'Error al desvincular departamentos'];
                }
            }

            if (!empty($departmentsToLink)) {
                $resultLink = $this->departmentsSedeRepo->linkDepartmentToSede($id_sede, $departmentsToLink);
                if (!$resultLink) {
                    $this->sedeRepository->pdo->rollBack();
                    return ['success' => false, 'message' => 'Error al vincular departamentos'];
                }
            }

            if(empty($sede_filter) && empty($departmentsToUnlink) && empty($departmentsToLink)) {
                return ['success' => false, 'message' => 'No hay datos por actualizar', 'type' => 'info'];
            }

            $this->sedeRepository->pdo->commit();
            return ['success' => true, 'message' => 'Sede actualizada exitosamente'];
        } catch (\Exception $e) {
            $this->sedeRepository->pdo->rollBack();
            return ['success' => false, 'message' => 'Ocurrió un error: ' . $e->getMessage()];
        }
    }
}