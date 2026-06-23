<div class="auth-screen">
    <div class="auth-card">
        <div class="auth-aside">
            <div class="auth-aside-bg" style="background-image:url('<?= BASE_URL ?>/assets/img/cards/2023-Porsche-911-GT3-RS-003-1080.webp')"></div>
            <div class="auth-aside-overlay"></div>
            <div>
                <p class="auth-eyebrow">// Nouvelle recrue</p>
                <div class="auth-brand">Entre dans<span>la course</span></div>
                <p class="auth-aside-tag">TON GARAGE T'ATTEND</p>
            </div>
            <ul class="auth-features">
                <li>🎁 +500 coins offerts pour démarrer</li>
                <li>📦 Ouvre tes premiers boosters</li>
                <li>🏆 Grimpe le classement des rivaux</li>
            </ul>
        </div>
        <div class="auth-main">
            <h1>Inscription</h1>
            <p class="auth-sub">Crée ton compte et ouvre tes premiers boosters.</p>
            <form action="<?= BASE_URL ?>/register" method="POST">
                <div class="form-group">
                    <label for="username">Pseudo</label>
                    <input type="text" id="username" name="username" required placeholder="VotrePseudo">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="pw-field">
                        <input type="password" id="password" name="password" required placeholder="6 caractères minimum">
                        <button type="button" class="pw-toggle" data-target="password" aria-label="Afficher le mot de passe">👁</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <div class="pw-field">
                        <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••">
                        <button type="button" class="pw-toggle" data-target="password_confirm" aria-label="Afficher le mot de passe">👁</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Créer mon compte →</button>
            </form>
            <p class="auth-switch">Déjà un compte ? <a href="<?= BASE_URL ?>/login">Se connecter</a></p>
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
