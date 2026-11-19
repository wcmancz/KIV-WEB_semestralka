<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=admin');
    exit;
}
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
    <div id="zpravy">
        <?php if ($uspech): ?>
            <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
        <?php endif; ?>
        <?php if ($chybaAkce): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
        <?php endif; ?>
    </div>

    <?php if ($jeAdmin): ?>
        <div id="bez-pece">
            <?php if ($bezPece): ?>
                <div class="alert alert-warning" role="alert">
                    <div class="fw-semibold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Dnes bez péče: <?= count($bezPece) ?> <?= count($bezPece) === 1 ? 'zvíře' : (count($bezPece) < 5 ? 'zvířata' : 'zvířat') ?></div>
                    <div class="small mb-2">Kliknutím zvíře vyberete ve formuláři „Přidělit péči“.</div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($bezPece as $bz): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-vybrat-zvire="<?= (int) $bz['id_zvirata'] ?>">
                                <?= htmlspecialchars($bz['jmeno_zvirete']) ?>
                                <small class="text-body-secondary"><?= htmlspecialchars($bz['jmeno_pavilonu'] ?? '') ?></small>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-success" role="alert"><i class="fa-solid fa-circle-check me-1"></i>O všechna zvířata je dnes postaráno.</div>
            <?php endif; ?>
        </div>

        <form method="post" action="index.php?page=admin" class="card shadow-sm mb-5" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="akce" value="pridat">

            <div class="card-header"><h2 class="h5 mb-0">Přidělit péči</h2></div>

            <div class="card-body">
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

    <h2 class="h4 mb-3" id="pridelena-pece" style="scroll-margin-top: 5rem;">Přidělená péče</h2>

    <?php /* Hledání a filtry vidí jen admin (formulář odesílá GET, takže filtr zůstane v adrese). */ ?>
    <?php if ($jeAdmin): ?>
        <form method="get" action="index.php#pridelena-pece" class="card shadow-sm mb-3" id="filtr-pece">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="razeni" value="<?= htmlspecialchars($razeni) ?>">
            <input type="hidden" name="smer" value="<?= htmlspecialchars($razeni !== '' ? $smer : '') ?>">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-5">
                        <label for="hledat" class="form-label mb-1">Hledat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="search" class="form-control" id="hledat" name="q" maxlength="100"
                                   placeholder="Zaměstnanec, zvíře, pavilon nebo druh péče"
                                   value="<?= htmlspecialchars($filtr['q']) ?>">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label for="filtr-typ" class="form-label mb-1">Druh péče</label>
                        <select id="filtr-typ" name="typ_filtr" class="form-select">
                            <option value="0">Všechny</option>
                            <?php foreach ($typy as $t): ?>
                                <option value="<?= (int) $t['ID_typ_pece'] ?>" <?= $filtr['typ'] === (int) $t['ID_typ_pece'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nazev_typu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <label for="filtr-stav" class="form-label mb-1">Stav</label>
                        <select id="filtr-stav" name="stav" class="form-select">
                            <option value="">Všechny</option>
                            <?php foreach ($stavy as $klic => $nazev): ?>
                                <option value="<?= htmlspecialchars($klic) ?>" <?= $filtr['stav'] === $klic ? 'selected' : '' ?>><?= htmlspecialchars($nazev) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Hledat</button>
                        <a href="<?= htmlspecialchars($zrusitFiltr) ?>" id="zrusit-filtr" data-ajax class="btn btn-outline-secondary<?= $jeFiltr ? '' : ' d-none' ?>" title="Zrušit filtr"><i class="fa-solid fa-xmark"></i></a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <div id="pridelena-pece-sekce">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <?php /* Hlavičky s odkazy: kliknutí seřadí tabulku podle sloupce, další kliknutí otočí směr. */ ?>
                        <?php foreach ($hlavicky as $h): ?>
                            <th scope="col" aria-sort="<?= $h['aria'] ?>">
                                <a href="<?= htmlspecialchars($h['url']) ?>" data-ajax class="text-decoration-none text-reset text-nowrap">
                                    <?= htmlspecialchars($h['text']) ?>
                                    <i class="fa-solid <?= $h['ikona'] ?> ms-1 <?= $h['aktivni'] ? '' : 'text-body-tertiary' ?>"></i>
                                </a>
                            </th>
                        <?php endforeach; ?>
                        <?php if ($jeAdmin): ?>
                            <th scope="col"><span class="visually-hidden">Akce</span></th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!$zaznamy): ?>
                        <tr><td colspan="<?= $jeAdmin ? 6 : 4 ?>" class="text-center text-body-secondary py-4">
                                <?= $jeFiltr ? 'Filtru nevyhovuje žádný záznam.' : ($jeAdmin ? 'Zatím není přidělena žádná péče.' : 'Zatím vám nebyla přidělena žádná péče.') ?>
                            </td></tr>
                    <?php endif; ?>

                    <?php foreach ($zaznamy as $z): ?>
                        <tr data-id="<?= (int) $z['ID_pece'] ?>"<?= $z['ukoncena'] ? ' class="row-ended"' : '' ?>>
                            <?php if ($jeAdmin): ?>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar"><?= htmlspecialchars($z['inicialy']) ?></span>
                                        <span class="lh-sm">
                                        <span class="fw-semibold"><?= htmlspecialchars($z['zamestnanec']) ?></span>
                                        <small class="d-block text-body-secondary"><?= htmlspecialchars($z['role_zam']) ?></small>
                                    </span>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td class="lh-sm">
                                <a class="fw-semibold text-decoration-none" href="index.php?page=zvire&amp;id=<?= (int) $z['ID_zvirata'] ?>"><?= htmlspecialchars($z['jmeno_zvirete']) ?></a>
                                <small class="d-block text-body-secondary"><?= htmlspecialchars($z['jmeno_pavilonu'] ?? '') ?></small>
                            </td>
                            <td><span class="badge text-bg-<?= $z['typ_barva'] ?>"><?= htmlspecialchars($z['nazev_typu'] ?? '') ?></span></td>
                            <td class="text-nowrap">
                                <?= htmlspecialchars($z['od_cz']) ?>
                                <span class="text-body-secondary">→ <?= htmlspecialchars($z['do_cz']) ?></span>
                            </td>
                            <td><span class="badge text-bg-<?= $z['stav_barva'] ?>"><?= htmlspecialchars($z['stav_text']) ?></span></td>
                            <?php if ($jeAdmin): ?>
                                <td class="cell-actions text-end">
                                    <?php if ($z['muze_ukoncit']): ?>
                                        <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-inline" data-ajax>
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                            <input type="hidden" name="akce" value="ukoncit">
                                            <input type="hidden" name="id_pece" value="<?= (int) $z['ID_pece'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči (poslední den bude dnes)">
                                                <i class="fa-solid fa-circle-stop"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-inline" data-ajax>
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="akce" value="smazat">
                                        <input type="hidden" name="id_pece" value="<?= (int) $z['ID_pece'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Smazat záznam">
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

        <?php if ($celkem > 0): ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <small class="text-body-secondary">Zobrazeno <?= $od ?>–<?= $do ?> z <?= $celkem ?></small>
                <?php if ($stran > 1): ?>
                    <nav aria-label="Stránkování péče">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item<?= $predchozi === null ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars($predchozi ?? '#') ?>"<?= $predchozi !== null ? ' data-ajax' : '' ?> aria-label="Předchozí"><i class="fa-solid fa-chevron-left"></i></a>
                            </li>
                            <?php foreach ($stranky as $s): ?>
                                <?php if ($s['cislo'] === null): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php else: ?>
                                    <li class="page-item<?= $s['aktualni'] ? ' active' : '' ?>"<?= $s['aktualni'] ? ' aria-current="page"' : '' ?>>
                                        <a class="page-link" href="<?= htmlspecialchars($s['url']) ?>" data-ajax><?= $s['cislo'] ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <li class="page-item<?= $dalsi === null ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars($dalsi ?? '#') ?>"<?= $dalsi !== null ? ' data-ajax' : '' ?> aria-label="Další"><i class="fa-solid fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    (function () {
        var sekce = document.getElementById('pridelena-pece-sekce');
        var zpravy = document.getElementById('zpravy');
        var filtr = document.getElementById('filtr-pece');
        if (!sekce || !zpravy || !window.fetch || !window.DOMParser) { return; }

        var bezi = false;

        function nacti(url, volby, bezHistorie) {
            if (bezi) { return; }
            bezi = true;
            sekce.style.opacity = '.5';
            fetch(url, volby || {})
                .then(function (odpoved) {
                    return odpoved.text().then(function (text) { return { url: odpoved.url, text: text }; });
                })
                .then(function (v) {
                    var doc = new DOMParser().parseFromString(v.text, 'text/html');
                    var novaSekce = doc.getElementById('pridelena-pece-sekce');
                    var noveZpravy = doc.getElementById('zpravy');
                    if (!novaSekce || !noveZpravy) { window.location.href = url; return; }

                    sekce.innerHTML = novaSekce.innerHTML;
                    zpravy.innerHTML = noveZpravy.innerHTML;
                    var bezPece = document.getElementById('bez-pece');
                    var noveBezPece = doc.getElementById('bez-pece');
                    if (bezPece && noveBezPece) { bezPece.innerHTML = noveBezPece.innerHTML; oznacBezPece(); }
                    srovnejFiltr(doc);
                    if (!bezHistorie && v.url) { history.pushState(null, '', v.url); }
                })
                .catch(function () { window.location.href = url; })
                .then(function () { bezi = false; sekce.style.opacity = ''; });
        }

        function srovnejFiltr(doc) {
            var novy = doc.getElementById('filtr-pece');
            if (!filtr || !novy) { return; }
            Array.prototype.forEach.call(novy.elements, function (pole) {
                if (!pole.name || pole.name === 'page') { return; }
                var stare = filtr.elements[pole.name];
                if (stare) { stare.value = pole.value; }
            });
            var zrusit = document.getElementById('zrusit-filtr');
            var noveZrusit = doc.getElementById('zrusit-filtr');
            if (zrusit && noveZrusit) {
                zrusit.className = noveZrusit.className;
                zrusit.setAttribute('href', noveZrusit.getAttribute('href'));
            }
        }

        document.addEventListener('click', function (e) {
            var odkaz = e.target.closest('a[data-ajax]');
            if (!odkaz || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) { return; }
            e.preventDefault();
            nacti(odkaz.href);
        });

        sekce.addEventListener('submit', function (e) {
            var formular = e.target.closest('form[data-ajax]');
            if (!formular) { return; }
            e.preventDefault();
            nacti(formular.action, { method: 'POST', body: new FormData(formular) });
        });

        if (filtr) {
            filtr.addEventListener('submit', function (e) {
                e.preventDefault();
                var params = new URLSearchParams();
                new FormData(filtr).forEach(function (hodnota, klic) {
                    if (hodnota !== '' && hodnota !== '0') { params.set(klic, hodnota); }
                });
                nacti('index.php?' + params.toString());
            });
            filtr.addEventListener('change', function (e) {
                if (e.target.tagName === 'SELECT') { filtr.requestSubmit ? filtr.requestSubmit() : filtr.dispatchEvent(new Event('submit', { cancelable: true })); }
            });
        }

        window.addEventListener('popstate', function () { nacti(window.location.href, null, true); });
    })();
</script>

<script>

    function oznacBezPece() {
        document.querySelectorAll('[data-vybrat-zvire]').forEach(function (tlacitko) {
            var pole = document.getElementById('a' + tlacitko.getAttribute('data-vybrat-zvire'));
            tlacitko.classList.toggle('active', !!(pole && pole.checked));
        });
    }
    document.addEventListener('click', function (e) {
        var tlacitko = e.target.closest('[data-vybrat-zvire]');
        if (!tlacitko) { return; }
        var pole = document.getElementById('a' + tlacitko.getAttribute('data-vybrat-zvire'));
        if (!pole) { return; }
        pole.checked = !pole.checked;
        oznacBezPece();
    });
    document.addEventListener('change', oznacBezPece);
    oznacBezPece();
</script>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>