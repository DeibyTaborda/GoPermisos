<?php
require_once __DIR__ . "/../interfaces/IMangerSession.php";
class ManagerSession implements IMangerSession {
    private $sessionName = 'user_session';
    private $sessionId;

    public function startSession(): void {
        session_name($this->sessionName);
        session_start();
        $this->sessionId = session_id();
    }

    public function setSession(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public function getSession(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public function destroySession(): void {
        session_unset();
        session_destroy();
    }       
}