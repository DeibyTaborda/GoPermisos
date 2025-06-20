<?php
class UserController {
    public $userRepository;
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function saveUser(array $data): ?int {
        $data_filter = array_filter($data, function($value) {
            return !empty($value);
        });
    
        if (empty($data_filter)) {
            return null;
        }
    
        return $this->userRepository->save($data_filter) ?: null;
    }
    
    public function updateUser($id, $data) {
        $contrasena = $data['Password'] ?? null;

        if (isset($contrasena) && empty($contrasena)) {
            return ['success' => false, 'message' => 'Ingresa una contraseña'];
        } else if (isset($contrasena) && !empty($contrasena)) {
            $contrasenaEncrypted = password_hash($contrasena, PASSWORD_DEFAULT);
            $data['Password'] = $contrasenaEncrypted;
        }

        $user = $this->userRepository->getById($id);
        $data_filter = [];

        foreach($data as $key => $value) {
            if ($value !== $user[0]->$key) {
                $data_filter[$key] = $value;
            } 
        }
        
        if (empty($data_filter)) {
            return ['success' => false, 'message' => 'No se realizaron cambios', 'type' => 'info'];
        }

        $result = $this->userRepository->update($id, $data_filter);

        if ($result) {
            return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el usuario'];
        }
    }
}