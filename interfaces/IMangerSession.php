<?php
interface IMangerSession {
    public function startSession(): void;
    public function setSession(string $key, mixed $value): void;
    public function getSession(string $key): mixed;
    public function destroySession(): void;
}