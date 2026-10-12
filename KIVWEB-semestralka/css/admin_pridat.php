<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přidat – Zoo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="site">

<header class="site__head">
    <a class="site__brand" href="index.php">Zoo</a>
    <nav class="site__nav" aria-label="Hlavní navigace">
        <a href="index.php">Přehled zvířat</a>
        <a href="admin.php">Správa péče</a>
        <a href="admin_pridat.php" aria-current="page">Přidat</a>
    </nav>
    <div class="site__user">
        <span class="site__name">Jan Pythagoras <small>admin</small></span>
        <form method="post" action="index.php">
            <button type="submit" name="odhlasit" value="1" class="link-btn">Odhlásit se</button>
        </form>
    </div>
</header>

<main class="site__main site__main--wide">
    <h1>Přidat</h1>

    <!-- Hláška po akci:
    <p class="notice">Zaměstnanec byl úspěšně vytvořen.</p>
    -->

    <div class="seg tabs" role="group" aria-label="Co přidat">
        <button type="button" data-tab="zam" aria-pressed="true">Nový zaměstnanec</button>
        <button type="button" data-tab="zvire" aria-pressed="false">Nové zvíře</button>
    </div>

    <!-- ===================== Nový zaměstnanec ===================== -->
    <form method="post" action="admin_pridat.php" class="assign" id="f-zam" data-panel="zam">
        <input type="hidden" name="akce" value="novy_zamestnanec">

        <div class="assign__top">
            <div class="who" role="radiogroup" aria-labelledby="h-role">
                <h3 id="h-role">1 · Role</h3>
                <div class="opt"><input class="pick" type="radio" name="id_role" id="r1" value="1" data-label="superadmin" required>
                    <label class="person person--plain" for="r1"><span class="person__text"><span class="person__name">superadmin</span><span class="person__meta">plný přístup</span></span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_role" id="r2" value="2" data-label="admin">
                    <label class="person person--plain" for="r2"><span class="person__text"><span class="person__name">admin</span><span class="person__meta">spravuje péči a zvířata</span></span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_role" id="r3" value="3" data-label="zaměstnanec">
                    <label class="person person--plain" for="r3"><span class="person__text"><span class="person__name">zaměstnanec</span><span class="person__meta">stará se o zvířata</span></span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_role" id="r4" value="4" data-label="brigádník">
                    <label class="person person--plain" for="r4"><span class="person__text"><span class="person__name">brigádník</span><span class="person__meta">dočasná výpomoc</span></span></label></div>
            </div>

            <div class="what">
                <h3>2 · Údaje zaměstnance</h3>
                <div class="fields">
                    <div class="field"><label for="jmeno">Jméno</label><input type="text" id="jmeno" name="jmeno" maxlength="50" autocomplete="off" required></div>
                    <div class="field"><label for="prijmeni">Příjmení</label><input type="text" id="prijmeni" name="prijmeni" maxlength="50" autocomplete="off" required></div>
                    <div class="field"><label for="vek">Věk</label><input type="number" id="vek" name="vek" min="15" max="99" required></div>
                    <div class="field"><label for="login">Přihlašovací jméno</label><input type="text" id="login" name="login" maxlength="50" autocomplete="off" required></div>
                    <div class="field"><label for="heslo">Heslo</label><input type="password" id="heslo" name="heslo" minlength="8" autocomplete="new-password" required>
                        <p class="hint">Alespoň 8 znaků.</p></div>
                    <div class="field"><label for="heslo2">Heslo znovu</label><input type="password" id="heslo2" name="heslo2" minlength="8" autocomplete="new-password" required>
                        <p class="hint" id="shoda" hidden>Hesla se neshodují.</p></div>
                </div>
            </div>
        </div>

        <div class="how how--simple">
            <div class="how__go">
                <p class="how__sum" id="sum-zam" aria-live="polite">Vyplňte údaje a vyberte roli.</p>
                <button type="submit" class="btn" id="btn-zam" disabled>Vytvořit zaměstnance</button>
            </div>
        </div>
    </form>

    <!-- ===================== Nové zvíře ===================== -->
    <form method="post" action="admin_pridat.php" class="assign" id="f-zvire" data-panel="zvire" hidden>
        <input type="hidden" name="akce" value="nove_zvire">

        <div class="assign__top">
            <div class="who" role="radiogroup" aria-labelledby="h-pav">
                <h3 id="h-pav">1 · Pavilon</h3>
                <div class="opt"><input class="pick" type="radio" name="pavilon" id="v1" value="1" data-label="Pavilon šelem" required>
                    <label class="person person--plain" for="v1"><span class="person__text"><span class="person__name">Pavilon šelem</span><span class="person__meta">Severní část · 5 000 m²</span></span><span class="person__load" title="Počet zvířat">2</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="pavilon" id="v2" value="2" data-label="Tropický pavilon">
                    <label class="person person--plain" for="v2"><span class="person__text"><span class="person__name">Tropický pavilon</span><span class="person__meta">Východní část · 3 500 m²</span></span><span class="person__load" title="Počet zvířat">9</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="pavilon" id="v3" value="3" data-label="Pavilon primátů">
                    <label class="person person--plain" for="v3"><span class="person__text"><span class="person__name">Pavilon primátů</span><span class="person__meta">Západní část · 4 200 m²</span></span><span class="person__load" title="Počet zvířat">1</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="pavilon" id="v4" value="4" data-label="Akvarijní část zoo">
                    <label class="person person--plain" for="v4"><span class="person__text"><span class="person__name">Akvarijní část zoo</span><span class="person__meta">Západní část · 1 500 m²</span></span><span class="person__load" title="Počet zvířat">2</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="pavilon" id="v5" value="5" data-label="Pavilon ptáků">
                    <label class="person person--plain" for="v5"><span class="person__text"><span class="person__name">Pavilon ptáků</span><span class="person__meta">Severní část · 2 000 m²</span></span><span class="person__load" title="Počet zvířat">1</span></label></div>
            </div>

            <div class="what">
                <h3>2 · Údaje zvířete</h3>
                <div class="fields">
                    <div class="field field--full"><label for="jmeno-zvirete">Název zvířete</label><input type="text" id="jmeno-zvirete" name="jmeno_zvirete" maxlength="50" autocomplete="off" required>
                        <p class="hint">Například „Lev pustinný“.</p></div>
                    <div class="field field--full"><label for="potrava">Potrava</label>
                        <select id="potrava" name="potrava">
                            <option value="">Neuvedeno</option>
                            <option value="1">Maso</option>
                            <option value="2">Ovoce a zelenina</option>
                            <option value="3">Tráva</option>
                            <option value="4">Smíšená</option>
                        </select></div>
                </div>
            </div>
        </div>

        <div class="how how--simple">
            <div class="how__go">
                <p class="how__sum" id="sum-zvire" aria-live="polite">Vyberte pavilon a zadejte název zvířete.</p>
                <button type="submit" class="btn" id="btn-zvire" disabled>Přidat zvíře</button>
            </div>
        </div>
    </form>
</main>

<script>
    (function () {
        var $ = function (s) { return document.querySelector(s); };
        var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };
        function el(text) { var e = document.createElement('strong'); e.textContent = text; return e; }
        function txt(t) { return document.createTextNode(t); }

        /* ---------- Přepínání karet ---------- */
        $$('.tabs button').forEach(function (b) {
            b.addEventListener('click', function () {
                $$('.tabs button').forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
                $$('[data-panel]').forEach(function (f) { f.hidden = f.dataset.panel !== b.dataset.tab; });
            });
        });

        /* ---------- Nový zaměstnanec ---------- */
        var fz = $('#f-zam');
        function updZam() {
            var j = $('#jmeno').value.trim(), p = $('#prijmeni').value.trim(), vek = $('#vek').value,
                lg = $('#login').value.trim(), h1 = $('#heslo').value, h2 = $('#heslo2').value,
                role = $('input[name="id_role"]:checked'), sum = $('#sum-zam');

            var shoda = h2 === '' || h1 === h2;
            $('#shoda').hidden = shoda;

            sum.textContent = '';
            if (!j && !p && !role) {
                sum.textContent = 'Vyplňte údaje a vyberte roli.';
            } else {
                sum.appendChild(txt('Vytvoří se '));
                sum.appendChild(el((j + ' ' + p).trim() || '…'));
                sum.appendChild(txt(' · '));
                sum.appendChild(el(role ? role.dataset.label : 'role nevybrána'));
                if (lg) sum.appendChild(txt(' · přihlášení: ' + lg));
            }
            $('#btn-zam').disabled = !(j && p && vek >= 15 && lg && role && h1.length >= 8 && h1 === h2);
        }
        fz.addEventListener('input', updZam);
        fz.addEventListener('change', updZam);
        updZam();

        /* ---------- Nové zvíře ---------- */
        var fv = $('#f-zvire');
        function updZvire() {
            var n = $('#jmeno-zvirete').value.trim(), pav = $('input[name="pavilon"]:checked'),
                pot = $('#potrava').selectedOptions[0], sum = $('#sum-zvire');

            sum.textContent = '';
            if (!n && !pav) {
                sum.textContent = 'Vyberte pavilon a zadejte název zvířete.';
            } else {
                sum.appendChild(txt('Přidá se '));
                sum.appendChild(el(n || '…'));
                sum.appendChild(txt(' do '));
                sum.appendChild(el(pav ? pav.dataset.label : 'pavilonu (nevybrán)'));
                sum.appendChild(txt(' · potrava: ' + (pot && pot.value ? pot.textContent : 'neuvedeno')));
            }
            $('#btn-zvire').disabled = !(n && pav);
        }
        fv.addEventListener('input', updZvire);
        fv.addEventListener('change', updZvire);
        updZvire();
    })();
</script>
</body>
</html>