<?php
/**
 * Composant carte TCG réutilisable — Cyberpunk Racing
 * Variables requises : $card (id, name, type, rarity, speed, power, handling, armor, image)
 * Variables optionnelles : $quantity (int), $showLink (bool, défaut true)
 */
$showLink = $showLink ?? true;
$quantity = $quantity ?? null;
$linkUrl = BASE_URL . '/cards/' . $card['id'];

// Rang de performance — remplace l'ancien système de kanji
$ranks = ['legendary' => 'S', 'epic' => 'A', 'rare' => 'B', 'common' => 'C'];
$icons = ['hypercar' => '👑', 'tuner' => '⚡', 'sport' => '🏎️', 'muscle' => '💥', 'truck' => '🛻'];
$rank = $ranks[$card['rarity']] ?? 'C';
$icon = $icons[$card['type']] ?? '🚗';
$finish = $card['finish'] ?? 'classic';

$tag = $showLink ? 'a' : 'div';
$href = $showLink ? "href=\"{$linkUrl}\"" : '';
?>
<<?= $tag ?> <?= $href ?> class="tcg-card rarity-<?= $card['rarity'] ?> type-<?= $card['type'] ?> finish-<?= $finish ?>" data-rarity="<?= $card['rarity'] ?>">
    <div class="tcg-top-bar"></div>
    <div class="tcg-art">
        <div class="tcg-art-grid"></div>
        <?php if (!empty($card['image'])): ?>
            <img src="<?= BASE_URL ?>/assets/img/cards/<?= htmlspecialchars($card['image']) ?>"
                alt="<?= htmlspecialchars($card['name']) ?>" class="tcg-car-img">
            <div class="tcg-art-overlay"></div>
        <?php else: ?>
            <span class="tcg-car-icon"><?= $icon ?></span>
        <?php endif; ?>
        <div class="tcg-scan"></div>
        <div class="tcg-shine"></div>
        <div class="tcg-watermark"><?= $rank ?></div>
        <?php if ($quantity && $quantity > 1): ?>
            <div class="tcg-qty-badge">×<?= $quantity ?></div>
        <?php endif; ?>
    </div>
    <div class="tcg-info">
        <div class="tcg-header">
            <span class="tcg-rarity-dot"></span>
            <span class="tcg-name"><?= htmlspecialchars($card['name']) ?></span>
            <span class="tcg-rank-badge"><?= $rank ?></span>
        </div>
        <div class="tcg-type-label"><?= strtoupper($card['type']) ?></div>
        <div class="tcg-stats">
            <div class="tcg-stat-row">
                <span class="tcg-stat-icon">⚡</span>
                <div class="tcg-stat-track">
                    <div class="tcg-stat-fill" style="width:<?= $card['speed'] ?>%"></div>
                </div>
                <span class="tcg-stat-num"><?= $card['speed'] ?></span>
            </div>
            <div class="tcg-stat-row">
                <span class="tcg-stat-icon">💪</span>
                <div class="tcg-stat-track">
                    <div class="tcg-stat-fill" style="width:<?= $card['power'] ?>%"></div>
                </div>
                <span class="tcg-stat-num"><?= $card['power'] ?></span>
            </div>
            <div class="tcg-stat-row">
                <span class="tcg-stat-icon">🎯</span>
                <div class="tcg-stat-track">
                    <div class="tcg-stat-fill" style="width:<?= $card['handling'] ?>%"></div>
                </div>
                <span class="tcg-stat-num"><?= $card['handling'] ?></span>
            </div>
            <div class="tcg-stat-row">
                <span class="tcg-stat-icon">🛡️</span>
                <div class="tcg-stat-track">
                    <div class="tcg-stat-fill" style="width:<?= $card['armor'] ?>%"></div>
                </div>
                <span class="tcg-stat-num"><?= $card['armor'] ?></span>
            </div>
        </div>
        <div class="tcg-bottom-accent"></div>
    </div>
    <div class="tcg-foil"></div>
    <?php if ($finish !== 'classic'): ?>
        <div class="tcg-finish-badge fb-<?= $finish ?>"><?= ['semiholo' => 'Semi', 'holo' => 'Holo', 'fullart' => 'Full Art'][$finish] ?? '' ?></div>
    <?php endif; ?>
</<?= $tag ?>>