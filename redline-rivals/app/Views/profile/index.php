<?php
$initials    = strtoupper(mb_substr($user['username'], 0, 2));
$memberSince = substr($user['created_at'], 0, 10);
$earnedCount = count(array_filter($badges, fn($b) => $b['earned']));
?>
<style>
.profile { max-width: 1100px; margin: 0 auto; }

.prof-head { display: flex; align-items: center; gap: 1.5rem; background: var(--bg2); border: 1px solid var(--border); padding: 1.5rem 2rem; margin-bottom: 2rem; }
.prof-avatar { width: 72px; height: 72px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-family: var(--font-head); font-weight: 900; font-size: 1.8rem; color: #07080d; background: linear-gradient(135deg, var(--red), var(--cyan)); clip-path: polygon(10px 0%, 100% 0%, calc(100% - 10px) 100%, 0% 100%); }
.prof-name { font-family: var(--font-head); font-weight: 900; font-size: 1.5rem; text-transform: uppercase; color: var(--text-bright); display: flex; align-items: center; gap: .7rem; }
.prof-role { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; color: var(--amber); border: 1px solid rgba(255,183,0,.4); padding: 3px 8px; }
.prof-meta { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); margin-top: .4rem; }

.prof-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); margin-bottom: 2.5rem; }
.prof-stat { background: var(--bg2); padding: 1.3rem; text-align: center; }
.prof-stat-num { font-family: var(--font-head); font-weight: 900; font-size: 1.8rem; color: var(--cyan); line-height: 1; }
.prof-stat-num small { font-size: .8rem; color: var(--muted); }
.prof-stat-label { font-family: var(--font-head); font-size: .5rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); margin-top: .45rem; }

.quests { display: flex; flex-direction: column; gap: .6rem; margin-bottom: 2.5rem; }
.quest { display: flex; align-items: center; gap: 1rem; background: var(--bg2); border: 1px solid var(--border); border-left: 3px solid var(--muted); padding: 1rem 1.2rem; }
.quest.done { border-left-color: #00ff80; }
.quest-icon { font-size: 1.6rem; flex-shrink: 0; }
.quest-body { flex: 1; min-width: 0; }
.quest-label { font-family: var(--font-head); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); display: flex; align-items: center; gap: .6rem; }
.quest-reward { font-family: var(--font-mono); font-size: .6rem; color: var(--amber); }
.quest-desc { font-size: .75rem; color: var(--muted); margin: .3rem 0 .5rem; }
.quest-bar { height: 5px; background: rgba(255,255,255,.05); overflow: hidden; }
.quest-fill { height: 100%; background: linear-gradient(90deg, var(--red), var(--cyan)); }
.quest.done .quest-fill { background: linear-gradient(90deg, #00ff80, var(--cyan)); }
.quest-action { flex-shrink: 0; }
.quest-action form { margin: 0; }
.quest-claimed { font-family: var(--font-head); font-size: .55rem; letter-spacing: 1px; color: #00ff80; }
.quest-progress { font-family: var(--font-mono); font-size: .72rem; color: var(--muted); }

.badges { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }
.badge { text-align: center; background: var(--bg2); border: 1px solid var(--border); padding: 1.3rem 1rem; transition: transform .2s, border-color .2s; }
.badge.earned { border-color: rgba(255,183,0,.3); }
.badge.earned:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(255,183,0,.08); }
.badge.locked { opacity: .4; filter: grayscale(1); }
.badge-icon { font-size: 2.2rem; display: block; margin-bottom: .6rem; }
.badge-name { font-family: var(--font-head); font-size: .62rem; letter-spacing: 1px; text-transform: uppercase; color: var(--text-bright); display: block; }
.badge-desc { font-size: .6rem; color: var(--muted); display: block; margin-top: .4rem; line-height: 1.4; }

@media (max-width: 760px) { .prof-stats { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="profile">

    <div class="hud-header">
        <div>
            <p class="hud-eyebrow">// Profil pilote</p>
            <h1 class="hud-title">Mon <span>profil</span></h1>
        </div>
        <div class="hud-header-right">
            <span class="coins-pill">💰 <?= number_format($user['coins'], 0, ',', ' ') ?> <small>COINS</small></span>
        </div>
    </div>

    <div class="prof-head">
        <div class="prof-avatar"><?= htmlspecialchars($initials) ?></div>
        <div>
            <div class="prof-name">
                <?= htmlspecialchars($user['username']) ?>
                <?php if ($user['role'] === 'admin'): ?><span class="prof-role">Admin</span><?php endif; ?>
            </div>
            <div class="prof-meta">Membre depuis le <?= $memberSince ?> · <?= $earnedCount ?>/<?= count($badges) ?> badges débloqués</div>
        </div>
    </div>

    <div class="prof-stats">
        <div class="prof-stat">
            <div class="prof-stat-num"><?= $stats['uniqueCards'] ?><small>/<?= $stats['catalogTotal'] ?></small></div>
            <div class="prof-stat-label">Cartes uniques</div>
        </div>
        <div class="prof-stat">
            <div class="prof-stat-num"><?= $stats['totalCards'] ?></div>
            <div class="prof-stat-label">Total cartes</div>
        </div>
        <div class="prof-stat">
            <div class="prof-stat-num"><?= $stats['wins'] ?></div>
            <div class="prof-stat-label">Victoires</div>
        </div>
        <div class="prof-stat">
            <div class="prof-stat-num"><?= $stats['decks'] ?></div>
            <div class="prof-stat-label">Decks</div>
        </div>
    </div>

    <p class="section-eyebrow">// Quêtes</p>
    <div class="quests">
        <?php foreach ($quests as $q):
            $pct = $q['target'] > 0 ? round($q['current'] / $q['target'] * 100) : 0;
        ?>
        <div class="quest <?= $q['done'] ? 'done' : '' ?>">
            <span class="quest-icon"><?= $q['icon'] ?></span>
            <div class="quest-body">
                <div class="quest-label"><?= htmlspecialchars($q['label']) ?> <span class="quest-reward">+<?= $q['reward'] ?> 💰</span></div>
                <div class="quest-desc"><?= htmlspecialchars($q['desc']) ?></div>
                <div class="quest-bar"><div class="quest-fill" style="width:<?= $pct ?>%"></div></div>
            </div>
            <div class="quest-action">
                <?php if ($q['claimed']): ?>
                    <span class="quest-claimed">✓ Réclamé</span>
                <?php elseif ($q['done']): ?>
                    <form action="<?= BASE_URL ?>/profil/claim/<?= $q['key'] ?>" method="POST">
                        <button type="submit" class="btn btn-secondary">Réclamer</button>
                    </form>
                <?php else: ?>
                    <span class="quest-progress"><?= $q['current'] ?>/<?= $q['target'] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <p class="section-eyebrow">// Badges · <?= $earnedCount ?>/<?= count($badges) ?></p>
    <div class="badges">
        <?php foreach ($badges as $b): ?>
        <div class="badge <?= $b['earned'] ? 'earned' : 'locked' ?>">
            <span class="badge-icon"><?= $b['icon'] ?></span>
            <span class="badge-name"><?= htmlspecialchars($b['name']) ?></span>
            <span class="badge-desc"><?= htmlspecialchars($b['desc']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

</div>
