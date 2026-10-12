<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo – přehled zvířat</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="site">

<header class="site__head">
    <a class="site__brand" href="index.php">Zoo</a>
    <div class="site__user">
        <!-- Nepřihlášený: -->
        <a class="link-btn" href="css/login.php">Přihlásit se</a>

        <!-- Přihlášený (zobraz místo tlačítka výše):
        <span class="site__name">Jan Pythagoras <small>admin</small></span>
        <form method="post" action="index.php">
            <button type="submit" name="odhlasit" value="1" class="link-btn">Odhlásit se</button>
        </form>
        -->
    </div>
</header>

<main class="site__main">
    <h1>Naše zvířata</h1>

    <!-- Zpráva jen pro zaměstnance:
    <p class="notice">Máte aktivní službu u 2 zvířat. Najdete je označené v seznamu.</p>
    -->

    <section class="pav">
        <header class="pav__head">
            <h2>Pavilon šelem</h2>
            <p>Severní část, 5 000 m²</p>
        </header>
        <ul class="zvirata">
            <li class="zvire">
                <div class="zvire__info">
                    <span class="zvire__jmeno">Lev pustinný</span>
                    <span class="zvire__potrava">Maso</span>
                </div>
            </li>
            <li class="zvire">
                <div class="zvire__info">
                    <span class="zvire__jmeno">Tygr indický</span>
                    <span class="zvire__potrava">Maso</span>
                </div>
            </li>

            <!-- Příklad zvířete, u kterého má zaměstnanec službu:
            <li class="zvire zvire--sluzba">
                <div class="zvire__info">
                    <span class="zvire__jmeno">Tygr indický</span>
                    <span class="zvire__potrava">Maso</span>
                </div>
                <span class="badge">Vaše služba: Krmení <small>od 1. 3. 2025</small></span>
            </li>
            -->
        </ul>
    </section>

    <section class="pav">
        <header class="pav__head">
            <h2>Tropický pavilon</h2>
            <p>Východní část, 3 500 m²</p>
        </header>
        <ul class="zvirata"> <!-- SEM MÍSTO PEVNĚ DANÝCH DAT PŘES PHP VYPSAT ZVÍŘATA -->
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Zebra stepní</span><span class="zvire__potrava">Tráva</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Slon africký</span><span class="zvire__potrava">Tráva</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Žirafa Rothschildova</span><span class="zvire__potrava">Tráva</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Chřestýš lesní</span><span class="zvire__potrava">Maso</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Kudlanka malajská</span><span class="zvire__potrava">Tráva</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Sklípkan Smithův</span><span class="zvire__potrava">Tráva</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Mlok skvrnitý</span><span class="zvire__potrava">Smíšená</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Želva sloní</span><span class="zvire__potrava">Potrava neuvedena</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Rosnice siná</span><span class="zvire__potrava">Potrava neuvedena</span></div></li>
        </ul>
    </section>

    <section class="pav">
        <header class="pav__head">
            <h2>Pavilon primátů</h2>
            <p>Západní část, 4 200 m²</p>
        </header>
        <ul class="zvirata">
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Gorila nížinná</span><span class="zvire__potrava">Ovoce a zelenina</span></div></li>
        </ul>
    </section>

    <section class="pav">
        <header class="pav__head">
            <h2>Akvarijní část zoo</h2>
            <p>Západní část, 1 500 m²</p>
        </header>
        <ul class="zvirata">
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Klaun očkatý</span><span class="zvire__potrava">Smíšená</span></div></li>
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Trnucha</span><span class="zvire__potrava">Smíšená</span></div></li>
        </ul>
    </section>

    <section class="pav">
        <header class="pav__head">
            <h2>Pavilon ptáků</h2>
            <p>Severní část, 2 000 m²</p>
        </header>
        <ul class="zvirata">
            <li class="zvire"><div class="zvire__info"><span class="zvire__jmeno">Plameňák růžový</span><span class="zvire__potrava">Smíšená</span></div></li>
        </ul>
    </section>
</main>

</body>
</html>