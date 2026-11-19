<?php
if (!defined('ROOT')) {
    exit;
}

$otviraci = [
    1 => ['od' => '09:00', 'do' => '18:00'],
    2 => ['od' => '09:00', 'do' => '18:00'],
    3 => ['od' => '09:00', 'do' => '18:00'],
    4 => ['od' => '09:00', 'do' => '18:00'],
    5 => ['od' => '09:00', 'do' => '18:00'],
    6 => ['od' => '09:00', 'do' => '19:00'],
    7 => ['od' => '09:00', 'do' => '19:00'],
];
$nazvyDnu = [1 => 'Pondělí', 2 => 'Úterý', 3 => 'Středa', 4 => 'Čtvrtek', 5 => 'Pátek', 6 => 'Sobota', 7 => 'Neděle'];

$ted     = new DateTime('now', new DateTimeZone('Europe/Prague'));
$dnes    = (int) $ted->format('N');
$cas     = $ted->format('H:i');
$dnesni  = $otviraci[$dnes] ?? null;

if ($dnesni === null) {
    $stav = ['zavřeno', 'danger', 'Dnes máme zavřeno.'];
} elseif ($cas < $dnesni['od']) {
    $stav = ['zavřeno', 'warning', 'Dnes otevíráme v ' . $dnesni['od'] . '.'];
} elseif ($cas < $dnesni['do']) {
    $stav = ['otevřeno', 'success', 'Dnes zavíráme v ' . $dnesni['do'] . '.'];
} else {
    $stav = ['zavřeno', 'danger', 'Dnes už je zavřeno (zavíralo se v ' . $dnesni['do'] . ').'];
}
?>
<footer class="bg-primary text-white mt-auto py-4" data-bs-theme="dark">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-md-6">
                <h2 class="h6 text-uppercase mb-3"><i class="fa-solid fa-clock me-2"></i>Otevírací doba</h2>
                <ul class="list-unstyled mb-0" style="max-width: 18rem;">
                    <?php foreach ($nazvyDnu as $cislo => $nazev): ?>
                        <?php $h = $otviraci[$cislo] ?? null; ?>
                        <li class="d-flex justify-content-between py-1<?= $cislo === $dnes ? ' fw-bold' : '' ?>">
                            <span><?= htmlspecialchars($nazev) ?></span>
                            <span><?= $h ? htmlspecialchars($h['od'] . ' – ' . $h['do']) : 'zavřeno' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h2 class="h6 text-uppercase mb-3">Právě teď</h2>
                <p class="mb-1"><span class="badge text-bg-<?= $stav[1] ?>"><?= htmlspecialchars($stav[0]) ?></span></p>
                <p class="mb-0 text-white-50"><?= htmlspecialchars($stav[2]) ?></p>
                <p class="mb-1">Petr Nam Hoang</p>
                <p class="mb-1">Zoologická zahrada - ještě vymyslet název :/</p>
            </div>
        </div>
    </div>
</footer>