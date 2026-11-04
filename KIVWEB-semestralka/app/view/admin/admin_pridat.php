<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přidat – Zoo</title>
    <link rel="stylesheet" href="../../../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container flex-wrap gap-2">
        <a class="navbar-brand" href="../../../index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="../../../index.php">Přehled zvířat</a></li>
            <li class="nav-item"><a class="nav-link" href="admin.php">Správa péče</a></li>
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="admin_pridat.php">Přidat</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="text-end lh-sm">Jan Pythagoras <small class="d-block text-white-50">admin</small></span>
            <form method="post" action="../../../index.php"><button type="submit" name="odhlasit" value="1" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</button></form>
        </div>
    </div>
</nav>

<main class="container py-4">
    <h1 class="mb-4">Přidat</h1>

    <!-- Hláška po akci:
    <div class="alert alert-success" role="alert">Zaměstnanec byl úspěšně vytvořen.</div>
    -->

    <div class="row g-4">
        <!-- ===================== Nový zaměstnanec ===================== -->
        <div class="col-xl-6">
            <form method="post" action="admin_pridat.php" class="card shadow-sm h-100">
                <input type="hidden" name="akce" value="novy_zamestnanec">
                <div class="card-header"><h2 class="h5 mb-0"><i class="fa-solid fa-user-plus me-2"></i>Nový zaměstnanec</h2></div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="jmeno" class="form-label">Jméno</label>
                            <input type="text" class="form-control" id="jmeno" name="jmeno" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="prijmeni" class="form-label">Příjmení</label>
                            <input type="text" class="form-control" id="prijmeni" name="prijmeni" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="vek" class="form-label">Věk</label>
                            <input type="number" class="form-control" id="vek" name="vek" min="15" max="99" required>
                        </div>
                        <div class="col-sm-8">
                            <label for="id-role" class="form-label">Role</label>
                            <select id="id-role" name="id_role" class="form-select" required>
                                <option value="" selected disabled>Vyberte…</option>
                                <option value="1">superadmin – plný přístup</option>
                                <option value="2">admin – spravuje péči a zvířata</option>
                                <option value="3">zaměstnanec – stará se o zvířata</option>
                                <option value="4">brigádník – dočasná výpomoc</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="login" class="form-label">Přihlašovací jméno</label>
                            <input type="text" class="form-control" id="login" name="login" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="heslo" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="heslo" name="heslo" minlength="8" autocomplete="new-password" required>
                            <div class="form-text">Alespoň 8 znaků.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="heslo2" class="form-label">Heslo znovu</label>
                            <input type="password" class="form-control" id="heslo2" name="heslo2" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Vytvořit zaměstnance</button>
                </div>
            </form>
        </div>

        <!-- ===================== Nové zvíře ===================== -->
        <div class="col-xl-6">
            <form method="post" action="admin_pridat.php" class="card shadow-sm h-100">
                <input type="hidden" name="akce" value="nove_zvire">
                <div class="card-header"><h2 class="h5 mb-0"><i class="fa-solid fa-paw me-2"></i>Nové zvíře</h2></div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="jmeno-zvirete" class="form-label">Název zvířete</label>
                            <input type="text" class="form-control" id="jmeno-zvirete" name="jmeno_zvirete" maxlength="50" autocomplete="off" required>
                            <div class="form-text">Například „Lev pustinný“.</div>
                        </div>
                        <div class="col-12">
                            <label for="potrava" class="form-label">Potrava</label>
                            <select id="potrava" name="potrava" class="form-select">
                                <option value="">Neuvedeno</option>
                                <option value="1">Maso</option>
                                <option value="2">Ovoce a zelenina</option>
                                <option value="3">Tráva</option>
                                <option value="4">Smíšená</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <span class="form-label d-block">Pavilon</span>
                            <div class="list-group list-pick">
                                <label class="list-group-item d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon" value="1" required>
                                    <span class="lh-sm flex-grow-1"><span class="fw-semibold">Pavilon šelem</span><small class="d-block text-body-secondary">Severní část · 5 000 m²</small></span>
                                    <span class="badge rounded-pill text-bg-light border" title="Počet zvířat">2</span>
                                </label>
                                <label class="list-group-item d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon" value="2" required>
                                    <span class="lh-sm flex-grow-1"><span class="fw-semibold">Tropický pavilon</span><small class="d-block text-body-secondary">Východní část · 3 500 m²</small></span>
                                    <span class="badge rounded-pill text-bg-light border" title="Počet zvířat">9</span>
                                </label>
                                <label class="list-group-item d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon" value="3" required>
                                    <span class="lh-sm flex-grow-1"><span class="fw-semibold">Pavilon primátů</span><small class="d-block text-body-secondary">Západní část · 4 200 m²</small></span>
                                    <span class="badge rounded-pill text-bg-light border" title="Počet zvířat">1</span>
                                </label>
                                <label class="list-group-item d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon" value="4" required>
                                    <span class="lh-sm flex-grow-1"><span class="fw-semibold">Akvarijní část zoo</span><small class="d-block text-body-secondary">Západní část · 1 500 m²</small></span>
                                    <span class="badge rounded-pill text-bg-light border" title="Počet zvířat">2</span>
                                </label>
                                <label class="list-group-item d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon" value="5" required>
                                    <span class="lh-sm flex-grow-1"><span class="fw-semibold">Pavilon ptáků</span><small class="d-block text-body-secondary">Severní část · 2 000 m²</small></span>
                                    <span class="badge rounded-pill text-bg-light border" title="Počet zvířat">1</span>
                                </label>
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