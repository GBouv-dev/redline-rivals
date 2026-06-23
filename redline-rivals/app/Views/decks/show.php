<style>
.deck-show { max-width: 1200px; margin: 0 auto; }

/* Header */
.deck-show-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 2.5rem; padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255,23,68,.15); position: relative;
}
.deck-show-header::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 60px; height: 1px; background: var(--red); }
.deck-show-eyebrow { font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 5px; color: rgba(0,229,255,.4); margin-bottom: .5rem; text-transform: uppercase; }
.deck-show-title { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; font-weight: 900; color: #f2f6fb; text-transform: uppercase; }
.deck-show-header-right { display: flex; gap: .8rem; align-items: center; }

/* Barre progression */
.deck-show-progress {
    background: #0c0f18; border: 1px solid rgba(0,229,255,.08);
    padding: 1.2rem 1.5rem; margin-bottom: 2rem;
    display: flex; align-items: center; gap: 2rem;
}
.deck-show-progress-num { font-family: 'Orbitron', sans-serif; font-size: 2rem; font-weight: 900; color: var(--cyan); flex-shrink: 0; }
.deck-show-progress-num span { font-size: .8rem; color: #4a5568; }
.deck-show-progress-bar { flex: 1; height: 6px; background: rgba(255,255,255,.05); border-radius: 3px; overflow: hidden; }
.deck-show-progress-fill { height: 100%; background: linear-gradient(90deg, var(--red), var(--cyan)); border-radius: 3px; transition: width .5s ease; }
.deck-show-progress-label { font-family: 'Orbitron', sans-serif; font-size: .55rem; color: #4a5568; letter-spacing: 2px; flex-shrink: 0; }

/* Métriques de puissance */
.deck-metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: rgba(0,229,255,.08); border: 1px solid rgba(0,229,255,.08); margin-bottom: 2rem; }
.deck-metric { background: #0c0f18; padding: 1.1rem 1rem; text-align: center; }
.deck-metric-num { font-family: 'Orbitron', sans-serif; font-weight: 900; font-size: 1.5rem; line-height: 1; color: var(--cyan); }
.deck-metric-label { font-family: 'Orbitron', sans-serif; font-size: .48rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .45rem; }

/* Layout deux colonnes */
.deck-show-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }

/* Section title */
.deck-col-title {
    font-family: 'Orbitron', sans-serif; font-size: .6rem; letter-spacing: 5px;
    text-transform: uppercase; padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,229,255,.08);
    display: flex; align-items: center; justify-content: space-between;
}
.deck-col-title.in-deck { color: var(--red); background: rgba(255,23,68,.03); }
.deck-col-title.collection { color: var(--cyan); background: rgba(0,229,255,.03); }

/* Colonnes */
.deck-col {
    background: #0c0f18; border: 1px solid rgba(0,229,255,.08);
    overflow: hidden;
}

/* Grille cartes dans col */
.deck-col-cards { padding: 1.5rem; }
.deck-col-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px,1fr)); gap: .8rem; }

/* Carte dans deck - avec bouton retirer */
.deck-card-item {
    position: relative;
}
.deck-card-item .tcg-card { width: 100%; }
.deck-card-remove {
    position: absolute; top: 6px; left: 6px; z-index: 10;
    background: rgba(255,23,68,.9); border: none; color: #fff;
    font-family: 'Orbitron', sans-serif; font-size: .5rem; letter-spacing: 1px;
    padding: 3px 8px; cursor: pointer;
    clip-path: polygon(4px 0%, 100% 0%, calc(100% - 4px) 100%, 0% 100%);
    transition: background .2s; opacity: 0;
    transition: opacity .2s;
}
.deck-card-item:hover .deck-card-remove { opacity: 1; }

/* Carte à ajouter */
.deck-card-add { position: relative; }
.deck-card-add .tcg-card { width: 100%; }
.deck-card-add-btn {
    position: absolute; bottom: 54px; left: 0; right: 0; z-index: 10;
    background: rgba(0,229,255,.9); border: none; color: #000;
    font-family: 'Orbitron', sans-serif; font-size: .5rem; letter-spacing: 1px;
    padding: 5px; cursor: pointer; text-align: center;
    opacity: 0; transition: opacity .2s;
    width: 100%;
}
.deck-card-add:hover .deck-card-add-btn { opacity: 1; }

.deck-col-empty { padding: 2rem; text-align: center; color: #4a5568; font-size: .8rem; }

/* Tip combat ready */
.deck-combat-tip {
    margin: 1rem 1.5rem 0;
    padding: .8rem 1rem;
    background: rgba(0,255,80,.04); border: 1px solid rgba(0,255,80,.15);
    font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 1px;
    color: #00ff80;
}
.deck-not-ready {
    margin: 1rem 1.5rem 0;
    padding: .8rem 1rem;
    background: rgba(255,23,68,.04); border: 1px solid rgba(255,23,68,.15);
    font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 1px;
    color: var(--red);
}
</style>

<?php
$cardCount = count($deck['cards']);
$pct = min(100, ($cardCount / 10) * 100);
$deckCardIds = array_column($deck['cards'], 'id');
$available = array_filter($collection, fn($c) => !in_array($c['id'], $deckCardIds));

$rankOrder = ['common' => 0, 'rare' => 1, 'epic' => 2, 'legendary' => 3];
$deckPower = 0; $bestRarity = null;
foreach ($deck['cards'] as $c) {
    $deckPower += $c['speed'] + $c['power'] + $c['handling'] + $c['armor'];
    if ($bestRarity === null || ($rankOrder[$c['rarity']] ?? 0) > ($rankOrder[$bestRarity] ?? 0)) $bestRarity = $c['rarity'];
}
$avgPower      = $cardCount > 0 ? round($deckPower / $cardCount) : 0;
$bestLetter    = ['legendary' => 'S', 'epic' => 'A', 'rare' => 'B', 'common' => 'C'][$bestRarity] ?? '–';
$bestColorVar  = ['legendary' => '--rank-s', 'epic' => '--rank-a', 'rare' => '--rank-b', 'common' => '--rank-c'][$bestRarity] ?? '--muted';
?>

<div class="deck-show">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Deck de combat</p>
            <h1 class="hud-title"><?= htmlspecialchars($deck['name']) ?></h1>
        </div>
        <div class="hud-header-right">
            <?php if ($cardCount >= 3): ?>
                <a href="<?= BASE_URL ?>/arena" class="btn btn-primary">⚔️ Combattre</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/decks" class="btn">← Mes decks</a>
        </div>
    </div>

    <!-- Barre progression -->
    <div class="deck-show-progress">
        <div class="deck-show-progress-num"><?= $cardCount ?><span>/10</span></div>
        <div class="deck-show-progress-bar">
            <div class="deck-show-progress-fill" style="width:<?= $pct ?>%"></div>
        </div>
        <span class="deck-show-progress-label">
            <?= $cardCount >= 10 ? '✓ DECK COMPLET' : ($cardCount >= 3 ? '⚔️ COMBAT READY' : 'MINIMUM 3 CARTES') ?>
        </span>
    </div>

    <!-- Métriques de puissance -->
    <div class="deck-metrics">
        <div class="deck-metric">
            <div class="deck-metric-num"><?= $cardCount ?><span style="font-size:.8rem;color:var(--muted)">/10</span></div>
            <div class="deck-metric-label">Cartes</div>
        </div>
        <div class="deck-metric">
            <div class="deck-metric-num"><?= $deckPower ?></div>
            <div class="deck-metric-label">Puissance totale</div>
        </div>
        <div class="deck-metric">
            <div class="deck-metric-num"><?= $avgPower ?></div>
            <div class="deck-metric-label">Moyenne / carte</div>
        </div>
        <div class="deck-metric">
            <div class="deck-metric-num" style="color:var(<?= $bestColorVar ?>)"><?= $bestLetter ?></div>
            <div class="deck-metric-label">Classe top</div>
        </div>
    </div>

    <?php if ($cardCount >= 3): ?>
        <div class="deck-combat-tip">✓ Ce deck est prêt au combat — minimum de 3 cartes atteint</div>
    <?php else: ?>
        <div class="deck-not-ready">⚠ Ajoutez encore <?= 3 - $cardCount ?> carte<?= (3 - $cardCount) > 1 ? 's' : '' ?> pour pouvoir combattre</div>
    <?php endif; ?>

    <br>

    <!-- Layout deux colonnes -->
    <div class="deck-show-layout">

        <!-- Cartes dans le deck -->
        <div class="deck-col">
            <div class="deck-col-title in-deck">
                <span>// Dans le deck</span>
                <span><?= $cardCount ?>/10</span>
            </div>
            <div class="deck-col-cards">
                <?php if (empty($deck['cards'])): ?>
                    <div class="deck-col-empty">Aucune carte — ajoutez des cartes depuis votre collection →</div>
                <?php else: ?>
                <div class="deck-col-grid">
                    <?php foreach ($deck['cards'] as $card): ?>
                    <div class="deck-card-item">
                        <?php $showLink = false; $quantity = null; include ROOT . '/app/Views/partials/_card.php'; ?>
                        <form action="<?= BASE_URL ?>/decks/<?= $deck['id'] ?>/remove" method="POST">
                            <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                            <button type="submit" class="deck-card-remove">✕ Retirer</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Collection -->
        <div class="deck-col">
            <div class="deck-col-title collection">
                <span>// Ma collection</span>
                <span><?= count($available) ?> disponibles</span>
            </div>
            <div class="deck-col-cards">
                <?php if (empty($available)): ?>
                    <div class="deck-col-empty">Toutes vos cartes sont déjà dans ce deck.</div>
                <?php else: ?>
                <div class="deck-col-grid">
                    <?php foreach ($available as $card): ?>
                    <div class="deck-card-add">
                        <?php $showLink = false; $quantity = null; include ROOT . '/app/Views/partials/_card.php'; ?>
                        <?php if ($cardCount < 10): ?>
                        <form action="<?= BASE_URL ?>/decks/<?= $deck['id'] ?>/add" method="POST">
                            <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                            <button type="submit" class="deck-card-add-btn">+ Ajouter</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>