<style>
.market { max-width: 1280px; margin: 0 auto; }

/* Formulaire de vente */
.sell-form { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
.sell-form .form-group { margin: 0; }

/* Mes annonces (compact) */
.mine-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(245px, 1fr)); gap: 1rem; }
.mine-item { display: flex; align-items: center; gap: .8rem; background: var(--bg2); border: 1px solid var(--border); border-left: 3px solid var(--rank-c); padding: .7rem .9rem; transition: transform .2s; }
.mine-item:hover { transform: translateX(3px); }
.mine-item form { margin: 0; }
.mine-thumb { width: 44px; height: 44px; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border); }
.mine-thumb-ph { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background: var(--bg3); border: 1px solid var(--border); flex-shrink: 0; }
.mine-info { flex: 1; min-width: 0; }
.mine-name { display: block; font-family: var(--font-head); font-size: .62rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mine-price { display: block; font-family: var(--font-mono); font-size: .72rem; color: var(--amber); margin-top: 3px; }

/* Annonces publiques = vraies cartes TCG + pied d'action */
.mk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(185px, 1fr)); gap: 1.5rem; }
.mk-card { display: flex; flex-direction: column; gap: .5rem; transition: transform .2s; }
.mk-card:hover { transform: translateY(-5px); }
.mk-card .tcg-card { width: 100%; max-width: none; cursor: default; }
.mk-card .tcg-card:hover { transform: none; }
.mk-foot { background: var(--bg2); border: 1px solid var(--border); padding: .7rem .8rem; display: flex; flex-direction: column; gap: .55rem; }
.mk-foot form { margin: 0; }
.mk-foot .btn { width: 100%; text-align: center; }
.mk-seller { font-family: var(--font-mono); font-size: .6rem; color: var(--muted); letter-spacing: .5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mk-price { font-family: var(--font-mono); font-size: 1rem; color: var(--amber); }
.mk-price small { color: var(--muted); font-size: .6rem; }
</style>

<div class="market">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Marché · Échange de cartes</p>
            <h1 class="hud-title">Le <span>marché noir</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="coins-pill">💰 <?= number_format($user['coins']) ?> <small>COINS</small></span>
        </div>
    </div>

    <!-- Mes annonces -->
    <?php
    $activeListings = array_filter($myListings, fn($i) => $i['status'] === 'active');
    if (!empty($activeListings)): ?>
    <p class="section-eyebrow">// Mes annonces en vente</p>
    <div class="mine-grid" style="margin-bottom:2.5rem;">
        <?php foreach ($activeListings as $item): ?>
        <div class="mine-item" style="border-left-color:<?= \Card::rarityColor($item['rarity']) ?>;">
            <?php if (!empty($item['image'])): ?>
                <img class="mine-thumb" src="<?= BASE_URL ?>/assets/img/cards/<?= htmlspecialchars($item['image']) ?>" alt="">
            <?php else: ?>
                <span class="mine-thumb-ph">🚗</span>
            <?php endif; ?>
            <div class="mine-info">
                <span class="mine-name"><?= htmlspecialchars($item['card_name']) ?></span>
                <span class="mine-price">💰 <?= $item['price'] ?> × <?= $item['quantity'] ?></span>
            </div>
            <form action="<?= BASE_URL ?>/market/cancel" method="POST">
                <input type="hidden" name="listing_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn" style="font-size:.55rem;padding:7px 12px;">Annuler</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Mettre en vente -->
    <?php if (!empty($collection)): ?>
    <div class="panel" style="margin-bottom:2.5rem;">
        <p class="panel-title">// Mettre une carte en vente</p>
        <form action="<?= BASE_URL ?>/market/list" method="POST" class="sell-form">
            <div class="form-group" style="flex:2;min-width:200px;">
                <label>Carte</label>
                <select name="card_id">
                    <?php foreach ($collection as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (x<?= $c['quantity'] ?>) — <?= \Card::rarityLabel($c['rarity']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;min-width:110px;">
                <label>Prix (coins)</label>
                <input type="number" name="price" min="1" placeholder="ex: 150" required>
            </div>
            <div class="form-group" style="width:90px;">
                <label>Qté</label>
                <input type="number" name="quantity" min="1" value="1">
            </div>
            <button type="submit" class="btn btn-primary">Mettre en vente</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Annonces disponibles -->
    <p class="section-eyebrow">// Cartes disponibles · <?= count($listings) ?> en vente</p>
    <?php if (empty($listings)): ?>
        <div class="empty-state"><p>Aucune carte en vente pour le moment. Reviens plus tard ou mets une de tes cartes en vente.</p></div>
    <?php else: ?>
    <div class="mk-grid">
        <?php foreach ($listings as $item):
            $card = [
                'id'       => $item['card_id'],
                'name'     => $item['card_name'],
                'type'     => $item['type'],
                'rarity'   => $item['rarity'],
                'speed'    => $item['speed'],
                'power'    => $item['power'],
                'handling' => $item['handling'],
                'armor'    => $item['armor'],
                'image'    => $item['image'],
            ];
            $tooPoor = $user['coins'] < $item['price'];
        ?>
        <div class="mk-card">
            <?php $showLink = false; $quantity = $item['quantity']; include ROOT . '/app/Views/partials/_card.php'; ?>
            <div class="mk-foot">
                <span class="mk-seller">⚑ <?= htmlspecialchars($item['seller_name']) ?></span>
                <span class="mk-price">💰 <?= $item['price'] ?> <small>coins</small></span>
                <form action="<?= BASE_URL ?>/market/buy" method="POST">
                    <input type="hidden" name="listing_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn-accent" <?= $tooPoor ? 'disabled' : '' ?>>
                        <?= $tooPoor ? 'Coins insuffisants' : 'Acheter' ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
