<?php
$rankOrder   = ['common' => 0, 'rare' => 1, 'epic' => 2, 'legendary' => 3];
$rankLetters = ['common' => 'C', 'rare' => 'B', 'epic' => 'A', 'legendary' => 'S'];
$rarityVar   = ['legendary' => '--rank-s', 'epic' => '--rank-a', 'rare' => '--rank-b', 'common' => '--rank-c'];
$counts      = ['legendary' => 0, 'epic' => 0, 'rare' => 0, 'common' => 0];
$bestIdx = 0; $bestRank = -1;
foreach ($drawnCards as $i => $c) {
    $counts[$c['rarity']]++;
    if (($rankOrder[$c['rarity']] ?? 0) > $bestRank) { $bestRank = $rankOrder[$c['rarity']] ?? 0; $bestIdx = $i; }
}
$bestRarity = $drawnCards[$bestIdx]['rarity'] ?? 'common';
?>
<style>
.open-page { max-width: 1000px; margin: 0 auto; text-align: center; }
.open-header { margin-bottom: 1.5rem; }
.open-header .eyebrow { font-family: var(--font-head); font-size: .6rem; letter-spacing: 6px; color: rgba(0,229,255,.5); text-transform: uppercase; margin-bottom: 1rem; display: block; }
.open-header h1 { font-family: var(--font-head); font-size: clamp(1.3rem,3.5vw,2rem); color: var(--amber); text-shadow: 0 0 20px rgba(255,183,0,.35); letter-spacing: 3px; }
.open-subtitle { font-size: .8rem; color: var(--muted); letter-spacing: 2px; margin-top: .6rem; display: block; }

.open-legend { display: flex; gap: .6rem; justify-content: center; flex-wrap: wrap; margin-top: 1.2rem; }
.legend-chip { display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-head); font-size: .55rem; letter-spacing: 1px; text-transform: uppercase; padding: 5px 11px; border: 1px solid; }
.legend-chip .L { font-weight: 900; font-size: .7rem; }

.open-divider { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, rgba(0,229,255,.15), transparent); margin: 2rem 0; }
.open-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1.5rem; }

.drawn-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(155px, 185px)); gap: 1.8rem; justify-content: center; margin: 2.5rem 0; }
.drawn-item {
    position: relative;
    opacity: 0; transform: rotateY(90deg) scale(.8);
    animation: cardFlipIn .55s cubic-bezier(.175,.885,.32,1.275) forwards;
}
.drawn-item .tcg-card { width: 100%; cursor: default; transition: transform .25s; }
.drawn-item .tcg-card:hover { transform: translateY(-6px) scale(1.03); }
.drawn-item.rarity-legendary { filter: drop-shadow(0 0 18px rgba(255,183,0,.45)); }
.drawn-item.rarity-epic      { filter: drop-shadow(0 0 14px rgba(139,92,246,.4)); }

.drawn-item:nth-child(1) { animation-delay: .10s; }
.drawn-item:nth-child(2) { animation-delay: .25s; }
.drawn-item:nth-child(3) { animation-delay: .40s; }
.drawn-item:nth-child(4) { animation-delay: .55s; }
.drawn-item:nth-child(5) { animation-delay: .70s; }
.drawn-item:nth-child(6) { animation-delay: .85s; }
.drawn-item:nth-child(7) { animation-delay: 1.00s; }
.drawn-item:nth-child(8) { animation-delay: 1.15s; }

.best-ribbon {
    position: absolute; top: -11px; left: 50%; transform: translateX(-50%); z-index: 10;
    font-family: var(--font-head); font-weight: 900; font-size: .5rem; letter-spacing: 2px;
    color: #07080d; background: linear-gradient(90deg, var(--rank-s), #fff3c4);
    padding: 3px 12px; box-shadow: 0 0 14px rgba(255,183,0,.55);
    clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%);
}
@keyframes cardFlipIn {
    from { opacity: 0; transform: rotateY(90deg) scale(.8); }
    to   { opacity: 1; transform: rotateY(0deg) scale(1); }
}
</style>

<div class="open-page">
    <div class="open-header">
        <span class="eyebrow">// Ouverture du pack</span>
        <h1>Cartes obtenues</h1>
        <span class="open-subtitle"><?= count($drawnCards) ?> cartes ajoutées à ta collection</span>
        <div class="open-legend">
            <?php foreach ($counts as $rar => $n): if (!$n) continue; $v = $rarityVar[$rar]; ?>
            <span class="legend-chip" style="color:var(<?= $v ?>);border-color:var(<?= $v ?>);">
                <span class="L"><?= $rankLetters[$rar] ?></span> ×<?= $n ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="drawn-cards">
        <?php foreach ($drawnCards as $i => $card):
            $showLink = false; $quantity = null;
            $isBest = ($i === $bestIdx) && in_array($bestRarity, ['epic', 'legendary']);
        ?>
        <div class="drawn-item rarity-<?= $card['rarity'] ?>">
            <?php if ($isBest): ?><span class="best-ribbon">★ Top pull</span><?php endif; ?>
            <?php include ROOT . '/app/Views/partials/_card.php'; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="open-divider"></div>
    <div class="open-actions">
        <a href="<?= BASE_URL ?>/boosters" class="btn btn-primary">Ouvrir un autre booster</a>
        <a href="<?= BASE_URL ?>/cards/collection" class="btn btn-accent">Voir ma collection</a>
    </div>
</div>
