<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AYBAR Corp — En mantenimiento</title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Animate.css (animaciones) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Font Awesome (iconos) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<!-- Tipografías -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          teal: {
            deep: '#0B3538',
            deeper: '#082A2C',
            line: '#12474B'
          },
          gold: {
            DEFAULT: '#F2A73C',
            dim: '#C9832A'
          },
          bone: '#F4F6F5'
        },
        fontFamily: {
          display: ['"Space Grotesk"', 'sans-serif'],
          body: ['Inter', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace']
        }
      }
    }
  }
</script>

<style>
  body {
    background:
      radial-gradient(circle at 50% 0%, #0F4245 0%, #082A2C 55%, #061F21 100%);
  }

  .tracking-widest-plus { letter-spacing: 0.35em; }

  /* Peak SVG stroke draw-in */
  .peak-path {
    stroke-dasharray: 900;
    stroke-dashoffset: 900;
    animation: draw 2.6s ease-out forwards 0.3s;
  }
  .peak-path-inner {
    stroke-dasharray: 500;
    stroke-dashoffset: 500;
    animation: draw 2.2s ease-out forwards 1s;
  }
  @keyframes draw {
    to { stroke-dashoffset: 0; }
  }

  /* Climbing marker ascending the slope */
  .ascent-dot {
    offset-path: path('M 60 260 L 190 40 L 320 260');
    offset-rotate: 0deg;
    animation: ascend 4.5s cubic-bezier(.45,0,.55,1) infinite;
  }
  @keyframes ascend {
    0%   { offset-distance: 0%;   opacity: 0; }
    8%   { opacity: 1; }
    46%  { offset-distance: 50%;  opacity: 1; }
    50%  { opacity: 0; }
    50.01% { offset-distance: 50%; }
    100% { offset-distance: 50%; opacity: 0; }
  }

  /* Ambient glow pulse behind peak */
  .glow-pulse {
    animation: pulse-glow 5s ease-in-out infinite;
  }
  @keyframes pulse-glow {
    0%, 100% { opacity: 0.35; transform: scale(1); }
    50%      { opacity: 0.6;  transform: scale(1.06); }
  }

  /* Progress bar shimmer */
  .progress-fill {
    background: linear-gradient(90deg, #C9832A, #F2A73C, #C9832A);
    background-size: 200% 100%;
    animation: shimmer 3s linear infinite;
  }
  @keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  @media (prefers-reduced-motion: reduce) {
    .peak-path, .peak-path-inner, .ascent-dot, .glow-pulse, .progress-fill {
      animation: none !important;
    }
  }

  ::selection { background: #F2A73C; color: #082A2C; }
</style>
</head>

<body class="min-h-screen text-bone font-body flex flex-col items-center justify-center px-6 py-16 overflow-x-hidden relative">

  <!-- top hairline nav -->
  <header class="fixed top-0 left-0 right-0 flex items-center justify-between px-6 md:px-10 py-6 z-20">
    <span class="font-display font-semibold tracking-widest-plus text-xs md:text-sm text-bone/80">AYBAR <span class="text-gold">CORP</span></span>
    <span class="text-xs md:text-sm text-bone/50 tracking-wide hidden sm:inline">Bienes raíces</span>
  </header>

  <main class="w-full max-w-2xl flex flex-col items-center text-center mt-8">

    <!-- Signature: ascending peak graphic -->
    <div class="relative w-64 h-56 md:w-80 md:h-64 mb-8 animate__animated animate__fadeIn">
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="glow-pulse w-40 h-40 md:w-52 md:h-52 rounded-full bg-gold/20 blur-3xl"></div>
      </div>
      <svg viewBox="0 0 380 300" class="relative w-full h-full">
        <!-- outer peak (bone) -->
        <path class="peak-path" d="M 60 260 L 190 40 L 320 260"
              fill="none" stroke="#F4F6F5" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <!-- inner peak (gold) -->
        <path class="peak-path-inner" d="M 108 260 L 190 110 L 272 260"
              fill="none" stroke="#F2A73C" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
        <!-- ascent marker -->
        <circle class="ascent-dot" r="7" fill="#F4F6F5">
          <animate attributeName="r" values="6;8;6" dur="1.6s" repeatCount="indefinite" />
        </circle>
      </svg>
    </div>

    <p class="font-mono text-[11px] md:text-xs tracking-widest-plus text-gold/90 uppercase mb-4 animate__animated animate__fadeInUp animate__delay-1s">
      <i class="fa-solid fa-house-chimney mr-2"></i>Estamos en obra
    </p>

    <h1 class="font-display font-semibold text-3xl md:text-5xl leading-tight mb-5 animate__animated animate__fadeInUp animate__delay-1s">
      Estamos preparando algo mejor para usted
    </h1>

    <p class="text-bone/65 text-sm md:text-base max-w-md leading-relaxed mb-10 animate__animated animate__fadeInUp animate__delay-2s">
      Lamentamos su tiempo, estamos mejorando el sistema para ofrecerle una
      mejor experiencia. Muy pronto podrá volver a explorar nuestros
      proyectos inmobiliarios y agendar su próxima cita con nosotros.
    </p>

    <!-- progress indicator -->
    <div class="w-full max-w-xs mb-12 animate__animated animate__fadeInUp animate__delay-2s">
      <div class="flex justify-between font-mono text-[10px] text-bone/50 mb-2 tracking-wider">
        <span>PREPARANDO</span>
        <span>LISTO PRONTO</span>
      </div>
      <div class="h-1.5 w-full rounded-full bg-teal-line overflow-hidden">
        <div class="progress-fill h-full w-[72%] rounded-full"></div>
      </div>
    </div>

    <!-- promesa de cita -->
    <div class="w-full max-w-sm animate__animated animate__fadeInUp animate__delay-3s">
      <div class="border border-white/10 rounded-lg bg-teal-line/40 px-6 py-6 text-center">
        <i class="fa-regular fa-calendar-check text-gold text-xl mb-3"></i>
        <p class="text-sm text-bone/75 leading-relaxed">
          Cuando regresemos, tendremos una cita preparada especialmente
          para usted y sus próximos proyectos con AYBAR Corp.
        </p>
      </div>
    </div>

  </main>

  <footer class="fixed bottom-6 text-bone/30 text-[11px] font-mono tracking-wide">
    © 2026 AYBAR CORP — Todos los derechos reservados
  </footer>

</body>
</html>
