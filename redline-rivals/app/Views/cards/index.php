<div class="hud-header">
    <div>
        <p class="hud-eyebrow">// Encyclopédie · Base de données</p>
        <h1 class="hud-title">Toutes les <span>machines</span></h1>
    </div>
    <div class="hud-header-right">
        <span class="collection-count">PAGE <?= $page ?> / <?= $totalPages ?></span>
    </div>
</div>

<div class="card-filter">
    <button class="filter-chip active" data-rarity="all">Toutes</button>
    <button class="filter-chip" data-rarity="legendary">S · Légendaire</button>
    <button class="filter-chip" data-rarity="epic">A · Épique</button>
    <button class="filter-chip" data-rarity="rare">B · Rare</button>
    <button class="filter-chip" data-rarity="common">C · Commun</button>
</div>

<div class="tcg-grid">
    <?php foreach ($cards as $card): ?>
        <?php $showLink = true; include ROOT . '/app/Views/partials/_card.php'; ?>
    <?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?= BASE_URL ?>/cards?page=<?= $page - 1 ?>" class="btn">← Précédent</a>
    <?php endif; ?>
    <span>Page <?= $page ?> / <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="<?= BASE_URL ?>/cards?page=<?= $page + 1 ?>" class="btn">Suivant →</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
(function () {
    var chips = document.querySelectorAll('.card-filter .filter-chip');
    var cards = document.querySelectorAll('.tcg-grid .tcg-card');
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