<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přihlášení – Zoo</title>
    <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="d-flex align-items-center min-vh-100">
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4 col-xl-3">
            <div class="text-center brand mb-3"><i class="fa-solid fa-paw me-2"></i>Zoo</div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4">Přihlášení</h1>

                    <form method="post" action="login.php">
                        <div class="btn-group w-100 mb-4" role="group" aria-label="Typ účtu">
                            <input type="radio" class="btn-check" name="typ" id="t-nav" value="navstevnik" checked>
                            <label class="btn btn-outline-primary" for="t-nav">Návštěvník</label>
                            <input type="radio" class="btn-check" name="typ" id="t-zam" value="zamestnanec">
                            <label class="btn btn-outline-primary" for="t-zam">Zaměstnanec</label>
                        </div>

                        <!-- Chybová hláška:
                        <div class="alert alert-danger" role="alert">Přihlašovací jméno nebo heslo není správné.</div>
                        -->

                        <div class="mb-3">
                            <label for="identifikator" class="form-label">E-mail / přihlašovací jméno</label>
                            <input type="text" class="form-control" id="identifikator" name="identifikator" autocomplete="username" required autofocus>
                            <div class="form-text">Návštěvník zadává e-mail, zaměstnanec přihlašovací jméno.</div>
                        </div>

                        <div class="mb-4">
                            <label for="heslo" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="heslo" name="heslo" autocomplete="current-password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-right-to-bracket me-2"></i>Přihlásit se</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-body-secondary small mt-3 mb-0">Nemáte účet? <a href="register.php">Zaregistrujte se</a></p>
        </div>
    </div>
</main>
</body>
</html>