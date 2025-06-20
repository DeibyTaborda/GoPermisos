<?php 
interface RepositoryInterface {
    public function save(array $data): bool|string;
    public function update(int $id, array $data): bool;
    public function getById(int $id): array;
    public function get(): array;
    public function delete(int $id): bool;
}