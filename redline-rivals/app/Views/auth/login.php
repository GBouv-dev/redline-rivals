<div class="auth-screen">
    <div class="auth-card">
        <div class="auth-aside">
            <div class="auth-aside-bg" style="background-image:url('<?= BASE_URL ?>/assets/img/cards/2016-Apollo-Arrow-002-1080.webp')"></div>
            <div class="auth-aside-overlay"></div>
            <div>
                <p class="auth-eyebrow">// Redline Racing League</p>
                <div class="auth-brand">Redline<span>Rivals</span></div>
                <p class="auth-aside-tag">COLLECTIONNE · CONSTRUIS · DOMINE</p>
            </div>
            <ul class="auth-features">
                <li>🃏 100+ machines à collectionner</li>
                <li>⚔️ Combats PvP dans l'arène</li>
                <li>💰 +500 coins offerts au départ</li>
            </ul>
        </div>
        <div class="auth-main">
            <h1>Connexion</h1>
            <p class="auth-sub">Reprends la course là où tu l'as laissée.</p>
            <form action="<?= BASE_URL ?>/login" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="pw-field">
                        <input type="password" id="password" name="password" required placeholder="••••••••">
                        <button type="button" class="pw-toggle" data-target="password" aria-label="Afficher le mot de passe">👁</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Se connecter →</button>
            </form>
            <p class="auth-switch">Pas encore de compte ? <a href="<?= BASE_URL ?>/register">S'inscrire</a></p>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.pw-toggle').forEach(function (b) {
    b.addEventListener('click', function () {
        var inp = document.getElementById(b.getAttribute('data-target'));
        if (!inp) return;
        var show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        b.classList.toggle('on', show);
    });
});
</script>
