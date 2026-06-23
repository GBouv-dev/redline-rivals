<style>
.card-show { display: grid; grid-template-columns: 240px 1fr; gap: 3rem; align-items: start; }

/* Carte TCG grande taille */
.card-show-card {
    width: 240px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
}
.card-show-card.rarity-legendary { box-shadow: 0 0 0 1px rgba(255,183,0,.6), 0 15px 50px rgba(255,183,0,.25); }
.card-show-card.rarity-epic      { box-shadow: 0 0 0 1px rgba(139,92,246,.6), 0 12px 40px rgba(139,92,246,.2); }
.card-show-card.rarity-rare      { box-shadow: 0 0 0 1px rgba(59,130,246,.5),  0 10px 35px rgba(59,130,246,.15); }
.card-show-card.rarity-common    { box-shadow: 0 0 0 1px rgba(107,114,128,.3); }

.card-show-top { height: 4px; }
.rarity-legendary .card-show-top { background: linear-gradient(90deg, #ffb700, #fff3c4, #ffb700); }
.rarity-epic      .card-show-top { background: linear-gradient(90deg, #8b5cf6, #a855f7, #8b5cf6); }
.rarity-rare      .card-show-top { background: #3b82f6; }
.rarity-common    .card-show-top { background: #6b7280; }

.card-show-art {
    height: 200px;
    position: relative;
    overflow: hidden;
}
.card-show-art img {
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center 25%;
    filter: brightness(1) saturate(1);
    transition: transform .4s, filter .3s;
}
.card-show-card:hover .card-show-art img {
    transform: scale(1.05);
    filter: brightness(0.95) saturate(1.05);
}
.card-show-art-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, transparent 50%, rgba(12,15,24,.7) 100%);
    z-index: 2; pointer-events: none;
}
.card-show-scan {
    position: absolute; top: 0; left: 0; width: 100%; height: 2px;
    background: linear-gradient(90deg, transparent, rgba(0,229,255,.7), transparent);
    animation: scanDown 3.5s ease-in-out infinite;
    pointer-events: none; z-index: 3;
}
.card-show-shine {
    position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.06) 50%, transparent 65%);
    animation: csShine 5s ease-in-out infinite;
    pointer-events: none; z-index: 4;
}
.card-show-watermark {
    position: absolute; bottom: -10px; right: 8px;
    font-family: var(--font-head);
    font-size: 5rem; font-weight: 900;
    line-height: 1; pointer-events: none; z-index: 1;
    opacity: .08; color: #fff;
}
.rarity-legendary .card-show-watermark { color: var(--rank-s); opacity: .16; }
.rarity-epic      .card-show-watermark { color: var(--rank-a); opacity: .13; }
.rarity-rare      .card-show-watermark { color: var(--rank-b); opacity: .11; }
.rarity-common    .card-show-watermark { color: var(--rank-c); opacity: .08; }

.card-show-info {
    background: #0c0f18;
    padding: 14px;
    border-top: 1px solid rgba(255,255,255,.05);
}
.card-show-header { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
.card-show-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.rarity-legendary .card-show-dot { background: #ffb700; box-shadow: 0 0 8px rgba(255,183,0,.7); }
.rarity-epic      .card-show-dot { background: #8b5cf6; box-shadow: 0 0 7px rgba(139,92,246,.6); }
.rarity-rare      .card-show-dot { background: #3b82f6; box-shadow: 0 0 6px rgba(59,130,246,.5); }
.rarity-common    .card-show-dot { background: #6b7280; }
.card-show-name {
    font-family: 'Orbitron', sans-serif;
    font-size: .75rem; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    color: #e0ecf8; flex: 1;
}
.card-show-id { font-family: 'Orbitron', sans-serif; font-size: .5rem; color: rgba(255,255,255,.15); }
.card-show-type { font-family: 'Orbitron', sans-serif; font-size: .5rem; letter-spacing: 2px; color: rgba(0,229,255,.35); margin-bottom: 10px; }
.card-show-stats { display: flex; flex-direction: column; gap: 5px; }
.card-show-stat { display: flex; align-items: center; gap: 6px; }
.card-show-stat-icon { font-size: .65rem; width: 14px; text-align: center; flex-shrink: 0; }
.card-show-stat-track { flex: 1; height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; overflow: hidden; }
.card-show-stat-fill { height: 100%; border-radius: 2px; }
.rarity-legendary .card-show-stat-fill { background: linear-gradient(90deg, #ffb700, #fff3c4); }
.rarity-epic      .card-show-stat-fill { background: linear-gradient(90deg, #6d28d9, #c4a8fb); }
.rarity-rare      .card-show-stat-fill { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
.rarity-common    .card-show-stat-fill { background: linear-gradient(90deg, #374151, #6b7280); }
.card-show-stat-val { font-family: 'Orbitron', sans-serif; font-size: .55rem; color: rgba(255,255,255,.3); width: 22px; text-align: right; }

/* Infos droite */
.card-show-right { display: flex; flex-direction: column; gap: 2rem; }

.card-show-title-area { border-bottom: 1px solid rgba(0,229,255,.1); padding-bottom: 1.5rem; }
.card-show-title-head { display: flex; align-items: center; gap: 1rem; }
.card-show-rank { display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; flex-shrink: 0; font-family: var(--font-head); font-weight: 900; font-size: 1.9rem; border: 2px solid; }
.rarity-legendary .card-show-rank { color: var(--rank-s); border-color: var(--rank-s); box-shadow: inset 0 0 18px rgba(255,183,0,.2), 0 0 18px rgba(255,183,0,.15); }
.rarity-epic      .card-show-rank { color: var(--rank-a); border-color: var(--rank-a); box-shadow: inset 0 0 15px rgba(139,92,246,.15); }
.rarity-rare      .card-show-rank { color: var(--rank-b); border-color: var(--rank-b); box-shadow: inset 0 0 15px rgba(59,130,246,.15); }
.rarity-common    .card-show-rank { color: var(--rank-c); border-color: var(--rank-c); }
.card-show-eyebrow { font-family: 'Orbitron', sans-serif; font-size: .6rem; letter-spacing: 4px; color: var(--red); margin-bottom: .5rem; }
.card-show-title { font-family: 'Orbitron', sans-serif; font-size: clamp(1.2rem,3vw,1.8rem); color: #f2f6fb; margin-bottom: .5rem; }
.card-show-desc { font-size: .9rem; color: var(--muted); line-height: 1.8; border-left: 2px solid rgba(255,23,68,.3); padding-left: 1rem; font-style: italic; }

/* Stats détaillées */
.card-stats-wrap { display: flex; gap: 1.8rem; align-items: center; flex-wrap: wrap; }
.card-radar { width: 185px; flex-shrink: 0; margin: 0 auto; }
.card-radar svg { width: 100%; height: auto; overflow: visible; }
.card-stats-side { flex: 1; min-width: 250px; }
.card-stats-detail { display: flex; flex-direction: column; gap: 1rem; }
.card-stat-detail { display: grid; grid-template-columns: 130px 1fr 45px; align-items: center; gap: 1rem; }
.card-stat-detail-label { font-family: 'Orbitron', sans-serif; font-size: .6rem; letter-spacing: 1px; color: var(--muted); text-transform: uppercase; }
.card-stat-detail-bar { background: rgba(255,255,255,.04); height: 6px; border-radius: 3px; overflow: hidden; }
.card-stat-detail-fill { height: 100%; border-radius: 3px; }
.rarity-legendary .card-stat-detail-fill { background: linear-gradient(90deg, #ffb700, #fff3c4); }
.rarity-epic      .card-stat-detail-fill { background: linear-gradient(90deg, #6d28d9, #c4a8fb); }
.rarity-rare      .card-stat-detail-fill { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
.rarity-common    .card-stat-detail-fill { background: linear-gradient(90deg, #374151, #6b7280); }
.card-stat-detail-val { font-family: 'Orbitron', sans-serif; font-size: .75rem; text-align: right; }
.rarity-legendary .card-stat-detail-val { color: #ffb700; }
.rarity-epic      .card-stat-detail-val { color: #c4a8fb; }
.rarity-rare      .card-stat-detail-val { color: #60a5fa; }
.rarity-common    .card-stat-detail-val { color: #6b7280; }

/* Score total */
.card-total-score {
    display: flex; justify-content: space-between; align-items: center;
    padding: .8rem 1rem;
    background: rgba(0,229,255,.03);
    border: 1px solid rgba(0,229,255,.1);
}
.card-total-score-label { font-family: 'Orbitron', sans-serif; font-size: .55rem; color: var(--muted); letter-spacing: 2px; }
.card-total-score-val { font-family: 'Orbitron', sans-serif; font-size: 1.4rem; color: var(--cyan); }
.card-total-score-max { font-size: .55rem; color: var(--muted); }

/* Status */
.card-status-badge {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem;
    border: 1px solid;
}
.card-status-badge.owned    { background: rgba(0,255,80,.03);  border-color: rgba(0,255,80,.2); }
.card-status-badge.missing  { background: rgba(255,23,68,.03);  border-color: rgba(255,23,68,.2); }
.card-status-icon { font-size: 1.5rem; }
.card-status-title { font-family: 'Orbitron', sans-serif; font-size: .65rem; letter-spacing: 2px; }
.card-status-badge.owned   .card-status-title { color: #00ff80; }
.card-status-badge.missing .card-status-title { color: var(--red); }
.card-status-sub { font-size: .75rem; color: var(--muted); margin-top: 2px; }

.card-show-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

@media (max-width: 768px) {
    .card-show { grid-template-columns: 1fr; }
    .card-show-card { width: 100%; max-width: 280px; margin: 0 auto; }
}

/* Finition sur la grande carte */
.card-show-foil { position: absolute; inset: 0; z-index: 5; pointer-events: none; border-radius: inherit; background: linear-gradient(115deg, transparent 16%, rgba(255,0,128,.5) 30%, rgba(0,229,255,.5) 46%, rgba(255,229,0,.45) 60%, rgba(0,255,128,.45) 72%, transparent 88%); background-size: 300% 300%; mix-blend-mode: color-dodge; opacity: 0; animation: foilShift 6s linear infinite; }
/* Semi-holo : reflet uniquement sur l'illustration (haut ~200px) */
.card-show-card.finish-semiholo .card-show-foil {
    opacity: .42;
    -webkit-mask-image: linear-gradient(to bottom, #000 0, #000 200px, transparent 250px);
    mask-image: linear-gradient(to bottom, #000 0, #000 200px, transparent 250px);
}
/* Holo : reflet sur toute la carte */
.card-show-card.finish-holo .card-show-foil { opacity: .32; }
/* Full art : l'illustration remplit toute la carte, infos superposées + bordure animée */
.card-show-card.finish-fullart { aspect-ratio: 5 / 7; animation: faBorder 4s linear infinite; }
.card-show-card.finish-fullart .card-show-art { position: absolute; inset: 0; height: 100%; z-index: 1; }
.card-show-card.finish-fullart .card-show-art img { height: 100%; object-position: center; }
.card-show-card.finish-fullart .card-show-art-overlay { background: linear-gradient(to bottom, transparent 32%, rgba(7,8,13,.65) 72%, rgba(7,8,13,.96) 100%); }
.card-show-card.finish-fullart .card-show-info { position: absolute; left: 0; right: 0; bottom: 0; z-index: 3; border-top: none; background: linear-gradient(to top, rgba(7,8,13,.97) 50%, rgba(7,8,13,.55) 80%, transparent); }
.card-show-card.finish-fullart .card-show-foil { opacity: .28; }
@keyframes faBorder {
    0%   { box-shadow: 0 0 0 2px rgba(255,0,128,.7),  0 0 26px rgba(255,0,128,.3); }
    33%  { box-shadow: 0 0 0 2px rgba(0,229,255,.7),  0 0 26px rgba(0,229,255,.3); }
    66%  { box-shadow: 0 0 0 2px rgba(255,229,0,.7),  0 0 26px rgba(255,229,0,.3); }
    100% { box-shadow: 0 0 0 2px rgba(255,0,128,.7),  0 0 26px rgba(255,0,128,.3); }
}
@media (prefers-reduced-motion: reduce) {
    .card-show-foil, .card-show-card.finish-fullart { animation: none; }
}
.card-finish-box { display: flex; align-items: center; gap: .8rem; flex-wrap: wrap; padding: 1rem; border: 1px solid var(--border); background: rgba(255,255,255,.02); }
.card-finish-label { font-family: var(--font-head); font-size: .55rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); }
.card-finish-chooser { display: flex; gap: 6px; flex-wrap: wrap; }
.card-finish-chooser form { margin: 0; }
</style>

<?php
$rarityClass = 'rarity-' . $card['rarity'];
$rankLetters = ['legendary' => 'S', 'epic' => 'A', 'rare' => 'B', 'common' => 'C'];
$watermark   = $rankLetters[$card['rarity']] ?? 'C';
$cardId      = str_pad($card['id'], 3, '0', STR_PAD_LEFT);
$total       = $card['speed'] + $card['power'] + $card['handling'] + $card['armor'];

// Radar de stats (diamant 4 axes : haut=vitesse, droite=puissance, bas=maniabilité, gauche=blindage)
$radarColors = ['legendary' => '#ffb700', 'epic' => '#8b5cf6', 'rare' => '#3b82f6', 'common' => '#6b7280'];
$rc = $radarColors[$card['rarity']] ?? '#6b7280';
$cx = 100; $cy = 100; $R = 76;
$radarPts = [
    [$cx, $cy - $R * $card['speed'] / 100],
    [$cx + $R * $card['power'] / 100, $cy],
    [$cx, $cy + $R * $card['handling'] / 100],
    [$cx - $R * $card['armor'] / 100, $cy],
];
$radarPoly = implode(' ', array_map(fn($p) => round($p[0], 1) . ',' . round($p[1], 1), $radarPts));
?>

<div class="page-header">
    <a href="<?= BASE_URL ?>/cards" class="btn">← Encyclopédie</a>
    <span class="nav-coins">💰 <?= $user['coins'] ?> coins</span>
</div>

<div class="card-show">

    <!-- Carte TCG grande -->
    <div class="card-show-card <?= $rarityClass ?> <?= $ownedFinish ? 'finish-' . $ownedFinish : '' ?>">
        <div class="card-show-top"></div>
        <div class="card-show-foil"></div>
        <div class="card-show-art">
            <?php if (!empty($card['image'])): ?>
                <img src="<?= BASE_URL ?>/assets/img/cards/<?= htmlspecialchars($card['image']) ?>" alt="<?= htmlspecialchars($card['name']) ?>">
                <div class="card-show-art-overlay"></div>
            <?php else: ?>
                <div style="font-size:5rem;display:flex;align-items:center;justify-content:center;height:100%;animation:float 3s ease-in-out infinite;">🚗</div>
            <?php endif; ?>
            <div class="card-show-scan"></div>
            <div class="card-show-shine"></div>
            <div class="card-show-watermark"><?= $watermark ?></div>
        </div>
        <div class="card-show-info">
            <div class="card-show-header">
                <div class="card-show-dot"></div>
                <span class="card-show-name"><?= htmlspecialchars($card['name']) ?></span>
                <span class="card-show-id">#<?= $cardId ?></span>
            </div>
            <div class="card-show-type"><?= strtoupper($card['type']) ?></div>
            <div class="card-show-stats">
                <div class="card-show-stat"><span class="card-show-stat-icon">⚡</span><div class="card-show-stat-track"><div class="card-show-stat-fill" style="width:<?= $card['speed'] ?>%"></div></div><span class="card-show-stat-val"><?= $card['speed'] ?></span></div>
                <div class="card-show-stat"><span class="card-show-stat-icon">💪</span><div class="card-show-stat-track"><div class="card-show-stat-fill" style="width:<?= $card['power'] ?>%"></div></div><span class="card-show-stat-val"><?= $card['power'] ?></span></div>
                <div class="card-show-stat"><span class="card-show-stat-icon">🎯</span><div class="card-show-stat-track"><div class="card-show-stat-fill" style="width:<?= $card['handling'] ?>%"></div></div><span class="card-show-stat-val"><?= $card['handling'] ?></span></div>
                <div class="card-show-stat"><span class="card-show-stat-icon">🛡️</span><div class="card-show-stat-track"><div class="card-show-stat-fill" style="width:<?= $card['armor'] ?>%"></div></div><span class="card-show-stat-val"><?= $card['armor'] ?></span></div>
            </div>
        </div>
    </div>

    <!-- Infos détaillées -->
    <div class="card-show-right <?= $rarityClass ?>">

        <div class="card-show-title-area">
            <div class="card-show-title-head">
                <div class="card-show-rank"><?= $watermark ?></div>
                <div>
                    <p class="card-show-eyebrow">// <?= strtoupper($card['type']) ?> · <?= Card::rarityLabel($card['rarity']) ?></p>
                    <h1 class="card-show-title" style="margin-bottom:0;"><?= htmlspecialchars($card['name']) ?></h1>
                </div>
            </div>
            <?php if ($card['description']): ?>
                <p class="card-show-desc" style="margin-top:1rem;"><?= htmlspecialchars($card['description']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <h3 style="font-size:.65rem;letter-spacing:3px;color:var(--cyan);margin-bottom:1.5rem;">// STATISTIQUES</h3>
            <div class="card-stats-wrap">
                <div class="card-radar">
                    <svg viewBox="0 0 200 200" role="img" aria-label="Diagramme des statistiques">
                        <?php foreach ([0.25, 0.5, 0.75, 1] as $L):
                            $g = $cx . ',' . round($cy - $R * $L, 1) . ' ' . round($cx + $R * $L, 1) . ',' . $cy . ' ' . $cx . ',' . round($cy + $R * $L, 1) . ' ' . round($cx - $R * $L, 1) . ',' . $cy; ?>
                        <polygon points="<?= $g ?>" fill="none" stroke="rgba(255,255,255,<?= $L == 1 ? '0.14' : '0.06' ?>)" stroke-width="1"></polygon>
                        <?php endforeach; ?>
                        <line x1="<?= $cx ?>" y1="<?= $cy - $R ?>" x2="<?= $cx ?>" y2="<?= $cy + $R ?>" stroke="rgba(255,255,255,0.06)"></line>
                        <line x1="<?= $cx - $R ?>" y1="<?= $cy ?>" x2="<?= $cx + $R ?>" y2="<?= $cy ?>" stroke="rgba(255,255,255,0.06)"></line>
                        <polygon points="<?= $radarPoly ?>" fill="<?= $rc ?>" fill-opacity="0.2" stroke="<?= $rc ?>" stroke-width="2"></polygon>
                        <?php foreach ($radarPts as $p): ?>
                        <circle cx="<?= round($p[0], 1) ?>" cy="<?= round($p[1], 1) ?>" r="2.6" fill="<?= $rc ?>"></circle>
                        <?php endforeach; ?>
                        <text x="100" y="13" text-anchor="middle" font-size="12">⚡</text>
                        <text x="194" y="105" text-anchor="middle" font-size="12">💪</text>
                        <text x="100" y="198" text-anchor="middle" font-size="12">🎯</text>
                        <text x="6" y="105" text-anchor="middle" font-size="12">🛡️</text>
                    </svg>
                </div>
                <div class="card-stats-side">
                    <div class="card-stats-detail">
                        <div class="card-stat-detail">
                            <span class="card-stat-detail-label">⚡ Vitesse</span>
                            <div class="card-stat-detail-bar"><div class="card-stat-detail-fill" style="width:<?= $card['speed'] ?>%"></div></div>
                            <span class="card-stat-detail-val"><?= $card['speed'] ?></span>
                        </div>
                        <div class="card-stat-detail">
                            <span class="card-stat-detail-label">💪 Puissance</span>
                            <div class="card-stat-detail-bar"><div class="card-stat-detail-fill" style="width:<?= $card['power'] ?>%"></div></div>
                            <span class="card-stat-detail-val"><?= $card['power'] ?></span>
                        </div>
                        <div class="card-stat-detail">
                            <span class="card-stat-detail-label">🎯 Maniabilité</span>
                            <div class="card-stat-detail-bar"><div class="card-stat-detail-fill" style="width:<?= $card['handling'] ?>%"></div></div>
                            <span class="card-stat-detail-val"><?= $card['handling'] ?></span>
                        </div>
                        <div class="card-stat-detail">
                            <span class="card-stat-detail-label">🛡️ Blindage</span>
                            <div class="card-stat-detail-bar"><div class="card-stat-detail-fill" style="width:<?= $card['armor'] ?>%"></div></div>
                            <span class="card-stat-detail-val"><?= $card['armor'] ?></span>
                        </div>
                    </div>
                    <div class="card-total-score" style="margin-top:1rem;">
                        <span class="card-total-score-label">Score total</span>
                        <span class="card-total-score-val"><?= $total ?><span class="card-total-score-max"> /400</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-status-badge <?= $owned ? 'owned' : 'missing' ?>">
            <span class="card-status-icon"><?= $owned ? '✅' : '🔒' ?></span>
            <div>
                <p class="card-status-title"><?= $owned ? 'DANS VOTRE COLLECTION' : 'NON OBTENUE' ?></p>
                <p class="card-status-sub"><?= $owned ? 'Vous possédez cette carte' : 'Ouvrez des boosters pour l\'obtenir' ?></p>
            </div>
        </div>

        <?php if ($owned && $ownedFinish): ?>
        <div class="card-finish-box">
            <span class="card-finish-label">Finition</span>
            <span class="coll-fin-pill fp-<?= $ownedFinish ?> on" style="cursor:default"><?= ['classic' => 'Classique', 'semiholo' => 'Semi-holo', 'holo' => 'Holo', 'fullart' => 'Full Art'][$ownedFinish] ?? $ownedFinish ?></span>
            <?php if (count($finishesOwned) > 1): ?>
                <span class="card-finish-label" style="margin-left:auto;">Afficher</span>
                <div class="card-finish-chooser">
                    <?php foreach (['classic' => 'C', 'semiholo' => 'Semi', 'holo' => 'Holo', 'fullart' => 'Full'] as $f => $lbl): if (!in_array($f, $finishesOwned, true)) continue; ?>
                    <form action="<?= BASE_URL ?>/cards/<?= $card['id'] ?>/finish" method="POST">
                        <input type="hidden" name="finish" value="<?= $f ?>">
                        <button type="submit" class="coll-fin-pill fp-<?= $f ?> <?= $ownedFinish === $f ? 'on' : '' ?>"><?= $lbl ?></button>
                    </form>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card-show-actions">
            <a href="<?= BASE_URL ?>/boosters" class="btn btn-primary">Ouvrir des boosters</a>
            <a href="<?= BASE_URL ?>/cards" class="btn">← Retour</a>
        </div>

    </div>
</div>