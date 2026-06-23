<?php
require_once ROOT . '/app/Models/Card.php';
$totalCards = Card::count();
$completion = $totalCards > 0 ? round(($cardCount / $totalCards) * 100) : 0;
$featuredCard = !empty($myCards) ? $myCards[0] : null;
?>
<style>
.dashboard { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 2.5rem; }

/* ── BANNER ── */
.dash-banner {
    position: relative; overflow: hidden;
    min-height: 280px;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 2.5rem 3rem;
    border: 1px solid rgba(255,23,68,.2);
}
.dash-banner-bg {
    position: absolute; inset: 0;
    background-image: url('<?= BASE_URL ?>/assets/img/cards/2016-Apollo-Arrow-002-1080.webp');
    background-size: cover;
    background-position: center 35%;
    filter: brightness(.18) saturate(.7);
    z-index: 0;
}
.dash-banner-overlay {
    position: absolute; inset: 0; z-index: 1;
    background: linear-gradient(90deg, rgba(5,8,16,.97) 0%, rgba(5,8,16,.8) 50%, rgba(5,8,16,.3) 100%);
}
/* Speed lines subtiles */
.dash-banner-speed {
    position: absolute; inset: -20%; z-index: 1;
    background: repeating-linear-gradient(75deg,
        transparent 0px, transparent 22px,
        rgba(255,255,255,.025) 22px, rgba(255,255,255,.03) 24px
    );
    animation: diagSpeed .4s linear infinite;
    pointer-events: none;
}
.dash-banner-scan { position: absolute; left: 0; right: 0; top: 0; height: 2px; z-index: 1; background: linear-gradient(90deg, transparent, rgba(0,229,255,.6), transparent); animation: scanDown 4s ease-in-out infinite; pointer-events: none; }
.dash-banner-left { position: relative; z-index: 2; }
.dash-banner-eyebrow { font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 5px; color: rgba(0,229,255,.4); margin-bottom: .8rem; text-transform: uppercase; }
.dash-banner-username { font-family: 'Orbitron', sans-serif; font-size: clamp(2rem,5vw,3.5rem); font-weight: 900; color: #f2f6fb; line-height: 1; margin-bottom: .5rem; text-transform: uppercase; }
.dash-banner-username span { color: var(--red); text-shadow: 0 0 30px rgba(255,23,68,.4); }
.dash-banner-sub { font-family: var(--font-head); font-size: .72rem; color: rgba(160,180,200,.5); letter-spacing: 4px; margin-bottom: 2rem; }
.dash-banner-coins {
    display: inline-flex; align-items: center; gap: 10px;
    background: rgba(255,183,0,.08); border: 1px solid rgba(255,183,0,.3);
    padding: 8px 20px; font-family: 'Orbitron', sans-serif;
    font-size: 1rem; color: #ffb700;
}
.dash-banner-coins small { font-size: .5rem; display: block; color: rgba(255,183,0,.5); letter-spacing: 2px; }

/* Carte featured dans le banner */
.dash-featured-card {
    position: relative; z-index: 2;
    transform: rotate(3deg);
    transition: transform .3s;
    margin-right: 2rem;
}
.dash-featured-card:hover { transform: rotate(0deg) scale(1.03); }
.dash-featured-card .tcg-card { width: 165px; }
.dash-featured-label { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); z-index: 5; font-family: 'Orbitron', sans-serif; font-weight: 900; font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: #07080d; background: linear-gradient(90deg, var(--rank-s), #fff3c4); padding: 3px 10px; white-space: nowrap; box-shadow: 0 0 12px rgba(255,183,0,.4); clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%); }

/* Barre progression */
.dash-progress-bar {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 3px; z-index: 2; background: rgba(255,255,255,.05);
}
.dash-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--red), var(--cyan));
    transition: width 1s ease;
    position: relative;
}
.dash-progress-fill::after {
    content: ''; position: absolute; right: 0; top: -3px;
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--cyan); box-shadow: 0 0 8px var(--cyan);
}
.dash-progress-label {
    position: absolute; bottom: 6px; right: 1rem;
    font-family: 'Orbitron', sans-serif; font-size: .5rem;
    color: rgba(0,229,255,.4); letter-spacing: 2px;
}

/* ── Stats ── */
.dash-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 1px; background: rgba(0,229,255,.06); border: 1px solid rgba(0,229,255,.06); }
.dash-stat { background: #0c0f18; padding: 1.5rem; text-align: center; transition: background .2s, transform .2s; position: relative; overflow: hidden; }
.dash-stat:hover { background: #12161f; transform: translateY(-3px); }
.dash-stat::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; opacity: .3; transition: opacity .3s; }
.dash-stat:hover::before { opacity: 1; }
.dash-stat:nth-child(1)::before { background: var(--red); }
.dash-stat:nth-child(2)::before { background: #ffb700; }
.dash-stat:nth-child(3)::before { background: #8b5cf6; }
.dash-stat:nth-child(4)::before { background: #00e5ff; }
.dash-stat-icon { font-size: 1.4rem; margin-bottom: .6rem; display: block; }
.dash-stat-num { font-family: 'Orbitron', sans-serif; font-size: 2rem; font-weight: 900; display: block; margin-bottom: .3rem; line-height: 1; }
.dash-stat:nth-child(1) .dash-stat-num { color: var(--red); }
.dash-stat:nth-child(2) .dash-stat-num { color: #ffb700; }
.dash-stat:nth-child(3) .dash-stat-num { color: #c4a8fb; }
.dash-stat:nth-child(4) .dash-stat-num { color: #00e5ff; }
.dash-stat-label { font-family: 'Orbitron', sans-serif; font-size: .48rem; letter-spacing: 3px; text-transform: uppercase; color: #4a5568; }

/* ── Collection ── */
.dash-section-title { font-family: 'Orbitron', sans-serif; font-size: .58rem; letter-spacing: 6px; color: rgba(0,229,255,.4); text-transform: uppercase; display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.dash-section-title a { margin-left: auto; font-size: .55rem; color: rgba(0,229,255,.4); letter-spacing: 2px; transition: color .2s; }
.dash-section-title a:hover { color: var(--cyan); }
.dash-section-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, rgba(0,229,255,.12), transparent); }
.dash-cards-preview { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; }
.dash-rarity { margin-bottom: 1.3rem; }
.dash-rarity-bar { display: flex; height: 8px; gap: 2px; background: rgba(255,255,255,.03); overflow: hidden; }
.dash-rarity-seg { height: 100%; min-width: 5px; transition: flex .6s ease; }
.dash-rarity-legend { display: flex; gap: 1.3rem; margin-top: .6rem; font-family: 'Orbitron', sans-serif; font-size: .55rem; letter-spacing: 1px; color: var(--muted); }
.dash-rarity-legend b { font-weight: 900; font-size: .72rem; margin-right: 3px; }
.dash-empty {
    grid-column: 1/-1; background: #0c0f18;
    border: 1px dashed rgba(0,229,255,.08);
    padding: 3rem; text-align: center;
}
.dash-empty p { color: #4a5568; font-size: .85rem; margin-bottom: 1rem; }

/* ── Deux colonnes bas ── */
.dash-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

/* ── Actions ── */
.dash-actions { display: flex; flex-direction: column; gap: 1px; background: rgba(0,229,255,.06); border: 1px solid rgba(0,229,255,.06); }
.dash-action {
    background: #0c0f18; padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 1.2rem;
    text-decoration: none; color: var(--text);
    transition: background .2s; position: relative;
}
.dash-action::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 2px; opacity: 0; transition: opacity .2s; }
.dash-action:hover { background: #12161f; color: var(--text); }
.dash-action:hover::before { opacity: 1; }
.dash-action.red::before   { background: var(--red); }
.dash-action.cyan::before  { background: var(--cyan); }
.dash-action.gold::before  { background: #ffb700; }
.dash-action.purple::before { background: #8b5cf6; }
.dash-action.green::before { background: #00ff80; }
.dash-action.blue::before  { background: #2563eb; }
.dash-action-icon { font-size: 1.5rem; flex-shrink: 0; }
.dash-action-name { font-family: 'Orbitron', sans-serif; font-size: .62rem; letter-spacing: 2px; text-transform: uppercase; color: var(--cyan); display: block; }
.dash-action-desc { font-size: .72rem; color: #4a5568; }
.dash-action-arrow { margin-left: auto; color: rgba(255,255,255,.1); transition: transform .2s, color .2s; flex-shrink: 0; }
.dash-action:hover .dash-action-arrow { transform: translateX(4px); color: rgba(255,255,255,.25); }

/* ── Boosters ── */
.dash-boosters { display: flex; flex-direction: column; gap: 1px; background: rgba(0,229,255,.06); border: 1px solid rgba(0,229,255,.06); }
.dash-booster {
    background: #0c0f18; padding: 1.2rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    transition: background .2s; position: relative; overflow: hidden;
}
.dash-booster:hover { background: #12161f; }
.dash-booster-icon { font-size: 2rem; flex-shrink: 0; }
.dash-booster-info { flex: 1; }
.dash-booster-name { font-family: 'Orbitron', sans-serif; font-size: .62rem; letter-spacing: 2px; text-transform: uppercase; color: var(--cyan); display: block; margin-bottom: .2rem; }
.dash-booster-desc { font-size: .72rem; color: #4a5568; }
.dash-booster-price { font-family: 'Orbitron', sans-serif; font-size: .75rem; color: #ffb700; flex-shrink: 0; }
</style>

<div class="dashboard">

    <!-- ═══ BANNER ═══ -->
    <div class="dash-banner">
        <div class="dash-banner-bg"></div>
        <div class="dash-banner-overlay"></div>
        <div class="dash-banner-speed"></div>
        <div class="dash-banner-scan"></div>

        <div class="dash-banner-left">
            <p class="dash-banner-eyebrow">// Tableau de bord</p>
            <h1 class="dash-banner-username"><?= htmlspecialchars($user['username']) ?> <span>//</span></h1>
            <p class="dash-banner-sub">COLLECTIONNE · CONSTRUIS · DOMINE</p>
            <div class="dash-banner-coins">
                💰
                <div>
                    <span><?= number_format($user['coins']) ?></span>
                    <small>COINS</small>
                </div>
            </div>
        </div>

        <?php if ($featuredCard): ?>
        <div class="dash-featured-card">
            <span class="dash-featured-label">★ Carte vedette</span>
            <?php
            $card = $featuredCard;
            $showLink = true; $quantity = null;
            include ROOT . '/app/Views/partials/_card.php';
            ?>
        </div>
        <?php endif; ?>

        <!-- Progress bar collection -->
        <div class="dash-progress-bar">
            <div class="dash-progress-fill" style="width:<?= $completion ?>%"></div>
        </div>
        <div class="dash-progress-label">COLLECTION <?= $completion ?>% · <?= $cardCount ?>/<?= $totalCards ?></div>
    </div>

    <!-- ═══ STATS ═══ -->
    <div class="dash-stats">
        <div class="dash-stat">
            <span class="dash-stat-icon">🃏</span>
            <span class="dash-stat-num" data-count="<?= (int)$cardCount ?>">0</span>
            <span class="dash-stat-label">Cartes</span>
        </div>
        <div class="dash-stat">
            <span class="dash-stat-icon">📦</span>
            <span class="dash-stat-num" data-count="<?= (int)$boosterCount ?>">0</span>
            <span class="dash-stat-label">Boosters</span>
        </div>
        <div class="dash-stat">
            <span class="dash-stat-icon">📋</span>
            <span class="dash-stat-num" data-count="<?= (int)$deckCount ?>">0</span>
            <span class="dash-stat-label">Decks</span>
        </div>
        <div class="dash-stat">
            <span class="dash-stat-icon">🏆</span>
            <span class="dash-stat-num" data-count="<?= (int)$battleWins ?>">0</span>
            <span class="dash-stat-label">Victoires</span>
        </div>
    </div>

    <!-- ═══ MA COLLECTION ═══ -->
    <div>
        <p class="dash-section-title">
            // Ma collection
            <?php if ($cardCount > 0): ?>
            <a href="<?= BASE_URL ?>/cards/collection">Voir tout →</a>
            <?php endif; ?>
        </p>
        <?php if ($cardCount > 0):
            $rmap = ['legendary' => ['S', '--rank-s'], 'epic' => ['A', '--rank-a'], 'rare' => ['B', '--rank-b'], 'common' => ['C', '--rank-c']];
        ?>
        <div class="dash-rarity">
            <div class="dash-rarity-bar">
                <?php foreach ($rmap as $rar => $info): if (($rarityBreakdown[$rar] ?? 0) <= 0) continue; ?>
                    <span class="dash-rarity-seg" style="flex:<?= $rarityBreakdown[$rar] ?>;background:var(<?= $info[1] ?>);" title="<?= $info[0] ?> · <?= $rarityBreakdown[$rar] ?>"></span>
                <?php endforeach; ?>
            </div>
            <div class="dash-rarity-legend">
                <?php foreach ($rmap as $rar => $info): ?>
                    <span style="color:var(<?= $info[1] ?>);"><b><?= $info[0] ?></b><?= $rarityBreakdown[$rar] ?? 0 ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if (empty($myCards)): ?>
        <div class="dash-empty">
            <p>Vous n'avez pas encore de cartes. Ouvrez des boosters pour commencer !</p>
            <a href="<?= BASE_URL ?>/boosters" class="btn btn-primary">Voir les boosters →</a>
        </div>
        <?php else: ?>
        <div class="dash-cards-preview">
            <?php foreach ($myCards as $card):
                $showLink = true; $quantity = $card['quantity'];
                include ROOT . '/app/Views/partials/_card.php';
            endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ═══ BAS : Actions + Boosters ═══ -->
    <div class="dash-bottom">

        <!-- Actions rapides -->
        <div>
            <p class="dash-section-title">// Actions rapides</p>
            <div class="dash-actions">
                <a href="<?= BASE_URL ?>/boosters" class="dash-action red">
                    <span class="dash-action-icon">📦</span>
                    <div><span class="dash-action-name">Boutique</span><span class="dash-action-desc">Acheter et ouvrir des boosters</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
                <a href="<?= BASE_URL ?>/arena" class="dash-action cyan">
                    <span class="dash-action-icon">⚔️</span>
                    <div><span class="dash-action-name">Arène</span><span class="dash-action-desc">Lancer ou rejoindre un combat</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
                <a href="<?= BASE_URL ?>/market" class="dash-action gold">
                    <span class="dash-action-icon">🏪</span>
                    <div><span class="dash-action-name">Marché</span><span class="dash-action-desc">Acheter et vendre des cartes</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
                <a href="<?= BASE_URL ?>/decks" class="dash-action purple">
                    <span class="dash-action-icon">📋</span>
                    <div><span class="dash-action-name">Mes decks</span><span class="dash-action-desc">Construire vos decks de combat</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
                <a href="<?= BASE_URL ?>/auctions" class="dash-action green">
                    <span class="dash-action-icon">🔨</span>
                    <div><span class="dash-action-name">Enchères</span><span class="dash-action-desc">Miser sur des cartes rares</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
                <a href="<?= BASE_URL ?>/cards" class="dash-action blue">
                    <span class="dash-action-icon">📖</span>
                    <div><span class="dash-action-name">Encyclopédie</span><span class="dash-action-desc">Toutes les cartes du jeu</span></div>
                    <span class="dash-action-arrow">›</span>
                </a>
            </div>
        </div>

        <!-- Boosters disponibles -->
        <div>
            <p class="dash-section-title">// Boosters disponibles</p>
            <div class="dash-boosters">
                <?php foreach ($boosters as $booster): ?>
                <div class="dash-booster">
                    <span class="dash-booster-icon">📦</span>
                    <div class="dash-booster-info">
                        <span class="dash-booster-name"><?= htmlspecialchars($booster['name']) ?></span>
                        <span class="dash-booster-desc"><?= $booster['card_count'] ?> cartes · pool de <?= $booster['pool_size'] ?></span>
                    </div>
                    <span class="dash-booster-price">💰 <?= $booster['price'] ?></span>
                    <a href="<?= BASE_URL ?>/boosters" class="btn btn-primary" style="font-size:.5rem;padding:5px 12px;flex-shrink:0;">Acheter</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>

<script>
(function () {
    document.querySelectorAll('.dash-stat-num[data-count]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        if (target <= 0) { el.textContent = '0'; return; }
        var start = null, dur = 900;
        function tick(now) {
            if (!start) start = now;
            var p = Math.min(1, (now - start) / dur);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    });
})();
</script>