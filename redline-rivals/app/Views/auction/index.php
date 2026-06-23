<style>
.auctions { max-width: 1280px; margin: 0 auto; }

.bid-form { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
.bid-form .form-group { margin: 0; }

/* Enchères = vraies cartes TCG + pied enchère */
.au-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(195px, 1fr)); gap: 1.5rem; }
.au-card { display: flex; flex-direction: column; gap: .5rem; transition: transform .2s; }
.au-card:hover { transform: translateY(-5px); }
.au-card .tcg-card { width: 100%; max-width: none; cursor: default; }
.au-card .tcg-card:hover { transform: none; }

.au-foot { background: var(--bg2); border: 1px solid var(--border); padding: .75rem .8rem; display: flex; flex-direction: column; gap: .5rem; }
.au-foot form { margin: 0; }
.au-bid-row { display: flex; align-items: baseline; justify-content: space-between; gap: .5rem; }
.au-bid-label { font-family: var(--font-head); font-size: .48rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); }
.au-bid { font-family: var(--font-mono); font-size: 1.05rem; color: var(--cyan); text-shadow: 0 0 12px rgba(0,229,255,.3); }
.au-bidder { font-family: var(--font-mono); font-size: .56rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.au-timer { display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); font-size: .64rem; color: var(--amber); letter-spacing: 1px; }
.au-timer::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: neonPulse 1.6s infinite; }
.au-timer.urgent { color: var(--red); }
.au-bid-form { display: flex; gap: .4rem; padding-top: .2rem; border-top: 1px solid rgba(255,255,255,.05); margin-top: .1rem; }
.au-bid-form input { flex: 1; min-width: 0; padding: 7px 10px; }
.au-bid-form .btn { flex-shrink: 0; font-size: .55rem; padding: 7px 12px; }
</style>

<div class="auctions">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Enchères · Le plus offrant l'emporte</p>
            <h1 class="hud-title">Salle des <span>enchères</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="coins-pill">💰 <?= number_format($user['coins']) ?> <small>COINS</small></span>
        </div>
    </div>

    <!-- Lancer une enchère -->
    <?php if (!empty($collection)): ?>
    <div class="panel" style="margin-bottom:2.5rem;">
        <p class="panel-title">// Lancer une enchère</p>
        <form action="<?= BASE_URL ?>/auctions/list" method="POST" class="bid-form">
            <div class="form-group" style="flex:2;min-width:200px;">
                <label>Carte</label>
                <select name="card_id">
                    <?php foreach ($collection as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= \Card::rarityLabel($c['rarity']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;min-width:120px;">
                <label>Mise de départ</label>
                <input type="number" name="start_price" min="1" placeholder="ex: 100" required>
            </div>
            <div class="form-group" style="width:110px;">
                <label>Durée</label>
                <select name="duration">
                    <option value="1">1h</option>
                    <option value="6">6h</option>
                    <option value="12">12h</option>
                    <option value="24" selected>24h</option>
                    <option value="48">48h</option>
                    <option value="72">72h</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lancer</button>
        </form>
    </div>
    <?php else: ?>
        <p style="color:var(--muted);margin-bottom:2.5rem;">Vous n'avez pas de cartes à mettre en enchère.</p>
    <?php endif; ?>

    <!-- Enchères en cours -->
    <p class="section-eyebrow">// Enchères en cours · <?= count($auctions) ?> actives</p>
    <?php if (empty($auctions)): ?>
        <div class="empty-state"><p>Aucune enchère en cours. Lance la première et fais monter les prix.</p></div>
    <?php else: ?>
    <div class="au-grid">
        <?php foreach ($auctions as $auction):
            $endsAt   = strtotime($auction['ends_at']);
            $diff     = max(0, $endsAt - time());
            $hours    = floor($diff / 3600);
            $minutes  = floor(($diff % 3600) / 60);
            $urgent   = $diff < 3600;
            $minBid   = $auction['current_bid'] + $auction['min_increment'];
            $tooPoor  = $user['coins'] < $minBid;
            $card = [
                'id'       => $auction['card_id'],
                'name'     => $auction['card_name'],
                'type'     => $auction['type'],
                'rarity'   => $auction['rarity'],
                'speed'    => $auction['speed'],
                'power'    => $auction['power'],
                'handling' => $auction['handling'],
                'armor'    => $auction['armor'],
                'image'    => $auction['image'],
            ];
        ?>
        <div class="au-card">
            <?php $showLink = false; $quantity = null; include ROOT . '/app/Views/partials/_card.php'; ?>
            <div class="au-foot">
                <div class="au-bid-row">
                    <span class="au-bid-label">Enchère actuelle</span>
                    <span class="au-bid">💰 <?= $auction['current_bid'] ?></span>
                </div>
                <?php if ($auction['bidder_name']): ?>
                    <span class="au-bidder">▲ <?= htmlspecialchars($auction['bidder_name']) ?></span>
                <?php else: ?>
                    <span class="au-bidder">Aucune offre — sois le premier</span>
                <?php endif; ?>
                <span class="au-timer <?= $urgent ? 'urgent' : '' ?>"><?= $hours ?>h <?= $minutes ?>min restantes</span>
                <form action="<?= BASE_URL ?>/auctions/bid" method="POST" class="au-bid-form">
                    <input type="hidden" name="auction_id" value="<?= $auction['id'] ?>">
                    <input type="number" name="amount" min="<?= $minBid ?>" value="<?= $minBid ?>">
                    <button type="submit" class="btn btn-accent" <?= $tooPoor ? 'disabled' : '' ?>>Enchérir</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
