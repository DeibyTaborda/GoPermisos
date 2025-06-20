<?php 

interface TableInterface {
    public function getAttributes(): array;
    public function getTableName(): string;
}