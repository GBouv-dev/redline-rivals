<style>
.arena { max-width: 1100px; margin: 0 auto; }

.create-form { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
.create-form .form-group { margin: 0; flex: 1; min-width: 200px; }

.battle-list { display: flex; flex-direction: column; gap: .8rem; }
.battle-row {
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    background: var(--bg2); border: 1px solid var(--border); border-left: 3px solid var(--muted);
    padding: .9rem 1.1rem; transition: border-color .2s, transform .2s;
}
.battle-row:hover { transform: translateX(3px); border-color: var(--border-red); }
.battle-row.waiting { border-left-color: var(--amber); }
.battle-row.active  { border-left-color: #00ff80; }

.battle-tag { font-family: var(--font-mono); font-size: .62rem; color: var(--muted); letter-spacing: 1px; flex-shrink: 0; }
.battle-match { display: flex; align-items: center; gap: .9rem; flex: 1; min-width: 200px; }
.battle-player { font-family: var(--font-head); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); }
.battle-player.ghost { color: var(--muted); }
.battle-vs { font-family: var(--font-head); font-weight: 900; font-size: .85rem; color: var(--red); text-shadow: 0 0 12px rgba(255,23,68,.5); }
.battle-deck { font-family: var(--font-mono); font-size: .58rem; color: rgba(0,229,255,.5); }

.battle-pill { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; padding: 4px 9px; border: 1px solid; flex-shrink: 0; }
.battle-pill.waiting { color: var(--amber); border-color: rgba(255,183,0,.4); background: rgba(255,183,0,.05); }
.battle-pill.active  { color: #00ff80; border-color: rgba(0,255,128,.4); background: rgba(0,255,128,.05); }

.battle-act { display: flex; align-items: center; gap: .5rem; }
.battle-act form { margin: 0; display: flex; gap: .5rem; align-items: center; }
.battle-act select { width: auto; min-width: 130px; font-size: .72rem; padding: 7px 10px; }
</style>

<div class="arena">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Arène · Duels PvP</p>
            <h1 class="hud-title">⚔️ L'<span>arène</span></h1>
        </div>
        <div class="hud-header-right">
            <a href="<?= BASE_URL ?>/dashboard" class="btn">← Dashboard</a>
        </div>
    </div>

    <!-- Créer un combat -->
    <?php if (empty($myDecks)): ?>
        <div class="empty-state">
            <p>Vous n'avez pas de deck. Créez-en un d'abord pour entrer dans l'arène.</p>
            <a href="<?= BASE_URL ?>/decks" class="btn btn-primary">Gérer mes decks</a>
        </div>
    <?php else: ?>
        <div class="panel" style="margin-bottom:2.5rem;">
            <p class="panel-title">// Lancer un nouveau combat</p>
            <form action="<?= BASE_URL ?>/arena/create" method="POST" class="create-form">
                <div class="form-group">
                    <label>Choisir un deck</label>
                    <select name="deck_id">
                        <?php foreach ($myDecks as $deck): ?>
                            <option value="<?= $deck['id'] ?>"><?= htmlspecialchars($deck['name']) ?> (<?= $deck['card_count'] ?> cartes)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Créer le combat</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Mes combats en cours -->
    <?php if (!empty($myBattles)): ?>
    <p class="section-eyebrow">// Mes combats en cours</p>
    <div class="battle-list" style="margin-bottom:2.5rem;">
        <?php foreach ($myBattles as $battle): ?>
        <div class="battle-row <?= $battle['status'] ?>">
            <span class="battle-tag">#<?= $battle['id'] ?></span>
            <div class="battle-match">
                <span class="battle-player"><?= htmlspecialchars($battle['player1_name']) ?></span>
                <span class="battle-vs">VS</span>
                <span class="battle-player <?= $battle['player2_name'] ? '' : 'ghost' ?>"><?= $battle['player2_name'] ? htmlspecialchars($battle['player2_name']) : 'En attente…' ?></span>
            </div>
            <span class="battle-pill <?= $battle['status'] ?>"><?= $battle['status'] === 'active' ? 'En cours' : 'En attente' ?></span>
            <div class="battle-act">
                <a href="<?= BASE_URL ?>/arena/<?= $battle['id'] ?>" class="btn btn-accent">Reprendre →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Combats ouverts -->
    <p class="section-eyebrow">// Combats ouverts · <?= count($waitingBattles) ?> à rejoindre</p>
    <?php if (empty($waitingBattles)): ?>
        <div class="empty-state"><p>Aucun combat en attente. Crée le premier et défie tes rivaux.</p></div>
    <?php else: ?>
    <div class="battle-list">
        <?php foreach ($waitingBattles as $battle): ?>
        <div class="battle-row waiting">
            <span class="battle-tag">#<?= $battle['id'] ?></span>
            <div class="battle-match">
                <span class="battle-player"><?= htmlspecialchars($battle['player1_name']) ?></span>
                <span class="battle-deck">▸ <?= htmlspecialchars($battle['deck1_name']) ?></span>
            </div>
            <span class="battle-pill waiting">Ouvert</span>
            <?php if (!empty($myDecks)): ?>
            <div class="battle-act">
                <form action="<?= BASE_URL ?>/arena/<?= $battle['id'] ?>/join" method="POST">
                    <select name="deck_id">
                        <?php foreach ($myDecks as $deck): ?>
                            <option value="<?= $deck['id'] ?>"><?= htmlspecialchars($deck['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Rejoindre</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
