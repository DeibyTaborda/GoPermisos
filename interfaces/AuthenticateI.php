<?php
interface AuthenticateI {
    public function authenticate(string $gmail, string $password): bool|array;
}