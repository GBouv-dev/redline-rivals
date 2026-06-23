<?php
/**
 * Classement réutilisable.
 * Requis : $players (array de [id, username, role, coins, wins, cards]), $meId (int)
 * Optionnel : $lbInitial (int, défaut 10) — nb de lignes visibles avant « Afficher plus »
 */
$lbInitial = $lbInitial ?? 10;
$lbTotal   = count($players);
$lbMedals  = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
?>
<div class="lb-head-row">
    <span>Rang</span>
    <span>Pilote</span>
    <span class="r">Victoires</span>
    <span class="r">Coins</span>
    <span class="r lb-col-cards">Cartes</span>
</div>

<div class="lb-list">
    <?php foreach ($players as $i => $p):
        $rank = $i + 1;
        $classes = 'lb-row';
        if ($rank <= 3) $classes .= ' top-' . $rank;
        if ($p['id'] == $meId) $classes .= ' me';
    ?>
    <div class="<?= $classes ?>"<?= $i >= $lbInitial ? ' style="display:none"' : '' ?>>
        <span class="lb-rank"><?= $lbMedals[$rank] ?? '#' . $rank ?></span>
        <span class="lb-name">
            <?= htmlspecialchars($p['username']) ?>
            <?php if ($p['id'] == $meId): ?><span class="lb-you">Vous</span><?php endif; ?>
            <?php if ($p['role'] === 'admin'): ?><span class="lb-adm">Admin</span><?php endif; ?>
        </span>
        <span class="lb-val wins"><?= $p['wins'] ?></span>
        <span class="lb-val coins"><?= number_format($p['coins'], 0, ',', ' ') ?></span>
        <span class="lb-val cards lb-col-cards"><?= $p['cards'] ?></span>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($lbTotal > $lbInitial): ?>
<div class="lb-more-wrap">
    <button id="lbMore" class="btn btn-accent" data-step="10" data-shown="<?= $lbInitial ?>">Afficher plus (<?= $lbTotal - $lbInitial ?>)</button>
</div>
<?php endif; ?>

<script>
(function () {
    var btn = document.getElementById('lbMore');
    if (!btn) return;
    var rows = Array.prototype.slice.call(document.querySelectorAll('.lb-list .lb-row'));
    var step = parseInt(btn.getAttribute('data-step'), 10) || 10;
    var shown = parseInt(btn.getAttribute('data-shown'), 10) || 10;
    btn.addEventListener('click', function () {
        shown += step;
        rows.forEach(function (r, i) { if (i < shown) r.style.display = ''; });
        var remaining = rows.length - shown;
        if (remaining <= 0) { btn.style.display = 'none'; }
        else { btn.textContent = 'Afficher plus (' + remaining + ')'; }
    });
})();
</script>
