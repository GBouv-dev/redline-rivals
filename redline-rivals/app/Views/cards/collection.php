<style>
.coll-stats { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; background: var(--bg2); border: 1px solid var(--border); padding: 1rem 1.4rem; margin-bottom: 1.5rem; }
.coll-stat { text-align: center; }
.coll-stat-num { font-family: var(--font-head); font-weight: 900; font-size: 1.4rem; color: var(--cyan); line-height: 1; }
.coll-stat-label { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .35rem; }
.coll-divider { width: 1px; height: 36px; background: var(--border); }
.coll-rarity { flex: 1; min-width: 200px; }
.coll-rarity-bar { display: flex; height: 8px; gap: 2px; background: rgba(255,255,255,.03); overflow: hidden; }
.coll-rarity-seg { height: 100%; min-width: 4px; }
.coll-rarity-legend { display: flex; gap: 1rem; margin-top: .5rem; font-family: var(--font-head); font-size: .52rem; letter-spacing: 1px; color: var(--muted); }
.coll-rarity-legend b { font-weight: 900; font-size: .66rem; margin-right: 2px; }

.coll-card { display: flex; flex-direction: column; gap: .4rem; }
.coll-finishes { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; }
.coll-finishes form { margin: 0; }
.coll-fin-pill { font-family: var(--font-head); font-weight: 700; font-size: .5rem; letter-spacing: 1px; text-transform: uppercase; padding: 3px 7px; cursor: pointer; border: 1px solid var(--border); background: var(--bg2); color: var(--muted); transition: all .15s; clip-path: polygon(4px 0%, 100% 0%, calc(100% - 4px) 100%, 0% 100%); }
.coll-fin-pill:hover { color: var(--text-bright); border-color: var(--cyan); }
.coll-fin-pill.on { color: #07080d; }
.coll-fin-pill.fp-classic.on { background: #cfd6e0; border-color: #cfd6e0; }
.coll-fin-pill.fp-semiholo.on { background: linear-gradient(90deg, #cbd5e1, #9fd0ff); border-color: #9fd0ff; }
.coll-fin-pill.fp-holo.on { background: linear-gradient(90deg, #ff5fa2, #00e5ff, #ffe14d); border-color: #00e5ff; }
.coll-fin-pill.fp-fullart.on { background: linear-gradient(90deg, #ffb700, #fff3c4); border-color: #ffb700; }
</style>

<div class="hud-header">
    <div>
        <p class="hud-eyebrow">// Collection · Ton garage</p>
        <h1 class="hud-title">Mon <span>garage</span></h1>
    </div>
    <div class="hud-header-right">
        <span class="collection-count"><?= count($cards) ?> carte<?= count($cards) > 1 ? 's' : '' ?></span>
    </div>
</div>

<?php if (empty($cards)): ?>
    <div class="empty-state">
        <p>Votre collection est vide. Ouvrez des boosters pour obtenir des cartes !</p>
        <a href="<?= BASE_URL ?>/boosters" class="btn btn-primary">Voir les boosters</a>
    </div>
<?php else:
    $rb = ['legendary' => 0, 'epic' => 0, 'rare' => 0, 'common' => 0]; $totalQty = 0;
    foreach ($cards as $c) { $rb[$c['rarity']] = ($rb[$c['rarity']] ?? 0) + 1; $totalQty += (int)$c['quantity']; }
    $rmap = ['legendary' => ['S', '--rank-s'], 'epic' => ['A', '--rank-a'], 'rare' => ['B', '--rank-b'], 'common' => ['C', '--rank-c']];
?>
    <div class="coll-stats">
        <div class="coll-stat"><div class="coll-stat-num"><?= count($cards) ?></div><div class="coll-stat-label">Uniques</div></div>
        <div class="coll-divider"></div>
        <div class="coll-stat"><div class="coll-stat-num"><?= $totalQty ?></div><div class="coll-stat-label">Total</div></div>
        <div class="coll-divider"></div>
        <div class="coll-rarity">
            <div class="coll-rarity-bar">
                <?php foreach ($rmap as $rar => $info): if ($rb[$rar] <= 0) continue; ?>
                    <span class="coll-rarity-seg" style="flex:<?= $rb[$rar] ?>;background:var(<?= $info[1] ?>);" title="<?= $info[0] ?> · <?= $rb[$rar] ?>"></span>
                <?php endforeach; ?>
            </div>
            <div class="coll-rarity-legend">
                <?php foreach ($rmap as $rar => $info): ?>
                    <span style="color:var(<?= $info[1] ?>);"><b><?= $info[0] ?></b><?= $rb[$rar] ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="card-filter">
        <button class="filter-chip active" data-rarity="all">Toutes</button>
        <button class="filter-chip" data-rarity="legendary">S · Légendaire</button>
        <button class="filter-chip" data-rarity="epic">A · Épique</button>
        <button class="filter-chip" data-rarity="rare">B · Rare</button>
        <button class="filter-chip" data-rarity="common">C · Commun</button>
    </div>
    <?php $finLabels = ['classic' => 'C', 'semiholo' => 'Semi', 'holo' => 'Holo', 'fullart' => 'Full']; ?>
    <div class="tcg-grid">
        <?php foreach ($cards as $card):
            $owned = ($card['finishes_owned'] ?? '') !== '' ? explode(',', $card['finishes_owned']) : [];
        ?>
        <div class="coll-card" data-rarity="<?= $card['rarity'] ?>">
            <?php $showLink = true; $quantity = $card['quantity']; include ROOT . '/app/Views/partials/_card.php'; ?>
            <?php if (count($owned) > 1): ?>
            <div class="coll-finishes" title="Finitions obtenues — clique pour choisir l'affichée">
                <?php foreach ($finLabels as $f => $lbl): if (!in_array($f, $owned, true)) continue; ?>
                <form action="<?= BASE_URL ?>/cards/<?= $card['id'] ?>/finish" method="POST">
                    <input type="hidden" name="finish" value="<?= $f ?>">
                    <button type="submit" class="coll-fin-pill fp-<?= $f ?> <?= ($card['finish'] ?? 'classic') === $f ? 'on' : '' ?>"><?= $lbl ?></button>
                </form>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
    (function () {
        var chips = document.querySelectorAll('.card-filter .filter-chip');
        var cards = document.querySelectorAll('.tcg-grid .coll-card');
        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                chips.forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                var r = chip.getAttribute('data-rarity');
                cards.forEach(function (card) {
                    card.style.display = (r === 'all' || card.getAttribute('data-rarity') === r) ? '' : 'none';
                });
            });
        });
    })();
    </script>
<?php endif; ?>