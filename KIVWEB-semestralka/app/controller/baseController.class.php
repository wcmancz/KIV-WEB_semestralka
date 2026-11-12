<?php

namespace App\Core;

abstract class BaseController{
    protected function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /*
     * pro kontrolu rolí na vyzadatAdmina()
     */
    protected function vyzadatRoli(array $povoleneRole): void {
        $user = $this->user();

        if ($user === null) {
            header("location: index.php?page=login");
            exit;
        }

        if (!in_array($user['role'], $povoleneRole, true)) {
            http_response_code(403);
            require ROOT . 'app/view/error/403.php';
        }
    }

    protected function vyzadatAdmina(): void {
        $this->vyzadatRoli(['admin', 'superadmin']);
    }
}