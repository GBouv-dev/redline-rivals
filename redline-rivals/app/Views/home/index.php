<style>
    /* ─── FULL BLEED : casse le max-width de <main> ─── */
    .home-wrap {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        margin-top: -2rem;
        margin-bottom: -2rem;
        overflow: hidden;
    }

    /* ─── HERO ─── */
    .hero {
        position: relative;
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        overflow: hidden;
        background: var(--bg);
    }

    .hero-bg {
        position: absolute;
        inset: 0;
        background-image: url('<?= BASE_URL ?>/assets/img/cards/2016-Apollo-Arrow-002-1080.webp');
        background-size: cover;
        background-position: center 40%;
        filter: brightness(.2) saturate(.7);
        transform: scale(1.05);
        z-index: 0;
    }

    .hero-road {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 35%;
        z-index: 1;
        background: linear-gradient(to bottom, transparent 0%, rgba(7, 8, 13, .6) 40%, rgba(10, 11, 16, .95) 100%);
    }

    .hero-road::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            repeating-linear-gradient(90deg, transparent 0px, transparent 3px, rgba(255, 255, 255, .008) 3px, rgba(255, 255, 255, .008) 4px),
            repeating-linear-gradient(0deg, transparent 0px, transparent 2px, rgba(255, 255, 255, .005) 2px, rgba(255, 255, 255, .005) 3px);
        background-size: 8px 8px, 8px 8px;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background:
            linear-gradient(90deg, rgba(7, 8, 13, .97) 0%, rgba(7, 8, 13, .75) 45%, rgba(7, 8, 13, .2) 100%),
            linear-gradient(180deg, rgba(7, 8, 13, .4) 0%, transparent 25%, transparent 65%, rgba(7, 8, 13, .9) 100%);
    }

    /* ─── STREAKS DYNAMIQUES (générés en JS) ─── */
    .hero-streaks {
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
        overflow: hidden;
    }

    .speed-streak {
        position: absolute;
        height: 2px;
        border-radius: 2px;
        animation: streakFly linear forwards;
        will-change: transform, opacity;
    }

    @keyframes streakFly {
        from {
            transform: rotate(-7deg) translateX(0);
            opacity: var(--op);
        }

        85% {
            opacity: var(--op);
        }

        to {
            transform: rotate(-7deg) translateX(var(--travel));
            opacity: 0;
        }
    }

    .streak-white {
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .95), transparent);
        box-shadow: 0 0 6px rgba(255, 255, 255, .5);
    }

    .streak-cyan {
        background: linear-gradient(90deg, transparent, rgba(0, 229, 255, .95), transparent);
        box-shadow: 0 0 8px rgba(0, 229, 255, .5);
    }

    .streak-red {
        background: linear-gradient(90deg, transparent, rgba(255, 23, 68, .95), transparent);
        box-shadow: 0 0 8px rgba(255, 23, 68, .5);
        height: 2.5px;
    }

    /* Marquages routiers — boucle parfaite (distance = période du motif) */
    .hero-road-line,
    .hero-road-line-2 {
        position: absolute;
        left: -10%;
        right: -10%;
        z-index: 3;
        pointer-events: none;
    }

    .hero-road-line {
        bottom: 18%;
        height: 2px;
        background: repeating-linear-gradient(90deg, transparent 0px, transparent 60px, rgba(255, 255, 255, .2) 60px, rgba(255, 255, 255, .25) 110px, transparent 110px, transparent 180px);
        filter: blur(.8px);
        animation: roadScroll1 .45s linear infinite;
    }

    .hero-road-line-2 {
        bottom: 22%;
        height: 1px;
        background: repeating-linear-gradient(90deg, transparent 0px, transparent 80px, rgba(255, 255, 255, .09) 80px, rgba(255, 255, 255, .12) 130px, transparent 130px, transparent 210px);
        filter: blur(1.5px);
        animation: roadScroll2 .6s linear infinite;
    }

    @keyframes roadScroll1 {
        from {
            transform: skewY(-1deg) translateX(0);
        }

        to {
            transform: skewY(-1deg) translateX(-180px);
        }
    }

    @keyframes roadScroll2 {
        from {
            transform: skewY(-1deg) translateX(0);
        }

        to {
            transform: skewY(-1deg) translateX(-210px);
        }
    }

    .hero-headlight {
        position: absolute;
        bottom: 0;
        left: 30%;
        right: 0;
        height: 45%;
        z-index: 2;
        background: radial-gradient(ellipse at 50% 100%, rgba(255, 240, 200, .05) 0%, transparent 65%);
        pointer-events: none;
    }

    .hero-grid {
        position: absolute;
        inset: 0;
        z-index: 2;
        background: linear-gradient(rgba(0, 229, 255, .015) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 229, 255, .015) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
        animation: heroVibrate .08s ease-in-out infinite;
    }

    /* Texte gauche */
    .hero-left {
        position: relative;
        z-index: 3;
        padding: 4rem 3rem 4rem 4rem;
    }

    .hero-eyebrow {
        font-family: var(--font-head);
        font-size: .6rem;
        letter-spacing: 8px;
        color: rgba(0, 229, 255, .5);
        text-transform: uppercase;
        margin-bottom: 2rem;
    }

    .hero-title {
        font-family: var(--font-head);
        font-size: clamp(3rem, 7vw, 6rem);
        font-weight: 900;
        line-height: .88;
        text-transform: uppercase;
        letter-spacing: -3px;
        color: var(--text-bright);
        margin-bottom: .3rem;
    }

    .hero-title .title-red {
        display: block;
        color: var(--red);
        text-shadow: 0 0 40px rgba(255, 23, 68, .45), 0 0 80px rgba(255, 23, 68, .15);
        animation: flicker 12s infinite;
    }

    .hero-sub {
        font-family: var(--font-head);
        font-size: clamp(.65rem, 1.6vw, .9rem);
        color: rgba(160, 180, 200, .55);
        letter-spacing: 6px;
        margin: 2rem 0 3rem;
    }

    .hero-cta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-hero {
        font-family: var(--font-head);
        font-size: .65rem;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 13px 28px;
        cursor: pointer;
        text-decoration: none;
        clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
        transition: all .2s;
    }

    .btn-hero-red {
        background: var(--red);
        color: #fff;
        border: none;
        box-shadow: 0 0 25px rgba(255, 23, 68, .35);
    }

    .btn-hero-red:hover {
        background: #ff3a5c;
        box-shadow: 0 0 45px rgba(255, 23, 68, .55);
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-hero-outline {
        background: transparent;
        color: var(--cyan);
        border: 1px solid rgba(0, 229, 255, .4);
    }

    .btn-hero-outline:hover {
        background: rgba(0, 229, 255, .06);
        box-shadow: 0 0 25px rgba(0, 229, 255, .2);
        color: var(--cyan);
    }

    /* Badge classe */
    .hero-car-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2rem;
        padding: 6px 14px;
        border: 1px solid rgba(255, 183, 0, .3);
        background: rgba(255, 183, 0, .05);
        font-family: var(--font-mono);
        font-size: .65rem;
        letter-spacing: 2px;
        color: var(--amber);
    }

    .hero-car-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--amber);
        animation: neonPulse 2s infinite;
    }

    .hero-car-badge .rank-s {
        font-family: var(--font-head);
        font-weight: 900;
        color: var(--rank-s);
        margin-right: 4px;
    }

    /* Cartes droite */
    .hero-right {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        height: 100%;
    }

    .hero-cards-stack {
        position: relative;
        width: 200px;
        height: 300px;
    }

    .hero-card-item {
        position: absolute;
        width: 175px;
        transition: transform .5s ease, opacity .5s ease;
    }

    .hero-card-item:nth-child(1) {
        transform: rotate(-8deg) translate(-40px, 20px);
        z-index: 1;
    }

    .hero-card-item:nth-child(2) {
        transform: rotate(3deg) translate(20px, -10px);
        z-index: 2;
    }

    .hero-card-item:nth-child(3) {
        transform: rotate(-2deg) translate(-10px, 5px);
        z-index: 3;
    }

    .hero-cards-stack:hover .hero-card-item:nth-child(1) {
        transform: rotate(-12deg) translate(-80px, 30px);
    }

    .hero-cards-stack:hover .hero-card-item:nth-child(2) {
        transform: rotate(0deg) translate(0px, -20px);
    }

    .hero-cards-stack:hover .hero-card-item:nth-child(3) {
        transform: rotate(10deg) translate(60px, 10px);
    }

    .hero-scroll {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        font-family: var(--font-head);
        font-size: .5rem;
        letter-spacing: 3px;
        color: rgba(255, 255, 255, .2);
        animation: float 3s ease-in-out infinite;
    }

    .hero-scroll-line {
        width: 1px;
        height: 40px;
        background: linear-gradient(to bottom, rgba(0, 229, 255, .4), transparent);
    }

    /* ─── STATS BAR ─── */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border-bottom: 1px solid rgba(0, 229, 255, .08);
    }

    .stat-item {
        padding: 2rem;
        text-align: center;
        border-right: 1px solid rgba(0, 229, 255, .06);
        background: rgba(12, 15, 24, .9);
        position: relative;
        overflow: hidden;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-item::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--c, var(--red)), transparent);
        opacity: 0;
        transition: opacity .3s;
    }

    .stat-item:hover::after {
        opacity: 1;
    }

    .stat-num {
        font-family: var(--font-head);
        font-size: 2.2rem;
        font-weight: 900;
        color: var(--cyan);
        display: block;
        text-shadow: 0 0 15px rgba(0, 229, 255, .3);
    }

    .stat-lbl {
        font-family: var(--font-head);
        font-size: .52rem;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--muted);
        margin-top: .4rem;
        display: block;
    }

    /* ─── CARTES SHOWCASE — pleine largeur ─── */
    .cards-showcase {
        padding: 6rem 3rem;
    }

    .showcase-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(155px, 185px));
        gap: 1.5rem;
        justify-content: center;
    }

    /* ─── FEATURES — étapes ─── */
    .features-section {
        padding: 5rem 3rem;
        background: rgba(12, 15, 24, .6);
        border-top: 1px solid rgba(0, 229, 255, .06);
        border-bottom: 1px solid rgba(0, 229, 255, .06);
    }

    .features-inner {
        max-width: 1600px;
        margin: 0 auto;
    }

    .features-steps {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .feature-step {
        display: grid;
        grid-template-columns: 60px 40px 1fr;
        gap: 0 1.5rem;
        align-items: start;
        padding: 2rem 0;
        border-bottom: 1px solid rgba(0, 229, 255, .06);
    }

    .feature-step:last-child {
        border-bottom: none;
    }

    .step-num {
        font-family: var(--font-head);
        font-size: 2.5rem;
        font-weight: 900;
        color: rgba(255, 23, 68, .15);
        line-height: 1;
        padding-top: .2rem;
    }

    .step-line {
        width: 1px;
        background: linear-gradient(to bottom, rgba(0, 229, 255, .3), rgba(0, 229, 255, .05));
        margin: 0 auto;
        height: 100%;
        min-height: 80px;
        position: relative;
    }

    .step-line::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 8px rgba(0, 229, 255, .6);
    }

    .step-line.last {
        background: linear-gradient(to bottom, rgba(0, 229, 255, .3), transparent);
    }

    .step-content {
        padding-top: .2rem;
    }

    .step-icon {
        font-size: 1.5rem;
        margin-bottom: .6rem;
        display: block;
    }

    .step-title {
        font-family: var(--font-head);
        font-size: .8rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: .6rem;
    }

    .step-desc {
        font-size: .88rem;
        color: var(--muted);
        line-height: 1.8;
        max-width: 900px;
    }

    /* ─── CLASSES DE PERFORMANCE (rangs S/A/B/C) ─── */
    .rarities-section {
        padding: 5rem 3rem;
        position: relative;
        overflow: hidden;
    }

    .rarities-inner {
        max-width: 1600px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .rarities-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: rgba(0, 229, 255, .06);
        margin-top: 3rem;
    }

    .rarity-card {
        background: #0c0f18;
        padding: 2rem 1.5rem;
        transition: background .2s, transform .2s;
        position: relative;
        overflow: hidden;
    }

    .rarity-card:hover {
        background: #12161f;
        transform: translateY(-3px);
    }

    .rarity-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
    }

    .rarity-card.common::before {
        background: var(--rank-c);
    }

    .rarity-card.rare::before {
        background: var(--rank-b);
    }

    .rarity-card.epic::before {
        background: var(--rank-a);
        box-shadow: 0 0 10px rgba(139, 92, 246, .5);
    }

    .rarity-card.legendary::before {
        background: linear-gradient(90deg, var(--rank-s), #fff3c4, var(--rank-s));
        box-shadow: 0 0 15px rgba(255, 183, 0, .4);
    }

    .rank-letter {
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 2.4rem;
        line-height: 1;
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid;
        margin-bottom: 1.2rem;
    }

    .rarity-card.common .rank-letter {
        color: var(--rank-c);
        border-color: var(--rank-c);
    }

    .rarity-card.rare .rank-letter {
        color: var(--rank-b);
        border-color: var(--rank-b);
        box-shadow: inset 0 0 15px rgba(59, 130, 246, .15);
    }

    .rarity-card.epic .rank-letter {
        color: var(--rank-a);
        border-color: var(--rank-a);
        box-shadow: inset 0 0 15px rgba(139, 92, 246, .15);
    }

    .rarity-card.legendary .rank-letter {
        color: var(--rank-s);
        border-color: var(--rank-s);
        box-shadow: inset 0 0 18px rgba(255, 183, 0, .2), 0 0 18px rgba(255, 183, 0, .15);
    }

    .rarity-name {
        font-family: var(--font-head);
        font-size: .68rem;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: .5rem;
    }

    .rarity-card.common .rarity-name {
        color: var(--rank-c);
    }

    .rarity-card.rare .rarity-name {
        color: var(--rank-b);
    }

    .rarity-card.epic .rarity-name {
        color: var(--rank-a);
    }

    .rarity-card.legendary .rarity-name {
        color: var(--rank-s);
    }

    .rarity-drop {
        font-family: var(--font-mono);
        font-size: .58rem;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, .18);
        margin-bottom: .9rem;
        text-transform: uppercase;
    }

    .rarity-desc {
        font-size: .78rem;
        color: var(--muted);
        line-height: 1.7;
        margin-bottom: 1.2rem;
    }

    .rarity-stats-preview {
        display: flex;
        align-items: center;
        gap: .6rem;
        font-family: var(--font-mono);
        font-size: .55rem;
        color: var(--muted);
    }

    .rarity-bar {
        flex: 1;
        height: 3px;
        background: rgba(255, 255, 255, .05);
        border-radius: 2px;
        overflow: hidden;
    }

    .rarity-bar div {
        height: 100%;
        border-radius: 2px;
    }

    /* ─── RÈGLES ─── */
    .rules-section {
        padding: 6rem 3rem;
        background: rgba(12, 15, 24, .7);
        border-top: 1px solid rgba(255, 23, 68, .1);
    }

    .rules-inner {
        max-width: 1600px;
        margin: 0 auto;
    }

    .rules-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1px;
        background: rgba(0, 229, 255, .06);
        margin-top: 3rem;
    }

    .rules-col {
        background: #0c0f18;
        padding: 2rem;
        transition: background .2s;
    }

    .rules-col:hover {
        background: #12161f;
    }

    .rules-col-title {
        font-family: var(--font-head);
        font-size: .7rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: 1.5rem;
        padding-bottom: .8rem;
        border-bottom: 1px solid rgba(0, 229, 255, .1);
    }

    .rules-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: .8rem;
    }

    .rules-list li {
        font-size: .82rem;
        color: var(--muted);
        line-height: 1.7;
        padding-left: 1rem;
        position: relative;
    }

    .rules-list li::before {
        content: '›';
        position: absolute;
        left: 0;
        color: rgba(255, 23, 68, .5);
        font-weight: 700;
    }

    .rules-list li strong {
        color: rgba(255, 255, 255, .5);
        font-weight: 600;
    }

    /* ─── CTA FINAL ─── */
    .final-cta {
        padding: 8rem 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .final-cta::before {
        content: 'REDLINE';
        position: absolute;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 14vw;
        letter-spacing: 1vw;
        white-space: nowrap;
        color: rgba(255, 23, 68, .025);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .final-cta-content {
        position: relative;
        z-index: 1;
        max-width: 700px;
        margin: 0 auto;
    }

    .final-cta h2 {
        font-family: var(--font-head);
        font-size: clamp(1.3rem, 4vw, 2.5rem);
        color: var(--text-bright);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 3px;
    }

    .final-cta p {
        color: var(--muted);
        font-size: .9rem;
        margin-bottom: 3rem;
        line-height: 1.7;
    }

    .home-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 23, 68, .3), rgba(0, 229, 255, .2), transparent);
    }

    @media (max-width: 900px) {
        .hero {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .hero-right {
            display: none;
        }

        .hero-left {
            padding: 6rem 2rem;
            text-align: center;
        }

        .hero-cta {
            justify-content: center;
        }

        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }

        .rarities-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .rules-grid {
            grid-template-columns: 1fr;
        }

        .feature-step {
            grid-template-columns: 40px 30px 1fr;
            gap: 0 1rem;
        }

        .step-num {
            font-size: 1.8rem;
        }

        .cards-showcase,
        .features-section,
        .rarities-section,
        .rules-section,
        .final-cta {
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }
    }
</style>

<div class="home-wrap">

    <!-- ═══ HERO ═══ -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-road"></div>
        <div class="hero-streaks" id="heroStreaks"></div>
        <div class="hero-road-line"></div>
        <div class="hero-road-line-2"></div>
        <div class="hero-headlight"></div>
        <div class="hero-grid"></div>

        <!-- Texte gauche -->
        <div class="hero-left">
            <div class="hero-car-badge"><span class="rank-s">S</span>CLASS · APOLLO ARROW '16</div>
            <p class="hero-eyebrow">// Redline Racing League</p>
            <h1 class="hero-title">
                REDLINE
                <span class="title-red">RIVALS</span>
            </h1>
            <p class="hero-sub">COLLECTIONNE · CONSTRUIS · DOMINE</p>
            <div class="hero-cta">
                <?php if ($user): ?>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn-hero btn-hero-red">Mon garage</a>
                    <a href="<?= BASE_URL ?>/boosters" class="btn-hero btn-hero-outline">Boosters</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/register" class="btn-hero btn-hero-red">Rejoindre</a>
                    <a href="<?= BASE_URL ?>/login" class="btn-hero btn-hero-outline">Connexion</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cartes empilées droite -->
        <div class="hero-right">
            <div class="hero-cards-stack" id="heroStack">
                <?php
                $heroCards = $cards;
                shuffle($heroCards);
                $heroCards = array_slice($heroCards, 0, 3);
                foreach (array_reverse($heroCards) as $card):
                    $showLink = false;
                    $quantity = null;
                    ?>
                    <div class="hero-card-item">
                        <?php include ROOT . '/app/Views/partials/_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="hero-scroll">
            <div class="hero-scroll-line"></div>
            <span>SCROLL</span>
        </div>
    </section>

    <script>
        // ── Génération continue de traînées de vitesse ──
        (function () {
            const container = document.getElementById('heroStreaks');
            if (!container) return;

            function spawnStreak() {
                const el = document.createElement('div');
                const dir = Math.random() < 0.5 ? 1 : -1;
                const len = 60 + Math.random() * 240;
                const dur = 0.35 + Math.random() * 1.0;
                const op = (0.15 + Math.random() * 0.45).toFixed(2);

                const roll = Math.random();
                const cls = roll < 0.08 ? 'streak-red' : roll < 0.18 ? 'streak-cyan' : 'streak-white';

                el.className = 'speed-streak ' + cls;
                el.style.top = (Math.random() * 100) + '%';
                el.style.width = len + 'px';
                el.style.setProperty('--op', op);
                el.style.animationDuration = dur + 's';

                if (dir === 1) {
                    el.style.left = (-len) + 'px';
                    el.style.setProperty('--travel', `calc(100vw + ${len * 2}px)`);
                } else {
                    el.style.right = (-len) + 'px';
                    el.style.setProperty('--travel', `calc(-100vw - ${len * 2}px)`);
                }

                container.appendChild(el);
                setTimeout(() => el.remove(), dur * 1000 + 80);
            }

            setInterval(spawnStreak, 70);
            for (let i = 0; i < 12; i++) setTimeout(spawnStreak, i * 50);
        })();

        // ── Rotation des cartes du hero ──
        (function () {
            const stack = document.getElementById('heroStack');
            if (!stack) return;
            setInterval(() => {
                const cards = stack.querySelectorAll('.hero-card-item');
                if (cards.length < 2) return;
                const top = cards[cards.length - 1];
                top.style.transform = 'translateX(120px) rotate(20deg)';
                top.style.opacity = '0';
                setTimeout(() => {
                    stack.insertBefore(top, cards[0]);
                    top.style.transition = 'none';
                    top.style.transform = 'translateX(-120px) rotate(-20deg)';
                    top.style.opacity = '0';
                    top.getBoundingClientRect();
                    top.style.transition = 'transform .5s ease, opacity .5s ease';
                    top.style.transform = '';
                    top.style.opacity = '1';
                }, 500);
            }, 4000);
        })();
    </script>

    <div class="checker-strip"></div>

    <!-- ═══ STATS ═══ -->
    <div class="stats-bar">
        <div class="stat-item" style="--c:var(--red)">
            <span class="stat-num"><?= count($cards) ?></span>
            <span class="stat-lbl">Cartes disponibles</span>
        </div>
        <div class="stat-item" style="--c:var(--cyan)">
            <span class="stat-num">3</span>
            <span class="stat-lbl">Types de boosters</span>
        </div>
        <div class="stat-item" style="--c:var(--rank-a)">
            <span class="stat-num">4</span>
            <span class="stat-lbl">Classes de performance</span>
        </div>
        <div class="stat-item" style="--c:var(--amber)">
            <span class="stat-num">∞</span>
            <span class="stat-lbl">Combats possibles</span>
        </div>
    </div>

    <!-- ═══ CARTES SHOWCASE ═══ -->
    <?php if (!empty($cards)): ?>
        <section class="cards-showcase">
            <p class="section-eyebrow">// Notre flotte</p>
            <h2 class="section-title">Les cartes <span>disponibles</span></h2>
            <div class="showcase-grid">
                <?php foreach ($cards as $card):
                    $showLink = true;
                    $quantity = null;
                    include ROOT . '/app/Views/partials/_card.php';
                endforeach; ?>
            </div>
            <div style="text-align:center;margin-top:3rem;">
                <a href="<?= BASE_URL ?>/cards" class="btn btn-accent">Voir l'encyclopédie complète →</a>
            </div>
        </section>
    <?php endif; ?>

    <!-- ═══ FEATURES — COMMENT JOUER ═══ -->
    <section class="features-section">
        <div class="features-inner">
            <p class="section-eyebrow">// Système de jeu</p>
            <h2 class="section-title">Comment <span>jouer</span></h2>
            <div class="features-steps">
                <div class="feature-step">
                    <div class="step-num">01</div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-icon">📦</div>
                        <h3 class="step-title">Achète des boosters</h3>
                        <p class="step-desc">Dépense tes coins pour ouvrir des packs de cartes. Chaque booster contient
                            plusieurs cartes tirées aléatoirement selon des taux de drop. Plus le pack est cher, plus
                            tes chances d'obtenir des classes élevées augmentent.</p>
                    </div>
                </div>
                <div class="feature-step">
                    <div class="step-num">02</div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-icon">🃏</div>
                        <h3 class="step-title">Construis ton deck</h3>
                        <p class="step-desc">Sélectionne jusqu'à 10 cartes de ta collection pour former un deck de
                            combat. Équilibre vitesse, puissance, maniabilité et blindage selon ta stratégie.</p>
                    </div>
                </div>
                <div class="feature-step">
                    <div class="step-num">03</div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-icon">⚔️</div>
                        <h3 class="step-title">Affronte tes rivaux</h3>
                        <p class="step-desc">Rejoins ou crée un combat dans l'arène. Chaque round, joue une carte de ta
                            main — le score total des stats détermine le vainqueur du round. Remporte le plus de rounds
                            pour gagner 50 coins.</p>
                    </div>
                </div>
                <div class="feature-step">
                    <div class="step-num">04</div>
                    <div class="step-line last"></div>
                    <div class="step-content">
                        <div class="step-icon">🏪</div>
                        <h3 class="step-title">Trade sur le marché</h3>
                        <p class="step-desc">Vends tes doublons à prix fixe ou mets aux enchères tes cartes rares pour
                            maximiser tes gains. Achète les cartes qui manquent à ta collection.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ CLASSES DE PERFORMANCE ═══ -->
    <section class="rarities-section">
        <div class="rarities-inner">
            <p class="section-eyebrow">// Classes de performance</p>
            <h2 class="section-title">Quatre classes de <span>puissance</span></h2>
            <div class="rarities-grid">
                <div class="rarity-card common">
                    <div class="rank-letter">C</div>
                    <p class="rarity-name">Commun</p>
                    <div class="rarity-drop">DROP RATE ~45%</div>
                    <p class="rarity-desc">Accessible à tous, idéal pour construire un premier deck solide. Stats
                        équilibrées, aucune surprise.</p>
                    <div class="rarity-stats-preview">
                        <span>SCORE MOY.</span>
                        <div class="rarity-bar">
                            <div style="width:45%;background:var(--rank-c)"></div>
                        </div>
                        <span style="color:var(--rank-c)">~200/400</span>
                    </div>
                </div>
                <div class="rarity-card rare">
                    <div class="rank-letter">B</div>
                    <p class="rarity-name">Rare</p>
                    <div class="rarity-drop">DROP RATE ~30%</div>
                    <p class="rarity-desc">Stats nettement supérieures. Moins fréquentes dans les boosters standard,
                        très recherchées au marché.</p>
                    <div class="rarity-stats-preview">
                        <span>SCORE MOY.</span>
                        <div class="rarity-bar">
                            <div style="width:63%;background:var(--rank-b)"></div>
                        </div>
                        <span style="color:var(--rank-b)">~263/400</span>
                    </div>
                </div>
                <div class="rarity-card epic">
                    <div class="rank-letter">A</div>
                    <p class="rarity-name">Épique</p>
                    <div class="rarity-drop">DROP RATE ~18%</div>
                    <p class="rarity-desc">Puissance élevée sur plusieurs stats. Prennent l'avantage dans la majorité
                        des matchups en arène.</p>
                    <div class="rarity-stats-preview">
                        <span>SCORE MOY.</span>
                        <div class="rarity-bar">
                            <div style="width:80%;background:var(--rank-a)"></div>
                        </div>
                        <span style="color:var(--rank-a)">~320/400</span>
                    </div>
                </div>
                <div class="rarity-card legendary">
                    <div class="rank-letter">S</div>
                    <p class="rarity-name">Légendaire</p>
                    <div class="rarity-drop">DROP RATE ~7%</div>
                    <p class="rarity-desc">Les plus puissantes du jeu. Leur obtention est un événement. Dominent l'arène
                        et valent une fortune aux enchères.</p>
                    <div class="rarity-stats-preview">
                        <span>SCORE MOY.</span>
                        <div class="rarity-bar">
                            <div style="width:95%;background:linear-gradient(90deg,var(--rank-s),#fff3c4)"></div>
                        </div>
                        <span style="color:var(--rank-s)">~314/400</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ RÈGLES ═══ -->
    <section class="rules-section">
        <div class="rules-inner">
            <p class="section-eyebrow">// Règles du jeu</p>
            <h2 class="section-title">Le <span>code de la route</span></h2>
            <div class="rules-grid">
                <div class="rules-col">
                    <h3 class="rules-col-title">⚔️ Combat</h3>
                    <ul class="rules-list">
                        <li>Chaque joueur choisit un deck de <strong>3 à 10 cartes</strong> avant le combat.</li>
                        <li>À chaque round, les deux joueurs jouent <strong>une carte simultanément</strong>.</li>
                        <li>La carte avec le <strong>score total le plus élevé</strong> (vitesse + puissance +
                            maniabilité + blindage) remporte le round.</li>
                        <li>En cas d'égalité de score, le round est déclaré <strong>nul</strong>.</li>
                        <li>Le combat se termine quand les mains sont vides ou après <strong>5 rounds maximum</strong>.
                        </li>
                        <li>Le joueur ayant remporté <strong>le plus de rounds</strong> gagne le combat et reçoit
                            <strong>50 coins</strong>.</li>
                    </ul>
                </div>
                <div class="rules-col">
                    <h3 class="rules-col-title">🏪 Marché & Enchères</h3>
                    <ul class="rules-list">
                        <li>Mettre une carte en vente la <strong>retire immédiatement</strong> de ta collection.</li>
                        <li>Tu ne peux pas acheter tes <strong>propres annonces</strong>.</li>
                        <li>Pour les enchères, chaque surenchère doit dépasser la mise actuelle d'un <strong>minimum de
                                10%</strong>.</li>
                        <li>À la fin d'une enchère, les coins sont automatiquement <strong>débités du gagnant</strong>
                            et crédités au vendeur.</li>
                        <li>Si une enchère expire sans enchérisseur, la carte est <strong>restituée au vendeur</strong>.
                        </li>
                        <li>Annuler une vente à prix fixe <strong>restitue la carte</strong> dans ta collection.</li>
                    </ul>
                </div>
                <div class="rules-col">
                    <h3 class="rules-col-title">💰 Économie</h3>
                    <ul class="rules-list">
                        <li>Chaque nouveau compte reçoit <strong>500 coins</strong> de départ.</li>
                        <li>Les coins sont la seule monnaie du jeu — ils ne s'achètent pas.</li>
                        <li>Gagner un combat rapporte <strong>50 coins</strong>.</li>
                        <li>Les boosters coûtent entre <strong>100 et 500 coins</strong> selon le niveau.</li>
                        <li>Les transactions de marché sont <strong>instantanées et définitives</strong>.</li>
                        <li>Il n'y a pas de limite au nombre de cartes dans ta collection.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ CTA FINAL ═══ -->
    <?php if (!$user): ?>
        <section class="final-cta">
            <div class="final-cta-content">
                <h2>Prêt à entrer dans la course ?</h2>
                <p>Crée ton compte gratuitement et reçois 500 coins de départ pour ouvrir tes premiers boosters et
                    construire ta collection.</p>
                <a href="<?= BASE_URL ?>/register" class="btn-hero btn-hero-red">Créer mon compte — Gratuit</a>
            </div>
        </section>
    <?php endif; ?>

</div>