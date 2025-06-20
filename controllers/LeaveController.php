<?php
require_once __DIR__ . "/../utils/utils.php";
class LeaveController {
    public $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository) {
        $this->leaveRepository = $leaveRepository;
    }

    public function saveLeave($data) {
        try {
            $FromDate = convertirHora12a24($data['FromDate']);
            $ToDate = convertirHora12a24($data['ToDate']);
            $LeaveTypeID = $data['LeaveTypeID'];

            if (!isset($data['LeaveTypeID'])) {
                return ["error" => "Debes ingresar el tipo de permiso"];
            }

            if ($FromDate >= $ToDate) {
                return ["error" => "La fecha de ingreso debe ser mayor a la fecha de salida"];
            }

            $data['FromDate'] = $FromDate;
            $data['ToDate'] = $ToDate;

            $result = $this->leaveRepository->save($data);

            return $result
                ? ["message" => "La solicitud de permiso se creó correctamente", "leaveId" => $result]
                : ["error" => "La solicitud de permiso no se creó"];

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function updateLeave($id, $data = []) {
        try {
            $leave = $this->leaveRepository->getLeaveById( $id);

            $filterData = [
                'ToDate' => isset($data['ToDate']) ? convertirHora12a24($data['ToDate']) : null,
                'FromDate' => isset($data['FromDate']) ? convertirHora12a24($data['FromDate']) : null
            ];

            foreach ($filterData as $key => $value) {
                if ($value != null) {
                    $data[$key] = $value;
                }
            }
    
            foreach ($data as $key => $value) {
                if ($value !== $leave->$key) {
                    $data[$key] = $value;
                }
            }

            $success = $this->leaveRepository->update($id, $data);

            return $success
                ? ["message" => "La solicitud de permiso se actualizó exitosamente"]
                : ["error" => "La solicitud no se actualizó"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        } 
    }
    
    public function getLeavesAsLeader($leader_id) {
        return $this->leaveRepository->getLeavesAsLeader($leader_id);
    }

    public function getLeavesAsDirector($director_id) {
        return $this->leaveRepository->getLeavesAsDirector($director_id);
    }

    public function getLeaves() {
        return $this->leaveRepository->getLeaves();
    }

    public function getLeaveById($id) {
        return $this->leaveRepository->getLeaveById($id);
    }

    public function deleteLeave($id) {
        try {
            $result = $this->leaveRepository->delete($id);
            
            return $result
                ? ["message" => "Se eliminó la solicitud de permiso"]
                : ["error" => "No se pudo eliminar la solicitud de permiso"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}