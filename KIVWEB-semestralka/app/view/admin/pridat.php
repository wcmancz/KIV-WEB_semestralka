<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=pridat');
    exit;
}

$popisRole = [
        'superadmin'  => 'plný přístup',
        'admin'       => 'spravuje péči a zvířata',
        'zamestnanec' => 'stará se o zvířata',
        'brigadnik'   => 'dočasná výpomoc',
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přidat – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php $aktivni = 'pridat'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4">
    <h1 class="mb-4">Přidat</h1>

    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-xl-6">
            <form method="post" action="index.php?page=pridat" class="card shadow-sm h-100" novalidate>
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
                                <?php foreach ($role as $r): ?>
                                    <option value="<?= (int) $r['id_role'] ?>"<?= $oldZam['id_role'] === (int) $r['id_role'] ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($r['role_zam']) ?><?= isset($popisRole[$r['role_zam']]) ? ' – ' . htmlspecialchars($popisRole[$r['role_zam']]) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
        </div>

        <div class="col-xl-6">
            <form method="post" action="index.php?page=pridat" class="card shadow-sm h-100" novalidate>
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="akce" value="nove_zvire">
                <div class="card-header"><h2 class="h5 mb-0"><i class="fa-solid fa-paw me-2"></i>Nové zvíře</h2></div>

                <div class="card-body">
                    <?php if ($chybyZvire): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($chybyZvire as $chyba): ?>
                                    <li><?= htmlspecialchars($chyba) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="jmeno-zvirete" class="form-label">Název zvířete</label>
                            <input type="text" class="form-control<?= isset($chybyZvire['jmeno']) ? ' is-invalid' : '' ?>" id="jmeno-zvirete" name="jmeno_zvirete"
                                   maxlength="50" autocomplete="off" required value="<?= htmlspecialchars($oldZvire['jmeno']) ?>">
                            <div class="form-text">Například „Lev pustinný“.</div>
                        </div>
                        <div class="col-12">
                            <label for="potrava" class="form-label">Potrava</label>
                            <select id="potrava" name="potrava" class="form-select<?= isset($chybyZvire['potrava']) ? ' is-invalid' : '' ?>">
                                <option value="0">Neuvedeno</option>
                                <?php foreach ($potravy as $p): ?>
                                    <option value="<?= (int) $p['ID_potrava'] ?>"<?= $oldZvire['potrava'] === (int) $p['ID_potrava'] ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($p['typ_potravy']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <span class="form-label d-block">Pavilon</span>
                            <div class="list-group list-pick">
                                <?php foreach ($pavilony as $pav): ?>
                                    <label class="list-group-item d-flex align-items-center gap-2">
                                        <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon"
                                               value="<?= (int) $pav['ID_pavilon'] ?>"
                                                <?= $oldZvire['pavilon'] === (int) $pav['ID_pavilon'] ? 'checked' : '' ?>>
                                        <span class="lh-sm flex-grow-1">
                                            <span class="fw-semibold"><?= htmlspecialchars($pav['jmeno_pavilonu']) ?></span>
                                            <small class="d-block text-body-secondary"><?= htmlspecialchars($pav['lokace']) ?> · <?= number_format((float) $pav['rozloha'], 0, ',', ' ') ?> m²</small>
                                        </span>
                                        <span class="badge rounded-pill text-bg-light border" title="Počet zvířat"><?= (int) $pav['pocet'] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Přidat zvíře</button>
                </div>
            </form>
        </div>
    </div>
</main>
</body>
</html>