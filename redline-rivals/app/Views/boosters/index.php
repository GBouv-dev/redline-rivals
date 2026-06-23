<style>
.shop { max-width: 1280px; margin: 0 auto; }

/* ── Grille de packs ── */
.pack-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 0 0 3.5rem;
}

.pack {
    position: relative;
    background: var(--bg2);
    border: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform .25s, border-color .25s, box-shadow .25s;
}
.pack:hover { transform: translateY(-6px); }

.pack-bar { height: 3px; width: 100%; }
.pack-standard .pack-bar { background: var(--cyan); }
.pack-pro .pack-bar      { background: linear-gradient(90deg, var(--rank-a), #c4a8fb, var(--rank-a)); }
.pack-elite .pack-bar    { background: linear-gradient(90deg, var(--rank-s), #fff3c4, var(--rank-s)); }

.pack-standard:hover { border-color: rgba(0,229,255,.4);  box-shadow: 0 10px 30px rgba(0,229,255,.08); }
.pack-pro:hover      { border-color: rgba(139,92,246,.4); box-shadow: 0 10px 30px rgba(139,92,246,.12); }
.pack-elite:hover    { border-color: rgba(255,183,0,.45); box-shadow: 0 10px 35px rgba(255,183,0,.15); }

/* Visuel du pack */
.pack-visual {
    position: relative;
    height: 150px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.pack-standard .pack-visual { background: radial-gradient(circle at 50% 120%, rgba(0,229,255,.12), transparent 60%), linear-gradient(135deg, #001520, #0c0f18); }
.pack-pro .pack-visual      { background: radial-gradient(circle at 50% 120%, rgba(139,92,246,.15), transparent 60%), linear-gradient(135deg, #14001f, #0c0f18); }
.pack-elite .pack-visual    { background: radial-gradient(circle at 50% 120%, rgba(255,183,0,.15), transparent 60%), linear-gradient(135deg, #1a1000, #0c0f18); }

.pack-grid-bg {
    position: absolute; inset: 0;
    background: linear-gradient(rgba(0,229,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0,229,255,.04) 1px, transparent 1px);
    background-size: 22px 22px;
}
.pack-shine {
    position: absolute; inset: 0;
    background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,.07) 50%, transparent 65%);
    animation: csShine 5s ease-in-out infinite;
    pointer-events: none;
}
.pack-icon {
    position: relative; z-index: 2;
    font-size: 3.4rem;
    filter: drop-shadow(0 0 14px rgba(0,229,255,.4));
}
.pack-pro .pack-icon   { filter: drop-shadow(0 0 14px rgba(139,92,246,.5)); }
.pack-elite .pack-icon { filter: drop-shadow(0 0 16px rgba(255,183,0,.6)); animation: float 3s ease-in-out infinite; }

.pack-tier {
    position: absolute; top: 10px; left: 10px; z-index: 3;
    font-family: var(--font-head); font-weight: 900;
    font-size: .5rem; letter-spacing: 3px; text-transform: uppercase;
    padding: 3px 8px;
}
.pack-standard .pack-tier { color: var(--cyan);   border: 1px solid rgba(0,229,255,.4); }
.pack-pro .pack-tier      { color: var(--rank-a); border: 1px solid rgba(139,92,246,.5); }
.pack-elite .pack-tier    { color: var(--rank-s); border: 1px solid rgba(255,183,0,.5); text-shadow: 0 0 8px rgba(255,183,0,.5); }

.pack-watermark {
    position: absolute; bottom: -14px; right: 4px; z-index: 1;
    font-family: var(--font-head); font-weight: 900; font-size: 3.4rem;
    color: #fff; opacity: .05; pointer-events: none; letter-spacing: -2px;
}

/* Corps */
.pack-body { padding: 1.2rem 1.3rem 1.3rem; display: flex; flex-direction: column; flex: 1; }
.pack-name { font-family: var(--font-head); font-size: .82rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); margin-bottom: .5rem; }
.pack-desc { font-size: .8rem; color: var(--muted); line-height: 1.6; margin-bottom: 1.1rem; flex: 1; }

.pack-stats { display: flex; gap: .5rem; margin-bottom: 1.1rem; }
.pack-stat { flex: 1; text-align: center; padding: .55rem; background: rgba(255,255,255,.02); border: 1px solid rgba(255,255,255,.04); }
.pack-stat .n { display: block; font-family: var(--font-head); font-weight: 900; font-size: 1.05rem; color: var(--cyan); line-height: 1; }
.pack-stat .l { display: block; font-family: var(--font-head); font-size: .45rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: 4px; }

.pack-foot { display: flex; align-items: center; justify-content: space-between; gap: .8rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,.05); }
.pack-foot form { margin: 0; }
.pack-price { font-family: var(--font-mono); font-size: 1.05rem; color: var(--amber); white-space: nowrap; }

/* ── Inventaire (packs possédés) ── */
.owned-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px,1fr)); gap: 1rem; margin-top: 1.5rem; }
.owned-pack { display: flex; align-items: center; gap: 1rem; background: var(--bg2); border: 1px solid var(--border); padding: 1rem 1.2rem; transition: border-color .2s, transform .2s; }
.owned-pack:hover { border-color: rgba(255,183,0,.3); transform: translateX(3px); }
.owned-pack form { margin: 0; }
.owned-icon { font-size: 2.2rem; filter: drop-shadow(0 0 10px rgba(255,183,0,.4)); flex-shrink: 0; }
.owned-info { flex: 1; min-width: 0; }
.owned-name { display: block; font-family: var(--font-head); font-size: .7rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.owned-qty { display: block; font-family: var(--font-mono); font-size: .65rem; color: var(--amber); margin-top: 4px; }
</style>

<div class="shop">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Boutique · Ravitaillement</p>
            <h1 class="hud-title">Packs de <span>boosters</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="coins-pill">💰 <?= number_format($user['coins']) ?> <small>COINS</small></span>
        </div>
    </div>

    <div class="pack-grid">
        <?php foreach ($boosters as $booster):
            $tier = $booster['price'] >= 350 ? 'elite' : ($booster['price'] >= 150 ? 'pro' : 'standard');
            $tierLabel = ['standard' => 'Standard', 'pro' => 'Pro', 'elite' => 'Elite'][$tier];
            $tooPoor = $user['coins'] < $booster['price'];
        ?>
        <div class="pack pack-<?= $tier ?>">
            <div class="pack-bar"></div>
            <div class="pack-visual">
                <div class="pack-grid-bg"></div>
                <div class="pack-shine"></div>
                <span class="pack-tier"><?= $tierLabel ?></span>
                <span class="pack-icon">📦</span>
                <span class="pack-watermark">RR</span>
            </div>
            <div class="pack-body">
                <h3 class="pack-name"><?= htmlspecialchars($booster['name']) ?></h3>
                <p class="pack-desc"><?= htmlspecialchars($booster['description']) ?></p>
                <div class="pack-stats">
                    <div class="pack-stat"><span class="n"><?= $booster['card_count'] ?></span><span class="l">Cartes</span></div>
                    <div class="pack-stat"><span class="n"><?= $booster['pool_size'] ?></span><span class="l">Pool</span></div>
                </div>
                <div class="pack-foot">
                    <span class="pack-price">💰 <?= $booster['price'] ?></span>
                    <form action="<?= BASE_URL ?>/boosters/buy" method="POST">
                        <input type="hidden" name="booster_id" value="<?= $booster['id'] ?>">
                        <button type="submit" class="btn btn-primary" <?= $tooPoor ? 'disabled' : '' ?>>
                            <?= $tooPoor ? 'Coins insuffisants' : 'Acheter' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($inventory)): ?>
    <p class="section-eyebrow">// Mon inventaire · prêt à ouvrir</p>
    <div class="owned-grid">
        <?php foreach ($inventory as $item): ?>
        <div class="owned-pack">
            <span class="owned-icon">📦</span>
            <div class="owned-info">
                <span class="owned-name"><?= htmlspecialchars($item['name']) ?></span>
                <span class="owned-qty">×<?= $item['quantity'] ?> en stock</span>
            </div>
            <form action="<?= BASE_URL ?>/boosters/open" method="POST">
                <input type="hidden" name="booster_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn-secondary">Ouvrir →</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
