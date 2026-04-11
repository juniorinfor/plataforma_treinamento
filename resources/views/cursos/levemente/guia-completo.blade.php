<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Onboarding — Grupo LeveMente</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Alpine.js CDN -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Bunny Fonts - Inter -->
  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

  <style>
    /* =============================================
       CSS VARIABLES — PALETA LEVEMENTE
    ============================================= */
    :root {
      --lm-primary:        #3D7A5E;
      --lm-primary-light:  #5A9E7C;
      --lm-primary-dark:   #2A5A42;
      --lm-primary-50:     #F0FAF5;
      --lm-accent:         #F4A261;
      --lm-accent-light:   #F9C291;
      --lm-gold:           #E9C46A;
      --lm-bg:             #F5FAF7;
      --lm-text:           #1A3329;
      --lm-text-secondary: #4A6358;
      --lm-border:         #D4E8DC;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--lm-bg);
      color: var(--lm-text);
      margin: 0;
      min-height: 100vh;
    }

    /* =============================================
       PROGRESS BAR
    ============================================= */
    .progress-bar-track {
      background: var(--lm-border);
      height: 6px;
      border-radius: 3px;
      overflow: hidden;
    }
    .progress-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--lm-primary), var(--lm-accent));
      border-radius: 3px;
      transition: width 0.5s ease;
    }

    /* =============================================
       STEP ANIMATION
    ============================================= */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .step-panel {
      animation: fadeInUp 0.45s ease both;
    }

    /* =============================================
       HERO CARD — MODULO 1
    ============================================= */
    .hero-gradient {
      background: linear-gradient(135deg, var(--lm-primary-dark) 0%, var(--lm-primary) 55%, var(--lm-primary-light) 100%);
    }
    .quote-block {
      border-left: 4px solid var(--lm-accent);
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(4px);
    }

    /* =============================================
       TIMELINE — MODULO 2
    ============================================= */
    .timeline-item { display: flex; align-items: flex-start; gap: 1rem; }
    .timeline-dot {
      width: 56px; height: 56px;
      background: white;
      border: 3px solid var(--lm-primary);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem;
      position: relative; z-index: 1;
      transition: all 0.3s ease;
      flex-shrink: 0;
      box-shadow: 0 2px 12px rgba(61,122,94,0.18);
    }
    .timeline-item:hover .timeline-dot {
      background: var(--lm-primary-50);
      border-color: var(--lm-accent);
      transform: scale(1.12);
      box-shadow: 0 4px 20px rgba(244,162,97,0.3);
    }
    .timeline-card {
      background: white;
      border: 1px solid var(--lm-border);
      border-radius: 14px;
      padding: 1.1rem 1.4rem;
      transition: all 0.3s ease;
      flex: 1;
    }
    .timeline-item:hover .timeline-card {
      border-color: var(--lm-primary-light);
      box-shadow: 0 4px 18px rgba(61,122,94,0.12);
      transform: translateX(4px);
    }
    .year-badge {
      display: inline-block;
      background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
      color: white;
      font-weight: 700;
      font-size: 0.8rem;
      padding: 2px 10px;
      border-radius: 20px;
      letter-spacing: 0.05em;
      margin-bottom: 4px;
    }

    /* =============================================
       FLIP CARDS — MODULO 3
    ============================================= */
    .flip-container {
      perspective: 1000px;
      height: 240px;
    }
    .flip-card {
      position: relative;
      width: 100%; height: 100%;
      transform-style: preserve-3d;
      transition: transform 0.6s cubic-bezier(0.4, 0.2, 0.2, 1);
      cursor: pointer;
    }
    .flip-container.flipped .flip-card {
      transform: rotateY(180deg);
    }
    .flip-front, .flip-back {
      position: absolute;
      width: 100%; height: 100%;
      border-radius: 16px;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 1.5rem;
      text-align: center;
    }
    .flip-front {
      background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
      color: white;
      border: 2px solid var(--lm-primary-dark);
    }
    .flip-back {
      background: white;
      border: 2px solid var(--lm-border);
      transform: rotateY(180deg);
      color: var(--lm-text);
      overflow-y: auto;
    }
    .flip-hint {
      position: absolute;
      bottom: 12px; right: 14px;
      font-size: 0.7rem;
      opacity: 0.7;
    }

    /* =============================================
       TABS — MODULO 4
    ============================================= */
    .tab-btn {
      padding: 0.6rem 1.2rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.25s;
      border: 2px solid transparent;
      white-space: nowrap;
    }
    .tab-btn.active {
      background: var(--lm-primary);
      color: white;
      border-color: var(--lm-primary-dark);
      box-shadow: 0 3px 12px rgba(61,122,94,0.3);
    }
    .tab-btn:not(.active) {
      background: white;
      color: var(--lm-text-secondary);
      border-color: var(--lm-border);
    }
    .tab-btn:not(.active):hover {
      border-color: var(--lm-primary-light);
      color: var(--lm-primary);
    }
    .tab-content { animation: fadeInUp 0.35s ease both; }

    /* =============================================
       TEAM CARDS — MODULO 5
    ============================================= */
    .avatar-card {
      background: white;
      border: 2px solid var(--lm-border);
      border-radius: 16px;
      padding: 1.1rem 0.8rem;
      text-align: center;
      transition: all 0.3s ease;
      cursor: default;
    }
    .avatar-card:hover {
      border-color: var(--lm-primary);
      box-shadow: 0 6px 22px rgba(61,122,94,0.15);
      transform: translateY(-4px);
    }
    .avatar-circle {
      width: 60px; height: 60px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem;
      margin: 0 auto 0.6rem;
    }
    .role-badge {
      display: inline-block;
      font-size: 0.68rem;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 20px;
      margin-top: 4px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    /* =============================================
       QUIZ — MODULO 6
    ============================================= */
    .quiz-option {
      width: 100%;
      text-align: left;
      padding: 0.9rem 1.2rem;
      border-radius: 12px;
      border: 2px solid var(--lm-border);
      background: white;
      color: var(--lm-text);
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.22s;
      font-family: 'Inter', sans-serif;
      font-weight: 500;
    }
    .quiz-option:hover:not(:disabled) {
      border-color: var(--lm-primary-light);
      background: var(--lm-primary-50);
      transform: translateX(4px);
    }
    .quiz-option:disabled { cursor: default; }
    .quiz-option.correct {
      border-color: #22c55e;
      background: #f0fdf4;
      color: #15803d;
    }
    .quiz-option.wrong {
      border-color: #ef4444;
      background: #fef2f2;
      color: #dc2626;
    }
    @keyframes shake {
      0%,100%{transform:translateX(0);}
      20%{transform:translateX(-6px);}
      40%{transform:translateX(6px);}
      60%{transform:translateX(-4px);}
      80%{transform:translateX(4px);}
    }
    .shake { animation: shake 0.4s ease; }
    .quiz-progress-dot {
      width: 28px; height: 28px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.7rem;
      font-weight: 700;
      border: 2px solid;
      transition: all 0.3s;
    }
    .score-circle {
      width: 100px; height: 100px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      color: white;
      margin: 0 auto;
      box-shadow: 0 6px 24px rgba(61,122,94,0.35);
    }

    /* =============================================
       CERTIFICATE — MODULO 7
    ============================================= */
    .certificate-wrapper {
      background: white;
      border: 3px solid var(--lm-primary);
      border-radius: 20px;
      position: relative;
      overflow: hidden;
      max-width: 680px;
      margin: 0 auto;
    }
    .cert-corner {
      position: absolute;
      width: 80px; height: 80px;
      border: 4px solid var(--lm-gold);
      opacity: 0.6;
    }
    .cert-corner.tl { top: 10px; left: 10px; border-right: none; border-bottom: none; border-radius: 8px 0 0 0; }
    .cert-corner.tr { top: 10px; right: 10px; border-left: none; border-bottom: none; border-radius: 0 8px 0 0; }
    .cert-corner.bl { bottom: 10px; left: 10px; border-right: none; border-top: none; border-radius: 0 0 0 8px; }
    .cert-corner.br { bottom: 10px; right: 10px; border-left: none; border-top: none; border-radius: 0 0 8px 0; }
    .cert-stripe {
      height: 8px;
      background: linear-gradient(90deg, var(--lm-primary-dark), var(--lm-primary), var(--lm-accent), var(--lm-gold));
    }
    @keyframes medalBounce {
      0%,100%{transform:translateY(0) rotate(-3deg);}
      50%{transform:translateY(-10px) rotate(3deg);}
    }
    .medal-anim { animation: medalBounce 2s ease-in-out infinite; display: inline-block; }

    /* =============================================
       NAV BUTTONS
    ============================================= */
    .btn-primary {
      background: linear-gradient(135deg, var(--lm-primary), var(--lm-primary-light));
      color: white;
      border: none;
      padding: 0.75rem 1.8rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.25s;
      font-family: 'Inter', sans-serif;
      display: inline-flex; align-items: center; gap: 6px;
      box-shadow: 0 3px 14px rgba(61,122,94,0.3);
    }
    .btn-primary:hover:not(:disabled) {
      box-shadow: 0 6px 22px rgba(61,122,94,0.4);
      transform: translateY(-2px);
    }
    .btn-primary:active:not(:disabled) { transform: translateY(0); }
    .btn-primary:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    .btn-secondary {
      background: white;
      color: var(--lm-primary);
      border: 2px solid var(--lm-border);
      padding: 0.75rem 1.6rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.25s;
      font-family: 'Inter', sans-serif;
      display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-secondary:hover:not(:disabled) { border-color: var(--lm-primary); background: var(--lm-primary-50); }
    .btn-secondary:disabled { opacity: 0.45; cursor: not-allowed; }
    .btn-accent {
      background: linear-gradient(135deg, var(--lm-accent), var(--lm-accent-light));
      color: white;
      border: none;
      padding: 0.75rem 1.8rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.25s;
      font-family: 'Inter', sans-serif;
      display: inline-flex; align-items: center; gap: 6px;
      box-shadow: 0 3px 14px rgba(244,162,97,0.35);
    }
    .btn-accent:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(244,162,97,0.45); }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--lm-border); border-radius: 3px; }
    ::-webkit-scrollbar-thumb { background: var(--lm-primary-light); border-radius: 3px; }

    /* Section card */
    .section-card {
      background: white;
      border: 1px solid var(--lm-border);
      border-radius: 18px;
      padding: 2rem;
      box-shadow: 0 2px 16px rgba(61,122,94,0.06);
    }
    .section-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--lm-primary-dark);
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 0.3rem;
    }
    .section-subtitle {
      color: var(--lm-text-secondary);
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }
    .divider {
      height: 2px;
      background: linear-gradient(90deg, var(--lm-primary), transparent);
      border-radius: 2px;
      margin: 1rem 0 1.5rem;
    }

    @media print {
      header, nav, .step-nav, .bottom-nav { display: none !important; }
      body { background: white; }
      .step-panel[style*="display: none"] { display: none !important; }
    }
  </style>
</head>

<body x-data="levementeApp()" x-init="init()">

  <!-- ===============================================
       TOP HEADER
  ================================================ -->
  <header style="background: linear-gradient(135deg, var(--lm-primary-dark) 0%, var(--lm-primary) 100%); position: sticky; top: 0; z-index: 50; box-shadow: 0 2px 16px rgba(0,0,0,0.18);">
    <div style="max-width: 920px; margin: 0 auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">

      <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
        <span style="font-size: 1.6rem;">🌿</span>
        <div>
          <div style="color: white; font-weight: 800; font-size: 1rem; line-height: 1.1;">Grupo LeveMente</div>
          <div style="color: rgba(255,255,255,0.7); font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase;">Onboarding Interativo</div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 12px; flex: 1; max-width: 420px;">
        <div style="flex: 1;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span style="color: rgba(255,255,255,0.8); font-size: 0.75rem; font-weight: 600;">Progresso</span>
            <span style="color: white; font-size: 0.75rem; font-weight: 700;" x-text="`${currentStep} / ${totalSteps}`"></span>
          </div>
          <div class="progress-bar-track">
            <div class="progress-bar-fill" :style="`width: ${(currentStep / totalSteps) * 100}%`"></div>
          </div>
        </div>
      </div>

    </div>
  </header>

  <!-- ===============================================
       STEP NAV BAR
  ================================================ -->
  <nav class="step-nav" style="background: white; border-bottom: 1px solid var(--lm-border); overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="max-width: 920px; margin: 0 auto; padding: 0.6rem 1.5rem; display: flex; gap: 6px; min-width: max-content;">
      <template x-for="i in totalSteps" :key="i">
        <button
          @click="goToStep(i)"
          :disabled="i > maxUnlockedStep"
          style="display: flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 20px; border: none; cursor: pointer; font-family: inherit; font-size: 0.75rem; font-weight: 600; transition: all 0.2s;"
          :style="i === currentStep
            ? 'background: var(--lm-primary); color: white; box-shadow: 0 2px 10px rgba(61,122,94,0.3);'
            : i <= maxUnlockedStep
              ? 'background: var(--lm-primary-50); color: var(--lm-primary); border: 1px solid var(--lm-border); cursor: pointer;'
              : 'background: #f3f4f6; color: #9ca3af; cursor: not-allowed; opacity: 0.6;'"
        >
          <span x-text="stepIcons[i-1]"></span>
          <span x-text="stepNames[i-1]" style="white-space: nowrap;"></span>
          <span x-show="i < currentStep">✓</span>
        </button>
      </template>
    </div>
  </nav>

  <!-- ===============================================
       MAIN CONTENT
  ================================================ -->
  <main style="max-width: 920px; margin: 0 auto; padding: 2rem 1.5rem 5rem;">

    <!-- ==========================================
         MODULO 1 — ESSENCIA E PROPOSITO
    =========================================== -->
    <div x-show="currentStep === 1" class="step-panel">

      <div class="hero-gradient" style="border-radius: 20px; padding: 2.5rem; color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
        <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
        <div style="position: relative; z-index: 1;">
          <div style="font-size: 3rem; margin-bottom: 0.5rem;">🌿💚</div>
          <h1 style="font-size: 2rem; font-weight: 900; margin: 0 0 0.4rem; line-height: 1.2;">Bem-vindo ao<br>Grupo LeveMente!</h1>
          <p style="opacity: 0.85; font-size: 1rem; margin-bottom: 1.5rem;">Uma jornada de alimentacao saudavel, inclusiva e saborosa comeca aqui.</p>
          <div class="quote-block" style="border-radius: 12px; padding: 1.1rem 1.4rem; margin-bottom: 1rem;">
            <p style="margin: 0; font-size: 1rem; font-style: italic; font-weight: 500; line-height: 1.6;">
              "A alimentacao saudavel e um manifesto de autocuidado."
            </p>
          </div>
          <div style="background: rgba(244,162,97,0.25); border: 1px solid rgba(244,162,97,0.4); border-radius: 12px; padding: 1rem 1.4rem;">
            <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.8; margin-bottom: 3px;">Lema</p>
            <p style="margin: 0; font-size: 1rem; font-weight: 700; line-height: 1.5;">
              "Primeiro a gente muda a alimentacao e depois a alimentacao muda a gente!"
            </p>
          </div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">

        <div class="section-card" style="padding: 1.4rem;">
          <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">👩‍🍳👩‍🍳</div>
          <h3 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">As Fundadoras</h3>
          <p style="margin: 0; font-size: 0.88rem; color: var(--lm-text-secondary); line-height: 1.6;">
            O Grupo LeveMente nasceu da parceria especial entre duas amigas:
            <strong style="color: var(--lm-primary);">Liz Galvao</strong> e <strong style="color: var(--lm-primary);">Priu Lucena</strong>.
          </p>
        </div>

        <div class="section-card" style="padding: 1.4rem;">
          <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">🎯</div>
          <h3 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">Proposito</h3>
          <p style="margin: 0; font-size: 0.88rem; color: var(--lm-text-secondary); line-height: 1.6;">
            Estimular uma alimentacao <strong style="color: var(--lm-primary);">saudavel, inclusiva e saborosa</strong>
            para cada pessoa que passa pela LeveMente.
          </p>
        </div>

        <div class="section-card" style="padding: 1.4rem;">
          <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">🌱</div>
          <h3 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">Filosofia</h3>
          <p style="margin: 0; font-size: 0.88rem; color: var(--lm-text-secondary); line-height: 1.6;">
            Acreditamos que a comida vai muito alem do prato — ela e expressao de
            <strong style="color: var(--lm-primary);">cuidado, afeto e consciencia</strong>.
          </p>
        </div>

      </div>
    </div>

    <!-- ==========================================
         MODULO 2 — LINHA DO TEMPO
    =========================================== -->
    <div x-show="currentStep === 2" class="step-panel">

      <div class="section-card">
        <div class="section-title">📅 Nossa Jornada</div>
        <p class="section-subtitle">Uma historia de evolucao, proposito e muito amor pela alimentacao saudavel.</p>
        <div class="divider"></div>

        <div style="position: relative; padding-left: 0;">
          <div style="position: absolute; left: 27px; top: 28px; bottom: 28px; width: 3px; background: linear-gradient(180deg, var(--lm-primary-light), var(--lm-accent)); border-radius: 2px;"></div>
          <div style="display: flex; flex-direction: column; gap: 1.2rem;">

            <div class="timeline-item">
              <div class="timeline-dot">🍱</div>
              <div class="timeline-card">
                <span class="year-badge">2015</span>
                <h3 style="margin: 4px 0 6px; font-size: 1.05rem; font-weight: 700; color: var(--lm-primary-dark);">Leve'mente Fit Food</h3>
                <p style="margin: 0; font-size: 0.87rem; color: var(--lm-text-secondary); line-height: 1.6;">
                  O inicio de tudo! Surge a linha de <strong>Congelados Fit</strong> — refeicoes saudaveis e praticas
                  para o dia a dia de quem nao abre mao do sabor.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot">📚</div>
              <div class="timeline-card">
                <span class="year-badge">2019</span>
                <h3 style="margin: 4px 0 6px; font-size: 1.05rem; font-weight: 700; color: var(--lm-primary-dark);">Leve'mente na Cozinha</h3>
                <p style="margin: 0; font-size: 0.87rem; color: var(--lm-text-secondary); line-height: 1.6;">
                  Ampliamos para <strong>Cursos e Conteudo</strong>! Workshops, e-books e cursos online de gastronomia
                  funcional chegam para transformar ainda mais vidas.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot">☕</div>
              <div class="timeline-card">
                <span class="year-badge">2023</span>
                <h3 style="margin: 4px 0 6px; font-size: 1.05rem; font-weight: 700; color: var(--lm-primary-dark);">Leve'mente Cafe</h3>
                <p style="margin: 0; font-size: 0.87rem; color: var(--lm-text-secondary); line-height: 1.6;">
                  Abrimos as portas do nosso <strong>Espaco Fisico</strong>! Um lugar acolhedor onde saude, sabor
                  e convivencia se encontram em cada detalhe.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot">🏢</div>
              <div class="timeline-card" style="border-color: var(--lm-primary-light); background: var(--lm-primary-50);">
                <span class="year-badge" style="background: linear-gradient(135deg, var(--lm-accent), var(--lm-gold)); color: #1A3329;">2025</span>
                <h3 style="margin: 4px 0 6px; font-size: 1.05rem; font-weight: 700; color: var(--lm-primary-dark);">Grupo LeveMente ✨</h3>
                <p style="margin: 0; font-size: 0.87rem; color: var(--lm-text-secondary); line-height: 1.6;">
                  A consolidacao de um sonho! Todas as frentes unidas em torno de um proposito maior:
                  <strong>transformar vidas atraves da alimentacao consciente</strong>.
                </p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================
         MODULO 3 — DIRETRIZES ESTRATEGICAS
    =========================================== -->
    <div x-show="currentStep === 3" class="step-panel">

      <div class="section-card" style="margin-bottom: 1.5rem;">
        <div class="section-title">🧭 Diretrizes Estrategicas</div>
        <p class="section-subtitle">Clique nos cards para descobrir nossa Missao, Visao e Valores. 👆</p>
        <div class="divider"></div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem;">

          <!-- Card Missao -->
          <div class="flip-container" :class="{'flipped': flippedCards[0]}" @click="flippedCards[0] = !flippedCards[0]">
            <div class="flip-card">
              <div class="flip-front">
                <div style="font-size: 3rem; margin-bottom: 0.8rem;">🎯</div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800;">Missao</h3>
                <p style="margin: 0.5rem 0 0; font-size: 0.8rem; opacity: 0.8;">Nosso proposito diario</p>
                <span class="flip-hint">Clique para ver &#x21BB;</span>
              </div>
              <div class="flip-back">
                <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">🎯</div>
                <h4 style="margin: 0 0 0.7rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--lm-primary);">Missao</h4>
                <p style="margin: 0; font-size: 0.88rem; color: var(--lm-text-secondary); line-height: 1.65; text-align: center;">
                  Inspirar pessoas a viverem de forma
                  <strong style="color: var(--lm-primary);">leve e consciente</strong>
                  atraves de alimentos que nutrem
                  <strong style="color: var(--lm-primary);">corpo e alma</strong>.
                </p>
                <span class="flip-hint" style="color: var(--lm-text-secondary);">Clique para fechar &#x21BA;</span>
              </div>
            </div>
          </div>

          <!-- Card Visao -->
          <div class="flip-container" :class="{'flipped': flippedCards[1]}" @click="flippedCards[1] = !flippedCards[1]">
            <div class="flip-card">
              <div class="flip-front" style="background: linear-gradient(135deg, var(--lm-accent) 0%, #d4854a 100%); border-color: #b86b30;">
                <div style="font-size: 3rem; margin-bottom: 0.8rem;">🔭</div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800;">Visao</h3>
                <p style="margin: 0.5rem 0 0; font-size: 0.8rem; opacity: 0.8;">Para onde vamos</p>
                <span class="flip-hint">Clique para ver &#x21BB;</span>
              </div>
              <div class="flip-back">
                <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">🔭</div>
                <h4 style="margin: 0 0 0.7rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--lm-accent);">Visao</h4>
                <p style="margin: 0 0 0.8rem; font-size: 0.88rem; color: var(--lm-text-secondary); line-height: 1.65; text-align: center;">
                  Ser <strong style="color: var(--lm-primary-dark);">referencia</strong> nos
                  <strong style="color: var(--lm-accent);">"S4"</strong>:
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; width: 100%;">
                  <div style="background: var(--lm-primary-50); border-radius: 8px; padding: 6px; text-align: center; font-size: 0.78rem; font-weight: 700; color: var(--lm-primary);">Significado</div>
                  <div style="background: var(--lm-primary-50); border-radius: 8px; padding: 6px; text-align: center; font-size: 0.78rem; font-weight: 700; color: var(--lm-primary);">Sensibilidade</div>
                  <div style="background: var(--lm-primary-50); border-radius: 8px; padding: 6px; text-align: center; font-size: 0.78rem; font-weight: 700; color: var(--lm-primary);">Saude</div>
                  <div style="background: var(--lm-primary-50); border-radius: 8px; padding: 6px; text-align: center; font-size: 0.78rem; font-weight: 700; color: var(--lm-primary);">Sabor</div>
                </div>
                <span class="flip-hint" style="color: var(--lm-text-secondary);">Clique para fechar &#x21BA;</span>
              </div>
            </div>
          </div>

          <!-- Card Valores -->
          <div class="flip-container" :class="{'flipped': flippedCards[2]}" @click="flippedCards[2] = !flippedCards[2]">
            <div class="flip-card">
              <div class="flip-front" style="background: linear-gradient(135deg, #6B4F12 0%, #9C7A2E 100%); border-color: #4a3008;">
                <div style="font-size: 3rem; margin-bottom: 0.8rem;">💛</div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800;">Valores</h3>
                <p style="margin: 0.5rem 0 0; font-size: 0.8rem; opacity: 0.8;">O que nos guia</p>
                <span class="flip-hint">Clique para ver &#x21BB;</span>
              </div>
              <div class="flip-back">
                <div style="font-size: 1.8rem; margin-bottom: 0.6rem;">💛</div>
                <h4 style="margin: 0 0 0.7rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--lm-gold);">Valores</h4>
                <div style="display: flex; flex-direction: column; gap: 7px; width: 100%;">
                  <div style="display: flex; align-items: center; gap: 8px; font-size: 0.83rem; color: var(--lm-text);">
                    <span>✨</span><span><strong>Fazer o bem</strong> sempre</span>
                  </div>
                  <div style="display: flex; align-items: center; gap: 8px; font-size: 0.83rem; color: var(--lm-text);">
                    <span>🧠</span><span><strong>Consciencia</strong> nas escolhas</span>
                  </div>
                  <div style="display: flex; align-items: center; gap: 8px; font-size: 0.83rem; color: var(--lm-text);">
                    <span>❤️</span><span><strong>Amor</strong> nos detalhes</span>
                  </div>
                  <div style="display: flex; align-items: center; gap: 8px; font-size: 0.83rem; color: var(--lm-text);">
                    <span>🌟</span><span><strong>Gerar bem-estar</strong></span>
                  </div>
                </div>
                <span class="flip-hint" style="color: var(--lm-text-secondary);">Clique para fechar &#x21BA;</span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div style="background: var(--lm-primary-50); border: 1px dashed var(--lm-primary-light); border-radius: 12px; padding: 1rem 1.2rem; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 1.2rem;">💡</span>
        <p style="margin: 0; font-size: 0.85rem; color: var(--lm-text-secondary);">
          Lembre-se dos <strong style="color: var(--lm-primary);">S4</strong>: Significado, Sensibilidade, Saude e Sabor — eles guiam todas as nossas decisoes!
        </p>
      </div>
    </div>

    <!-- ==========================================
         MODULO 4 — AREAS DE ATUACAO
    =========================================== -->
    <div x-show="currentStep === 4" class="step-panel">

      <div class="section-card">
        <div class="section-title">🏪 Areas de Atuacao</div>
        <p class="section-subtitle">Conheca cada frente do Grupo LeveMente em detalhe.</p>
        <div class="divider"></div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.5rem;">
          <button class="tab-btn" :class="{'active': activeTab === 0}" @click="activeTab = 0">
            ☕ Leve'mente Cafe
          </button>
          <button class="tab-btn" :class="{'active': activeTab === 1}" @click="activeTab = 1">
            🎉 Eventos &amp; Corporativo
          </button>
          <button class="tab-btn" :class="{'active': activeTab === 2}" @click="activeTab = 2">
            📚 Na Cozinha
          </button>
        </div>

        <!-- Tab 0 — Cafe -->
        <div x-show="activeTab === 0" class="tab-content">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">🍽️</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">Cardapio A la Carte</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">Opcoes saudaveis no espaco fisico</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">🛍️</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">To Go</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">Praticidade sem abrir mao da saude</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">❄️</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">Congelados Fit</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">Refeicoes prontas e nutritivas para casa</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">🛵</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">Delivery</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">iFood e WhatsApp — na sua porta!</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">🎂</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">Bolos &amp; Tortas</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">Delicias artesanais com ingredientes selecionados</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 12px; padding: 1rem; text-align: center;">
              <div style="font-size: 2rem; margin-bottom: 6px;">🥧</div>
              <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: var(--lm-primary-dark);">Quiches</p>
              <p style="margin: 4px 0 0; font-size: 0.78rem; color: var(--lm-text-secondary);">Receitas que encantam no primeiro sabor</p>
            </div>
          </div>
        </div>

        <!-- Tab 1 — Eventos -->
        <div x-show="activeTab === 1" class="tab-content">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background: linear-gradient(135deg, #FFF8F0, #FFEEDD); border: 1px solid var(--lm-accent-light); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">🌹</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: #8B4513;">Mesas Montadas</h4>
              <p style="margin: 0; font-size: 0.84rem; color: #6B4226; line-height: 1.6;">Apresentacoes visuais deslumbrantes para eventos e celebracoes especiais.</p>
            </div>
            <div style="background: linear-gradient(135deg, #FFF8F0, #FFEEDD); border: 1px solid var(--lm-accent-light); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">🎁</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: #8B4513;">Lunch Boxes</h4>
              <p style="margin: 0; font-size: 0.84rem; color: #6B4226; line-height: 1.6;">Caixas tematicas com refeicoes saudaveis para eventos corporativos.</p>
            </div>
            <div style="background: linear-gradient(135deg, #FFF8F0, #FFEEDD); border: 1px solid var(--lm-accent-light); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">🏷️</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: #8B4513;">Brindes Corporativos</h4>
              <p style="margin: 0; font-size: 0.84rem; color: #6B4226; line-height: 1.6;">Presentes saudaveis e personalizados para empresas que valorizam o bem-estar.</p>
            </div>
          </div>
        </div>

        <!-- Tab 2 — Na Cozinha -->
        <div x-show="activeTab === 2" class="tab-content">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div style="background: linear-gradient(135deg, var(--lm-primary-50), #E6F5EC); border: 1px solid var(--lm-border); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">💻</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">Cursos Online</h4>
              <p style="margin: 0; font-size: 0.84rem; color: var(--lm-text-secondary); line-height: 1.6;">Gastronomia funcional ao alcance de todos, de qualquer lugar do mundo.</p>
            </div>
            <div style="background: linear-gradient(135deg, var(--lm-primary-50), #E6F5EC); border: 1px solid var(--lm-border); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">📖</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">E-books</h4>
              <p style="margin: 0; font-size: 0.84rem; color: var(--lm-text-secondary); line-height: 1.6;">Receitas, dicas e guias praticos de alimentacao saudavel e intuitiva.</p>
            </div>
            <div style="background: linear-gradient(135deg, var(--lm-primary-50), #E6F5EC); border: 1px solid var(--lm-border); border-radius: 14px; padding: 1.4rem; text-align: center;">
              <div style="font-size: 2.2rem; margin-bottom: 8px;">👨‍🍳</div>
              <h4 style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">Workshops</h4>
              <p style="margin: 0; font-size: 0.84rem; color: var(--lm-text-secondary); line-height: 1.6;">Experiencias presenciais de gastronomia funcional com hands-on e muito aprendizado.</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ==========================================
         MODULO 5 — OPERACAO CARNEIROS
    =========================================== -->
    <div x-show="currentStep === 5" class="step-panel">

      <div style="background: linear-gradient(135deg, var(--lm-primary-dark), var(--lm-primary)); border-radius: 18px; padding: 1.8rem 2rem; color: white; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
        <div style="font-size: 3rem; flex-shrink: 0;">🎆</div>
        <div>
          <h2 style="margin: 0 0 4px; font-size: 1.4rem; font-weight: 800;">Operacao Carneiros</h2>
          <p style="margin: 0; opacity: 0.85; font-size: 0.9rem;">Reveillon — 25/12 a 03/01</p>
          <p style="margin: 6px 0 0; font-size: 0.85rem; opacity: 0.8;">🥤 Shakes · Smoothies · Salgados Funcionais em grandes eventos</p>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; margin-bottom: 1.2rem;">

        <!-- Cozinha -->
        <div class="section-card" style="padding: 1.4rem;">
          <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark); display: flex; align-items: center; gap: 8px;">
            👩‍🍳 Equipe Cozinha
          </h3>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem;">
            <div class="avatar-card">
              <div class="avatar-circle" style="background: linear-gradient(135deg, #FFE0B2, #FFCC80);">👩‍🍳</div>
              <p style="margin: 0; font-size: 0.82rem; font-weight: 700; color: var(--lm-text);">Elaine</p>
              <span class="role-badge" style="background: var(--lm-primary-50); color: var(--lm-primary);">Cozinha</span>
            </div>
            <div class="avatar-card">
              <div class="avatar-circle" style="background: linear-gradient(135deg, #F8BBD0, #F48FB1);">👩‍🍳</div>
              <p style="margin: 0; font-size: 0.82rem; font-weight: 700; color: var(--lm-text);">Kali</p>
              <span class="role-badge" style="background: var(--lm-primary-50); color: var(--lm-primary);">Cozinha</span>
            </div>
            <div class="avatar-card">
              <div class="avatar-circle" style="background: linear-gradient(135deg, #C8E6C9, #A5D6A7);">👩‍🍳</div>
              <p style="margin: 0; font-size: 0.82rem; font-weight: 700; color: var(--lm-text);">Antonia</p>
              <span class="role-badge" style="background: var(--lm-primary-50); color: var(--lm-primary);">Cozinha</span>
            </div>
            <div class="avatar-card">
              <div class="avatar-circle" style="background: linear-gradient(135deg, #BBDEFB, #90CAF9);">👩‍🍳</div>
              <p style="margin: 0; font-size: 0.82rem; font-weight: 700; color: var(--lm-text);">Mari</p>
              <span class="role-badge" style="background: var(--lm-primary-50); color: var(--lm-primary);">Cozinha</span>
            </div>
          </div>
        </div>

        <!-- Atendimento + Apoio -->
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div class="section-card" style="padding: 1.4rem;">
            <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">🤝 Atendimento</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.7rem;">
              <div class="avatar-card" style="padding: 0.8rem 0.5rem;">
                <div class="avatar-circle" style="background: linear-gradient(135deg, #D1C4E9, #B39DDB); width: 46px; height: 46px; font-size: 1.2rem;">🧑</div>
                <p style="margin: 0; font-size: 0.78rem; font-weight: 700; color: var(--lm-text);">Vinicius</p>
                <span class="role-badge" style="background: #FFF3E0; color: #E65100; font-size: 0.6rem;">Atend.</span>
              </div>
              <div class="avatar-card" style="padding: 0.8rem 0.5rem;">
                <div class="avatar-circle" style="background: linear-gradient(135deg, #FCE4EC, #F8BBD0); width: 46px; height: 46px; font-size: 1.2rem;">👩</div>
                <p style="margin: 0; font-size: 0.78rem; font-weight: 700; color: var(--lm-text);">Fran</p>
                <span class="role-badge" style="background: #FFF3E0; color: #E65100; font-size: 0.6rem;">Atend.</span>
              </div>
              <div class="avatar-card" style="padding: 0.8rem 0.5rem;">
                <div class="avatar-circle" style="background: linear-gradient(135deg, #E8F5E9, #C8E6C9); width: 46px; height: 46px; font-size: 1.2rem;">👩</div>
                <p style="margin: 0; font-size: 0.78rem; font-weight: 700; color: var(--lm-text);">Thaina</p>
                <span class="role-badge" style="background: #FFF3E0; color: #E65100; font-size: 0.6rem;">Atend.</span>
              </div>
            </div>
          </div>

          <div class="section-card" style="padding: 1.4rem;">
            <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">🏠 Apoio Casa</h3>
            <div style="display: flex; justify-content: center;">
              <div class="avatar-card" style="max-width: 120px; padding: 1rem;">
                <div class="avatar-circle" style="background: linear-gradient(135deg, #FFF9C4, #FFF176);">🌟</div>
                <p style="margin: 0; font-size: 0.82rem; font-weight: 700; color: var(--lm-text);">Cristiane</p>
                <span class="role-badge" style="background: #F3E5F5; color: #7B1FA2;">Apoio</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Turnos -->
      <div class="section-card" style="padding: 1.4rem;">
        <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 700; color: var(--lm-primary-dark);">🕐 Turnos de Trabalho</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
          <div style="background: linear-gradient(135deg, var(--lm-primary-50), white); border: 2px solid var(--lm-primary-light); border-radius: 14px; padding: 1.2rem; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 6px;">🌅</div>
            <p style="margin: 0; font-weight: 700; font-size: 1.1rem; color: var(--lm-primary-dark);">10h as 19h</p>
            <p style="margin: 4px 0 0; font-size: 0.8rem; color: var(--lm-text-secondary);">Turno manha/tarde</p>
          </div>
          <div style="background: linear-gradient(135deg, #FFF8F0, white); border: 2px solid var(--lm-accent-light); border-radius: 14px; padding: 1.2rem; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 6px;">🌆</div>
            <p style="margin: 0; font-weight: 700; font-size: 1.1rem; color: #8B4513;">13h as 22h</p>
            <p style="margin: 4px 0 0; font-size: 0.8rem; color: var(--lm-text-secondary);">Turno tarde/noite</p>
          </div>
        </div>
      </div>

    </div>

    <!-- ==========================================
         MODULO 6 — QUIZ
    =========================================== -->
    <div x-show="currentStep === 6" class="step-panel">

      <div style="background: linear-gradient(135deg, var(--lm-primary-dark), var(--lm-primary)); border-radius: 18px; padding: 1.8rem 2rem; color: white; margin-bottom: 1.5rem; text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 6px;">🧠</div>
        <h2 style="margin: 0 0 4px; font-size: 1.4rem; font-weight: 800;">Quiz Final</h2>
        <p style="margin: 0; opacity: 0.85; font-size: 0.9rem;">Teste seus conhecimentos sobre o Grupo LeveMente!</p>
      </div>

      <!-- Progress dots -->
      <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 1.5rem;" x-show="!quizSubmitted">
        <template x-for="(q, idx) in quizQuestions" :key="idx">
          <div class="quiz-progress-dot"
            :style="idx === quizCurrentQ
              ? 'background: var(--lm-primary); color: white; border-color: var(--lm-primary-dark);'
              : quizAnswers[idx] !== undefined
                ? (quizAnswers[idx] === quizQuestions[idx].correct
                    ? 'background: #22c55e; color: white; border-color: #16a34a;'
                    : 'background: #ef4444; color: white; border-color: #dc2626;')
                : 'background: white; color: var(--lm-text-secondary); border-color: var(--lm-border);'"
            x-text="quizAnswers[idx] !== undefined
              ? (quizAnswers[idx] === quizQuestions[idx].correct ? '&#10003;' : '&#10007;')
              : (idx + 1)"
          ></div>
        </template>
      </div>

      <!-- Question view -->
      <div x-show="!quizSubmitted">
        <template x-if="quizCurrentQ < quizQuestions.length">
          <div class="section-card">
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 1.2rem;">
              <div style="background: var(--lm-primary); color: white; border-radius: 10px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0;" x-text="quizCurrentQ + 1"></div>
              <p style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--lm-text); line-height: 1.5;" x-text="quizQuestions[quizCurrentQ].question"></p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
              <template x-for="(opt, oi) in quizQuestions[quizCurrentQ].options" :key="oi">
                <button
                  class="quiz-option"
                  :class="{
                    'correct': quizAnswers[quizCurrentQ] !== undefined && oi === quizQuestions[quizCurrentQ].correct,
                    'wrong': quizAnswers[quizCurrentQ] !== undefined && oi === quizAnswers[quizCurrentQ] && oi !== quizQuestions[quizCurrentQ].correct,
                    'shake': quizShake && quizAnswers[quizCurrentQ] !== undefined && oi === quizAnswers[quizCurrentQ] && oi !== quizQuestions[quizCurrentQ].correct
                  }"
                  :disabled="quizAnswers[quizCurrentQ] !== undefined"
                  @click="answerQuiz(quizCurrentQ, oi)"
                >
                  <span style="font-weight: 700; margin-right: 8px;" x-text="['A)', 'B)', 'C)', 'D)'][oi]"></span>
                  <span x-text="opt"></span>
                </button>
              </template>
            </div>

            <div x-show="quizAnswers[quizCurrentQ] !== undefined" style="margin-top: 1rem;">
              <div
                x-show="quizAnswers[quizCurrentQ] === quizQuestions[quizCurrentQ].correct"
                style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 0.8rem 1rem; display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;"
              >
                <span style="font-size: 1.2rem;">🎉</span>
                <span style="font-size: 0.88rem; color: #15803d; font-weight: 600;">Correto! Muito bem!</span>
              </div>
              <div
                x-show="quizAnswers[quizCurrentQ] !== quizQuestions[quizCurrentQ].correct"
                style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 0.8rem 1rem; display: flex; align-items: center; gap: 8px; margin-bottom: 0.8rem;"
              >
                <span style="font-size: 1.2rem;">💡</span>
                <span style="font-size: 0.88rem; color: #dc2626; font-weight: 600;">Quase! Continue que voce chega la!</span>
              </div>

              <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button class="btn-primary"
                  x-show="quizCurrentQ < quizQuestions.length - 1"
                  @click="quizCurrentQ++"
                >
                  Proxima &#9658;
                </button>
                <button class="btn-accent"
                  x-show="quizCurrentQ === quizQuestions.length - 1"
                  @click="submitQuiz()"
                >
                  Ver Resultado 🏁
                </button>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Result view -->
      <div x-show="quizSubmitted" class="section-card" style="text-align: center;">
        <div style="margin-bottom: 1.5rem;">
          <div class="score-circle" style="margin-bottom: 1rem;">
            <span style="font-size: 1.8rem; font-weight: 900;" x-text="quizScore"></span>
            <span style="font-size: 0.75rem; opacity: 0.85;">/ <span x-text="quizQuestions.length"></span></span>
          </div>
          <h3 style="margin: 0 0 6px; font-size: 1.3rem; font-weight: 800; color: var(--lm-primary-dark);" x-text="quizFeedbackTitle"></h3>
          <p style="margin: 0; color: var(--lm-text-secondary);" x-text="quizFeedbackMsg"></p>
        </div>

        <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 1.5rem; flex-wrap: wrap;">
          <template x-for="(q, idx) in quizQuestions" :key="idx">
            <div style="display: flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;"
              :style="quizAnswers[idx] === q.correct
                ? 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;'
                : 'background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;'"
            >
              <span x-text="quizAnswers[idx] === q.correct ? '&#10003;' : '&#10007;'"></span>
              <span x-text="`Pergunta ${idx + 1}`"></span>
            </div>
          </template>
        </div>

        <button class="btn-primary" @click="nextStep()">
          🏆 Ver meu Certificado
        </button>
      </div>

    </div>

    <!-- ==========================================
         MODULO 7 — CERTIFICADO
    =========================================== -->
    <div x-show="currentStep === 7" class="step-panel">

      <div style="text-align: center; margin-bottom: 2rem;">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎊🎉🎊</div>
        <h2 style="margin: 0 0 6px; font-size: 1.8rem; font-weight: 900; color: var(--lm-primary-dark);">Parabens, Colaborador!</h2>
        <p style="margin: 0; color: var(--lm-text-secondary); font-size: 1rem;">Voce concluiu o onboarding do Grupo LeveMente com sucesso!</p>
      </div>

      <div class="certificate-wrapper" style="margin-bottom: 2rem;">
        <div class="cert-stripe"></div>
        <div class="cert-corner tl"></div>
        <div class="cert-corner tr"></div>
        <div class="cert-corner bl"></div>
        <div class="cert-corner br"></div>

        <div style="padding: 3rem 2.5rem; text-align: center; position: relative; z-index: 1;">

          <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 2rem;">
            <span style="font-size: 2rem;">🌿</span>
            <span style="font-size: 1.2rem; font-weight: 800; color: var(--lm-primary-dark); letter-spacing: 0.02em;">Grupo LeveMente</span>
            <span style="font-size: 2rem;">🌿</span>
          </div>

          <p style="margin: 0 0 0.3rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--lm-text-secondary); font-weight: 600;">Certificado de Conclusao</p>
          <div style="height: 2px; background: linear-gradient(90deg, transparent, var(--lm-gold), transparent); margin: 0.6rem auto 1.5rem; max-width: 200px;"></div>

          <div class="medal-anim" style="font-size: 4rem; margin-bottom: 1rem;">🏆</div>

          <p style="margin: 0 0 0.3rem; color: var(--lm-text-secondary); font-size: 0.9rem;">Certificamos que</p>
          <h2 style="margin: 0 0 0.3rem; font-size: 2rem; font-weight: 900; color: var(--lm-primary-dark);">Colaborador</h2>
          <p style="margin: 0 0 1.5rem; color: var(--lm-text-secondary); font-size: 0.9rem;">concluiu com exito o curso</p>

          <div style="background: linear-gradient(135deg, var(--lm-primary-50), white); border: 2px solid var(--lm-primary-light); border-radius: 12px; padding: 1rem 1.5rem; display: inline-block; margin-bottom: 1.5rem;">
            <p style="margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--lm-text-secondary); margin-bottom: 2px;">Curso</p>
            <p style="margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--lm-primary-dark);">Onboarding Grupo LeveMente</p>
          </div>

          <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 10px; padding: 0.6rem 1rem; text-align: center;">
              <p style="margin: 0; font-size: 0.7rem; color: var(--lm-text-secondary); text-transform: uppercase; letter-spacing: 0.06em;">Modulos</p>
              <p style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--lm-primary);">6 completos</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 10px; padding: 0.6rem 1rem; text-align: center;">
              <p style="margin: 0; font-size: 0.7rem; color: var(--lm-text-secondary); text-transform: uppercase; letter-spacing: 0.06em;">Quiz</p>
              <p style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--lm-primary);"><span x-text="quizScore"></span> / 5</p>
            </div>
            <div style="background: var(--lm-primary-50); border: 1px solid var(--lm-border); border-radius: 10px; padding: 0.6rem 1rem; text-align: center;">
              <p style="margin: 0; font-size: 0.7rem; color: var(--lm-text-secondary); text-transform: uppercase; letter-spacing: 0.06em;">Data</p>
              <p style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--lm-primary);" x-text="todayDate"></p>
            </div>
          </div>

          <div style="display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap; border-top: 1px solid var(--lm-border); padding-top: 1.5rem;">
            <div style="text-align: center;">
              <div style="height: 1px; background: var(--lm-text); width: 120px; margin: 0 auto 4px;"></div>
              <p style="margin: 0; font-size: 0.8rem; font-weight: 700; color: var(--lm-text);">Liz Galvao</p>
              <p style="margin: 0; font-size: 0.7rem; color: var(--lm-text-secondary);">Co-fundadora</p>
            </div>
            <div style="text-align: center;">
              <div style="height: 1px; background: var(--lm-text); width: 120px; margin: 0 auto 4px;"></div>
              <p style="margin: 0; font-size: 0.8rem; font-weight: 700; color: var(--lm-text);">Priu Lucena</p>
              <p style="margin: 0; font-size: 0.7rem; color: var(--lm-text-secondary);">Co-fundadora</p>
            </div>
          </div>

        </div>
        <div class="cert-stripe"></div>
      </div>

      <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <button class="btn-secondary" @click="restartCourse()">
          🔄 Reiniciar Curso
        </button>
        <button class="btn-accent" onclick="window.print()">
          🖨️ Imprimir Certificado
        </button>
      </div>

    </div>

    <!-- ==========================================
         NAVIGATION BOTTOM
    =========================================== -->
    <div class="bottom-nav" style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--lm-border);">

      <button class="btn-secondary"
        :disabled="currentStep === 1"
        @click="prevStep()"
      >
        &#8592; Anterior
      </button>

      <!-- Dot indicators -->
      <div style="display: flex; align-items: center; gap: 8px;">
        <template x-for="i in totalSteps" :key="i">
          <div
            style="border-radius: 50%; transition: all 0.3s; cursor: pointer;"
            :style="i === currentStep
              ? 'width: 12px; height: 12px; background: var(--lm-primary); box-shadow: 0 0 0 3px rgba(61,122,94,0.2);'
              : i < currentStep
                ? 'width: 8px; height: 8px; background: var(--lm-primary-light);'
                : 'width: 8px; height: 8px; background: var(--lm-border);'"
            @click="goToStep(i)"
          ></div>
        </template>
      </div>

      <template x-if="currentStep < totalSteps">
        <button class="btn-primary"
          :disabled="currentStep === 6 && !quizSubmitted"
          @click="nextStep()"
        >
          <span x-text="currentStep === 6 && !quizSubmitted ? 'Conclua o Quiz' : 'Proximo'"></span>
          <span x-show="!(currentStep === 6 && !quizSubmitted)">&#8594;</span>
        </button>
      </template>
      <template x-if="currentStep === totalSteps">
        <div style="width: 120px;"></div>
      </template>

    </div>

  </main>

  <!-- ===============================================
       ALPINE.JS DATA
  ================================================ -->
  <script>
    function levementeApp() {
      return {
        currentStep: 1,
        totalSteps: 7,
        maxUnlockedStep: 1,
        stepIcons: ['🌿','📅','🧭','🏪','🎆','🧠','🏆'],
        stepNames: ['Essencia','Linha do Tempo','Diretrizes','Atuacao','Operacao','Quiz','Certificado'],

        flippedCards: [false, false, false],
        activeTab: 0,

        quizQuestions: [
          {
            question: 'Qual e o lema do Grupo LeveMente?',
            options: [
              'Saude e tudo',
              'Primeiro a gente muda a alimentacao e depois a alimentacao muda a gente!',
              'Comer bem e viver bem',
              'Leveza acima de tudo'
            ],
            correct: 1
          },
          {
            question: "Em que ano o Grupo LeveMente surgiu como Leve'mente Fit Food?",
            options: ['2010', '2013', '2015', '2019'],
            correct: 2
          },
          {
            question: 'Quais sao os "S4" da visao do grupo?',
            options: [
              'Sucesso, Saude, Sabor, Simplicidade',
              'Significado, Sensibilidade, Saude e Sabor',
              'Saudavel, Sustentavel, Satisfatorio, Simples',
              'Sintese, Sabedoria, Saude, Solidariedade'
            ],
            correct: 1
          },
          {
            question: 'Qual plataforma e usada para delivery?',
            options: ['Rappi', 'Uber Eats', 'iFood', 'James'],
            correct: 2
          },
          {
            question: 'Qual e a missao do Grupo LeveMente?',
            options: [
              'Ser a maior rede de alimentacao saudavel do Brasil',
              'Inspirar pessoas a viverem de forma leve e consciente atraves de alimentos que nutrem corpo e alma',
              'Vender o maior numero de produtos naturais',
              'Criar cursos online de gastronomia'
            ],
            correct: 1
          }
        ],
        quizAnswers: {},
        quizScore: 0,
        quizSubmitted: false,
        quizCurrentQ: 0,
        quizShake: false,
        quizFeedbackTitle: '',
        quizFeedbackMsg: '',

        todayDate: '',

        init() {
          const now = new Date();
          this.todayDate = now.toLocaleDateString('pt-BR', {
            day: '2-digit', month: '2-digit', year: 'numeric'
          });
        },

        nextStep() {
          if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            if (this.currentStep > this.maxUnlockedStep) {
              this.maxUnlockedStep = this.currentStep;
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        },

        prevStep() {
          if (this.currentStep > 1) {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        },

        goToStep(n) {
          if (n <= this.maxUnlockedStep) {
            this.currentStep = n;
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }
        },

        answerQuiz(qIdx, optionIdx) {
          if (this.quizAnswers[qIdx] !== undefined) return;
          this.quizAnswers[qIdx] = optionIdx;
          if (optionIdx !== this.quizQuestions[qIdx].correct) {
            this.quizShake = true;
            setTimeout(() => { this.quizShake = false; }, 500);
          }
        },

        submitQuiz() {
          let score = 0;
          this.quizQuestions.forEach((q, idx) => {
            if (this.quizAnswers[idx] === q.correct) score++;
          });
          this.quizScore = score;
          this.quizSubmitted = true;

          if (score === 5) {
            this.quizFeedbackTitle = '🌟 Perfeito! Nota 100!';
            this.quizFeedbackMsg = 'Voce absorveu tudo sobre o Grupo LeveMente. Que talento!';
          } else if (score >= 4) {
            this.quizFeedbackTitle = '🎉 Muito bem!';
            this.quizFeedbackMsg = 'Quase perfeito! Voce conhece muito bem a LeveMente.';
          } else if (score >= 3) {
            this.quizFeedbackTitle = '💪 Bom trabalho!';
            this.quizFeedbackMsg = 'Voce passou! Continue explorando o universo LeveMente.';
          } else {
            this.quizFeedbackTitle = '🌱 Continue crescendo!';
            this.quizFeedbackMsg = 'Revise o material e tente novamente. Voce consegue!';
          }
        },

        restartCourse() {
          this.currentStep = 1;
          this.maxUnlockedStep = 1;
          this.quizAnswers = {};
          this.quizScore = 0;
          this.quizSubmitted = false;
          this.quizCurrentQ = 0;
          this.quizShake = false;
          this.flippedCards = [false, false, false];
          this.activeTab = 0;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      };
    }
  </script>

</body>
</html>
