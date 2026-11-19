<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=admin');
    exit;
}

$barva = [1 => 'warning', 4 => 'warning', 2 => 'success', 6 => 'success', 3 => 'secondary', 5 => 'secondary'];

$cz = fn(?string $d): string => $d ? date('j. n. Y', strtotime($d)) : '';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Správa péče – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'sprava'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-4"><?= $jeAdmin ? 'Správa péče' : 'Moje péče' ?></h1>
    <?php /* Zprávy po provedené akci (zelená = povedlo se, červená = nepovedlo se). */ ?>
    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <?php if ($jeAdmin): ?>
        <form method="post" action="index.php?page=admin" class="card shadow-sm mb-5" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="akce" value="pridat">

        <div class="card-header"><h2 class="h5 mb-0">Přidělit péči</h2></div>

        <div class="card-body">
            <?php /* Všechny chyby formuláře najednou. */ ?>
            <?php if ($chyby): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($chyby as $chyba): ?>
                            <li><?= htmlspecialchars($chyba) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="h6 text-uppercase text-body-secondary mb-2">1 · Zaměstnanec</h3>
                    <div class="list-group list-pick">
                        <?php foreach ($zamestnanci as $zam): ?>
                            <label class="list-group-item d-flex align-items-center gap-2">
                                <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance"
                                       value="<?= (int) $zam['ID_zamestnance'] ?>"
                                        <?= $old['zamestnanec'] === (int) $zam['ID_zamestnance'] ? 'checked' : '' ?>>
                                <span class="lh-sm flex-grow-1">
                                    <span class="fw-semibold"><?= htmlspecialchars($zam['jmeno'] . ' ' . $zam['prijmeni']) ?></span>
                                    <small class="d-block text-body-secondary"><?= htmlspecialchars($zam['role_zam']) ?></small>
                                </span>
                                <span class="badge rounded-pill text-bg-light border" title="Aktivní služby"><?= (int) $zam['pocet'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h3 class="h6 text-uppercase text-body-secondary mb-3">2 · Zvířata <span class="fw-normal text-lowercase">(lze vybrat více)</span></h3>
                    <?php foreach ($pavilony as $pav): ?>
                        <?php if (empty($pav['zvirata'])) { continue; } ?>
                        <p class="fw-semibold small mb-2"><?= htmlspecialchars($pav['info']['jmeno_pavilonu']) ?></p>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($pav['zvirata'] as $z): ?>
                                <?php /* Tlačítko-přepínač: skrytý checkbox + štítek, který vypadá jako tlačítko (Bootstrap btn-check). */ ?>
                                <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a<?= (int) $z['id_zvirata'] ?>"
                                       value="<?= (int) $z['id_zvirata'] ?>"
                                        <?= in_array((int) $z['id_zvirata'], $old['zvirata'], true) ? 'checked' : '' ?>>
                                <label class="btn btn-outline-primary btn-sm rounded-pill" for="a<?= (int) $z['id_zvirata'] ?>"><?= htmlspecialchars($z['jmeno_zvirete']) ?></label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="typ-pece" class="form-label mb-1">3 · Druh péče</label>
                    <select id="typ-pece" name="typ_pece" class="form-select">
                        <option value="">Vyberte…</option>
                        <?php foreach ($typy as $t): ?>
                            <option value="<?= (int) $t['ID_typ_pece'] ?>" <?= $old['typ'] === (int) $t['ID_typ_pece'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nazev_typu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="datum-od" class="form-label mb-1">Od</label>
                    <input type="date" id="datum-od" name="datum_od" class="form-control" value="<?= htmlspecialchars($old['od']) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label for="datum-do" class="form-label mb-1">Do <span class="text-body-secondary">(nepovinné)</span></label>
                    <input type="date" id="datum-do" name="datum_do" class="form-control" value="<?= htmlspecialchars($old['do']) ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Přidělit</button>
                </div>
            </div>
            <div class="form-text mt-2">Každé vybrané zvíře se uloží jako samostatný záznam. Prázdné „Do“ znamená dobu neurčitou.</div>
        </div>
    </form>
    <?php endif; ?>

    <h2 class="h4 mb-3">Přidělená péče</h2>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <?php if ($jeAdmin): ?>
                        <th scope="col">Zaměstnanec</th>
                    <?php endif; ?>
                    <th scope="col">Zvíře</th>
                    <th scope="col">Péče</th>
                    <th scope="col">Období</th>
                    <th scope="col">Stav</th>
                    <?php if ($jeAdmin): ?>
                        <th scope="col"><span class="visually-hidden">Akce</span></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (!$zaznamy): ?>
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">Zatím není přidělena žádná péče.</td></tr>
                <?php endif; ?>

                <?php foreach ($zaznamy as $z): ?>
                    <?php
                    // Stav záznamu podle dnešního data (texty ve tvaru RRRR-MM-DD se dají porovnat přímo).
                    $planovana = $z['datum_od'] !== null && $z['datum_od'] > $dnes;           // ještě nezačala
                    $ukoncena  = $z['datum_do'] !== null && $z['datum_do'] < $dnes;           // už skončila
                    $konciDnes = $z['datum_do'] !== null && $z['datum_do'] === $dnes;         // dnes je poslední den
                    $muzeUkoncit = !$planovana && !$ukoncena && !$konciDnes;                  // běží a nemá konec
                    $inicialy = mb_strtoupper(mb_substr($z['jmeno'], 0, 1) . mb_substr($z['prijmeni'], 0, 1));
                    ?>
                    <tr data-id="<?= (int) $z['ID_pece'] ?>"<?= $ukoncena ? ' class="row-ended"' : '' ?>>
                        <?php if($jeAdmin): ?>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar"><?= htmlspecialchars($inicialy) ?></span>
                                    <span class="lh-sm">
                                            <span class="fw-semibold"><?= htmlspecialchars($z['jmeno'] . ' ' . $z['prijmeni']) ?></span>
                                            <small class="d-block text-body-secondary"><?= htmlspecialchars($z['role_zam']) ?></small>
                                        </span>
                                </div>
                            </td>
                        <?php endif; ?>
                        <td class="lh-sm">
                            <span class="fw-semibold"><?= htmlspecialchars($z['jmeno_zvirete']) ?></span>
                            <small class="d-block text-body-secondary"><?= htmlspecialchars($z['jmeno_pavilonu'] ?? '') ?></small>
                        </td>
                        <td><span class="badge text-bg-<?= $barva[(int) $z['typ_pece']] ?? 'secondary' ?>"><?= htmlspecialchars($z['nazev_typu'] ?? '') ?></span></td>
                        <td class="text-nowrap">
                            <?= $cz($z['datum_od']) ?>
                            <span class="text-body-secondary">→ <?= $z['datum_do'] ? $cz($z['datum_do']) : 'bez konce' ?></span>
                        </td>
                        <td>
                            <?php if ($ukoncena): ?>
                                <span class="badge text-bg-secondary">Ukončená</span>
                            <?php elseif ($planovana): ?>
                                <span class="badge text-bg-info">Naplánovaná</span>
                            <?php elseif ($konciDnes): ?>
                                <span class="badge text-bg-warning">Končí dnes</span>
                            <?php else: ?>
                                <span class="badge text-bg-success">Aktivní</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($jeAdmin): ?>
                            <td class="cell-actions text-end">
                                <?php if ($muzeUkoncit): ?>
                                    <form method="post" action="index.php?page=admin" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="id_pece" value="<?= (int) $z['ID_pece'] ?>">
                                        <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči (poslední den bude dnes)">
                                            <i class="fa-solid fa-circle-stop"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="index.php?page=admin" class="d-inline">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="id_pece" value="<?= (int) $z['ID_pece'] ?>">
                                    <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat záznam">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
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