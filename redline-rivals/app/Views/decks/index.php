<style>
/* ── Page decks ── */
.decks-page { max-width: 1100px; margin: 0 auto; }

.decks-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 2.5rem; padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255,23,68,.15);
    position: relative;
}
.decks-header::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 60px; height: 1px; background: var(--red); }
.decks-header-left .eyebrow { font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 5px; color: rgba(0,229,255,.4); margin-bottom: .5rem; text-transform: uppercase; }
.decks-header-left h1 { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; font-weight: 900; color: #f2f6fb; text-transform: uppercase; }

/* Formulaire création */
.deck-create-form {
    background: #0c0f18; border: 1px solid rgba(0,229,255,.08);
    padding: 1.5rem 2rem; margin-bottom: 2.5rem;
    display: flex; gap: 1rem; align-items: flex-end;
    position: relative; overflow: hidden;
}
.deck-create-form::before { content: '//'; position: absolute; bottom: -10px; right: 15px; font-family: var(--font-head); font-weight: 900; font-size: 5rem; color: rgba(0,229,255,.03); pointer-events: none; }
.deck-create-form .form-group { flex: 1; margin: 0; }
.deck-create-form label { font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 3px; color: rgba(0,229,255,.4); text-transform: uppercase; margin-bottom: 6px; display: block; }

/* Grille decks */
.decks-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 1.5rem; }

.deck-card {
    background: #0c0f18;
    border: 1px solid rgba(0,229,255,.08);
    padding: 0;
    position: relative; overflow: hidden;
    transition: border-color .2s, transform .2s;
}
.deck-card:hover { border-color: rgba(255,23,68,.2); transform: translateY(-4px); }

/* Barre couleur en haut */
.deck-card-top {
    height: 3px;
    background: linear-gradient(90deg, var(--red), var(--cyan));
}

.deck-card-body { padding: 1.5rem; }

/* Stats mini deck */
.deck-card-stats {
    display: flex; gap: .5rem; margin-bottom: 1rem;
}
.deck-stat-pill {
    font-family: 'Orbitron', sans-serif; font-size: .5rem; letter-spacing: 1px;
    padding: 3px 8px; border: 1px solid;
}
.deck-stat-pill.full { border-color: rgba(0,255,80,.3); color: #00ff80; background: rgba(0,255,80,.05); }
.deck-stat-pill.partial { border-color: rgba(0,229,255,.25); color: var(--cyan); background: rgba(0,229,255,.04); }
.deck-stat-pill.empty { border-color: rgba(255,23,68,.25); color: var(--red); background: rgba(255,23,68,.04); }

.deck-card-name { font-family: 'Orbitron', sans-serif; font-size: .9rem; letter-spacing: 2px; text-transform: uppercase; color: #f2f6fb; margin-bottom: .5rem; }
.deck-card-count { font-family: 'Orbitron', sans-serif; font-size: .55rem; color: #4a5568; letter-spacing: 2px; }

/* Barre de progression cartes */
.deck-progress { margin: 1rem 0; }
.deck-progress-track { height: 4px; background: rgba(255,255,255,.05); border-radius: 2px; overflow: hidden; margin-top: 4px; }
.deck-progress-fill { height: 100%; background: linear-gradient(90deg, var(--red), var(--cyan)); border-radius: 2px; }

.deck-card-actions { display: flex; gap: .5rem; margin-top: 1.2rem; }

/* Kanji déco */
.deck-card::after { content: '//'; position: absolute; bottom: -5px; right: 10px; font-family: var(--font-head); font-weight: 900; font-size: 4rem; color: rgba(0,229,255,.03); pointer-events: none; line-height: 1; }

.decks-empty {
    background: #0c0f18; border: 1px dashed rgba(0,229,255,.08);
    padding: 4rem; text-align: center;
}
.decks-empty .empty-icon { font-size: 3rem; margin-bottom: 1rem; opacity: .4; display: block; }
.decks-empty p { color: #4a5568; margin-bottom: 1.5rem; }
</style>

<div class="decks-page">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Gestion des decks</p>
            <h1 class="hud-title">Mes <span>decks</span></h1>
        </div>
        <div class="hud-header-right">
            <a href="<?= BASE_URL ?>/dashboard" class="btn">← Dashboard</a>
        </div>
    </div>

    <!-- Créer un deck -->
    <form action="<?= BASE_URL ?>/decks/create" method="POST" class="deck-create-form">
        <div class="form-group">
            <label>Nom du nouveau deck</label>
            <input type="text" name="name" placeholder="Ex: Deck Vitesse, Full Légendaire..." required>
        </div>
        <button type="submit" class="btn btn-primary">+ Créer</button>
    </form>

    <?php if (empty($decks)): ?>
    <div class="decks-empty">
        <span class="empty-icon">📋</span>
        <p>Vous n'avez pas encore de deck.<br>Créez-en un pour pouvoir combattre dans l'arène.</p>
    </div>
    <?php else: ?>
    <div class="decks-grid">
        <?php foreach ($decks as $deck):
            $count = (int)$deck['card_count'];
            $pillClass = $count >= 10 ? 'full' : ($count > 0 ? 'partial' : 'empty');
            $pillLabel = $count >= 10 ? '✓ Complet' : ($count > 0 ? 'En cours' : 'Vide');
            $pct = min(100, ($count / 10) * 100);
        ?>
        <div class="deck-card">
            <div class="deck-card-top"></div>
            <div class="deck-card-body">
                <div class="deck-card-stats">
                    <span class="deck-stat-pill <?= $pillClass ?>"><?= $pillLabel ?></span>
                    <?php if ($count >= 3): ?><span class="deck-stat-pill partial">⚔️ Combat ready</span><?php endif; ?>
                </div>
                <h3 class="deck-card-name"><?= htmlspecialchars($deck['name']) ?></h3>
                <span class="deck-card-count"><?= $count ?>/10 CARTES</span>
                <div class="deck-progress">
                    <div class="deck-progress-track">
                        <div class="deck-progress-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <div class="deck-card-actions">
                    <a href="<?= BASE_URL ?>/decks/<?= $deck['id'] ?>" class="btn btn-accent" style="flex:1;text-align:center;">Gérer →</a>
                    <form action="<?= BASE_URL ?>/decks/<?= $deck['id'] ?>/delete" method="POST">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Supprimer ce deck ?')">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>