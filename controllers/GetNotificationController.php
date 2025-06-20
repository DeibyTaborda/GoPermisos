<?php
class GetNotificationController {
    private $getNotifications;

    public function __construct(IGetNotifications $getNotifications) {
        $this->getNotifications = $getNotifications;
    }

    public function getNotifications($rol_id, $user_id) {
        $leavesColaboradores = [];
        $leavesUser = [];

        $leavesUser = $this->getNotifications->getNotificationsColaborador($user_id);

        
        if ($rol_id === 2) {
            $leavesColaboradores = $this->getNotifications->getNotificationsLeader($user_id);
        } else if ($rol_id === 3) {
            $leavesColaboradores = $this->getNotifications->getNotificationsDirector($user_id);
        }

        return ['leavesColaboradores' => $leavesColaboradores, 'leavesUser' => $leavesUser];
    }
}