<?php

namespace App\Controller;

use App\Core\BaseController;

class AdminController extends BaseController {
    public function index(): void {
        $this->requireAdmin();

        $user = $this->user();
        echo 'Administrace OK - přihlášen: ' . htmlspecialchars($user['jmeno']) . ' (' . htmlspecialchars($user['role']) . ')';
    }
}