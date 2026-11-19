<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=statistiky');
    exit;
}

/** Vodorovný pruh: šířka v % podle hodnoty. */
$pruh = fn(int $hodnota, int $max): int => (int) round($hodnota / $max * 100);

$dlazdice = [
    ['hodnota' => $souhrn['zvirat'],            'popis' => 'zvířat',                 'ikona' => 'fa-paw'],
    ['hodnota' => $souhrn['zamestnancu'],       'popis' => 'zaměstnanců',            'ikona' => 'fa-users'],
    ['hodnota' => $souhrn['aktivni_pece'],      'popis' => 'péčí platí dnes',        'ikona' => 'fa-hand-holding-heart'],
    ['hodnota' => $souhrn['bez_pece'],          'popis' => 'zvířat dnes bez péče',   'ikona' => 'fa-triangle-exclamation'],
    ['hodnota' => $souhrn['otevrene_problemy'], 'popis' => 'otevřených problémů',    'ikona' => 'fa-circle-exclamation'],
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistiky – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'statistiky'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-4">Statistiky</h1>

    <div class="row g-3 mb-4">
        <?php foreach ($dlazdice as $d): ?>
            <div class="col-6 col-md-4 col-xl">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <i class="fa-solid <?= $d['ikona'] ?> text-body-secondary mb-2"></i>
                        <div class="fs-2 fw-semibold lh-1"><?= (int) $d['hodnota'] ?></div>
                        <small class="text-body-secondary"><?= htmlspecialchars($d['popis']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h2 class="h6 mb-0">Péče podle druhu <small class="text-body-secondary fw-normal">(všechny záznamy · dnes)</small></h2></div>
                <div class="card-body">
                    <?php foreach ($druhy as $r): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span><?= htmlspecialchars($r['nazev']) ?></span>
                                <span class="text-body-secondary"><?= (int) $r['celkem'] ?> · <?= (int) $r['dnes'] ?></span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="<?= htmlspecialchars($r['nazev']) ?>" aria-valuenow="<?= (int) $r['celkem'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxDruh ?>">
                                <div class="progress-bar" style="width: <?= $pruh((int) $r['celkem'], $maxDruh) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h2 class="h6 mb-0">Pavilony</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th scope="col">Pavilon</th><th scope="col" class="text-end">Zvířat</th><th scope="col" class="text-end">Péčí dnes</th></tr></thead>
                        <tbody>
                        <?php foreach ($pavilony as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nazev']) ?></td>
                                <td class="text-end"><?= (int) $r['zvirat'] ?></td>
                                <td class="text-end"><?= (int) $r['dnes'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header"><h2 class="h6 mb-0">Zaměstnanci <small class="text-body-secondary fw-normal">(počet záznamů péče, zvířat a dnů péče)</small></h2></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col">Zaměstnanec</th>
                    <th scope="col">Role</th>
                    <th scope="col" style="min-width: 10rem;">Záznamů</th>
                    <th scope="col" class="text-end">Zvířat</th>
                    <th scope="col" class="text-end">Dnů péče</th>
                    <th scope="col" class="text-end">Dnes</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($zamestnanci as $r): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($r['zamestnanec']) ?></td>
                        <td><?= htmlspecialchars($r['role_zam']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" role="progressbar" aria-label="Záznamů péče" aria-valuenow="<?= (int) $r['pocet_zaznamu'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxZam ?>" style="height: .5rem;">
                                    <div class="progress-bar" style="width: <?= $pruh((int) $r['pocet_zaznamu'], $maxZam) ?>%"></div>
                                </div>
                                <span class="small"><?= (int) $r['pocet_zaznamu'] ?></span>
                            </div>
                        </td>
                        <td class="text-end"><?= (int) $r['pocet_zvirat'] ?></td>
                        <td class="text-end"><?= (int) $r['dnu_pece'] ?></td>
                        <td class="text-end"><?= (int) $r['dnes'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h2 class="h6 mb-0">Nejvíce péče: top 10 zvířat</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr><th scope="col">Zvíře</th><th scope="col">Pavilon</th><th scope="col" style="min-width: 8rem;">Záznamů</th><th scope="col" class="text-end">Dnes</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($zvirata as $r): ?>
                            <tr>
                                <td><a class="fw-semibold text-decoration-none" href="index.php?page=zvire&amp;id=<?= (int) $r['id_zvirata'] ?>"><?= htmlspecialchars($r['jmeno_zvirete']) ?></a></td>
                                <td><?= htmlspecialchars($r['jmeno_pavilonu'] ?? '–') ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" role="progressbar" aria-label="Záznamů péče" aria-valuenow="<?= (int) $r['zaznamu'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxZvire ?>" style="height: .5rem;">
                                            <div class="progress-bar" style="width: <?= $pruh((int) $r['zaznamu'], $maxZvire) ?>%"></div>
                                        </div>
                                        <span class="small"><?= (int) $r['zaznamu'] ?></span>
                                    </div>
                                </td>
                                <td class="text-end"><?= (int) $r['dnes'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h2 class="h6 mb-0">Dnes bez péče <span class="badge text-bg-warning ms-1"><?= count($bezPece) ?></span></h2></div>
                <?php if (!$bezPece): ?>
                    <div class="card-body text-body-secondary">O všechna zvířata je dnes postaráno.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bezPece as $r): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <a class="text-decoration-none" href="index.php?page=zvire&amp;id=<?= (int) $r['id_zvirata'] ?>"><?= htmlspecialchars($r['jmeno_zvirete']) ?></a>
                                <small class="text-body-secondary"><?= htmlspecialchars($r['jmeno_pavilonu'] ?? '–') ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>