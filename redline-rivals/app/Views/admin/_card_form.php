<?php
/**
 * Formulaire de carte (création ou édition).
 * Requis : $action (URL), $submitLabel (string), $imageOptions (array)
 * Optionnel : $card (array existant) — null pour une création
 */
$c = $card ?? ['name' => '', 'description' => '', 'rarity' => 'common', 'type' => 'sport', 'speed' => 50, 'power' => 50, 'handling' => 50, 'armor' => 50, 'image' => '', 'finish' => 'classic'];
$rarityLabels = ['common' => 'Commun (C)', 'rare' => 'Rare (B)', 'epic' => 'Épique (A)', 'legendary' => 'Légendaire (S)'];
$typeLabels   = ['sport' => 'Sport', 'muscle' => 'Muscle', 'tuner' => 'Tuner', 'hypercar' => 'Hypercar', 'truck' => 'Truck'];
$finishLabels = ['classic' => 'Classique', 'semiholo' => 'Semi-holo', 'holo' => 'Holo', 'fullart' => 'Full art'];
?>
<form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="cardform">
    <div class="cardform-grid">
        <div class="form-group cardform-wide">
            <label>Nom</label>
            <input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" required placeholder="Ex: Phantom GT" maxlength="100">
        </div>
        <div class="form-group">
            <label>Rareté</label>
            <select name="rarity">
                <?php foreach ($rarityLabels as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= $c['rarity'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type">
                <?php foreach ($typeLabels as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= $c['type'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group cardform-wide">
            <label>Finition</label>
            <select name="finish">
                <?php foreach ($finishLabels as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= ($c['finish'] ?? 'classic') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group cardform-wide">
            <label>Description</label>
            <textarea name="description" rows="2" placeholder="Description courte de la voiture…"><?= htmlspecialchars($c['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>⚡ Vitesse (1-100)</label>
            <input type="number" name="speed" min="1" max="100" value="<?= (int) $c['speed'] ?>" required>
        </div>
        <div class="form-group">
            <label>💪 Puissance (1-100)</label>
            <input type="number" name="power" min="1" max="100" value="<?= (int) $c['power'] ?>" required>
        </div>
        <div class="form-group">
            <label>🎯 Maniabilité (1-100)</label>
            <input type="number" name="handling" min="1" max="100" value="<?= (int) $c['handling'] ?>" required>
        </div>
        <div class="form-group">
            <label>🛡️ Blindage (1-100)</label>
            <input type="number" name="armor" min="1" max="100" value="<?= (int) $c['armor'] ?>" required>
        </div>
        <div class="form-group cardform-wide">
            <label>Image</label>
            <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp">
            <div class="cardform-or">— ou choisir une image déjà présente —</div>
            <select name="image">
                <option value="">— Aucune (icône par défaut) —</option>
                <?php foreach ($imageOptions as $img): ?>
                    <option value="<?= htmlspecialchars($img) ?>" <?= ($c['image'] ?? '') === $img ? 'selected' : '' ?>><?= htmlspecialchars($img) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($c['image'])): ?>
                <div class="cardform-current">Image actuelle : <strong><?= htmlspecialchars($c['image']) ?></strong></div>
            <?php endif; ?>
            <div class="cardform-hint">JPG, PNG ou WebP · 3 Mo max. Un fichier téléversé remplace la sélection ci-dessus.</div>
        </div>
    </div>
    <div class="cardform-actions">
        <button type="submit" class="btn btn-primary"><?= $submitLabel ?></button>
    </div>
</form>
