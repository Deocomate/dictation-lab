@props([
    'title' => 'Dictation Lab',
    'description' => '',
])

<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}" />

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'app-bg': '#F9F9FA',
            'surface': '#FFFFFF',
            'border-light': '#E1E4E8',
            'text-primary': '#0E101A',
            'text-secondary': '#6D758D',
            'text-disabled': '#B9BDC5',
            'brand': '#11A683',
            'brand-dark': '#0E8A6D',
            'brand-light': '#E8F8F3',
            'semantic-red': '#FF5E5E',
            'semantic-blue': '#007AFF',
            'semantic-green': '#11A683',
            'semantic-purple': '#8F00FF',
            'semantic-yellow': '#FFD500',
          },
          fontFamily: {
            sans: ['Inter', 'Roboto', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
            brand: ['Plus Jakarta Sans', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
          },
          boxShadow: {
            'card': '0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)',
            'card-hover': '0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)',
            'float': '0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)',
          },
          animation: {
            'fade-in': 'fadeIn 0.6s ease-out forwards',
            'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
            'slide-in-left': 'slideInLeft 0.6s ease-out forwards',
            'slide-in-right': 'slideInRight 0.6s ease-out forwards',
            'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
            'typing-cursor': 'blink 1s step-end infinite',
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' },
            },
            fadeInUp: {
              '0%': { opacity: '0', transform: 'translateY(24px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            slideInLeft: {
              '0%': { opacity: '0', transform: 'translateX(-32px)' },
              '100%': { opacity: '1', transform: 'translateX(0)' },
            },
            slideInRight: {
              '0%': { opacity: '0', transform: 'translateX(32px)' },
              '100%': { opacity: '1', transform: 'translateX(0)' },
            },
            pulseSoft: {
              '0%, 100%': { opacity: '1' },
              '50%': { opacity: '0.7' },
            },
            blink: {
              '0%, 100%': { opacity: '1' },
              '50%': { opacity: '0' },
            },
          },
        },
      },
    };
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />

  <style>
    [x-cloak] {
      display: none !important;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #0E101A;
      background-color: #F9F9FA;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .underline-grammar {
      border-bottom: 2px solid #FF5E5E;
      padding-bottom: 1px;
      cursor: pointer;
    }

    .underline-coherence {
      border-bottom: 2px solid #007AFF;
      padding-bottom: 1px;
      cursor: pointer;
    }

    .underline-lexical {
      border-bottom: 2px solid #11A683;
      padding-bottom: 1px;
      cursor: pointer;
    }

    .underline-gra {
      border-bottom: 2px solid #8F00FF;
      padding-bottom: 1px;
      cursor: pointer;
    }

    .underline-wavy {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='6' height='3' viewBox='0 0 6 3'%3E%3Cpath d='M0 2.5 Q1.5 0 3 2.5 Q4.5 5 6 2.5' fill='none' stroke='%23FF5E5E' stroke-width='1'/%3E%3C/svg%3E");
      background-repeat: repeat-x;
      background-position: bottom;
      background-size: 6px 3px;
      padding-bottom: 3px;
    }

    .tooltip-card {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
      transition: opacity 0.2s ease-in-out;
    }

    .typing-char-correct {
      color: #11A683;
    }

    .typing-char-wrong {
      color: #FF5E5E;
      background: rgba(255, 94, 94, 0.08);
      border-radius: 2px;
    }

    .typing-char-pending {
      color: #B9BDC5;
    }

    .typing-cursor-line {
      display: inline-block;
      width: 2px;
      height: 1.2em;
      background: #0E101A;
      vertical-align: text-bottom;
      animation: blink 1s step-end infinite;
    }

    .reveal {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.7s ease-out, transform 0.7s ease-out;
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .navbar-blur {
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      background: rgba(255, 255, 255, 0.85);
    }

    .feature-card {
      transition: box-shadow 0.25s ease, transform 0.25s ease;
    }

    .feature-card:hover {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
      transform: translateY(-4px);
    }

    .band-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 10px;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      line-height: 1.5;
    }

    .pricing-card-popular {
      border: 2px solid #11A683;
      position: relative;
    }

    .skeleton {
      background: linear-gradient(90deg, #E1E4E8 25%, #F0F1F5 50%, #E1E4E8 75%);
      background-size: 200% 100%;
      animation: shimmer 1.5s infinite;
      border-radius: 4px;
    }

    @keyframes shimmer {
      0% {
        background-position: 200% 0;
      }

      100% {
        background-position: -200% 0;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal {
        transition: none;
        opacity: 1;
        transform: none;
      }

      .feature-card {
        transition: none;
      }

      .typing-cursor-line {
        animation: none;
        opacity: 1;
      }

      .skeleton {
        animation: none;
      }

      * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
      }
    }

    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: #F9F9FA;
    }

    ::-webkit-scrollbar-thumb {
      background: #B9BDC5;
      border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #6D758D;
    }
  </style>
</head>

<body class="bg-app-bg text-text-primary">
  <x-client.layout.header />

  {{ $slot }}

  <x-client.layout.footer />
  <x-client.layout.ai-chat-widget />

  <script>
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
      mobileMenuBtn.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden');
        mobileMenuBtn.setAttribute('aria-expanded', !isOpen);
      });

      mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
          mobileMenu.classList.add('hidden');
          mobileMenuBtn.setAttribute('aria-expanded', 'false');
        });
      });
    }

    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -60px 0px',
    });

    document.querySelectorAll('.reveal').forEach((el) => {
      revealObserver.observe(el);
    });

    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener('click', function onClick(event) {
        const targetId = this.getAttribute('href');

        if (targetId === '#') {
          return;
        }

        const target = document.querySelector(targetId);

        if (target) {
          event.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    const navbar = document.querySelector('nav');

    if (navbar) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
          navbar.classList.add('shadow-card');
        } else {
          navbar.classList.remove('shadow-card');
        }
      }, { passive: true });
    }
  </script>
</body>
</html>
