<?php

namespace App\Core;

abstract class BaseController{
    protected function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    protected function jeZamestnanec(): bool {
        $user = $this->user();
        return $user !== null && $user['role'] !== 'navstevnik';
    }

    protected function vyzadatRoli(array $povoleneRole): void {
        $user = $this->user();

        if ($user === null) {
            header("location: index.php?page=login");
            exit;
        }

        if (!in_array($user['role'], $povoleneRole, true)) {
            http_response_code(403);
            require ROOT . 'app/view/error/403.php';
            exit;
        }
    }

    protected function csrfToken(): string {
        if(empty($_SESSION['csrf'])){
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    protected function csrfPlatny(): bool {
        $token = $_POST["csrf"] ?? '';
        return is_string($token) && hash_equals($this->csrfToken(), $token);
    }

    protected function overCsrf(): void {
        $token = $_POST["csrf"] ?? '';
        if (!is_string($token) || !hash_equals($this->csrfToken(), $token)) {
            http_response_code(400);
            exit('Neplatný požadavek (chybí bezpečnostní token). Načtěte stránku znovu');
        }
    }

    protected function vyzadatAdmina(): void {
        $this->vyzadatRoli(['admin', 'superadmin']);
    }
}