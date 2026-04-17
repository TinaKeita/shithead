<!doctype html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shithead — Sākt spēli</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #050811;
            --surface: rgba(15, 23, 42, 0.92);
            --surface-soft: rgba(15, 23, 42, 0.70);
            --accent: #2dd4bf;
            --accent-strong: #14b8a6;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --border: rgba(148, 163, 184, 0.22);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, rgba(45, 212, 191, 0.15), transparent 20%),
                        linear-gradient(180deg, #020617 0%, #081024 55%, #04080f 100%);
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(56, 189, 248, 0.14), transparent 18%),
                        radial-gradient(circle at 15% 15%, rgba(45, 212, 191, 0.14), transparent 16%);
            pointer-events: none;
            z-index: -1;
        }

        .page {
            max-width: 980px;
            margin: 0 auto;
            padding: 32px 24px 40px;
            display: grid;
            gap: 28px;
        }

        .hero {
            background: rgba(10, 18, 34, 0.95);
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            padding: 38px 34px;
            display: grid;
            gap: 22px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(3rem, 5vw, 4.3rem);
            letter-spacing: -0.05em;
            line-height: 0.96;
        }

        .hero p {
            margin: 0;
            max-width: 770px;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .hero .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .button,
        .cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 700;
            padding: 1rem 1.45rem;
        }

        .button {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #020617;
        }

        .cta {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            border-color: var(--border);
        }

        .info-panel {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 24px;
            display: grid;
            gap: 12px;
        }

        .card h2 {
            margin: 0;
            color: var(--accent);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .card p {
            margin: 0;
            color: var(--text);
            line-height: 1.7;
        }

        .cards-preview {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cards-preview img {
            width: 100px;
            border-radius: 16px;
            box-shadow: 0 22px 42px rgba(0, 0, 0, 0.24);
            transform: rotate(-6deg);
        }

        .cards-preview img:nth-child(2) {
            transform: translateY(10px) rotate(8deg);
        }

        .cards-preview img:nth-child(3) {
            transform: translateY(-8px) rotate(-2deg);
        }

        @media (max-width: 760px) {
            .hero {
                padding: 28px 22px;
            }

            .button,
            .cta {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <section class="hero">
            <div>
                <h1>Shithead</h1>
                <p>Sāc kārtīm bāzētu spēli tieši tagad. Spēlē pret datoru, atrodi pareizo kārti un uzvari pretinieku bez liekas drāmas.</p>
            </div>
            <div class="actions">
                <a href="/start/2" class="button">Sākt 2 spēlētājus</a>
                <a href="/start/3" class="cta">Sākt 3 spēlētājus</a>
            </div>
        </section>

        <div class="info-panel">
            <div class="card">
                <h2>Īsumā</h2>
                <p>Spēle ir balstīta uz klasisko "shithead" noteikumiem. Spēlē kārts no rokas vai galda, izvairies no čupas paceļšanas un izmanto 6/10 īpašības stratēģiski.</p>
            </div>
            <div class="card">
                <h2>Ērta spēle</h2>
                <p>Vienkārša vadība ar klikšķiem uz kārtīm, skaidrs dizains un viegli saprotama informācija par gājienu, kaudzi un redzamajām/slēptajām kārtīm.</p>
            </div>
            <div class="card">
                <h2>Vizuāls stils</h2>
                <p>Moderns tumšais dizains ar spilgtām pogām, sevišķi skaistiem kāršu spaiņiem un pietiekami atvērtām sadaļām, lai viss būtu viegli pārskatāms.</p>
            </div>
        </div>

        <div class="cards-preview">
            <img src="{{ asset('cards/hearts_6.png') }}" alt="Kārts 6">
            <img src="{{ asset('cards/spades_10.png') }}" alt="Kārts 10">
            <img src="{{ asset('cards/clubs_9.png') }}" alt="Kārts 9">
        </div>
    </div>
</body>
</html>
