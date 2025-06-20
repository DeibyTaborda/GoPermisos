<?php
class UserDepartmentController {
    private $userDepartmentRepository;

    public function __construct(UserDepartmentRepository $userDepartmentRepository) {
        $this->userDepartmentRepository = $userDepartmentRepository;
    }

    public function saveUserDepartment($data) {

        if (empty($data['DepartmentID'])) {
            return ['error' => 'Selecciona un departamento'];
        }

        $data_filter = array_filter($data, function($value) {
            return !empty($value);
        });

        $result = $this->userDepartmentRepository->save($data_filter);

        if ($result) {
            return [
                'message' => 'Usuario creado correctamente',
                'id' => $result
            ];
        } else {
            return ['error' => 'Error en crear el usuario'];
        }
    }

    public function updateUserDepartment($id, $data) {
        $userDepartment = $this->userDepartmentRepository->getById($id);
        $data_filter = [];

        foreach($data as $key => $value) {
            if ($value !== $userDepartment[0]->$key) {
                $data_filter[$key] = $value;
            } else {
                $data_filter[$key] = $data[$key];
            }
        }

        $result = $this->userDepartmentRepository->update($id, $data_filter);

        if ($result) {
            return ['message' => 'Departamento actualizado correctamente'];
        } else {
            return ['error' => 'Error en actualizar el departamento'];
        }
    }
}