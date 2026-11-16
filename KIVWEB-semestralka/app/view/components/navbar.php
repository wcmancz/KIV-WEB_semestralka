<?php
/*
 * NAVBAR.PHP – společná navigační lišta pro všechny stránky (vkládá se přes require).
 *
 * Šablona, která ji vkládá, musí dřív nastavit:
 *     $aktivni = 'prehled';    // nebo 'sprava' či 'pridat' – podle toho se zvýrazní odkaz
 * a mít k dispozici $user (přihlášený uživatel, nebo null).
 *
 * Odkazy „Správa péče“ a „Přidat“ vidí jen admin a superadmin. Ostatní vidí jen přehled.
 */
if (!defined('ROOT')) {
    exit;
}
$jeAdmin = $user && in_array($user['role'] ?? '', ['admin', 'superadmin'], true);
?>
<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container flex-wrap gap-2">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link<?= $aktivni === 'prehled' ? ' active' : '' ?>" href="index.php">Přehled zvířat</a>
            </li>
            <?php if ($jeAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link<?= $aktivni === 'sprava' ? ' active' : '' ?>" href="index.php?page=admin">Správa péče</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $aktivni === 'pridat' ? ' active' : '' ?>" href="index.php?page=pridat">Přidat</a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="d-flex align-items-center gap-3 text-white">
            <?php if ($user): ?>
                <span class="text-end lh-sm">
                    <?= htmlspecialchars($user['jmeno']) ?>
                    <?php if ($user['typ'] === 'zamestnanec'): ?>
                        <small class="d-block text-white-50"><?= htmlspecialchars($user['role']) ?></small>
                    <?php endif; ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="index.php?page=logout"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</a>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="index.php?page=login"><i class="fa-solid fa-right-to-bracket me-1"></i>Přihlásit se</a>
            <?php endif; ?>
        </div>
    </div>
</nav>