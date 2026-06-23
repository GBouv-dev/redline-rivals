<?php
$rmap = ['legendary' => ['S', '--rank-s'], 'epic' => ['A', '--rank-a'], 'rare' => ['B', '--rank-b'], 'common' => ['C', '--rank-c']];
$avgCoins = $totalUsers > 0 ? round($totalCoins / $totalUsers) : 0;
?>
<style>
.admin { max-width: 1280px; margin: 0 auto; }

.admin-badge { font-family: var(--font-head); font-weight: 900; font-size: .55rem; letter-spacing: 3px; text-transform: uppercase; color: var(--amber); border: 1px solid rgba(255,183,0,.4); background: rgba(255,183,0,.06); padding: 7px 13px; clip-path: polygon(6px 0%, 100% 0%, calc(100% - 6px) 100%, 0% 100%); }

/* KPIs */
.admin-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); margin-bottom: 2rem; }
.admin-kpi { background: var(--bg2); padding: 1.4rem 1.2rem; position: relative; overflow: hidden; transition: background .2s, transform .2s; }
.admin-kpi:hover { background: var(--bg3); transform: translateY(-3px); }
.admin-kpi::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: var(--c, var(--cyan)); opacity: .55; }
.admin-kpi-icon { font-size: 1.2rem; margin-bottom: .5rem; display: block; }
.admin-kpi-num { font-family: var(--font-head); font-weight: 900; font-size: 1.9rem; line-height: 1; color: var(--text-bright); }
.admin-kpi-label { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .45rem; }
.admin-kpi-sub { font-family: var(--font-mono); font-size: .55rem; color: var(--cyan); margin-top: .3rem; }

/* Panneaux */
.admin-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }

.eco-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.eco-stat { background: rgba(255,255,255,.02); border: 1px solid rgba(255,255,255,.04); padding: 1rem; }
.eco-stat-num { font-family: var(--font-head); font-weight: 900; font-size: 1.3rem; line-height: 1; color: var(--amber); }
.eco-stat-label { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .4rem; }

.adm-rarity + .adm-rarity { margin-top: 1.3rem; }
.adm-rarity-head { font-family: var(--font-head); font-size: .52rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-bottom: .5rem; }
.adm-rarity-bar { display: flex; height: 10px; gap: 2px; background: rgba(255,255,255,.03); overflow: hidden; }
.adm-rarity-seg { height: 100%; min-width: 4px; }
.adm-rarity-legend { display: flex; gap: 1rem; margin-top: .5rem; font-family: var(--font-head); font-size: .52rem; letter-spacing: 1px; color: var(--muted); }
.adm-rarity-legend b { font-weight: 900; font-size: .66rem; margin-right: 2px; }

/* Tables */
.admin-tables { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 1.5rem; }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); text-align: left; padding: 0 0 .7rem; border-bottom: 1px solid var(--border); }
.admin-table td { font-family: var(--font-mono); font-size: .7rem; color: var(--text); padding: .55rem .4rem .55rem 0; border-bottom: 1px solid rgba(255,255,255,.03); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
.admin-table tr:last-child td { border-bottom: none; }
.admin-table .num { text-align: right; color: var(--amber); padding-right: 0; }
.admin-table .rk { color: var(--muted); width: 16px; }
.admin-empty { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); padding: 1rem 0; }
.role-badge { font-family: var(--font-head); font-size: .45rem; letter-spacing: 1px; text-transform: uppercase; padding: 2px 6px; border: 1px solid; }
.role-badge.admin { color: var(--amber); border-color: rgba(255,183,0,.4); }
.role-badge.user { color: var(--muted); border-color: var(--border); }

@media (max-width: 900px) {
    .admin-kpis { grid-template-columns: repeat(2, 1fr); }
    .admin-row, .admin-tables { grid-template-columns: 1fr; }
}
</style>

<div class="admin">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Admin · Centre de contrôle</p>
            <h1 class="hud-title">Poste de <span>commandement</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="admin-badge">⚙ <?= htmlspecialchars($user['username']) ?></span>
            <a href="<?= BASE_URL ?>/admin/cards" class="btn btn-accent">🃏 Gérer les cartes</a>
            <a href="<?= BASE_URL ?>/dashboard" class="btn">← Dashboard</a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="admin-kpis">
        <div class="admin-kpi" style="--c:var(--cyan)">
            <span class="admin-kpi-icon">👥</span>
            <div class="admin-kpi-num" data-count="<?= $totalUsers ?>">0</div>
            <div class="admin-kpi-label">Joueurs</div>
            <div class="admin-kpi-sub"><?= $totalAdmins ?> admin<?= $totalAdmins > 1 ? 's' : '' ?></div>
        </div>
        <div class="admin-kpi" style="--c:var(--rank-a)">
            <span class="admin-kpi-icon">🃏</span>
            <div class="admin-kpi-num" data-count="<?= $totalCards ?>">0</div>
            <div class="admin-kpi-label">Cartes catalogue</div>
        </div>
        <div class="admin-kpi" style="--c:var(--rank-b)">
            <span class="admin-kpi-icon">📋</span>
            <div class="admin-kpi-num" data-count="<?= $totalDecks ?>">0</div>
            <div class="admin-kpi-label">Decks créés</div>
        </div>
        <div class="admin-kpi" style="--c:var(--red)">
            <span class="admin-kpi-icon">⚔️</span>
            <div class="admin-kpi-num" data-count="<?= $totalBattles ?>">0</div>
            <div class="admin-kpi-label">Combats</div>
            <div class="admin-kpi-sub"><?= $finishedBattles ?> terminés · <?= $activeBattles ?> actifs</div>
        </div>
        <div class="admin-kpi" style="--c:var(--amber)">
            <span class="admin-kpi-icon">🔨</span>
            <div class="admin-kpi-num" data-count="<?= $activeAuctions ?>">0</div>
            <div class="admin-kpi-label">Enchères actives</div>
        </div>
        <div class="admin-kpi" style="--c:var(--cyan)">
            <span class="admin-kpi-icon">🏪</span>
            <div class="admin-kpi-num" data-count="<?= $activeListings ?>">0</div>
            <div class="admin-kpi-label">Annonces actives</div>
            <div class="admin-kpi-sub"><?= $soldListings ?> vendues</div>
        </div>
        <div class="admin-kpi" style="--c:var(--amber)">
            <span class="admin-kpi-icon">💰</span>
            <div class="admin-kpi-num" data-count="<?= $totalCoins ?>">0</div>
            <div class="admin-kpi-label">Coins en circulation</div>
        </div>
        <div class="admin-kpi" style="--c:var(--rank-a)">
            <span class="admin-kpi-icon">📦</span>
            <div class="admin-kpi-num" data-count="<?= $ownedBoosters ?>">0</div>
            <div class="admin-kpi-label">Boosters en stock</div>
        </div>
    </div>

    <!-- Économie + Répartition -->
    <div class="admin-row">
        <div class="panel">
            <p class="panel-title">// Économie du jeu</p>
            <div class="eco-grid">
                <div class="eco-stat"><div class="eco-stat-num"><?= number_format($totalCoins, 0, ',', ' ') ?></div><div class="eco-stat-label">Coins totaux</div></div>
                <div class="eco-stat"><div class="eco-stat-num"><?= number_format($avgCoins, 0, ',', ' ') ?></div><div class="eco-stat-label">Moyenne / joueur</div></div>
                <div class="eco-stat"><div class="eco-stat-num"><?= number_format($ownedCards, 0, ',', ' ') ?></div><div class="eco-stat-label">Cartes détenues</div></div>
                <div class="eco-stat"><div class="eco-stat-num"><?= number_format($soldListings, 0, ',', ' ') ?></div><div class="eco-stat-label">Ventes conclues</div></div>
            </div>
        </div>

        <div class="panel">
            <p class="panel-title">// Répartition par classe</p>
            <?php
            $bars = [['Catalogue', $catalogByRarity], ['Cartes détenues', $ownedByRarity]];
            foreach ($bars as [$label, $data]):
                $sum = array_sum($data);
            ?>
            <div class="adm-rarity">
                <div class="adm-rarity-head"><?= $label ?> · <?= $sum ?></div>
                <div class="adm-rarity-bar">
                    <?php foreach ($rmap as $rar => $info): if (($data[$rar] ?? 0) <= 0) continue; ?>
                        <span class="adm-rarity-seg" style="flex:<?= $data[$rar] ?>;background:var(<?= $info[1] ?>);" title="<?= $info[0] ?> · <?= $data[$rar] ?>"></span>
                    <?php endforeach; ?>
                </div>
                <div class="adm-rarity-legend">
                    <?php foreach ($rmap as $rar => $info): ?>
                        <span style="color:var(<?= $info[1] ?>);"><b><?= $info[0] ?></b><?= $data[$rar] ?? 0 ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Derniers inscrits -->
    <div class="panel" style="margin-bottom:2rem;">
        <p class="panel-title">// Derniers inscrits</p>
        <table class="admin-table">
            <thead><tr><th>Joueur</th><th>Rôle</th><th class="num">Coins</th><th class="num">Inscrit</th></tr></thead>
            <tbody>
                <?php foreach ($recentUsers as $u): ?>
                <tr>
                    <td title="<?= htmlspecialchars($u['email']) ?>"><?= htmlspecialchars($u['username']) ?></td>
                    <td><span class="role-badge <?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                    <td class="num"><?= number_format($u['coins'], 0, ',', ' ') ?></td>
                    <td class="num"><?= substr($u['created_at'], 0, 10) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Classement complet -->
    <div class="panel">
        <p class="panel-title">// Classement complet · <?= count($allPlayers) ?> pilotes</p>
        <?php $players = $allPlayers; $lbInitial = 8; include ROOT . '/app/Views/partials/_leaderboard.php'; ?>
    </div>

</div>

<script>
(function () {
    document.querySelectorAll('.admin-kpi-num[data-count]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        if (target <= 0) { el.textContent = '0'; return; }
        var start = null, dur = 900;
        function tick(now) {
            if (!start) start = now;
            var p = Math.min(1, (now - start) / dur);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('fr-FR');
            if (p < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    });
})();
</script>
