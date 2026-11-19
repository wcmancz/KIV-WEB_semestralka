<?php

if (!defined('ROOT')) {
    exit;
}
$jeAdmin = $user && in_array($user['role'] ?? '', ['admin', 'superadmin'], true);
$jeSpravce = $user && in_array($user['role'] ?? '', ['zamestnanec', 'admin', 'superadmin'], true);
?>
<nav class="navbar navbar-expand-xl bg-primary sticky-top" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hlavni-menu"
                aria-controls="hlavni-menu" aria-expanded="false" aria-label="Zobrazit menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="hlavni-menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link<?= $aktivni === 'prehled' ? ' active' : '' ?>" href="index.php">Přehled zvířat</a>
                </li>
                <?php if ($user): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'rezervace' ? ' active' : '' ?>" href="index.php?page=rezervace">Rezervace</a>
                    </li>
                <?php endif; ?>
                <?php if ($this->jeZamestnanec()): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'dnes' ? ' active' : '' ?>" href="index.php?page=dnes">Dnes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'sprava' ? ' active' : '' ?>" href="index.php?page=admin"><?= $jeAdmin ? 'Správa péče' : 'Moje péče' ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($jeSpravce): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'zvirata' ? ' active' : '' ?>" href="index.php?page=zvirata">Zvířata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'statistiky' ? ' active' : '' ?>" href="index.php?page=statistiky">Statistiky</a>
                    </li>
                <?php endif; ?>
                <?php if ($jeAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $aktivni === 'uzivatele' ? ' active' : '' ?>" href="index.php?page=uzivatele">Uživatelé</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white py-2 py-xl-0">
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
    </div>
</nav>
<script src="assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>