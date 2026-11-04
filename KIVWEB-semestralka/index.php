<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo – přehled zvířat</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <!-- Nepřihlášený: -->
            <a class="btn btn-outline-light btn-sm" href="app/view/login.php"><i class="fa-solid fa-right-to-bracket me-1"></i>Přihlásit se</a>

            <!-- Přihlášený (zobraz místo tlačítka výše):
            <span class="text-end lh-sm">Jan Pythagoras <small class="d-block text-white-50">admin</small></span>
            <form method="post" action="index.php"><button type="submit" name="odhlasit" value="1" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</button></form>
            -->
        </div>
    </div>
</nav>

<main class="container py-4" style="max-width: 52rem;">
    <h1 class="mb-4">Naše zvířata</h1>

    <!-- Zpráva jen pro zaměstnance:
    <div class="alert alert-warning" role="alert"><i class="fa-solid fa-circle-info me-2"></i>Máte aktivní službu u 2 zvířat. Najdete je označené v seznamu.</div>
    -->

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
            <h2 class="h5 mb-0">Pavilon šelem</h2>
            <small class="text-body-secondary">Severní část, 5 000 m²</small>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Lev pustinný</span><small class="d-block text-body-secondary">Maso</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Tygr indický</span><small class="d-block text-body-secondary">Maso</small></div>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
            <h2 class="h5 mb-0">Tropický pavilon</h2>
            <small class="text-body-secondary">Východní část, 3 500 m²</small>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Zebra stepní</span><small class="d-block text-body-secondary">Tráva</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Slon africký</span><small class="d-block text-body-secondary">Tráva</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Žirafa Rothschildova</span><small class="d-block text-body-secondary">Tráva</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Chřestýš lesní</span><small class="d-block text-body-secondary">Maso</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Kudlanka malajská</span><small class="d-block text-body-secondary">Tráva</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Sklípkan Smithův</span><small class="d-block text-body-secondary">Tráva</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Mlok skvrnitý</span><small class="d-block text-body-secondary">Smíšená</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Želva sloní</span><small class="d-block text-body-secondary">Potrava neuvedena</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Rosnice siná</span><small class="d-block text-body-secondary">Potrava neuvedena</small></div>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
            <h2 class="h5 mb-0">Pavilon primátů</h2>
            <small class="text-body-secondary">Západní část, 4 200 m²</small>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Gorila nížinná</span><small class="d-block text-body-secondary">Ovoce a zelenina</small></div>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
            <h2 class="h5 mb-0">Akvarijní část zoo</h2>
            <small class="text-body-secondary">Západní část, 1 500 m²</small>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Klaun očkatý</span><small class="d-block text-body-secondary">Smíšená</small></div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Trnucha</span><small class="d-block text-body-secondary">Smíšená</small></div>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
            <h2 class="h5 mb-0">Pavilon ptáků</h2>
            <small class="text-body-secondary">Severní část, 2 000 m²</small>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><span class="fw-semibold">Plameňák růžový</span><small class="d-block text-body-secondary">Smíšená</small></div>
            </li>
        </ul>
    </div>

    <!-- Příklad zvířete, u kterého má zaměstnanec službu:
    <li class="list-group-item list-group-item-warning d-flex justify-content-between align-items-center">
        <div><span class="fw-semibold">Tygr indický</span><small class="d-block text-body-secondary">Maso</small></div>
        <span class="badge text-bg-warning">Vaše služba: Krmení · od 1. 3. 2025</span>
    </li>
    -->
</main>
</body>
</html>