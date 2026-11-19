<?php
if (!defined('ROOT')) {
    header('Location: ../../index.php?page=dnes');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dnešní péče – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'dnes'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-1">Dnešní péče</h1>
    <div id="dnes-obsah">
        <p class="text-body-secondary">
            <?= htmlspecialchars($datum) ?> · celkem <?= (int) $celkem ?>, z toho vaše <?= (int) $moje ?>
            · aktualizováno <span id="aktualizovano"><?= htmlspecialchars($aktualizovano) ?></span>
        </p>

        <?php if (!$pavilony): ?>
            <div class="alert alert-info" role="alert">Dnes není přidělena žádná péče.</div>
        <?php endif; ?>

        <?php foreach ($pavilony as $nazevPavilonu => $radky): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0"><i class="fa-solid fa-house me-2"></i><?= htmlspecialchars($nazevPavilonu) ?></h2>
                    <span class="badge text-bg-light border"><?= count($radky) ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="table-layout: fixed; min-width: 36rem;">
                        <colgroup><col style="width: 30%"><col style="width: 25%"><col style="width: 27%"><col style="width: 18%"></colgroup>
                        <tbody>
                        <?php foreach ($radky as $r): ?>
                            <tr<?= $r['moje'] ? ' class="table-primary"' : '' ?>>
                                <td class="lh-sm">
                                    <a class="fw-semibold text-decoration-none" href="index.php?page=zvire&amp;id=<?= (int) $r['ID_zvirata'] ?>"><?= htmlspecialchars($r['jmeno_zvirete']) ?></a>
                                </td>
                                <td><span class="badge text-bg-secondary"><?= htmlspecialchars($r['nazev_typu'] ?? '') ?></span></td>
                                <td class="lh-sm">
                                    <?= htmlspecialchars($r['zamestnanec']) ?>
                                    <?php if ($r['moje']): ?><span class="badge text-bg-primary ms-1">vy</span><?php endif; ?>
                                </td>
                                <td class="text-nowrap text-body-secondary">
                                    <?php if ($r['zacina_dnes']): ?><span class="badge text-bg-info">Začíná dnes</span><?php endif; ?>
                                    <?php if ($r['konci_dnes']): ?><span class="badge text-bg-warning">Končí dnes</span><?php endif; ?>
                                    <?php if (!$r['zacina_dnes'] && !$r['konci_dnes']): ?><?= htmlspecialchars($r['do_text']) ?><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    (function () {
        var obsah = document.getElementById('dnes-obsah');
        if (!obsah || !window.fetch || !window.DOMParser) { return; }

        setInterval(function () {
            if (document.hidden) { return; }
            fetch(window.location.href, { credentials: 'same-origin' })
                .then(function (odpoved) { return odpoved.ok ? odpoved.text() : Promise.reject(); })
                .then(function (text) {
                    var nove = new DOMParser().parseFromString(text, 'text/html').getElementById('dnes-obsah');
                    if (nove) { obsah.innerHTML = nove.innerHTML; }
                })
                .catch(function () {  });
        }, 60000);
    })();
</script>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>