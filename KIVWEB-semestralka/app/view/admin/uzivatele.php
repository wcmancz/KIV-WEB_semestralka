<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=uzivatele');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uživatelé – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'uzivatele'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-2">Uživatelé</h1>
    <p class="text-body-secondary">
        Určujte role a mažte účty.
        <?= $jeSuperadmin ? 'Jako superadmin můžete spravovat i administrátory.' : 'Administrátory smí upravit jen superadmin.' ?>
    </p>

    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <details class="mb-4"<?= $chybyZam ? ' open' : '' ?>>
        <summary class="btn btn-primary mb-3"><i class="fa-solid fa-user-plus me-1"></i>Nový zaměstnanec</summary>
        <form method="post" action="index.php?page=uzivatele" class="card shadow-sm mb-4" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="akce" value="novy_zamestnanec">
            <div class="card-header"><h2 class="h5 mb-0"><i class="fa-solid fa-user-plus me-2"></i>Nový zaměstnanec</h2></div>

            <div class="card-body">
                <?php if ($chybyZam): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($chybyZam as $chyba): ?>
                                <li><?= htmlspecialchars($chyba) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="jmeno" class="form-label">Jméno</label>
                        <input type="text" class="form-control<?= isset($chybyZam['jmeno']) ? ' is-invalid' : '' ?>" id="jmeno" name="jmeno"
                               maxlength="50" autocomplete="off" required value="<?= htmlspecialchars($oldZam['jmeno']) ?>">
                    </div>
                    <div class="col-sm-6">
                        <label for="prijmeni" class="form-label">Příjmení</label>
                        <input type="text" class="form-control<?= isset($chybyZam['prijmeni']) ? ' is-invalid' : '' ?>" id="prijmeni" name="prijmeni"
                               maxlength="50" autocomplete="off" required value="<?= htmlspecialchars($oldZam['prijmeni']) ?>">
                    </div>
                    <div class="col-sm-4">
                        <label for="vek" class="form-label">Věk</label>
                        <input type="number" class="form-control<?= isset($chybyZam['vek']) ? ' is-invalid' : '' ?>" id="vek" name="vek"
                               min="15" max="99" required value="<?= htmlspecialchars($oldZam['vek']) ?>">
                    </div>
                    <div class="col-sm-8">
                        <label for="id-role" class="form-label">Role</label>
                        <select id="id-role" name="id_role" class="form-select<?= isset($chybyZam['id_role']) ? ' is-invalid' : '' ?>" required>
                            <option value="" disabled<?= $oldZam['id_role'] === 0 ? ' selected' : '' ?>>Vyberte…</option>
                            <?php foreach ($roleNovy as $r): ?>
                                <option value="<?= (int) $r['id_role'] ?>"<?= $oldZam['id_role'] === (int) $r['id_role'] ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($r['role_zam']) ?><?= isset($popisRole[$r['role_zam']]) ? ' – ' . htmlspecialchars($popisRole[$r['role_zam']]) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control<?= isset($chybyZam['email']) ? ' is-invalid' : '' ?>" id="email" name="email"
                               maxlength="100" autocomplete="off" required value="<?= htmlspecialchars($oldZam['email']) ?>">
                        <div class="form-text">Zaměstnanec se může přihlásit e-mailem i přihlašovacím jménem.</div>
                    </div>
                    <div class="col-12">
                        <label for="login" class="form-label">Přihlašovací jméno</label>
                        <input type="text" class="form-control<?= isset($chybyZam['login']) ? ' is-invalid' : '' ?>" id="login" name="login"
                               maxlength="50" autocomplete="off" required value="<?= htmlspecialchars($oldZam['login']) ?>">
                        <div class="form-text">Písmena bez diakritiky, čísla, tečka, pomlčka a podtržítko (3 až 50 znaků).</div>
                    </div>
                    <div class="col-sm-6">
                        <label for="heslo" class="form-label">Heslo</label>
                        <input type="password" class="form-control<?= isset($chybyZam['heslo']) ? ' is-invalid' : '' ?>" id="heslo" name="heslo"
                               minlength="8" autocomplete="new-password" required>
                        <div class="form-text">Alespoň 8 znaků.</div>
                    </div>
                    <div class="col-sm-6">
                        <label for="heslo2" class="form-label">Heslo znovu</label>
                        <input type="password" class="form-control<?= isset($chybyZam['heslo2']) ? ' is-invalid' : '' ?>" id="heslo2" name="heslo2"
                               minlength="8" autocomplete="new-password" required>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Vytvořit zaměstnance</button>
            </div>
        </form>
    </details>

    <form method="get" action="index.php" class="card shadow-sm mb-3">
        <input type="hidden" name="page" value="uzivatele">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-8 col-lg-6">
                    <label for="hledat" class="form-label mb-1">Hledat</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="hledat" name="q" maxlength="100"
                               placeholder="Jméno, login, e-mail nebo role" value="<?= htmlspecialchars($q) ?>">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Hledat</button>
                    <?php if ($q !== ''): ?>
                        <a href="index.php?page=uzivatele" class="btn btn-outline-secondary" title="Zrušit hledání"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-karty align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col">Uživatel</th>
                    <th scope="col">Role</th>
                    <th scope="col"><span class="visually-hidden">Akce</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$uzivatele): ?>
                    <tr><td colspan="3" class="text-center text-body-secondary py-4"><?= $q !== '' ? 'Hledání nevyhovuje žádný uživatel.' : 'Žádní uživatelé.' ?></td></tr>
                <?php endif; ?>

                <?php foreach ($uzivatele as $u): ?>
                    <?php $jmeno = trim($u['jmeno'] . ' ' . ($u['prijmeni'] ?? '')); ?>
                    <tr>
                        <td class="lh-sm">
                            <span class="fw-semibold"><?= htmlspecialchars($jmeno) ?></span>
                            <small class="d-block text-body-secondary"><?= htmlspecialchars($u['login']) ?> · <?= htmlspecialchars($u['email']) ?></small>
                        </td>

                        <td data-label="Role">
                            <?php if ($u['duvod'] === null): ?>
                                <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-flex gap-2">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="akce" value="zmenit_roli">
                                    <input type="hidden" name="id" value="<?= (int) $u['id_uzivatele'] ?>">
                                    <select name="id_role" class="form-select form-select-sm" aria-label="Role uživatele <?= htmlspecialchars($jmeno) ?>" style="min-width: 9rem;">
                                        <?php foreach ($role as $r): ?>
                                            <option value="<?= (int) $r['id_role'] ?>" <?= (int) $r['id_role'] === (int) $u['id_role'] ? 'selected' : '' ?>><?= htmlspecialchars($r['role_zam']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Uložit roli"><i class="fa-solid fa-floppy-disk"></i></button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($u['role_zam']) ?>
                            <?php endif; ?>
                        </td>

                        <td class="cell-actions text-end text-nowrap">
                            <?php if ($u['duvod_smazat'] === null): ?>
                                <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-inline"
                                      onsubmit="return confirm('Opravdu smazat účet <?= htmlspecialchars(addslashes($jmeno), ENT_QUOTES) ?>? Nelze to vrátit.');">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="akce" value="smazat">
                                    <input type="hidden" name="id" value="<?= (int) $u['id_uzivatele'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Smazat účet"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <span title="<?= htmlspecialchars($u['duvod_smazat']) ?>">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled><i class="fa-solid fa-trash"></i></button>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>