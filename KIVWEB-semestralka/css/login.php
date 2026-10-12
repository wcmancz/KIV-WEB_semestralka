<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přihlášení – Zoo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="login">
    <aside class="login__side" aria-hidden="true">
        <p class="login__brand">Zoo</p>
        <ul class="login__pavilony"> <!-- PŘES PHP -->
            <li>Pavilon šelem</li>
            <li>Tropický pavilon</li>
            <li>Pavilon primátů</li>
            <li>Akvarijní část</li>
            <li>Pavilon ptáků</li>
        </ul>
    </aside>

    <section class="login__panel">
        <h1>Přihlášení</h1>

        <form method="post" action="login.php" class="form">

            <fieldset class="switch">
                <legend class="sr-only">Typ účtu</legend>
                <input type="radio" id="t-nav" name="typ" value="navstevnik" checked>
                <label for="t-nav">Návštěvník</label>
                <input type="radio" id="t-zam" name="typ" value="zamestnanec">
                <label for="t-zam">Zaměstnanec</label>
            </fieldset>

            <!-- Chybová hláška (zobraz, když se přihlášení nepovede):
            <p class="alert" role="alert">Přihlašovací jméno nebo heslo není správné.</p>
            -->

            <div class="field">
                <label for="identifikator" id="lbl-ident">E-mail</label>
                <input type="text" id="identifikator" name="identifikator" autocomplete="username" required autofocus>
            </div>

            <div class="field">
                <label for="heslo">Heslo</label>
                <input type="password" id="heslo" name="heslo" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn">Přihlásit se</button>
        </form>

        <p class="login__foot" id="foot-nav">
            Nemáte účet? <a href="register.php">Zaregistrujte se</a>
        </p>
    </section>
</main>

<script>
    // Přepínač návštěvník / zaměstnanec: změní popisek pole a skryje odkaz na registraci
    (function () {
        var radios = document.querySelectorAll('input[name="typ"]');
        var label  = document.getElementById('lbl-ident');
        var foot   = document.getElementById('foot-nav');
        function sync() {
            var zam = document.getElementById('t-zam').checked;
            label.textContent = zam ? 'Přihlašovací jméno' : 'E-mail';
            foot.hidden = zam;
        }
        radios.forEach(function (r) { r.addEventListener('change', sync); });
        sync();
    })();
</script>
</body>
</html>