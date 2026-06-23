<?php
$gs        = $gs ?? json_decode($battle['game_state'], true);
$myKey     = $isPlayer1 ? 'p1Played' : 'p2Played';
$hasPlayed = $gs[$myKey] !== null;
$finished  = $gs['finished'] ?? false;

$myWins = 0; $oppWins = 0;
foreach ($gs['rounds'] as $r) {
    if ($r['winner'] === 'draw') continue;
    $isMine = ($isPlayer1 && $r['winner'] === 'p1') || (!$isPlayer1 && $r['winner'] === 'p2');
    $isMine ? $myWins++ : $oppWins++;
}
?>
<style>
.combat { max-width: 1100px; margin: 0 auto; }

/* Barre de score */
.combat-score { display: flex; align-items: center; justify-content: center; gap: 2rem; background: var(--bg2); border: 1px solid var(--border); padding: 1rem 1.5rem; margin-bottom: 1.5rem; position: relative; }
.combat-score-side { text-align: center; min-width: 90px; }
.combat-score-num { font-family: var(--font-head); font-weight: 900; font-size: 2rem; line-height: 1; }
.combat-score-side.me .combat-score-num { color: var(--cyan); text-shadow: 0 0 14px rgba(0,229,255,.3); }
.combat-score-side.opp .combat-score-num { color: var(--red); text-shadow: 0 0 14px rgba(255,23,68,.3); }
.combat-score-label { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .4rem; }
.combat-score-vs { font-family: var(--font-head); font-weight: 900; color: var(--muted); font-size: 1rem; }
.combat-score-turn { position: absolute; top: 8px; right: 12px; font-family: var(--font-mono); font-size: .55rem; color: var(--amber); letter-spacing: 1px; }

/* Layout */
.combat-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; align-items: start; }
.combat-panel { background: var(--bg2); border: 1px solid var(--border); padding: 1.3rem; }
.combat-panel-title { font-family: var(--font-head); font-size: .6rem; letter-spacing: 3px; text-transform: uppercase; color: var(--cyan); margin-bottom: 1.2rem; display: flex; align-items: center; justify-content: space-between; }

/* Main */
.hand { display: grid; grid-template-columns: repeat(auto-fill, minmax(135px, 1fr)); gap: 1rem; }
.play-card { position: relative; cursor: pointer; transition: transform .2s; }
.play-card .tcg-card { width: 100%; max-width: none; cursor: pointer; }
.play-card::after {
    content: 'JOUER'; position: absolute; inset: 0; z-index: 8;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-head); font-weight: 900; font-size: .8rem; letter-spacing: 4px; color: #fff;
    background: rgba(255,23,68,.55); opacity: 0; transition: opacity .2s; border-radius: 8px;
}
.play-card:hover { transform: translateY(-6px); }
.play-card:hover::after { opacity: 1; }

/* Journal de manches */
.rlog { display: flex; flex-direction: column; gap: .5rem; }
.rlog-item { display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding: .6rem .8rem; background: rgba(255,255,255,.02); border-left: 2px solid var(--muted); font-family: var(--font-mono); font-size: .66rem; color: var(--muted); }
.rlog-item.win { border-left-color: #00ff80; color: #00ff80; }
.rlog-item.lose { border-left-color: var(--red); color: var(--red); }
.rlog-item.draw { border-left-color: var(--amber); color: var(--amber); }
.rlog-score { font-size: .6rem; opacity: .8; }
.rlog-empty { color: var(--muted); font-size: .78rem; text-align: center; padding: 1.5rem; }

/* Résultat */
.result { text-align: center; max-width: 520px; margin: 1rem auto; background: var(--bg2); border: 1px solid; padding: 3rem 2rem; position: relative; overflow: hidden; }
.result.win { border-color: rgba(0,255,128,.3); }
.result.lose { border-color: var(--border-red); }
.result.draw { border-color: rgba(255,183,0,.3); }
.result::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, currentColor, transparent); opacity: .4; }
.result-icon { font-size: 3.5rem; margin-bottom: 1rem; }
.result-title { font-family: var(--font-head); font-weight: 900; font-size: 2rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: .4rem; }
.result.win .result-title { color: #00ff80; text-shadow: 0 0 30px rgba(0,255,128,.4); }
.result.lose .result-title { color: var(--red); text-shadow: 0 0 30px rgba(255,23,68,.4); }
.result.draw .result-title { color: var(--amber); }
.result-sub { font-family: var(--font-mono); color: var(--amber); margin-bottom: 1.5rem; font-size: .8rem; }
.result-score { font-family: var(--font-head); font-weight: 900; font-size: 2.6rem; color: var(--text-bright); letter-spacing: 4px; margin-bottom: 2rem; }

/* Attente */
.combat-wait { text-align: center; max-width: 520px; margin: 1rem auto; background: var(--bg2); border: 1px solid rgba(255,183,0,.25); padding: 3rem 2rem; }
.combat-wait-icon { font-size: 3rem; margin-bottom: 1rem; display: inline-block; animation: float 3s ease-in-out infinite; }
.combat-wait-title { font-family: var(--font-head); font-size: 1rem; letter-spacing: 3px; color: var(--amber); text-transform: uppercase; margin-bottom: .6rem; }
.combat-wait p { color: var(--muted); font-size: .85rem; margin-bottom: 1.5rem; }

#playMsg { margin-bottom: 1rem; }
@media (max-width: 780px) { .combat-grid { grid-template-columns: 1fr; } }
</style>

<div class="combat">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Arène · Combat #<?= $battle['id'] ?></p>
            <h1 class="hud-title">Duel <span><?= $finished ? 'terminé' : 'en cours' ?></span></h1>
        </div>
        <div class="hud-header-right">
            <a href="<?= BASE_URL ?>/arena" class="btn">← Arène</a>
        </div>
    </div>

    <?php if ($finished):
        $iWon = $gs['winnerId'] == Auth::id();
        $cls  = $gs['winnerId'] ? ($iWon ? 'win' : 'lose') : 'draw';
    ?>
        <div class="result <?= $cls ?>">
            <div class="result-icon"><?= $cls === 'win' ? '🏆' : ($cls === 'lose' ? '💀' : '🤝') ?></div>
            <div class="result-title"><?= $cls === 'win' ? 'Victoire' : ($cls === 'lose' ? 'Défaite' : 'Match nul') ?></div>
            <div class="result-sub"><?= $cls === 'win' ? '+50 coins encaissés' : ($cls === 'lose' ? 'La prochaine sera la bonne' : 'Personne ne cède') ?></div>
            <div class="result-score"><?= $myWins ?> – <?= $oppWins ?></div>
            <a href="<?= BASE_URL ?>/arena" class="btn btn-primary">Retour à l'arène</a>
        </div>

    <?php elseif ($battle['status'] === 'waiting'): ?>
        <div class="combat-wait">
            <span class="combat-wait-icon">📡</span>
            <div class="combat-wait-title">En attente d'un adversaire</div>
            <p>Ton combat est ouvert dans l'arène. Dès qu'un rival rejoint, la course commence.</p>
            <a href="<?= BASE_URL ?>/arena" class="btn">← Retour à l'arène</a>
        </div>

    <?php else: ?>

        <div class="combat-score">
            <div class="combat-score-side me">
                <div class="combat-score-num"><?= $myWins ?></div>
                <div class="combat-score-label">Vous</div>
            </div>
            <div class="combat-score-vs">VS</div>
            <div class="combat-score-side opp">
                <div class="combat-score-num"><?= $oppWins ?></div>
                <div class="combat-score-label">Adversaire</div>
            </div>
            <span class="combat-score-turn">TOUR <?= $gs['currentTurn'] ?? count($gs['rounds']) + 1 ?> · BO5</span>
        </div>

        <div id="playMsg"></div>

        <div class="combat-grid">
            <!-- Ma main -->
            <div class="combat-panel">
                <div class="combat-panel-title">
                    <span>// Votre main</span>
                    <span style="color:var(--muted)"><?= count($handCards) ?> cartes</span>
                </div>
                <?php if ($hasPlayed): ?>
                    <div class="flash success" style="margin:0;">Carte jouée. En attente de l'adversaire…</div>
                <?php elseif (empty($handCards)): ?>
                    <div class="rlog-empty">Plus de cartes en main.</div>
                <?php else: ?>
                    <div class="hand">
                        <?php foreach ($handCards as $hc):
                            $card = $hc; $showLink = false; $quantity = null;
                        ?>
                        <div class="play-card" onclick="playCard(<?= $hc['id'] ?>)" title="Jouer <?= htmlspecialchars($hc['name']) ?>">
                            <?php include ROOT . '/app/Views/partials/_card.php'; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Journal -->
            <div class="combat-panel">
                <div class="combat-panel-title">
                    <span>// Manches</span>
                    <span style="color:var(--muted)"><?= count($gs['rounds']) ?>/5</span>
                </div>
                <?php if (empty($gs['rounds'])): ?>
                    <div class="rlog-empty">Aucune manche jouée. Choisis ta première carte.</div>
                <?php else: ?>
                    <div class="rlog">
                        <?php foreach (array_reverse($gs['rounds']) as $round):
                            $myWin = ($isPlayer1 && $round['winner'] === 'p1') || (!$isPlayer1 && $round['winner'] === 'p2');
                            $cls   = $round['winner'] === 'draw' ? 'draw' : ($myWin ? 'win' : 'lose');
                            $myScore  = $isPlayer1 ? $round['p1Score'] : $round['p2Score'];
                            $oppScore = $isPlayer1 ? $round['p2Score'] : $round['p1Score'];
                        ?>
                        <div class="rlog-item <?= $cls ?>">
                            <span>T<?= $round['turn'] ?> · <?= $cls === 'win' ? '✓ Gagné' : ($cls === 'draw' ? '= Nul' : '✗ Perdu') ?></span>
                            <span class="rlog-score"><?= $myScore ?> – <?= $oppScore ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
        function playCard(cardId) {
            if (!confirm('Jouer cette carte ?')) return;
            var msg = document.getElementById('playMsg');
            fetch('<?= BASE_URL ?>/arena/<?= $battle['id'] ?>/play', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'card_id=' + encodeURIComponent(cardId)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    msg.innerHTML = '<div class="flash error" style="margin:0;">' + (data.message || 'Action impossible.') + '</div>';
                }
            })
            .catch(function () { location.reload(); });
        }
        </script>

    <?php endif; ?>

</div>
