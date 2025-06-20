<?php

interface IUserDepartmentManager {
    public function createUser(array $userData, array $departmentsID, ?array $permissions_director): array;
    public function updateUser(int $idUser, array $userData, array $departmentsID, ?array $permissions_director): array;
}