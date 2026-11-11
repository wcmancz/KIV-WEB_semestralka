<?php
/*
 * INDEX – vstupní bod webu (rozcestník) a úvodní stránka s přehledem zvířat.
 *
 * Co se tu děje, shora dolů:
 *   1. načte se autoloader (ten pak sám načítá všechny třídy podle namespace)
 *   2. spustí se session (aby web poznal přihlášeného uživatele)
 *   3. podle ?page= se rozhodne, kterou stránku zobrazit
 *   4. z databáze se načtou pavilony a zvířata
 *   5. dole je HTML, do kterého se data vypíšou
 */
const ROOT = __DIR__ . '/';

// Jediný ruční require pro třídy. Autoloader pak při prvním použití třídy (new Database)
require ROOT . 'app/core/autoloader.inc.php';

// Řekne PHP, že `Database` znamená App\Core\Database (soubor app/core/database.class.php).
// Samotné `use` nic nenačítá, třída se načte až ve chvíli, kdy ji poprvé použijeme.
use App\Core\Database;
use App\Controller\RegisterController;
use App\Controller\LoginController;

session_start();   // musí být před jakýmkoli výstupem; pak je dostupné pole $_SESSION

// adresa index.php?page=login načte login.php, ?page=logout načte logout.php.
// Bez parametru je stránka 'uvod' (tento soubor). Cokoli jiného = chyba 404.
// (require zde načítá stránky/šablony, ne třídy, proto zůstává.)
$page = $_GET['page'] ?? 'uvod';
if ($page === 'login')  { (new LoginController())->login();   exit; }   // exit = dál už nic nevykonávat
if ($page === 'register') { (new RegisterController())->register(); exit; }
if ($page === 'logout') { (new LoginController())->logout();  exit; }
if ($page !== 'uvod')   { http_response_code(404); exit('Stránka nenalezena.'); }

// ÚVODNÍ STRÁNKA: načtení dat. Jeden dotaz vrátí řádek pro každé zvíře (spojení tří tabulek).
// LEFT JOIN zajistí, že se zobrazí i pavilon bez zvířat a zvíře bez potravy.
$db = new Database();   // tady autoloader načte app/core/database.class.php
$radky = $db->select(
        'SELECT pav.ID_pavilon, pav.jmeno_pavilonu, pav.lokace, pav.rozloha, z.jmeno_zvirete, p.typ_potravy
     FROM pavilon pav
     LEFT JOIN zvirata z ON z.pavilon = pav.ID_pavilon
     LEFT JOIN potrava p ON p.ID_potrava = z.potrava
     ORDER BY pav.jmeno_pavilonu, z.jmeno_zvirete'
);

// Výsledek: $pavilony[ID]['info'] = údaje o pavilonu, $pavilony[ID]['zvirata'] = seznam zvířat.
$pavilony = [];
foreach ($radky as $r) {
    $pavilony[$r['ID_pavilon']]['info'] = $r;
    if ($r['jmeno_zvirete'] !== null) {                // pavilon bez zvířat má v tomto sloupci NULL
        $pavilony[$r['ID_pavilon']]['zvirata'][] = $r;
    }
}

// Přihlášený uživatel: pole uložené při přihlášení v login.php, nebo null (nikdo přihlášen).
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo – přehled zvířat</title>
    <?php /* Styly.
       1) bootstrap.min.css – hotové styly (karty, tlačítka, navigace, mřížka…); HTML je jen používá přes názvy tříd
       2) all.min.css – ikony Font Awesome (<i class="fa-solid fa-paw">)
       3) style.css – naše úpravy (zelená barva, drobnosti) */ ?>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php /* Navigační lišta. „navbar“, „bg-primary“ apod. jsou třídy Bootstrapu – jejich vzhled je definovaný v bootstrap.min.css. */ ?>
<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <?php /* Podmínka: přihlášený vidí jméno + Odhlásit se, nepřihlášený tlačítko Přihlásit se.
                     htmlspecialchars() zabrání vložení cizího HTML (ochrana před XSS) */ ?>
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

<main class="container py-4" style="max-width: 52rem;">
    <h1 class="mb-4">Naše zvířata</h1>

    <?php /* Pro každý pavilon jedna karta z Bootstrapu, uvnitř seznam jeho zvířat. */ ?>
    <?php foreach ($pavilony as $pav): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
                <h2 class="h5 mb-0"><?= htmlspecialchars($pav['info']['jmeno_pavilonu']) ?></h2>
                <small class="text-body-secondary"><?= htmlspecialchars($pav['info']['lokace']) ?>, <?= number_format($pav['info']['rozloha'], 0, ',', ' ') ?> m²</small>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($pav['zvirata'] ?? [] as $z): ?>
                    <li class="list-group-item">
                        <span class="fw-semibold"><?= htmlspecialchars($z['jmeno_zvirete']) ?></span>
                        <small class="d-block text-body-secondary"><?= htmlspecialchars($z['typ_potravy'] ?? 'Potrava neuvedena') ?></small>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($pav['zvirata'])): ?>
                    <li class="list-group-item text-body-secondary">Zatím tu nejsou žádná zvířata.</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</main>
</body>
</html>