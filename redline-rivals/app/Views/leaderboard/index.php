<?php $total = count($players); ?>

<div class="lb">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Classement · Pilotes</p>
            <h1 class="hud-title">Classement <span>général</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="coins-pill" style="color:var(--cyan);border-color:rgba(0,229,255,.28);background:rgba(0,229,255,.06);">🏁 <?= $total ?> <small>PILOTES</small></span>
        </div>
    </div>

    <?php if (empty($players)): ?>
        <div class="empty-state"><p>Aucun pilote classé pour le moment.</p></div>
    <?php else: ?>
        <?php $lbInitial = 10; include ROOT . '/app/Views/partials/_leaderboard.php'; ?>
    <?php endif; ?>

</div>
