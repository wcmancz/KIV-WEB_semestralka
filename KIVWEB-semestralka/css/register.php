<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrace – Zoo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="login">
    <aside class="login__side" aria-hidden="true">
        <p class="login__brand">Zoo</p>
        <ul class="login__pavilony"> <!-- TAKY PŘES PHP -->
            <li>Pavilon šelem</li>
            <li>Tropický pavilon</li>
            <li>Pavilon primátů</li>
            <li>Akvarijní část</li>
            <li>Pavilon ptáků</li>
        </ul>
    </aside>

    <section class="login__panel">
        <h1>Registrace</h1>

        <form method="post" action="register.php" class="form">

            <!-- Chybová hláška (zobraz, když se registrace nepovede):
            <p class="alert" role="alert">Účet s tímto e-mailem už existuje.</p>
            -->

            <div class="field">
                <label for="jmeno">Jméno</label>
                <input type="text" id="jmeno" name="jmeno" maxlength="50" autocomplete="name" required autofocus>
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="100" autocomplete="email" required>
            </div>

            <div class="field">
                <label for="heslo">Heslo</label>
                <input type="password" id="heslo" name="heslo" minlength="8" autocomplete="new-password" required>
            </div>

            <div class="field">
                <label for="heslo2">Heslo znovu</label>
                <input type="password" id="heslo2" name="heslo2" minlength="8" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn">Vytvořit účet</button>
        </form>

        <p class="login__foot">
            Už máte účet? <a href="login.php">Přihlaste se</a>
        </p>
    </section>
</main>
</body>
</html>