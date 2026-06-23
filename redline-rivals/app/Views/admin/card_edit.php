<style>
.cardedit-layout { display: grid; grid-template-columns: 230px 1fr; gap: 2rem; align-items: start; }
.cardedit-preview { position: sticky; top: 78px; }
.cardedit-preview .tcg-card { width: 100%; max-width: none; }
.cardedit-note { font-family: var(--font-mono); font-size: .58rem; color: var(--muted); text-align: center; margin-top: .8rem; letter-spacing: 1px; text-transform: uppercase; }
.cardedit-danger { margin-top: 1.4rem; padding-top: 1.2rem; border-top: 1px solid var(--border-red); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.cardedit-danger span { font-family: var(--font-mono); font-size: .65rem; color: var(--muted); }
.cardedit-danger form { margin: 0; }
@media (max-width: 760px) { .cardedit-layout { grid-template-columns: 1fr; } .cardedit-preview { max-width: 230px; margin: 0 auto; position: static; } }
</style>

<div class="admin">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Admin · Modifier une carte</p>
            <h1 class="hud-title"><?= htmlspecialchars($card['name']) ?></h1>
        </div>
        <div class="hud-header-right">
            <a href="<?= BASE_URL ?>/admin/cards" class="btn">← Catalogue</a>
        </div>
    </div>

    <div class="cardedit-layout">
        <div class="cardedit-preview">
            <?php $showLink = false; $quantity = null; include ROOT . '/app/Views/partials/_card.php'; ?>
            <p class="cardedit-note">Aperçu actuel</p>
        </div>

        <div class="panel">
            <p class="panel-title">// Attributs de la carte</p>
            <?php
            $action = BASE_URL . '/admin/cards/' . $card['id'] . '/update';
            $submitLabel = 'Enregistrer les modifications';
            include ROOT . '/app/Views/admin/_card_form.php';
            ?>
            <div class="cardedit-danger">
                <span>Zone dangereuse — suppression définitive</span>
                <form action="<?= BASE_URL ?>/admin/cards/<?= $card['id'] ?>/delete" method="POST" onsubmit="return confirm('Supprimer cette carte ? Elle sera retirée de toutes les collections, decks, annonces et enchères.');">
                    <button type="submit" class="btn btn-primary">Supprimer la carte</button>
                </form>
            </div>
        </div>
    </div>

</div>
