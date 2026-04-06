<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OnBoarding | Grupo LeveMente</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* =============================================
           LeveMente OnBoarding — Design Exclusivo
           Cores não afetam o restante do TrainUp
        ============================================= */
        :root {
            --lm-primary: #3D7A5E;
            --lm-primary-light: #5A9E7C;
            --lm-primary-dark: #2A5A42;
            --lm-primary-50: #F0FAF5;
            --lm-primary-100: #D8F0E4;
            --lm-accent: #F4A261;
            --lm-accent-light: #F9C291;
            --lm-accent-dark: #E07A35;
            --lm-gold: #E9C46A;
            --lm-gold-dark: #C9A040;
            --lm-bg: #F5FAF7;
            --lm-bg-card: #FFFFFF;
            --lm-text: #1A3329;
            --lm-text-secondary: #4A6358;
            --lm-text-muted: #7A9B8A;
            --lm-border: #D4E8DC;
            --lm-success: #2E7D52;
            --lm-error: #C0392B;
            --lm-shadow: 0 4px 16px rgba(61, 122, 94, 0.12);
            --lm-shadow-lg: 0 12px 40px rgba(61, 122, 94, 0.18);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--lm-bg);
            color: var(--lm-text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== ANIMAÇÕES ===== */
        @keyframes lmFadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes lmSlideRight {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes lmBounceIn {
            0%   { opacity: 0; transform: scale(0.5); }
            60%  { opacity: 1; transform: scale(1.08); }
            80%  { transform: scale(0.96); }
            100% { transform: scale(1); }
        }
        @keyframes lmFloat {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-8px); }
        }
        @keyframes lmPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(61, 122, 94, 0.4); }
            50%       { box-shadow: 0 0 0 12px rgba(61, 122, 94, 0); }
        }
        @keyframes lmShake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-6px); }
            20%, 40%, 60%, 80%      { transform: translateX(6px); }
        }
        @keyframes lmConfetti {
            0%   { opacity: 1; transform: translateY(0) rotate(0deg); }
            100% { opacity: 0; transform: translateY(-120px) rotate(720deg); }
        }
        @keyframes lmProgressFill {
            from { width: 0; }
        }
        @keyframes lmLeafSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes lmHeartBeat {
            0%, 100% { transform: scale(1); }
            25%       { transform: scale(1.15); }
            50%       { transform: scale(1); }
            75%       { transform: scale(1.08); }
        }
        @keyframes lmTypewriter {
            from { width: 0; }
            to   { width: 100%; }
        }
        @keyframes lmGlow {
            0%, 100% { text-shadow: 0 0 8px rgba(244, 162, 97, 0.3); }
            50%       { text-shadow: 0 0 20px rgba(244, 162, 97, 0.8); }
        }
        @keyframes lmTimelinePop {
            0%   { opacity: 0; transform: scale(0.7) translateX(-20px); }
            100% { opacity: 1; transform: scale(1) translateX(0); }
        }
        @keyframes lmFlipIn {
            from { opacity: 0; transform: rotateY(-90deg); }
            to   { opacity: 1; transform: rotateY(0deg); }
        }

        .animate-fade  { animation: lmFadeIn 0.5s ease-out forwards; }
        .animate-slide { animation: lmSlideRight 0.5s ease-out forwards; }
        .animate-pop   { animation: lmBounceIn 0.6s ease-out forwards; }
        .animate-float { animation: lmFloat 3s ease-in-out infinite; }
        .animate-pulse-lm { animation: lmPulse 2s infinite; }
        .animate-shake-lm { animation: lmShake 0.5s ease-in-out; }
        .animate-heartbeat { animation: lmHeartBeat 1.2s ease-in-out infinite; }
        .animate-glow { animation: lmGlow 2s ease-in-out infinite; }

        /* ===== HEADER / TOPBAR ===== */
        .lm-header {
            background: white;
            border-bottom: 1px solid var(--lm-border);
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(61,122,94,0.08);
        }
        .lm-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .lm-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .lm-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--lm-primary-dark);
        }
        .lm-logo-text span { color: var(--lm-accent); }

        /* ===== PROGRESS BAR ===== */
        .lm-progress-track {
            height: 6px;
            background: var(--lm-primary-100);
            border-radius: 999px;
            overflow: hidden;
            width: 200px;
        }
        .lm-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--lm-primary), var(--lm-primary-light), var(--lm-accent));
            border-radius: 999px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===== STEP PILLS ===== */
        .lm-steps {
            display: flex;
            gap: 0.375rem;
            align-items: center;
        }
        .lm-step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--lm-primary-100);
            transition: all 0.3s;
        }
        .lm-step-dot.done { background: var(--lm-primary); width: 20px; border-radius: 4px; }
        .lm-step-dot.current { background: var(--lm-accent); width: 24px; border-radius: 4px; animation: lmPulse 2s infinite; }

        /* ===== MAIN CONTENT ===== */
        .lm-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        /* ===== CARD ===== */
        .lm-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--lm-shadow);
            border: 1px solid var(--lm-border);
            padding: 2.5rem;
            margin-bottom: 1.5rem;
        }

        /* ===== WELCOME SCREEN ===== */
        .lm-welcome-hero {
            background: linear-gradient(135deg, var(--lm-primary-dark) 0%, var(--lm-primary) 50%, var(--lm-primary-light) 100%);
            border-radius: 24px;
            padding: 3.5rem 2.5rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .lm-welcome-hero::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .lm-welcome-hero::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -30px;
            width: 250px; height: 250px;
            background: rgba(244, 162, 97, 0.1);
            border-radius: 50%;
        }
        .lm-welcome-emoji {
            font-size: 4rem;
            display: block;
            margin-bottom: 1rem;
        }
        .lm-welcome-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        .lm-welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.85;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .lm-welcome-lema {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-style: italic;
            font-size: 1rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(4px);
        }
        .lm-welcome-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .lm-welcome-stat {
            text-align: center;
        }
        .lm-welcome-stat-num {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--lm-gold);
        }
        .lm-welcome-stat-label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ===== BUTTONS ===== */
        .lm-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .lm-btn:active { transform: scale(0.97); }
        .lm-btn-primary {
            background: var(--lm-accent);
            color: white;
            box-shadow: 0 4px 12px rgba(244, 162, 97, 0.35);
        }
        .lm-btn-primary:hover { background: var(--lm-accent-dark); box-shadow: 0 6px 20px rgba(244, 162, 97, 0.45); transform: translateY(-1px); }
        .lm-btn-green {
            background: var(--lm-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(61, 122, 94, 0.3);
        }
        .lm-btn-green:hover { background: var(--lm-primary-dark); transform: translateY(-1px); }
        .lm-btn-outline {
            background: transparent;
            color: var(--lm-primary);
            border: 2px solid var(--lm-primary);
        }
        .lm-btn-outline:hover { background: var(--lm-primary-50); }
        .lm-btn-lg { padding: 1rem 2.5rem; font-size: 1.05rem; border-radius: 14px; }
        .lm-btn-sm { padding: 0.5rem 1.25rem; font-size: 0.85rem; }

        /* ===== NAV BUTTONS ===== */
        .lm-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--lm-border);
        }

        /* ===== SECTION TITLE ===== */
        .lm-section-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: var(--lm-primary-50);
            color: var(--lm-primary);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.375rem 0.875rem;
            border-radius: 999px;
            margin-bottom: 1rem;
            border: 1px solid var(--lm-primary-100);
        }
        .lm-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--lm-primary-dark);
            margin-bottom: 0.5rem;
            line-height: 1.25;
        }
        .lm-section-desc {
            color: var(--lm-text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.75rem;
        }

        /* ===== FILOSOFIA CARD ===== */
        .lm-filosofia {
            background: linear-gradient(135deg, var(--lm-primary-50), white);
            border: 2px solid var(--lm-primary-100);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .lm-filosofia::before {
            content: '"';
            position: absolute;
            top: -0.5rem;
            left: 1.25rem;
            font-size: 4rem;
            color: var(--lm-primary-light);
            font-family: 'Playfair Display', serif;
            line-height: 1;
            opacity: 0.5;
        }
        .lm-filosofia-text {
            font-style: italic;
            font-size: 1.125rem;
            color: var(--lm-primary-dark);
            font-weight: 600;
            line-height: 1.6;
            padding-left: 0.5rem;
        }

        /* ===== LEMA DESTAQUE ===== */
        .lm-lema {
            background: linear-gradient(135deg, var(--lm-accent), var(--lm-accent-dark));
            color: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            text-align: center;
            font-style: italic;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.5;
            box-shadow: 0 4px 16px rgba(244, 162, 97, 0.3);
        }
        .lm-lema-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.8;
            font-style: normal;
            font-weight: 700;
            margin-bottom: 0.375rem;
        }

        /* ===== FUNDADORAS ===== */
        .lm-founders {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .lm-founder-card {
            background: var(--lm-primary-50);
            border: 1px solid var(--lm-primary-100);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        .lm-founder-card:hover { transform: translateY(-3px); box-shadow: var(--lm-shadow); }
        .lm-founder-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        .lm-founder-avatar.liz { background: linear-gradient(135deg, #A8E6CF, #3D7A5E); }
        .lm-founder-avatar.priu { background: linear-gradient(135deg, #FFD3A5, #F4A261); }
        .lm-founder-name { font-weight: 700; color: var(--lm-primary-dark); margin-bottom: 0.25rem; }
        .lm-founder-role { font-size: 0.8rem; color: var(--lm-text-muted); }

        /* ===== TIMELINE ===== */
        .lm-timeline {
            position: relative;
            padding-left: 2rem;
        }
        .lm-timeline::before {
            content: '';
            position: absolute;
            left: 0.875rem;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, var(--lm-primary), var(--lm-accent));
            border-radius: 999px;
        }
        .lm-timeline-item {
            position: relative;
            padding-bottom: 2rem;
            cursor: pointer;
        }
        .lm-timeline-item:last-child { padding-bottom: 0; }
        .lm-timeline-dot {
            position: absolute;
            left: -2rem;
            top: 0.25rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--lm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(61,122,94,0.2);
        }
        .lm-timeline-item:hover .lm-timeline-dot,
        .lm-timeline-item.active .lm-timeline-dot {
            background: var(--lm-primary);
            transform: scale(1.2);
            box-shadow: 0 0 0 6px rgba(61,122,94,0.15);
        }
        .lm-timeline-year {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--lm-accent);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.25rem;
        }
        .lm-timeline-title {
            font-weight: 700;
            color: var(--lm-primary-dark);
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .lm-timeline-desc {
            font-size: 0.875rem;
            color: var(--lm-text-secondary);
            line-height: 1.6;
        }
        .lm-timeline-content {
            background: var(--lm-primary-50);
            border: 1px solid var(--lm-primary-100);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 0.5rem;
            display: none;
            animation: lmFadeIn 0.3s ease-out;
        }
        .lm-timeline-item.active .lm-timeline-content { display: block; }

        /* ===== MVV CARDS ===== */
        .lm-mvv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .lm-mvv-card {
            background: white;
            border: 2px solid var(--lm-border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .lm-mvv-card:hover { border-color: var(--lm-primary); transform: translateY(-4px); box-shadow: var(--lm-shadow); }
        .lm-mvv-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        .lm-mvv-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--lm-text-muted);
            font-weight: 700;
            margin-bottom: 0.375rem;
        }
        .lm-mvv-title {
            font-weight: 700;
            color: var(--lm-primary-dark);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .lm-mvv-text {
            font-size: 0.8rem;
            color: var(--lm-text-secondary);
            line-height: 1.5;
        }

        /* ===== S4 GRID ===== */
        .lm-s4-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .lm-s4-card {
            background: linear-gradient(135deg, var(--lm-primary-50), white);
            border: 1px solid var(--lm-primary-100);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }
        .lm-s4-card:hover { border-color: var(--lm-primary); box-shadow: var(--lm-shadow); transform: translateX(4px); }
        .lm-s4-letter {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-family: 'Playfair Display', serif;
        }
        .lm-s4-info-label { font-size: 0.75rem; color: var(--lm-text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .lm-s4-info-value { font-weight: 700; color: var(--lm-primary-dark); }

        /* ===== VALORES ===== */
        .lm-valores-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .lm-valor-card {
            background: white;
            border: 1px solid var(--lm-border);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            transition: all 0.3s;
        }
        .lm-valor-card:hover { border-color: var(--lm-accent); box-shadow: 0 4px 16px rgba(244,162,97,0.1); transform: translateY(-2px); }
        .lm-valor-emoji { font-size: 1.75rem; flex-shrink: 0; }
        .lm-valor-title { font-weight: 700; color: var(--lm-primary-dark); margin-bottom: 0.25rem; }
        .lm-valor-desc { font-size: 0.82rem; color: var(--lm-text-secondary); line-height: 1.5; }

        /* ===== AREAS DE ATUAÇÃO - TABS ===== */
        .lm-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--lm-border);
            padding-bottom: 0;
            overflow-x: auto;
        }
        .lm-tab {
            padding: 0.625rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--lm-text-muted);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            border-radius: 8px 8px 0 0;
        }
        .lm-tab:hover { color: var(--lm-primary); background: var(--lm-primary-50); }
        .lm-tab.active { color: var(--lm-primary); border-bottom-color: var(--lm-primary); background: var(--lm-primary-50); }
        .lm-tab-content { display: none; animation: lmFadeIn 0.3s ease-out; }
        .lm-tab-content.active { display: block; }

        /* ===== AREA CARDS ===== */
        .lm-area-hero {
            background: linear-gradient(135deg, var(--lm-primary-dark), var(--lm-primary));
            color: white;
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .lm-area-icon-big {
            font-size: 3.5rem;
            flex-shrink: 0;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        .lm-area-hero-title { font-family: 'Playfair Display', serif; font-size: 1.375rem; font-weight: 700; margin-bottom: 0.375rem; }
        .lm-area-hero-desc { opacity: 0.85; font-size: 0.875rem; line-height: 1.6; }
        .lm-service-list { list-style: none; display: grid; gap: 0.75rem; }
        .lm-service-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            background: var(--lm-primary-50);
            border: 1px solid var(--lm-primary-100);
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 0.9rem;
            color: var(--lm-text);
            transition: all 0.2s;
        }
        .lm-service-item:hover { border-color: var(--lm-primary); background: var(--lm-primary-100); transform: translateX(4px); }
        .lm-service-bullet {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--lm-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ===== OPERAÇÃO CARNEIROS ===== */
        .lm-op-header {
            background: linear-gradient(135deg, #1A2A1E, #2D4A3E);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .lm-op-header::before {
            content: '🎆';
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.2;
        }
        .lm-op-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: rgba(244, 162, 97, 0.2);
            border: 1px solid rgba(244, 162, 97, 0.4);
            color: var(--lm-accent-light);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            margin-bottom: 0.75rem;
        }
        .lm-op-title { font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 700; margin-bottom: 0.375rem; }
        .lm-op-period { font-size: 0.875rem; opacity: 0.75; margin-bottom: 0.5rem; }
        .lm-op-focus { font-size: 0.95rem; opacity: 0.9; }
        .lm-op-focus strong { color: var(--lm-gold); }

        .lm-team-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .lm-team-card {
            background: white;
            border: 1px solid var(--lm-border);
            border-radius: 14px;
            padding: 1.25rem;
        }
        .lm-team-role {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--lm-primary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .lm-team-role::before { content: ''; display: block; width: 12px; height: 3px; background: var(--lm-primary); border-radius: 2px; }
        .lm-team-members { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .lm-member-chip {
            background: var(--lm-primary-50);
            border: 1px solid var(--lm-primary-100);
            color: var(--lm-primary-dark);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
        }
        .lm-turno-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem; }
        .lm-turno {
            background: linear-gradient(135deg, var(--lm-primary-50), white);
            border: 1px solid var(--lm-primary-100);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }
        .lm-turno-icon { font-size: 1.75rem; margin-bottom: 0.375rem; }
        .lm-turno-time { font-weight: 800; color: var(--lm-primary-dark); font-size: 1rem; }
        .lm-turno-label { font-size: 0.75rem; color: var(--lm-text-muted); }

        /* ===== ATIVIDADE INTERATIVA ===== */
        .lm-activity {
            background: linear-gradient(135deg, var(--lm-primary-50), white);
            border: 2px solid var(--lm-primary-100);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .lm-activity-title {
            font-weight: 700;
            color: var(--lm-primary-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .lm-drag-items { display: flex; flex-wrap: wrap; gap: 0.625rem; margin-bottom: 1rem; }
        .lm-drag-chip {
            background: white;
            border: 2px solid var(--lm-border);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }
        .lm-drag-chip:hover { border-color: var(--lm-primary); background: var(--lm-primary-50); transform: translateY(-2px); }
        .lm-drag-chip.selected { background: var(--lm-primary); color: white; border-color: var(--lm-primary); }
        .lm-drag-chip.correct { background: #E8F5E9; color: #2E7D52; border-color: #2E7D52; }
        .lm-drag-chip.wrong { background: #FFEBEE; color: #C0392B; border-color: #C0392B; animation: lmShake 0.5s ease-in-out; }

        /* ===== QUIZ ===== */
        .lm-quiz-header {
            background: linear-gradient(135deg, var(--lm-primary-dark), var(--lm-primary));
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .lm-quiz-title { font-size: 1.125rem; font-weight: 700; }
        .lm-quiz-counter { font-size: 0.875rem; opacity: 0.8; margin-top: 0.25rem; }
        .lm-hearts { display: flex; gap: 0.25rem; }
        .lm-heart { font-size: 1.375rem; transition: all 0.3s; }
        .lm-heart.lost { filter: grayscale(1); opacity: 0.4; transform: scale(0.8); }

        .lm-question-card {
            background: white;
            border: 2px solid var(--lm-border);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1rem;
        }
        .lm-question-num {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--lm-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }
        .lm-question-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--lm-primary-dark);
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }
        .lm-options { display: grid; gap: 0.75rem; }
        .lm-option {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.875rem 1.125rem;
            border: 2px solid var(--lm-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .lm-option:hover:not(.answered) { border-color: var(--lm-primary); background: var(--lm-primary-50); }
        .lm-option.selected { border-color: var(--lm-primary); background: var(--lm-primary-50); }
        .lm-option.correct  { border-color: #2E7D52; background: #E8F5E9; color: #1A4731; }
        .lm-option.wrong    { border-color: #C0392B; background: #FFEBEE; color: #7B241C; animation: lmShake 0.5s ease-in-out; }
        .lm-option-letter {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--lm-primary-100);
            color: var(--lm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .lm-option.correct .lm-option-letter  { background: #2E7D52; color: white; }
        .lm-option.wrong .lm-option-letter    { background: #C0392B; color: white; }
        .lm-option.selected .lm-option-letter { background: var(--lm-primary); color: white; }

        .lm-feedback {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }
        .lm-feedback.correct { background: #E8F5E9; color: #1A4731; border: 1px solid #A5D6A7; }
        .lm-feedback.wrong   { background: #FFEBEE; color: #7B241C; border: 1px solid #EF9A9A; }

        .lm-quiz-progress {
            height: 8px;
            background: var(--lm-primary-100);
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .lm-quiz-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--lm-primary), var(--lm-accent));
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        /* ===== RESULTADO ===== */
        .lm-result-hero {
            text-align: center;
            padding: 2.5rem 1.5rem;
        }
        .lm-result-emoji { font-size: 5rem; margin-bottom: 1rem; animation: lmBounceIn 0.8s ease-out; display: block; }
        .lm-result-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; color: var(--lm-primary-dark); margin-bottom: 0.5rem; }
        .lm-result-subtitle { color: var(--lm-text-secondary); margin-bottom: 2rem; font-size: 1rem; }
        .lm-score-ring {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(var(--lm-primary) calc(var(--score) * 1%), var(--lm-primary-100) 0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            box-shadow: var(--lm-shadow-lg);
        }
        .lm-score-ring::before {
            content: '';
            position: absolute;
            inset: 12px;
            background: white;
            border-radius: 50%;
        }
        .lm-score-num {
            position: relative;
            font-size: 2rem;
            font-weight: 800;
            color: var(--lm-primary-dark);
            z-index: 1;
        }
        .lm-result-stats {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .lm-result-stat {
            text-align: center;
            padding: 0.875rem 1.25rem;
            background: var(--lm-primary-50);
            border: 1px solid var(--lm-primary-100);
            border-radius: 12px;
            min-width: 90px;
        }
        .lm-result-stat-num { font-size: 1.5rem; font-weight: 800; color: var(--lm-primary-dark); }
        .lm-result-stat-label { font-size: 0.7rem; color: var(--lm-text-muted); text-transform: uppercase; letter-spacing: 0.05em; }

        /* ===== CERTIFICADO ===== */
        .lm-cert {
            border: 3px solid var(--lm-gold);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            background: linear-gradient(135deg, #FFFDF0, white);
            position: relative;
            overflow: hidden;
        }
        .lm-cert::before, .lm-cert::after {
            content: '🌿';
            position: absolute;
            font-size: 6rem;
            opacity: 0.06;
        }
        .lm-cert::before { top: -1rem; left: -1rem; }
        .lm-cert::after { bottom: -1rem; right: -1rem; }
        .lm-cert-badge {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--lm-gold), var(--lm-gold-dark));
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 4px 20px rgba(233, 196, 106, 0.4);
            animation: lmHeartBeat 2s ease-in-out infinite;
        }
        .lm-cert-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--lm-gold-dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .lm-cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--lm-primary-dark);
            margin-bottom: 0.5rem;
        }
        .lm-cert-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--lm-primary);
            border-bottom: 2px solid var(--lm-gold);
            display: inline-block;
            padding-bottom: 0.375rem;
            margin: 1rem 0;
        }
        .lm-cert-course {
            font-weight: 600;
            color: var(--lm-text);
            margin-bottom: 0.5rem;
        }
        .lm-cert-date { font-size: 0.85rem; color: var(--lm-text-muted); margin-bottom: 1.5rem; }
        .lm-cert-seal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--lm-text-muted);
        }

        /* ===== XP POPUP ===== */
        .lm-xp-popup {
            position: fixed;
            top: 5rem;
            right: 1.5rem;
            background: linear-gradient(135deg, var(--lm-gold), var(--lm-gold-dark));
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(233,196,106,0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            pointer-events: none;
        }

        /* ===== CONFETTI PARTICLES ===== */
        .lm-confetti-particle {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            pointer-events: none;
            z-index: 9998;
            animation: lmConfetti 1.5s ease-out forwards;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .lm-mvv-grid, .lm-founders, .lm-s4-grid, .lm-valores-grid, .lm-team-grid, .lm-turno-grid {
                grid-template-columns: 1fr;
            }
            .lm-welcome-stats { gap: 1rem; }
            .lm-welcome-title { font-size: 1.625rem; }
            .lm-section-title { font-size: 1.5rem; }
            .lm-card { padding: 1.5rem; }
            .lm-cert { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body x-data="onboarding()" x-init="init()">

    <!-- XP Popup -->
    <div x-show="showXp" x-transition class="lm-xp-popup">
        ⭐ <span x-text="'+' + xpEarned + ' XP'"></span>
    </div>

    <!-- HEADER -->
    <header class="lm-header">
        <div class="lm-logo">
            <div class="lm-logo-icon">🌿</div>
            <div>
                <div class="lm-logo-text">Leve<span>'</span>Mente</div>
                <div style="font-size:0.7rem;color:var(--lm-text-muted);">OnBoarding Oficial</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:1.5rem;">
            <div style="text-align:right;display:none;" id="progress-label">
                <div style="font-size:0.75rem;color:var(--lm-text-muted);margin-bottom:0.25rem;" x-text="Math.round(progress) + '%' + ' concluído'"></div>
                <div class="lm-progress-track">
                    <div class="lm-progress-fill" :style="'width:' + progress + '%'"></div>
                </div>
            </div>
            <div class="lm-steps">
                <template x-for="(s, i) in totalSteps" :key="i">
                    <div class="lm-step-dot"
                         :class="{ 'done': i < currentStep, 'current': i === currentStep }">
                    </div>
                </template>
            </div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="lm-container">

        <!-- ========== STEP 0: BOAS-VINDAS ========== -->
        <div x-show="currentStep === 0" x-transition class="animate-fade">
            <div class="lm-welcome-hero">
                <span class="lm-welcome-emoji animate-float">🌿</span>
                <h1 class="lm-welcome-title">Bem-vindo(a) ao<br>Grupo LeveMente!</h1>
                <p class="lm-welcome-subtitle">
                    Você está começando sua jornada em um lugar onde a alimentação é<br>um ato de amor, cuidado e consciência.
                </p>
                <div class="lm-welcome-lema">
                    🌱 "Primeiro a gente muda a alimentação e depois<br>a alimentação muda a gente!"
                </div>
                <div class="lm-welcome-stats">
                    <div class="lm-welcome-stat">
                        <div class="lm-welcome-stat-num">5</div>
                        <div class="lm-welcome-stat-label">Módulos</div>
                    </div>
                    <div class="lm-welcome-stat">
                        <div class="lm-welcome-stat-num">~20</div>
                        <div class="lm-welcome-stat-label">Minutos</div>
                    </div>
                    <div class="lm-welcome-stat">
                        <div class="lm-welcome-stat-num">250</div>
                        <div class="lm-welcome-stat-label">XP Total</div>
                    </div>
                </div>
                <button class="lm-btn lm-btn-primary lm-btn-lg" @click="nextStep()">
                    🚀 Começar OnBoarding
                </button>
            </div>

            <div style="margin-top:1.5rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                <div class="lm-card" style="text-align:center;padding:1.25rem;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">📚</div>
                    <div style="font-weight:700;color:var(--lm-primary-dark);font-size:0.9rem;">Conteúdo Rico</div>
                    <div style="font-size:0.78rem;color:var(--lm-text-muted);margin-top:0.25rem;">Historia, valores e estrutura da empresa</div>
                </div>
                <div class="lm-card" style="text-align:center;padding:1.25rem;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">🎯</div>
                    <div style="font-weight:700;color:var(--lm-primary-dark);font-size:0.9rem;">Quiz Interativo</div>
                    <div style="font-size:0.78rem;color:var(--lm-text-muted);margin-top:0.25rem;">Teste seus conhecimentos ao final</div>
                </div>
                <div class="lm-card" style="text-align:center;padding:1.25rem;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">🏆</div>
                    <div style="font-weight:700;color:var(--lm-primary-dark);font-size:0.9rem;">Certificado</div>
                    <div style="font-size:0.78rem;color:var(--lm-text-muted);margin-top:0.25rem;">Receba ao concluir com aprovação</div>
                </div>
            </div>
        </div>

        <!-- ========== STEP 1: ESSÊNCIA E PROPÓSITO ========== -->
        <div x-show="currentStep === 1" x-transition class="animate-fade">
            <div class="lm-section-tag">📖 Módulo 1 de 5</div>
            <h2 class="lm-section-title">Essência e Propósito</h2>
            <p class="lm-section-desc">
                Conheça as raízes e os valores que movem o Grupo LeveMente todos os dias. Entender de onde viemos é o primeiro passo para construir juntos.
            </p>

            <!-- Filosofia -->
            <div class="lm-card">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                    <div style="width:40px;height:40px;background:var(--lm-primary-50);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;border:1px solid var(--lm-primary-100);">🌱</div>
                    <div>
                        <div style="font-weight:700;color:var(--lm-primary-dark);">Nossa Origem</div>
                        <div style="font-size:0.8rem;color:var(--lm-text-muted);">Como tudo começou</div>
                    </div>
                </div>
                <p style="color:var(--lm-text-secondary);line-height:1.8;font-size:0.95rem;margin-bottom:1.25rem;">
                    O <strong style="color:var(--lm-primary-dark);">Grupo LeveMente</strong> nasceu da união de duas amigas — <strong>Liz Galvão</strong> e <strong>Priu Lucena</strong> — com o propósito de estimular uma alimentação <em>saudável, inclusiva e saborosa</em>.
                </p>

                <div class="lm-filosofia">
                    <p class="lm-filosofia-text">A alimentação saudável é um manifesto de autocuidado.</p>
                </div>

                <div class="lm-lema">
                    <div class="lm-lema-label">✨ Nosso Lema</div>
                    "Primeiro a gente muda a alimentação e depois a alimentação muda a gente!"
                </div>
            </div>

            <!-- Fundadoras -->
            <div class="lm-card">
                <div style="font-weight:700;color:var(--lm-primary-dark);margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                    👩‍🤝‍👩 As Fundadoras
                </div>
                <div class="lm-founders">
                    <div class="lm-founder-card">
                        <div class="lm-founder-avatar liz">👩‍🍳</div>
                        <div class="lm-founder-name">Liz Galvão</div>
                        <div class="lm-founder-role">Co-fundadora</div>
                        <div style="font-size:0.78rem;color:var(--lm-text-secondary);margin-top:0.5rem;line-height:1.5;">Visionária por uma alimentação que nutre corpo e alma com leveza e consciência.</div>
                    </div>
                    <div class="lm-founder-card">
                        <div class="lm-founder-avatar priu">🌸</div>
                        <div class="lm-founder-name">Priu Lucena</div>
                        <div class="lm-founder-role">Co-fundadora</div>
                        <div style="font-size:0.78rem;color:var(--lm-text-secondary);margin-top:0.5rem;line-height:1.5;">Apaixonada por criar experiências gastronômicas inclusivas e cheias de sabor.</div>
                    </div>
                </div>
            </div>

            <!-- Atividade -->
            <div class="lm-activity">
                <div class="lm-activity-title">🎯 Atividade Rápida — Selecione as palavras corretas</div>
                <p style="font-size:0.875rem;color:var(--lm-text-secondary);margin-bottom:0.875rem;">Quais palavras descrevem a proposta do Grupo LeveMente? Selecione todas que se aplicam:</p>
                <div class="lm-drag-items">
                    <template x-for="word in activityWords" :key="word.text">
                        <div class="lm-drag-chip"
                             :class="{ 'selected': word.selected, 'correct': activityChecked && word.correct && word.selected, 'wrong': activityChecked && !word.correct && word.selected }"
                             @click="!activityChecked && (word.selected = !word.selected)">
                            <span x-text="word.text"></span>
                        </div>
                    </template>
                </div>
                <div style="display:flex;gap:0.75rem;align-items:center;">
                    <button class="lm-btn lm-btn-green lm-btn-sm" @click="checkActivity()" x-show="!activityChecked">
                        ✓ Verificar
                    </button>
                    <div x-show="activityChecked && activityCorrect" class="lm-feedback correct" style="margin-top:0;">
                        ✅ Muito bem! Saudável, Inclusiva e Saborosa são os pilares do Grupo LeveMente!
                    </div>
                    <div x-show="activityChecked && !activityCorrect" class="lm-feedback wrong" style="margin-top:0;">
                        💡 Tente novamente! Pense nos 3 pilares: Saudável, Inclusiva e Saborosa.
                        <button class="lm-btn lm-btn-sm" style="background:rgba(255,255,255,0.3);color:inherit;margin-left:0.5rem;" @click="resetActivity()">Tentar de novo</button>
                    </div>
                </div>
            </div>

            <div class="lm-nav">
                <div></div>
                <button class="lm-btn lm-btn-primary" @click="nextStep()">
                    Próximo: Linha do Tempo →
                </button>
            </div>
        </div>

        <!-- ========== STEP 2: LINHA DO TEMPO ========== -->
        <div x-show="currentStep === 2" x-transition class="animate-fade">
            <div class="lm-section-tag">🕐 Módulo 2 de 5</div>
            <h2 class="lm-section-title">Nossa Jornada</h2>
            <p class="lm-section-desc">
                Uma trajetória de 10 anos de crescimento, reinvenção e muito propósito. Clique em cada marco para saber mais!
            </p>

            <div class="lm-card">
                <div class="lm-timeline" x-data="{ activeItem: null }">

                    <div class="lm-timeline-item" :class="{ active: activeItem === 0 }" @click="activeItem = activeItem === 0 ? null : 0">
                        <div class="lm-timeline-dot">🥗</div>
                        <div class="lm-timeline-year">2015</div>
                        <div class="lm-timeline-title">Leve'mente Fit Food</div>
                        <div class="lm-timeline-desc">O começo da história com congelados fit saudáveis.</div>
                        <div class="lm-timeline-content">
                            <p style="font-size:0.875rem;color:var(--lm-text-secondary);line-height:1.7;">
                                🧊 Tudo começou com <strong>Congelados Fit</strong> — refeições saudáveis e saborosas para o dia a dia. A ideia era simples: facilitar a alimentação consciente de quem não tem tempo, mas tem vontade de cuidar de si.
                            </p>
                            <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                                <span style="background:var(--lm-accent-light);color:var(--lm-accent-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🥡 Congelados</span>
                                <span style="background:var(--lm-accent-light);color:var(--lm-accent-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🚚 Delivery</span>
                                <span style="background:var(--lm-accent-light);color:var(--lm-accent-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">💚 Fit Food</span>
                            </div>
                        </div>
                    </div>

                    <div class="lm-timeline-item" :class="{ active: activeItem === 1 }" @click="activeItem = activeItem === 1 ? null : 1">
                        <div class="lm-timeline-dot">👩‍🍳</div>
                        <div class="lm-timeline-year">2019</div>
                        <div class="lm-timeline-title">Leve'mente na Cozinha</div>
                        <div class="lm-timeline-desc">Expansão para cursos, conteúdo e educação gastronômica.</div>
                        <div class="lm-timeline-content">
                            <p style="font-size:0.875rem;color:var(--lm-text-secondary);line-height:1.7;">
                                📱 A marca evoluiu para o mundo digital com <strong>Cursos Online</strong>, E-books e Workshops de gastronomia funcional. O conhecimento passou a ser o produto principal — democratizando a alimentação saudável.
                            </p>
                            <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                                <span style="background:var(--lm-primary-100);color:var(--lm-primary-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">📚 Cursos Online</span>
                                <span style="background:var(--lm-primary-100);color:var(--lm-primary-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">📖 E-books</span>
                                <span style="background:var(--lm-primary-100);color:var(--lm-primary-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🎓 Workshops</span>
                            </div>
                        </div>
                    </div>

                    <div class="lm-timeline-item" :class="{ active: activeItem === 2 }" @click="activeItem = activeItem === 2 ? null : 2">
                        <div class="lm-timeline-dot">☕</div>
                        <div class="lm-timeline-year">2023</div>
                        <div class="lm-timeline-title">Leve'mente Café</div>
                        <div class="lm-timeline-desc">Um espaço físico para viver a experiência LeveMente.</div>
                        <div class="lm-timeline-content">
                            <p style="font-size:0.875rem;color:var(--lm-text-secondary);line-height:1.7;">
                                🏡 O sonho ganhou endereço! O <strong>Leve'mente Café</strong> trouxe a experiência para o mundo real, com cardápio à la carte, produtos To Go, deliveries via iFood e WhatsApp, além de encomendas especiais e eventos.
                            </p>
                            <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                                <span style="background:#FFF3E0;color:#E65100;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🏠 Espaço Físico</span>
                                <span style="background:#FFF3E0;color:#E65100;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">📦 iFood</span>
                                <span style="background:#FFF3E0;color:#E65100;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🎂 Encomendas</span>
                            </div>
                        </div>
                    </div>

                    <div class="lm-timeline-item" :class="{ active: activeItem === 3 }" @click="activeItem = activeItem === 3 ? null : 3">
                        <div class="lm-timeline-dot">✨</div>
                        <div class="lm-timeline-year">2025</div>
                        <div class="lm-timeline-title">Grupo LeveMente</div>
                        <div class="lm-timeline-desc">Consolidação como grupo com múltiplas frentes de atuação.</div>
                        <div class="lm-timeline-content">
                            <p style="font-size:0.875rem;color:var(--lm-text-secondary);line-height:1.7;">
                                🌟 Hoje somos o <strong>Grupo LeveMente</strong> — com braços no varejo, educação, eventos corporativos e espaço físico. Uma marca consolidada que carrega 10 anos de propósito e crescimento. <strong>E você faz parte dessa história agora!</strong>
                            </p>
                            <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                                <span style="background:linear-gradient(135deg,var(--lm-primary-100),var(--lm-primary-50));color:var(--lm-primary-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🏆 Grupo Consolidado</span>
                                <span style="background:linear-gradient(135deg,var(--lm-primary-100),var(--lm-primary-50));color:var(--lm-primary-dark);padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">🌍 Múltiplas Frentes</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mini dinâmica: ordene os anos -->
            <div class="lm-activity">
                <div class="lm-activity-title">🎯 Dinâmica — Linha do Tempo</div>
                <p style="font-size:0.875rem;color:var(--lm-text-secondary);margin-bottom:0.875rem;">Em que ano o Leve'mente Café (espaço físico) foi inaugurado?</p>
                <div class="lm-drag-items">
                    <div class="lm-drag-chip" :class="cafeYear === '2019' ? (activityChecked2 ? 'wrong' : 'selected') : ''" @click="!activityChecked2 && (cafeYear = '2019')">2019</div>
                    <div class="lm-drag-chip" :class="cafeYear === '2021' ? (activityChecked2 ? 'wrong' : 'selected') : ''" @click="!activityChecked2 && (cafeYear = '2021')">2021</div>
                    <div class="lm-drag-chip" :class="cafeYear === '2023' ? (activityChecked2 ? (cafeYear === '2023' ? 'correct' : 'wrong') : 'selected') : (activityChecked2 && cafeYear !== '2023' ? 'correct' : '')" @click="!activityChecked2 && (cafeYear = '2023')">2023</div>
                    <div class="lm-drag-chip" :class="cafeYear === '2025' ? (activityChecked2 ? 'wrong' : 'selected') : ''" @click="!activityChecked2 && (cafeYear = '2025')">2025</div>
                </div>
                <button class="lm-btn lm-btn-green lm-btn-sm" @click="checkCafeYear()" x-show="!activityChecked2 && cafeYear">✓ Confirmar</button>
                <div x-show="activityChecked2 && cafeYear === '2023'" class="lm-feedback correct">✅ Correto! O Leve'mente Café abriu as portas em 2023!</div>
                <div x-show="activityChecked2 && cafeYear !== '2023'" class="lm-feedback wrong">💡 Não foi bem esse... Revise a linha do tempo e tente novamente! <button class="lm-btn lm-btn-sm" style="background:rgba(255,255,255,0.3);color:inherit;margin-left:0.5rem;" @click="activityChecked2 = false; cafeYear = ''">Tentar de novo</button></div>
            </div>

            <div class="lm-nav">
                <button class="lm-btn lm-btn-outline" @click="prevStep()">← Voltar</button>
                <button class="lm-btn lm-btn-primary" @click="nextStep()">Próximo: Missão e Valores →</button>
            </div>
        </div>

        <!-- ========== STEP 3: MISSÃO, VISÃO E VALORES ========== -->
        <div x-show="currentStep === 3" x-transition class="animate-fade">
            <div class="lm-section-tag">🌟 Módulo 3 de 5</div>
            <h2 class="lm-section-title">Missão, Visão & Valores</h2>
            <p class="lm-section-desc">
                Nossa bússola estratégica. Esses pilares guiam cada decisão, cada prato e cada interação do Grupo LeveMente.
            </p>

            <!-- MVV Cards -->
            <div class="lm-card">
                <div class="lm-mvv-grid">
                    <div class="lm-mvv-card">
                        <span class="lm-mvv-icon">🎯</span>
                        <div class="lm-mvv-label">Missão</div>
                        <div class="lm-mvv-title">Por que existimos</div>
                        <div class="lm-mvv-text">Inspirar pessoas a viverem de forma leve e consciente através de alimentos que nutrem corpo e alma.</div>
                    </div>
                    <div class="lm-mvv-card">
                        <span class="lm-mvv-icon">🔭</span>
                        <div class="lm-mvv-label">Visão</div>
                        <div class="lm-mvv-title">Para onde vamos</div>
                        <div class="lm-mvv-text">Ser referência em alimentação saudável, reconhecida pelos S4: Significado, Sensibilidade, Saúde e Sabor.</div>
                    </div>
                    <div class="lm-mvv-card">
                        <span class="lm-mvv-icon">💎</span>
                        <div class="lm-mvv-label">Valores</div>
                        <div class="lm-mvv-title">Como atuamos</div>
                        <div class="lm-mvv-text">Fazer o bem, consciência nas escolhas, amor nos detalhes e gerar bem-estar em tudo que fazemos.</div>
                    </div>
                </div>
            </div>

            <!-- S4 -->
            <div class="lm-card">
                <div style="font-weight:700;color:var(--lm-primary-dark);font-size:1.1rem;margin-bottom:0.375rem;">Os 4 S da Visão</div>
                <p style="font-size:0.875rem;color:var(--lm-text-secondary);margin-bottom:1.25rem;">Nossa visão é ser reconhecida por estes quatro pilares fundamentais:</p>
                <div class="lm-s4-grid">
                    <div class="lm-s4-card">
                        <div class="lm-s4-letter">S</div>
                        <div><div class="lm-s4-info-label">Pilar 1</div><div class="lm-s4-info-value">Significado</div></div>
                    </div>
                    <div class="lm-s4-card">
                        <div class="lm-s4-letter">S</div>
                        <div><div class="lm-s4-info-label">Pilar 2</div><div class="lm-s4-info-value">Sensibilidade</div></div>
                    </div>
                    <div class="lm-s4-card">
                        <div class="lm-s4-letter">S</div>
                        <div><div class="lm-s4-info-label">Pilar 3</div><div class="lm-s4-info-value">Saúde</div></div>
                    </div>
                    <div class="lm-s4-card">
                        <div class="lm-s4-letter">S</div>
                        <div><div class="lm-s4-info-label">Pilar 4</div><div class="lm-s4-info-value">Sabor</div></div>
                    </div>
                </div>
            </div>

            <!-- Valores -->
            <div class="lm-card">
                <div style="font-weight:700;color:var(--lm-primary-dark);font-size:1.1rem;margin-bottom:1.25rem;">💚 Nossos Valores na Prática</div>
                <div class="lm-valores-grid">
                    <div class="lm-valor-card">
                        <div class="lm-valor-emoji">🤝</div>
                        <div>
                            <div class="lm-valor-title">Fazer o Bem</div>
                            <div class="lm-valor-desc">Cada ação nossa visa impactar positivamente quem está ao redor — clientes, fornecedores e comunidade.</div>
                        </div>
                    </div>
                    <div class="lm-valor-card">
                        <div class="lm-valor-emoji">🧠</div>
                        <div>
                            <div class="lm-valor-title">Consciência</div>
                            <div class="lm-valor-desc">Escolhas intencionais em cada ingrediente, processo e relação que construímos.</div>
                        </div>
                    </div>
                    <div class="lm-valor-card">
                        <div class="lm-valor-emoji">❤️</div>
                        <div>
                            <div class="lm-valor-title">Amor nos Detalhes</div>
                            <div class="lm-valor-desc">A excelência está nos pequenos gestos — na apresentação, no atendimento, no tempero certo.</div>
                        </div>
                    </div>
                    <div class="lm-valor-card">
                        <div class="lm-valor-emoji">✨</div>
                        <div>
                            <div class="lm-valor-title">Gerar Bem-Estar</div>
                            <div class="lm-valor-desc">Nosso objetivo final é que cada pessoa saia de uma experiência LeveMente se sentindo melhor.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lm-nav">
                <button class="lm-btn lm-btn-outline" @click="prevStep()">← Voltar</button>
                <button class="lm-btn lm-btn-primary" @click="nextStep()">Próximo: Áreas de Atuação →</button>
            </div>
        </div>

        <!-- ========== STEP 4: ÁREAS DE ATUAÇÃO ========== -->
        <div x-show="currentStep === 4" x-transition class="animate-fade">
            <div class="lm-section-tag">🏢 Módulo 4 de 5</div>
            <h2 class="lm-section-title">Áreas de Atuação</h2>
            <p class="lm-section-desc">
                O Grupo LeveMente atua em três frentes complementares. Explore cada uma delas:
            </p>

            <div class="lm-card" x-data="{ activeTab: 'cafe' }">
                <div class="lm-tabs">
                    <div class="lm-tab" :class="{ active: activeTab === 'cafe' }" @click="activeTab = 'cafe'">☕ Leve'mente Café</div>
                    <div class="lm-tab" :class="{ active: activeTab === 'eventos' }" @click="activeTab = 'eventos'">🎪 Eventos & Corporativo</div>
                    <div class="lm-tab" :class="{ active: activeTab === 'cozinha' }" @click="activeTab = 'cozinha'">🎓 Na Cozinha</div>
                </div>

                <!-- Tab: Café -->
                <div class="lm-tab-content" :class="{ active: activeTab === 'cafe' }">
                    <div class="lm-area-hero">
                        <div class="lm-area-icon-big">☕</div>
                        <div>
                            <div class="lm-area-hero-title">Leve'mente Café</div>
                            <div class="lm-area-hero-desc">O coração físico da marca — um espaço onde alimentação saudável encontra experiência e acolhimento.</div>
                        </div>
                    </div>
                    <ul class="lm-service-list">
                        <li class="lm-service-item"><div class="lm-service-bullet">🍽️</div>Cardápio à la carte com opções saudáveis e saborosas</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🛍️</div>Itens "To Go" para levar a leveza no dia a dia</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">❄️</div>Produtos congelados e delivery via iFood e WhatsApp</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🎂</div>Encomendas de bolos, tortas, quiches e produtos sazonais</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🎄</div>Produtos especiais de Natal e Páscoa</li>
                    </ul>
                </div>

                <!-- Tab: Eventos -->
                <div class="lm-tab-content" :class="{ active: activeTab === 'eventos' }">
                    <div class="lm-area-hero" style="background:linear-gradient(135deg,#1A2A1E,#3D7A5E);">
                        <div class="lm-area-icon-big">🎪</div>
                        <div>
                            <div class="lm-area-hero-title">Eventos & Corporativo</div>
                            <div class="lm-area-hero-desc">Levamos a experiência LeveMente para empresas e grandes eventos com qualidade e cuidado.</div>
                        </div>
                    </div>
                    <ul class="lm-service-list">
                        <li class="lm-service-item"><div class="lm-service-bullet">🗂️</div>Mesas montadas para eventos corporativos</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🥡</div>Lunch Boxes personalizadas para empresas</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🎁</div>Brindes corporativos artesanais e saudáveis</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🌟</div>Operações em eventos externos de grande porte</li>
                    </ul>
                </div>

                <!-- Tab: Na Cozinha -->
                <div class="lm-tab-content" :class="{ active: activeTab === 'cozinha' }">
                    <div class="lm-area-hero" style="background:linear-gradient(135deg,#2D4A3E,#5A9E7C);">
                        <div class="lm-area-icon-big">🎓</div>
                        <div>
                            <div class="lm-area-hero-title">Leve'mente na Cozinha</div>
                            <div class="lm-area-hero-desc">Nossa plataforma de educação em gastronomia funcional e saudável.</div>
                        </div>
                    </div>
                    <ul class="lm-service-list">
                        <li class="lm-service-item"><div class="lm-service-bullet">💻</div>Cursos online de gastronomia funcional</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">📖</div>E-books com receitas e conteúdo educativo</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🍳</div>Workshops presenciais e ao vivo</li>
                        <li class="lm-service-item"><div class="lm-service-bullet">🌐</div>Plataforma digital de educação alimentar</li>
                    </ul>
                </div>
            </div>

            <div class="lm-nav">
                <button class="lm-btn lm-btn-outline" @click="prevStep()">← Voltar</button>
                <button class="lm-btn lm-btn-primary" @click="nextStep()">Próximo: Operação Carneiros →</button>
            </div>
        </div>

        <!-- ========== STEP 5: OPERAÇÃO CARNEIROS ========== -->
        <div x-show="currentStep === 5" x-transition class="animate-fade">
            <div class="lm-section-tag">🎆 Módulo 5 de 5</div>
            <h2 class="lm-section-title">Projeto Especial</h2>
            <p class="lm-section-desc">
                Conheça a nossa maior operação do ano — um projeto que coloca toda a equipe em movimento!
            </p>

            <div class="lm-op-header">
                <div class="lm-op-badge">🚀 Projeto Especial</div>
                <div class="lm-op-title">Operação Carneiros</div>
                <div class="lm-op-period">📅 25 de dezembro a 03 de janeiro (Réveillon)</div>
                <div class="lm-op-focus">Foco: <strong>Experiência saudável em grandes eventos</strong> — Shakes, Smoothies e Salgados Funcionais</div>
            </div>

            <div class="lm-card">
                <div style="font-weight:700;color:var(--lm-primary-dark);font-size:1.05rem;margin-bottom:1.25rem;">👥 Estrutura da Equipe</div>
                <div class="lm-team-grid">
                    <div class="lm-team-card">
                        <div class="lm-team-role">🍳 Cozinha</div>
                        <div class="lm-team-members">
                            <span class="lm-member-chip">Elaine</span>
                            <span class="lm-member-chip">Kali</span>
                            <span class="lm-member-chip">Antônia</span>
                            <span class="lm-member-chip">Mari</span>
                        </div>
                    </div>
                    <div class="lm-team-card">
                        <div class="lm-team-role">😊 Atendimento</div>
                        <div class="lm-team-members">
                            <span class="lm-member-chip">Vinicius</span>
                            <span class="lm-member-chip">Fran</span>
                            <span class="lm-member-chip">Thainá</span>
                        </div>
                    </div>
                    <div class="lm-team-card" style="grid-column: span 2;">
                        <div class="lm-team-role">🏠 Apoio Casa</div>
                        <div class="lm-team-members">
                            <span class="lm-member-chip">Cristiane</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.5rem;">
                    <div style="font-weight:700;color:var(--lm-primary-dark);margin-bottom:0.75rem;">⏰ Divisão de Turnos</div>
                    <div class="lm-turno-grid">
                        <div class="lm-turno">
                            <div class="lm-turno-icon">🌅</div>
                            <div class="lm-turno-time">10h às 19h</div>
                            <div class="lm-turno-label">Turno Manhã/Tarde</div>
                        </div>
                        <div class="lm-turno">
                            <div class="lm-turno-icon">🌙</div>
                            <div class="lm-turno-time">13h às 22h</div>
                            <div class="lm-turno-label">Turno Tarde/Noite</div>
                        </div>
                    </div>
                    <div style="margin-top:0.75rem;background:linear-gradient(135deg,#FFF9E6,white);border:1px solid var(--lm-gold);border-radius:12px;padding:0.875rem;text-align:center;font-size:0.875rem;color:var(--lm-text);">
                        🌟 <strong>Dias Especiais:</strong> A equipe completa mobilizada no Réveillon para garantir a melhor experiência do evento!
                    </div>
                </div>
            </div>

            <div class="lm-nav">
                <button class="lm-btn lm-btn-outline" @click="prevStep()">← Voltar</button>
                <button class="lm-btn lm-btn-primary" @click="nextStep()">
                    🎯 Iniciar Avaliação Final →
                </button>
            </div>
        </div>

        <!-- ========== STEP 6: QUIZ ========== -->
        <div x-show="currentStep === 6" x-transition class="animate-fade">
            <div class="lm-section-tag">🎯 Avaliação Final</div>
            <h2 class="lm-section-title">Quiz do OnBoarding</h2>
            <p class="lm-section-desc">
                Teste o que você aprendeu! São <strong>5 perguntas</strong> sobre o Grupo LeveMente. Você precisa de pelo menos <strong>4 acertos</strong> para conquistar o certificado.
            </p>

            <!-- Quiz Header -->
            <div class="lm-quiz-header">
                <div>
                    <div class="lm-quiz-title">🌿 Grupo LeveMente</div>
                    <div class="lm-quiz-counter" x-text="'Questão ' + (currentQuestion + 1) + ' de ' + questions.length"></div>
                </div>
                <div class="lm-hearts">
                    <template x-for="i in 5" :key="i">
                        <span class="lm-heart" :class="{ 'lost': i > hearts }">❤️</span>
                    </template>
                </div>
            </div>

            <!-- Progress -->
            <div class="lm-quiz-progress">
                <div class="lm-quiz-progress-fill" :style="'width:' + ((currentQuestion / questions.length) * 100) + '%'"></div>
            </div>

            <!-- Question -->
            <template x-if="!quizDone">
                <div class="lm-question-card animate-fade">
                    <div class="lm-question-num" x-text="'Pergunta ' + (currentQuestion + 1)"></div>
                    <div class="lm-question-text" x-text="questions[currentQuestion].text"></div>
                    <div class="lm-options">
                        <template x-for="(option, idx) in questions[currentQuestion].options" :key="idx">
                            <div class="lm-option"
                                 :class="{
                                     'answered': selectedAnswer !== null,
                                     'selected': selectedAnswer === idx,
                                     'correct': selectedAnswer !== null && idx === questions[currentQuestion].correct,
                                     'wrong': selectedAnswer === idx && idx !== questions[currentQuestion].correct
                                 }"
                                 @click="selectAnswer(idx)">
                                <div class="lm-option-letter" x-text="['A','B','C','D'][idx]"></div>
                                <span x-text="option"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Feedback -->
                    <div x-show="selectedAnswer !== null && selectedAnswer === questions[currentQuestion].correct"
                         class="lm-feedback correct">
                        ✅ <span x-text="questions[currentQuestion].correctFeedback"></span>
                    </div>
                    <div x-show="selectedAnswer !== null && selectedAnswer !== questions[currentQuestion].correct"
                         class="lm-feedback wrong">
                        💡 <span x-text="questions[currentQuestion].wrongFeedback"></span>
                    </div>

                    <div x-show="selectedAnswer !== null" style="margin-top:1rem;text-align:right;">
                        <button class="lm-btn lm-btn-green" @click="nextQuestion()">
                            <span x-text="currentQuestion < questions.length - 1 ? 'Próxima →' : 'Ver Resultado 🏆'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Resultado do Quiz -->
            <template x-if="quizDone">
                <div class="lm-result-hero animate-pop">
                    <span class="lm-result-emoji" x-text="score >= 4 ? '🏆' : '📚'"></span>
                    <h3 class="lm-result-title" x-text="score >= 4 ? 'Parabéns!' : 'Continue aprendendo!'"></h3>
                    <p class="lm-result-subtitle" x-text="score >= 4 ? 'Você concluiu o OnBoarding com sucesso! Bem-vindo(a) à equipe LeveMente!' : 'Você está quase lá! Revise o conteúdo e tente novamente.'"></p>

                    <div class="lm-score-ring" :style="'--score:' + (score / questions.length * 100)">
                        <div class="lm-score-num" x-text="score + '/' + questions.length"></div>
                    </div>

                    <div class="lm-result-stats">
                        <div class="lm-result-stat">
                            <div class="lm-result-stat-num" x-text="score"></div>
                            <div class="lm-result-stat-label">Acertos</div>
                        </div>
                        <div class="lm-result-stat">
                            <div class="lm-result-stat-num" x-text="questions.length - score"></div>
                            <div class="lm-result-stat-label">Erros</div>
                        </div>
                        <div class="lm-result-stat">
                            <div class="lm-result-stat-num" x-text="Math.round(score / questions.length * 100) + '%'"></div>
                            <div class="lm-result-stat-label">Aproveitamento</div>
                        </div>
                    </div>

                    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                        <button x-show="score >= 4" class="lm-btn lm-btn-primary lm-btn-lg" @click="nextStep(); launchConfetti();">
                            🎓 Ver meu Certificado
                        </button>
                        <button x-show="score < 4" class="lm-btn lm-btn-green lm-btn-lg" @click="restartQuiz()">
                            🔄 Tentar Novamente
                        </button>
                        <button x-show="score < 4" class="lm-btn lm-btn-outline" @click="currentStep = 1">
                            📚 Revisar Conteúdo
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- ========== STEP 7: CERTIFICADO ========== -->
        <div x-show="currentStep === 7" x-transition class="animate-fade">
            <div class="lm-section-tag">🏆 Conclusão</div>
            <h2 class="lm-section-title">Certificado de Conclusão</h2>
            <p class="lm-section-desc">
                Você completou o OnBoarding oficial do Grupo LeveMente! Este certificado confirma que você está pronto(a) para fazer parte da nossa história.
            </p>

            <div class="lm-cert">
                <div class="lm-cert-badge">🏆</div>
                <div class="lm-cert-label">Certificado de Conclusão</div>
                <h3 class="lm-cert-title">Grupo LeveMente</h3>
                <p style="color:var(--lm-text-secondary);margin-bottom:0.75rem;">Certificamos que</p>
                <div class="lm-cert-name">Novo(a) Colaborador(a)</div>
                <div class="lm-cert-course">concluiu com êxito o treinamento</div>
                <div style="font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:var(--lm-primary);margin:0.5rem 0;">OnBoarding Oficial — Grupo LeveMente</div>
                <div class="lm-cert-date">📅 Emitido em {{ now()->format('d \d\e F \d\e Y') }}</div>

                <div style="display:flex;justify-content:center;gap:3rem;margin-bottom:1.5rem;">
                    <div style="text-align:center;">
                        <div style="font-family:'Playfair Display',serif;font-weight:700;color:var(--lm-primary-dark);border-top:1px solid var(--lm-border);padding-top:0.5rem;margin-top:0.5rem;">Liz Galvão</div>
                        <div style="font-size:0.75rem;color:var(--lm-text-muted);">Co-fundadora</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-family:'Playfair Display',serif;font-weight:700;color:var(--lm-primary-dark);border-top:1px solid var(--lm-border);padding-top:0.5rem;margin-top:0.5rem;">Priu Lucena</div>
                        <div style="font-size:0.75rem;color:var(--lm-text-muted);">Co-fundadora</div>
                    </div>
                </div>

                <div class="lm-cert-seal">
                    🌿 Grupo LeveMente &bull; Alimentação Saudável, Inclusiva e Saborosa
                </div>
            </div>

            <div style="text-align:center;margin-top:1.5rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <button class="lm-btn lm-btn-green lm-btn-lg" onclick="window.print()">
                    🖨️ Imprimir Certificado
                </button>
                <button class="lm-btn lm-btn-outline" @click="currentStep = 0; resetAll()">
                    🔄 Refazer OnBoarding
                </button>
            </div>
        </div>

    </main>

    <script>
    function onboarding() {
        return {
            currentStep: 0,
            totalSteps: 8,
            hearts: 5,
            showXp: false,
            xpEarned: 0,

            // Atividade 1
            activityWords: [
                { text: '🥗 Saudável', correct: true, selected: false },
                { text: '🤝 Inclusiva', correct: true, selected: false },
                { text: '😋 Saborosa', correct: true, selected: false },
                { text: '💰 Lucrativa', correct: false, selected: false },
                { text: '🚀 Tecnológica', correct: false, selected: false },
                { text: '🌍 Global', correct: false, selected: false },
            ],
            activityChecked: false,
            activityCorrect: false,

            // Atividade 2
            cafeYear: '',
            activityChecked2: false,

            // Quiz
            currentQuestion: 0,
            selectedAnswer: null,
            score: 0,
            quizDone: false,
            questions: [
                {
                    text: 'Qual é o lema oficial do Grupo LeveMente?',
                    options: [
                        '"Saúde é tudo que importa"',
                        '"Primeiro a gente muda a alimentação e depois a alimentação muda a gente!"',
                        '"Comer bem é viver melhor"',
                        '"Leveza acima de tudo"'
                    ],
                    correct: 1,
                    correctFeedback: 'Exato! Esse lema reflete a transformação que acontece quando mudamos nossa relação com a alimentação.',
                    wrongFeedback: 'O lema correto é: "Primeiro a gente muda a alimentação e depois a alimentação muda a gente!"'
                },
                {
                    text: 'Em que ano o Grupo LeveMente iniciou suas atividades como Leve\'mente Fit Food?',
                    options: ['2010', '2013', '2015', '2019'],
                    correct: 2,
                    correctFeedback: 'Correto! Em 2015 tudo começou com os Congelados Fit.',
                    wrongFeedback: 'A história começou em 2015 com o Leve\'mente Fit Food — congelados saudáveis.'
                },
                {
                    text: 'Quais são os "S4" que definem a Visão do Grupo LeveMente?',
                    options: [
                        'Sucesso, Saúde, Sabor, Simplicidade',
                        'Significado, Sensibilidade, Saúde e Sabor',
                        'Saudável, Sustentável, Satisfatório, Simples',
                        'Síntese, Sabedoria, Saúde, Solidariedade'
                    ],
                    correct: 1,
                    correctFeedback: 'Perfeito! Significado, Sensibilidade, Saúde e Sabor — os quatro pilares da nossa visão!',
                    wrongFeedback: 'Os S4 são: Significado, Sensibilidade, Saúde e Sabor.'
                },
                {
                    text: 'Qual é a filosofia do Grupo LeveMente sobre alimentação saudável?',
                    options: [
                        'A alimentação saudável é obrigação de todos',
                        'A alimentação saudável é uma tendência passageira',
                        'A alimentação saudável é um manifesto de autocuidado',
                        'A alimentação saudável é privilégio de poucos'
                    ],
                    correct: 2,
                    correctFeedback: 'Isso mesmo! Alimentação saudável como ato de autocuidado — esse é o nosso manifesto!',
                    wrongFeedback: 'Nossa filosofia é clara: "A alimentação saudável é um manifesto de autocuidado."'
                },
                {
                    text: 'Qual área do Grupo LeveMente oferece Lunch Boxes e mesas montadas para empresas?',
                    options: [
                        'Leve\'mente na Cozinha',
                        'Leve\'mente Fit Food',
                        'Operação Carneiros',
                        'Eventos & Corporativo'
                    ],
                    correct: 3,
                    correctFeedback: 'Correto! A área de Eventos & Corporativo atende empresas com Lunch Boxes, mesas montadas e brindes.',
                    wrongFeedback: 'É a área de Eventos & Corporativo que oferece esses serviços para empresas!'
                }
            ],

            get progress() {
                return (this.currentStep / (this.totalSteps - 1)) * 100;
            },

            init() {
                document.getElementById('progress-label').style.display = 'block';
            },

            nextStep() {
                if (this.currentStep < this.totalSteps - 1) {
                    this.currentStep++;
                    this.earnXp(50);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            earnXp(amount) {
                this.xpEarned = amount;
                this.showXp = true;
                setTimeout(() => { this.showXp = false; }, 2000);
            },

            checkActivity() {
                this.activityChecked = true;
                const selected = this.activityWords.filter(w => w.selected);
                const correct = this.activityWords.filter(w => w.correct);
                const allCorrectSelected = correct.every(w => w.selected);
                const noWrongSelected = !this.activityWords.some(w => !w.correct && w.selected);
                this.activityCorrect = allCorrectSelected && noWrongSelected;
                if (this.activityCorrect) this.earnXp(20);
            },

            resetActivity() {
                this.activityChecked = false;
                this.activityCorrect = false;
                this.activityWords.forEach(w => w.selected = false);
            },

            checkCafeYear() {
                this.activityChecked2 = true;
                if (this.cafeYear === '2023') this.earnXp(20);
            },

            selectAnswer(idx) {
                if (this.selectedAnswer !== null) return;
                this.selectedAnswer = idx;
                if (idx === this.questions[this.currentQuestion].correct) {
                    this.score++;
                    this.earnXp(30);
                } else {
                    this.hearts = Math.max(0, this.hearts - 1);
                }
            },

            nextQuestion() {
                if (this.currentQuestion < this.questions.length - 1) {
                    this.currentQuestion++;
                    this.selectedAnswer = null;
                } else {
                    this.quizDone = true;
                    if (this.score >= 4) {
                        setTimeout(() => this.launchConfetti(), 300);
                    }
                }
            },

            restartQuiz() {
                this.currentQuestion = 0;
                this.selectedAnswer = null;
                this.score = 0;
                this.quizDone = false;
                this.hearts = 5;
            },

            resetAll() {
                this.restartQuiz();
                this.activityChecked = false;
                this.activityCorrect = false;
                this.activityWords.forEach(w => w.selected = false);
                this.activityChecked2 = false;
                this.cafeYear = '';
            },

            launchConfetti() {
                const colors = ['#3D7A5E','#F4A261','#E9C46A','#5A9E7C','#2A5A42'];
                for (let i = 0; i < 60; i++) {
                    setTimeout(() => {
                        const p = document.createElement('div');
                        p.className = 'lm-confetti-particle';
                        p.style.left = Math.random() * 100 + 'vw';
                        p.style.top = Math.random() * 40 + 20 + 'vh';
                        p.style.background = colors[Math.floor(Math.random() * colors.length)];
                        p.style.transform = 'rotate(' + (Math.random() * 360) + 'deg)';
                        p.style.animationDuration = (1 + Math.random()) + 's';
                        document.body.appendChild(p);
                        setTimeout(() => p.remove(), 2000);
                    }, i * 30);
                }
            }
        }
    }
    </script>

</body>
</html>
