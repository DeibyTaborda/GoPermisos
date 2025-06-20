<?php
class LeaveTypeController {
    private $leaveTypeRepository;

    public function __construct(LeaveTypeRepository $leaveTypeRepository) {
        $this->leaveTypeRepository = $leaveTypeRepository;
    }

    public function saveLeaveType($data) {
        $data_filter = [];
        $data_filter = array_filter($data, function($value) {
            return !empty($value);
        });

        $result = $this->leaveTypeRepository->save($data_filter);

        if ($result) {
            return ['success' => true, 'message' => 'Licencia creada correctamente', 'id' => $result];
        } else {
            return ['success' => false, 'message' => 'Error en crear la licencia'];
        }
    }

    public function updateLeaveType($id, $data) {
        $leavetype = $this->leaveTypeRepository->getById($id);
        $data_filter = [];

        foreach($data as $key => $value) {
            if ($value !== $leavetype[0]->$key) {
                $data_filter[$key] = $value;
            }
        }

        if (empty($data_filter)) {
            return ['success' => false, 'message' => 'No se realizaron cambios',  'type' => 'info'];
        }

        $result = $this->leaveTypeRepository->update($id, $data_filter);

        if ($result) {
            return ['success' => true, 'message' => 'Licencia actualizada correctamente'];
        } else {
            return ['success' => false , 'message' => 'Error en actualizar la licencia'];
        }
    }

    public function getLeaveTypes($filter = []) {
        return $this->leaveTypeRepository->getLeaveTypes($filter);
    }

    public function getByIdLeaveType($id) {
        return $this->leaveTypeRepository->getById($id);
    }

    public function deleteLeaveType($id) {
        $result = $this->leaveTypeRepository->delete($id);
        if ($result) {
            return ['message' => 'Tipo de licencia eliminado correctamente'];
        } else {
            return ['error' => 'No se pudo eliminar el tipo de licencia'];
        }
    }
}