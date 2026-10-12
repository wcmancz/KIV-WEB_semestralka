<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Správa péče – Zoo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="site">

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></symbol>
    <symbol id="i-stop" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><rect x="9" y="9" width="6" height="6" rx="1"/></symbol>
    <symbol id="i-trash" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></symbol>
</svg>

<header class="site__head">
    <a class="site__brand" href="index.php">Zoo</a>
    <nav class="site__nav" aria-label="Hlavní navigace">
        <a href="index.php">Přehled zvířat</a>
        <a href="admin.php" aria-current="page">Správa péče</a>
    </nav>
    <div class="site__user">
        <span class="site__name">Jan Pythagoras <small>admin</small></span>
        <form method="post" action="index.php">
            <button type="submit" name="odhlasit" value="1" class="link-btn">Odhlásit se</button>
        </form>
    </div>
</header>

<main class="site__main site__main--wide">
    <h1>Správa péče</h1>

    <!-- Hláška po akci:
    <p class="notice">Péče byla úspěšně přidělena.</p>
    -->

    <h2 class="panel-title">Přidělit péči</h2>

    <form method="post" action="admin.php" class="assign" id="assign">
        <!-- Při úpravě záznamu: <input type="hidden" name="id_pece" value=""> -->

        <div class="assign__top">
            <div class="who" role="radiogroup" aria-labelledby="h-who">
                <h3 id="h-who">1 · Zaměstnanec</h3>
                <div class="opt"><input class="pick" type="radio" name="id_zamestnance" id="z1" value="1" data-label="Nam Hoang" required>
                    <label class="person" for="z1"><span class="avatar">NH</span><span class="person__text"><span class="person__name">Nam Hoang</span><span class="person__meta">superadmin</span></span><span class="person__load" title="Aktivní služby">2</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_zamestnance" id="z2" value="2" data-label="Jan Pythagoras" required>
                    <label class="person" for="z2"><span class="avatar">JP</span><span class="person__text"><span class="person__name">Jan Pythagoras</span><span class="person__meta">admin</span></span><span class="person__load" title="Aktivní služby">2</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_zamestnance" id="z3" value="3" data-label="Petr Dvořák" required>
                    <label class="person" for="z3"><span class="avatar">PD</span><span class="person__text"><span class="person__name">Petr Dvořák</span><span class="person__meta">zaměstnanec</span></span><span class="person__load" title="Aktivní služby">1</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_zamestnance" id="z4" value="4" data-label="Ondřej Novák" required>
                    <label class="person" for="z4"><span class="avatar">ON</span><span class="person__text"><span class="person__name">Ondřej Novák</span><span class="person__meta">brigádník</span></span><span class="person__load" title="Aktivní služby">0</span></label></div>
                <div class="opt"><input class="pick" type="radio" name="id_zamestnance" id="z5" value="5" data-label="Marie Svobodová" required>
                    <label class="person" for="z5"><span class="avatar">MS</span><span class="person__text"><span class="person__name">Marie Svobodová</span><span class="person__meta">brigádník</span></span><span class="person__load" title="Aktivní služby">1</span></label></div>
            </div>

            <div class="what">
                <h3>2 · Zvířata <span class="optional">(lze vybrat více)</span></h3>
                <div class="pavs">
                    <fieldset><legend class="pav-name">Pavilon šelem</legend><div class="chips"><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a1" value="1" data-label="Lev pustinný"><label class="chip" for="a1">Lev pustinný</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a11" value="11" data-label="Tygr indický"><label class="chip" for="a11">Tygr indický</label></span></div></fieldset>
                    <fieldset class="pav-wide"><legend class="pav-name">Tropický pavilon</legend><div class="chips"><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a5" value="5" data-label="Zebra stepní"><label class="chip" for="a5">Zebra stepní</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a12" value="12" data-label="Slon africký"><label class="chip" for="a12">Slon africký</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a13" value="13" data-label="Žirafa Rothschildova"><label class="chip" for="a13">Žirafa Rothschildova</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a6" value="6" data-label="Chřestýš lesní"><label class="chip" for="a6">Chřestýš lesní</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a7" value="7" data-label="Kudlanka malajská"><label class="chip" for="a7">Kudlanka malajská</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a8" value="8" data-label="Sklípkan Smithův"><label class="chip" for="a8">Sklípkan Smithův</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a14" value="14" data-label="Mlok skvrnitý"><label class="chip" for="a14">Mlok skvrnitý</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a9" value="9" data-label="Želva sloní"><label class="chip" for="a9">Želva sloní</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a10" value="10" data-label="Rosnice siná"><label class="chip" for="a10">Rosnice siná</label></span></div></fieldset>
                    <fieldset><legend class="pav-name">Pavilon primátů</legend><div class="chips"><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a3" value="3" data-label="Gorila nížinná"><label class="chip" for="a3">Gorila nížinná</label></span></div></fieldset>
                    <fieldset><legend class="pav-name">Akvarijní část zoo</legend><div class="chips"><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a2" value="2" data-label="Klaun očkatý"><label class="chip" for="a2">Klaun očkatý</label></span><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a15" value="15" data-label="Trnucha"><label class="chip" for="a15">Trnucha</label></span></div></fieldset>
                    <fieldset><legend class="pav-name">Pavilon ptáků</legend><div class="chips"><span class="opt"><input class="pick" type="checkbox" name="id_zvirata[]" id="a4" value="4" data-label="Plameňák růžový"><label class="chip" for="a4">Plameňák růžový</label></span></div></fieldset>
                </div>
            </div>
        </div>

        <div class="how">
            <div class="field"><label for="typ-pece">3 · Péče</label>
                <select id="typ-pece" name="typ_pece" required><option value="" selected disabled>Vyberte…</option><option value="1">Krmení a kontrola</option><option value="2">Veterinární dohled</option><option value="3">Denní péče</option><option value="4">Krmení</option><option value="5">Údržba terária</option><option value="6">Zdravotní kontrola</option></select></div>
            <div class="field"><label for="datum-od">Od</label><input type="date" id="datum-od" name="datum_od" required></div>
            <div class="field"><label for="datum-do">Do</label><input type="date" id="datum-do" name="datum_do"></div>
            <label class="check"><input type="checkbox" id="bez-konce" checked> Na dobu neurčitou</label>

            <div class="how__go">
                <p class="how__sum" id="sum" aria-live="polite">Vyberte zaměstnance, zvířata a druh péče.</p>
                <button type="submit" class="btn" id="submit" disabled>Přidělit péči</button>
            </div>
        </div>
    </form>

    <h2 class="panel-title panel-title--next">Přidělená péče <span class="count" id="pocet" aria-live="polite"></span></h2>

    <div class="toolbar">
        <input type="search" class="search" id="hledat" placeholder="Hledat zaměstnance nebo zvíře…" aria-label="Hledat">
        <div class="seg" role="group" aria-label="Filtr stavu">
            <button type="button" data-filter="" aria-pressed="true">Vše</button>
            <button type="button" data-filter="on" aria-pressed="false">Aktivní</button>
            <button type="button" data-filter="off" aria-pressed="false">Ukončené</button>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table" id="tabulka">
            <thead>
            <tr><th scope="col">Zaměstnanec</th><th scope="col">Zvíře</th><th scope="col">Péče</th><th scope="col">Období</th><th scope="col">Stav</th><th scope="col"><span class="sr-only">Akce</span></th></tr>
            </thead>
            <tbody>
            <!-- Jeden řádek = jeden záznam z tabulky `pece`. -->
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">NH</span><div><span class="cell-name">Nam Hoang</span><span class="cell-sub">superadmin</span></div></div></td>
                <td><span class="cell-name">Lev pustinný</span><span class="cell-sub">Pavilon šelem</span></td>
                <td><span class="pill pill--a">Krmení a kontrola</span></td>
                <td class="period">1. 1. 2025 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="1"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">JP</span><div><span class="cell-name">Jan Pythagoras</span><span class="cell-sub">admin</span></div></div></td>
                <td><span class="cell-name">Lev pustinný</span><span class="cell-sub">Pavilon šelem</span></td>
                <td><span class="pill pill--b">Veterinární dohled</span></td>
                <td class="period">1. 1. 2025 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="2"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">PD</span><div><span class="cell-name">Petr Dvořák</span><span class="cell-sub">zaměstnanec</span></div></div></td>
                <td><span class="cell-name">Gorila nížinná</span><span class="cell-sub">Pavilon primátů</span></td>
                <td><span class="pill pill--c">Denní péče</span></td>
                <td class="period">15. 6. 2023 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="3"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="off" class="is-ended">
                <td><div class="cell-person"><span class="avatar">ON</span><div><span class="cell-name">Ondřej Novák</span><span class="cell-sub">brigádník</span></div></div></td>
                <td><span class="cell-name">Chřestýš lesní</span><span class="cell-sub">Tropický pavilon</span></td>
                <td><span class="pill pill--c">Denní péče</span></td>
                <td class="period">1. 8. 2023 <span>→ 1. 6. 2024</span></td>
                <td><span class="status status--off">Ukončená</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="4"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn is-gap" tabindex="-1" aria-hidden="true" type="button"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">MS</span><div><span class="cell-name">Marie Svobodová</span><span class="cell-sub">brigádník</span></div></div></td>
                <td><span class="cell-name">Rosnice siná</span><span class="cell-sub">Tropický pavilon</span></td>
                <td><span class="pill pill--c">Údržba terária</span></td>
                <td class="period">1. 2. 2024 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="5"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">NH</span><div><span class="cell-name">Nam Hoang</span><span class="cell-sub">superadmin</span></div></div></td>
                <td><span class="cell-name">Tygr indický</span><span class="cell-sub">Pavilon šelem</span></td>
                <td><span class="pill pill--a">Krmení</span></td>
                <td class="period">1. 3. 2025 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="6"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr data-stav="on">
                <td><div class="cell-person"><span class="avatar">JP</span><div><span class="cell-name">Jan Pythagoras</span><span class="cell-sub">admin</span></div></div></td>
                <td><span class="cell-name">Slon africký</span><span class="cell-sub">Tropický pavilon</span></td>
                <td><span class="pill pill--b">Zdravotní kontrola</span></td>
                <td class="period">10. 1. 2025 <span>→ bez konce</span></td>
                <td><span class="status status--on">Aktivní</span></td>
                <td class="cell-actions"><form method="post" action="admin.php"><input type="hidden" name="id_pece" value="7"><button class="icon-btn" name="akce" value="upravit" title="Upravit" aria-label="Upravit"><svg class="icon"><use href="#i-edit"/></svg></button><button class="icon-btn" name="akce" value="ukoncit" title="Ukončit péči" aria-label="Ukončit péči"><svg class="icon"><use href="#i-stop"/></svg></button><button class="icon-btn icon-btn--danger" name="akce" value="smazat" title="Smazat" aria-label="Smazat"><svg class="icon"><use href="#i-trash"/></svg></button></form></td>
            </tr>
            <tr class="empty-row" id="prazdno" hidden><td colspan="6">Žádná péče neodpovídá hledání.</td></tr>
            </tbody>
        </table>
    </div>
</main>

<script>
    (function () {
        var $ = function (s) { return document.querySelector(s); };
        var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };

        /* ---------- Přidělování: živé shrnutí ---------- */
        var od = $('#datum-od'), dod = $('#datum-do'), bez = $('#bez-konce'), sum = $('#sum');
        od.value = new Date().toISOString().slice(0, 10);
        function cz(d) { var p = d.split('-'); return +p[2] + '. ' + +p[1] + '. ' + p[0]; }
        function el(tag, text) { var e = document.createElement(tag); e.textContent = text; return e; }

        function update() {
            dod.disabled = bez.checked;
            if (bez.checked) dod.value = '';
            var zam = $('input[name="id_zamestnance"]:checked'),
                zv  = $$('input[name="id_zvirata[]"]:checked'),
                pe  = $('#typ-pece').selectedOptions[0],
                hasPe = pe && pe.value !== '',
                okDo = bez.checked || (dod.value && dod.value >= od.value),
                ready = zam && zv.length && hasPe && od.value && okDo;

            sum.textContent = '';
            if (!zam && !zv.length && !hasPe) {
                sum.textContent = 'Vyberte zaměstnance, zvířata a druh péče.';
            } else {
                sum.appendChild(zam ? el('strong', zam.dataset.label) : document.createTextNode('…'));
                sum.appendChild(document.createTextNode(' se postará o '));
                sum.appendChild(zv.length ? el('strong', zv.map(function (c) { return c.dataset.label; }).join(', ')) : document.createTextNode('…'));
                sum.appendChild(document.createTextNode(' · '));
                sum.appendChild(hasPe ? el('strong', pe.textContent) : document.createTextNode('…'));
                sum.appendChild(document.createTextNode(od.value ? ' · od ' + cz(od.value) + (bez.checked ? ' na dobu neurčitou' : (dod.value ? ' do ' + cz(dod.value) : '')) : ''));
                if (zv.length > 1) sum.appendChild(document.createTextNode(' · ' + zv.length + ' záznamy'));
            }
            $('#submit').disabled = !ready;
        }
        $('#assign').addEventListener('change', update);
        update();

        /* ---------- Filtrování tabulky ---------- */
        var rows = $$('#tabulka tbody tr[data-stav]'), stav = '';
        function filtr() {
            var q = $('#hledat').value.trim().toLowerCase(), n = 0;
            rows.forEach(function (r) {
                var show = (!stav || r.dataset.stav === stav) && (!q || r.textContent.toLowerCase().indexOf(q) > -1);
                r.hidden = !show;
                if (show) n++;
            });
            $('#prazdno').hidden = n > 0;
            $('#pocet').textContent = n + ' z ' + rows.length;
        }
        $('#hledat').addEventListener('input', filtr);
        $$('.seg button').forEach(function (b) {
            b.addEventListener('click', function () {
                stav = b.dataset.filter;
                $$('.seg button').forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
                filtr();
            });
        });
        filtr();
    })();
</script>
</body>
</html>