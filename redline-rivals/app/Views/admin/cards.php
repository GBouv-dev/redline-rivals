<style>
.acard-list { display: flex; flex-direction: column; gap: .5rem; }
.acard { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; background: var(--bg2); border: 1px solid var(--border); border-left: 3px solid var(--rank-c); padding: .8rem 1.1rem; transition: transform .15s, border-color .2s; }
.acard:hover { transform: translateX(3px); border-color: var(--border-red); }
.acard.rarity-legendary { border-left-color: var(--rank-s); }
.acard.rarity-epic { border-left-color: var(--rank-a); }
.acard.rarity-rare { border-left-color: var(--rank-b); }
.acard.rarity-common { border-left-color: var(--rank-c); }
.acard-main { flex: 1; min-width: 170px; }
.acard-name { font-family: var(--font-head); font-size: .75rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); }
.acard-meta { font-family: var(--font-mono); font-size: .56rem; letter-spacing: 1px; color: var(--muted); margin-top: 3px; text-transform: uppercase; }
.acard-stats { font-family: var(--font-mono); font-size: .64rem; color: var(--muted); display: flex; gap: .8rem; flex-wrap: wrap; }
.acard-stats b { color: var(--cyan); font-weight: 400; }
.acard-actions { display: flex; gap: .5rem; }
.acard-actions form { margin: 0; }
.acard-actions .btn { font-size: .55rem; padding: 7px 12px; }
</style>

<div class="admin">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Admin · Catalogue</p>
            <h1 class="hud-title">Gestion des <span>cartes</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="admin-badge">🃏 <?= count($cards) ?> cartes</span>
            <a href="<?= BASE_URL ?>/admin" class="btn">← Admin</a>
        </div>
    </div>

    <div class="panel" style="margin-bottom:2.5rem;">
        <p class="panel-title">// Nouvelle carte</p>
        <?php
        $card = null;
        $action = BASE_URL . '/admin/cards/create';
        $submitLabel = '+ Créer la carte';
        include ROOT . '/app/Views/admin/_card_form.php';
        ?>
    </div>

    <p class="section-eyebrow">// Catalogue · <?= count($cards) ?> cartes</p>

    <?php if (empty($cards)): ?>
        <div class="empty-state"><p>Aucune carte dans le catalogue. Crée la première ci-dessus.</p></div>
    <?php else: ?>
    <div class="acard-list">
        <?php
        $ranks = ['legendary' => 'S', 'epic' => 'A', 'rare' => 'B', 'common' => 'C'];
        foreach ($cards as $card):
        ?>
        <div class="acard rarity-<?= $card['rarity'] ?>">
            <div class="acard-main">
                <div class="acard-name"><?= htmlspecialchars($card['name']) ?></div>
                <div class="acard-meta">#<?= str_pad($card['id'], 3, '0', STR_PAD_LEFT) ?> · <?= strtoupper($card['type']) ?> · Classe <?= $ranks[$card['rarity']] ?? 'C' ?></div>
            </div>
            <div class="acard-stats">
                <span>⚡<b><?= $card['speed'] ?></b></span>
                <span>💪<b><?= $card['power'] ?></b></span>
                <span>🎯<b><?= $card['handling'] ?></b></span>
                <span>🛡️<b><?= $card['armor'] ?></b></span>
            </div>
            <div class="acard-actions">
                <a href="<?= BASE_URL ?>/admin/cards/<?= $card['id'] ?>/edit" class="btn btn-accent">Modifier</a>
                <form action="<?= BASE_URL ?>/admin/cards/<?= $card['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer cette carte ? Elle sera retirée de toutes les collections, decks, annonces et enchères.');">
                    <button type="submit" class="btn btn-primary">Suppr.</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
