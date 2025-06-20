<?php
interface IGetNotifications {
    public function getNotificationsLeader(int $user_id): ?array;
    public function getNotificationsDirector(int $user_id): ?array;
    public function getNotificationsColaborador(int $user_id):?array;
}