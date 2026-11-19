<?php

if (!defined('ROOT')) {
    exit;
}
$jeAdmin = $user && in_array($user['role'] ?? '', ['admin', 'superadmin'], true);
?>
<nav class="navbar navbar-expand bg-primary sticky-top" data-bs-theme="dark">
    <div class="container flex-wrap gap-2">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link<?= $aktivni === 'prehled' ? ' active' : '' ?>" href="index.php">Přehled zvířat</a>
            </li>
            <?php if ($this->jeZamestnanec()): ?>
                <li class="nav-item">
                    <a class="nav-link<?= $aktivni === 'sprava' ? ' active' : '' ?>" href="index.php?page=admin"><?= $jeAdmin ? 'Správa péče' : 'Moje péče' ?></a>
                </li>
            <?php endif; ?>
            <?php if ($jeAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link<?= $aktivni === 'pridat' ? ' active' : '' ?>" href="index.php?page=pridat">Přidat a odebrat</a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="d-flex align-items-center gap-3 text-white">
            <?php if ($user): ?>
                <span class="text-end lh-sm">
                    <?= htmlspecialchars($user['jmeno']) ?>
                    <?php if ($this->jeZamestnanec()): ?>
                        <small class="d-block text-white-50"><?= htmlspecialchars($user['role']) ?></small>
                    <?php endif; ?>
                </span>
                <form method="post" action="index.php?page=logout" class="d-inline">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($this->csrfToken()) ?>">
                    <button type="submit" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</button>
                </form>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="index.php?page=login"><i class="fa-solid fa-right-to-bracket me-1"></i>Přihlásit se</a>
            <?php endif; ?>
        </div>
    </div>
</nav>