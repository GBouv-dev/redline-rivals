<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Redline Rivals' ?> — REDLINE RIVALS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= @filemtime(ROOT . '/public/assets/css/style.css') ?>">
    <script>document.documentElement.className += ' js-reveal';</script>
</head>
<body>

<div id="preloader" class="preloader" aria-hidden="true">
    <div class="pl-grid"></div>
    <div class="pl-scan"></div>
    <div class="pl-streaks"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <div class="pl-content">
        <p class="pl-eyebrow">// Redline Racing League</p>
        <div class="pl-brand">Redline <span>Rivals</span></div>
        <div class="pl-tach"><div class="pl-tach-fill"></div></div>
        <p class="pl-status">Initialisation du moteur <span class="pl-dots"><i>.</i><i>.</i><i>.</i></span></p>
    </div>
</div>
<script>
(function () {
    var pl = document.getElementById('preloader');
    if (!pl) return;
    try {
        if (sessionStorage.getItem('rr_splash')) { pl.parentNode.removeChild(pl); return; }
        sessionStorage.setItem('rr_splash', '1');
    } catch (e) {}
    var statusEl = pl.querySelector('.pl-status');
    var msgs = ['Initialisation du moteur', 'Chargement des circuits', 'Mise en grille de départ'];
    var mi = 0;
    var statusTimer = setInterval(function () {
        mi = (mi + 1) % msgs.length;
        if (statusEl && statusEl.firstChild) statusEl.firstChild.nodeValue = msgs[mi] + ' ';
    }, 900);

    var loaded = false, minDone = false, done = false;
    function hide() {
        if (done) return;
        done = true;
        clearInterval(statusTimer);
        pl.classList.add('hide');
        setTimeout(function () { if (pl.parentNode) pl.parentNode.removeChild(pl); }, 550);
    }
    function maybe() { if (loaded && minDone) hide(); }
    window.addEventListener('load', function () { loaded = true; maybe(); });
    setTimeout(function () { minDone = true; maybe(); }, 2800);
    setTimeout(hide, 6000);
})();
</script>

<?php
$navPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/');
$navActive = function (string $p) use ($navPath) {
    if ($p === '/') return $navPath === '/' ? 'active' : '';
    return ($navPath === $p || str_starts_with($navPath, $p . '/')) ? 'active' : '';
};
?>
<nav>
    <a href="<?= BASE_URL ?>/" class="nav-brand">REDLINE RIVALS</a>
    <?php if (Auth::check()): ?>
        <a href="<?= BASE_URL ?>/dashboard" class="<?= $navActive('/dashboard') ?>">Dashboard</a>
        <a href="<?= BASE_URL ?>/cards/collection" class="<?= $navActive('/cards/collection') ?>">Collection</a>
        <a href="<?= BASE_URL ?>/boosters" class="<?= $navActive('/boosters') ?>">Boutique</a>
        <a href="<?= BASE_URL ?>/arena" class="<?= $navActive('/arena') ?>">Arène</a>
        <a href="<?= BASE_URL ?>/market" class="<?= $navActive('/market') ?>">Marché</a>
        <a href="<?= BASE_URL ?>/auctions" class="<?= $navActive('/auctions') ?>">Enchères</a>
        <a href="<?= BASE_URL ?>/classement" class="<?= $navActive('/classement') ?>">Classement</a>
        <a href="<?= BASE_URL ?>/profil" class="<?= $navActive('/profil') ?>">Profil</a>
        <?php if (Auth::isAdmin()): ?>
            <a href="<?= BASE_URL ?>/admin" class="nav-admin <?= $navActive('/admin') ?>">⚙ Admin</a>
        <?php endif; ?>
        <span class="nav-coins">💰 <?= Auth::user()['coins'] ?></span>
        <a href="<?= BASE_URL ?>/logout">Quitter</a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>/login" class="<?= $navActive('/login') ?>">Connexion</a>
        <a href="<?= BASE_URL ?>/register" class="<?= $navActive('/register') ?>">Inscription</a>
    <?php endif; ?>
</nav>

<main>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash <?= $_SESSION['flash']['type'] ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?>
</main>

<div class="fx-overlay" aria-hidden="true"><div class="fx-sweep"></div></div>

<script>
(function () {
    var root = document.documentElement;
    if (!root.classList.contains('js-reveal')) return;
    var items = Array.prototype.slice.call(document.querySelectorAll('main > *'));
    function revealAll() { items.forEach(function (el) { el.classList.add('in'); }); }
    try {
        if (!('IntersectionObserver' in window)) { revealAll(); return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        items.forEach(function (el) { io.observe(el); });
        setTimeout(revealAll, 2500);
    } catch (e) { revealAll(); }
})();
</script>

<script>
(function () {
    try {
        if (!window.matchMedia) return;
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (matchMedia('(hover: none)').matches) return;
        document.querySelectorAll('.tcg-card').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width;
                var py = (e.clientY - r.top) / r.height;
                card.style.setProperty('--rx', ((py - 0.5) * -14).toFixed(2) + 'deg');
                card.style.setProperty('--ry', ((px - 0.5) * 14).toFixed(2) + 'deg');
                card.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
                card.style.setProperty('--my', (py * 100).toFixed(1) + '%');
                if (!card.classList.contains('tilt')) card.classList.add('tilt');
            });
            card.addEventListener('mouseleave', function () {
                card.classList.remove('tilt');
                card.style.removeProperty('--rx');
                card.style.removeProperty('--ry');
            });
        });
    } catch (e) {}
})();
</script>

</body>
</html>
