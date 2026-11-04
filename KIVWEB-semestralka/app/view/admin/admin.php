<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Správa péče – Zoo</title>
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
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="admin.php">Správa péče</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_pridat.php">Přidat</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="text-end lh-sm">Jan Pythagoras <small class="d-block text-white-50">admin</small></span>
            <form method="post" action="../../../index.php"><button type="submit" name="odhlasit" value="1" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</button></form>
        </div>
    </div>
</nav>

<main class="container py-4">
    <h1 class="mb-4">Správa péče</h1>

    <!-- Hláška po akci:
    <div class="alert alert-success" role="alert">Péče byla úspěšně přidělena.</div>
    -->

    <form method="post" action="pece.php" class="card shadow-sm mb-5">
        <!-- Při úpravě záznamu: <input type="hidden" name="id_pece" value=""> -->
        <div class="card-header"><h2 class="h5 mb-0">Přidělit péči</h2></div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="h6 text-uppercase text-body-secondary mb-2">1 · Zaměstnanec</h3>
                    <div class="list-group list-pick">
                        <label class="list-group-item d-flex align-items-center gap-2">
                            <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance" value="1" required>
                            <span class="lh-sm flex-grow-1"><span class="fw-semibold">Nam Hoang</span><small class="d-block text-body-secondary">superadmin</small></span>
                            <span class="badge rounded-pill text-bg-light border" title="Aktivní služby">2</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2">
                            <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance" value="2" required>
                            <span class="lh-sm flex-grow-1"><span class="fw-semibold">Jan Pythagoras</span><small class="d-block text-body-secondary">admin</small></span>
                            <span class="badge rounded-pill text-bg-light border" title="Aktivní služby">2</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2">
                            <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance" value="3" required>
                            <span class="lh-sm flex-grow-1"><span class="fw-semibold">Petr Dvořák</span><small class="d-block text-body-secondary">zaměstnanec</small></span>
                            <span class="badge rounded-pill text-bg-light border" title="Aktivní služby">1</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2">
                            <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance" value="4" required>
                            <span class="lh-sm flex-grow-1"><span class="fw-semibold">Ondřej Novák</span><small class="d-block text-body-secondary">brigádník</small></span>
                            <span class="badge rounded-pill text-bg-light border" title="Aktivní služby">0</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2">
                            <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="id_zamestnance" value="5" required>
                            <span class="lh-sm flex-grow-1"><span class="fw-semibold">Marie Svobodová</span><small class="d-block text-body-secondary">brigádník</small></span>
                            <span class="badge rounded-pill text-bg-light border" title="Aktivní služby">1</span>
                        </label>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h3 class="h6 text-uppercase text-body-secondary mb-3">2 · Zvířata <span class="fw-normal text-lowercase">(lze vybrat více)</span></h3>
                    <p class="fw-semibold small mb-2">Pavilon šelem</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a1" value="1">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a1">Lev pustinný</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a11" value="11">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a11">Tygr indický</label>
                    </div>
                    <p class="fw-semibold small mb-2">Tropický pavilon</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a5" value="5">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a5">Zebra stepní</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a12" value="12">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a12">Slon africký</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a13" value="13">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a13">Žirafa Rothschildova</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a6" value="6">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a6">Chřestýš lesní</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a7" value="7">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a7">Kudlanka malajská</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a8" value="8">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a8">Sklípkan Smithův</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a14" value="14">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a14">Mlok skvrnitý</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a9" value="9">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a9">Želva sloní</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a10" value="10">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a10">Rosnice siná</label>
                    </div>
                    <p class="fw-semibold small mb-2">Pavilon primátů</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a3" value="3">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a3">Gorila nížinná</label>
                    </div>
                    <p class="fw-semibold small mb-2">Akvarijní část zoo</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a2" value="2">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a2">Klaun očkatý</label>
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a15" value="15">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a15">Trnucha</label>
                    </div>
                    <p class="fw-semibold small mb-2">Pavilon ptáků</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="checkbox" class="btn-check" name="id_zvirata[]" id="a4" value="4">
                        <label class="btn btn-outline-primary btn-sm rounded-pill" for="a4">Plameňák růžový</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="typ-pece" class="form-label mb-1">3 · Druh péče</label>
                    <select id="typ-pece" name="typ_pece" class="form-select" required><option value="" selected disabled>Vyberte…</option><option value="1">Krmení a kontrola</option><option value="2">Veterinární dohled</option><option value="3">Denní péče</option><option value="4">Krmení</option><option value="5">Údržba terária</option><option value="6">Zdravotní kontrola</option></select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="datum-od" class="form-label mb-1">Od</label>
                    <input type="date" id="datum-od" name="datum_od" class="form-control" required>
                </div>
                <div class="col-6 col-md-3">
                    <label for="datum-do" class="form-label mb-1">Do <span class="text-body-secondary">(nepovinné)</span></label>
                    <input type="date" id="datum-do" name="datum_do" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Přidělit</button>
                </div>
            </div>
            <div class="form-text mt-2">Každé vybrané zvíře se uloží jako samostatný záznam. Prázdné „Do“ znamená dobu neurčitou.</div>
        </div>
    </form>

    <h2 class="h4 mb-3">Přidělená péče</h2>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr><th scope="col">Zaměstnanec</th><th scope="col">Zvíře</th><th scope="col">Péče</th><th scope="col">Období</th><th scope="col">Stav</th><th scope="col"><span class="visually-hidden">Akce</span></th></tr>
                </thead>
                <tbody>
                <!-- Jeden řádek = jeden záznam z tabulky `pece`. -->
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">NH</span><span class="lh-sm"><span class="fw-semibold">Nam Hoang</span><small class="d-block text-body-secondary">superadmin</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Lev pustinný</span><small class="d-block text-body-secondary">Pavilon šelem</small></td>
                    <td><span class="badge text-bg-warning">Krmení a kontrola</span></td>
                    <td class="text-nowrap">1. 1. 2025 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="1">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">JP</span><span class="lh-sm"><span class="fw-semibold">Jan Pythagoras</span><small class="d-block text-body-secondary">admin</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Lev pustinný</span><small class="d-block text-body-secondary">Pavilon šelem</small></td>
                    <td><span class="badge text-bg-success">Veterinární dohled</span></td>
                    <td class="text-nowrap">1. 1. 2025 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="2">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">PD</span><span class="lh-sm"><span class="fw-semibold">Petr Dvořák</span><small class="d-block text-body-secondary">zaměstnanec</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Gorila nížinná</span><small class="d-block text-body-secondary">Pavilon primátů</small></td>
                    <td><span class="badge text-bg-secondary">Denní péče</span></td>
                    <td class="text-nowrap">15. 6. 2023 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="3">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr class="row-ended">
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">ON</span><span class="lh-sm"><span class="fw-semibold">Ondřej Novák</span><small class="d-block text-body-secondary">brigádník</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Chřestýš lesní</span><small class="d-block text-body-secondary">Tropický pavilon</small></td>
                    <td><span class="badge text-bg-secondary">Denní péče</span></td>
                    <td class="text-nowrap">1. 8. 2023 <span class="text-body-secondary">→ 1. 6. 2024</span></td>
                    <td><span class="badge text-bg-secondary">Ukončená</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="4">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>

                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">MS</span><span class="lh-sm"><span class="fw-semibold">Marie Svobodová</span><small class="d-block text-body-secondary">brigádník</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Rosnice siná</span><small class="d-block text-body-secondary">Tropický pavilon</small></td>
                    <td><span class="badge text-bg-secondary">Údržba terária</span></td>
                    <td class="text-nowrap">1. 2. 2024 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="5">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">NH</span><span class="lh-sm"><span class="fw-semibold">Nam Hoang</span><small class="d-block text-body-secondary">superadmin</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Tygr indický</span><small class="d-block text-body-secondary">Pavilon šelem</small></td>
                    <td><span class="badge text-bg-warning">Krmení</span></td>
                    <td class="text-nowrap">1. 3. 2025 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="6">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><span class="avatar">JP</span><span class="lh-sm"><span class="fw-semibold">Jan Pythagoras</span><small class="d-block text-body-secondary">admin</small></span></div></td>
                    <td class="lh-sm"><span class="fw-semibold">Slon africký</span><small class="d-block text-body-secondary">Tropický pavilon</small></td>
                    <td><span class="badge text-bg-success">Zdravotní kontrola</span></td>
                    <td class="text-nowrap">10. 1. 2025 <span class="text-body-secondary">→ bez konce</span></td>
                    <td><span class="badge text-bg-success">Aktivní</span></td>
                    <td class="cell-actions text-end">
                        <form method="post" action="pece.php" class="d-inline-flex gap-1">
                            <input type="hidden" name="id_pece" value="7">
                            <button type="submit" name="akce" value="upravit" class="btn btn-sm btn-outline-secondary" title="Upravit" aria-label="Upravit"><i class="fa-solid fa-pen"></i></button>
                            <button type="submit" name="akce" value="ukoncit" class="btn btn-sm btn-outline-secondary" title="Ukončit péči" aria-label="Ukončit péči"><i class="fa-solid fa-circle-stop"></i></button>
                            <button type="submit" name="akce" value="smazat" class="btn btn-sm btn-outline-danger" title="Smazat" aria-label="Smazat"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>