/* S-ART / Shary on Tour — premium street-art interactions */

(function () {
  'use strict';

  /* ── Scroll reveal ─────────────────────────────────────────────── */
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );

  document.querySelectorAll('.reveal').forEach((el) => revealObserver.observe(el));

  /* ── Mobile nav toggle ─────────────────────────────────────────── */
  const navToggle = document.querySelector('.nav-toggle');
  const mainNav   = document.querySelector('.main-nav');

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      const isOpen = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', String(isOpen));
    });

    mainNav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        mainNav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });

    document.addEventListener('click', (e) => {
      if (!mainNav.contains(e.target) && !navToggle.contains(e.target)) {
        mainNav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ── Hero parallax on scroll ───────────────────────────────────── */
  const heroSection  = document.querySelector('.hero');
  const heroSpray    = document.querySelector('.hero-spray');

  if (heroSection) {
    let ticking = false;

    const onScroll = () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const scrollY  = window.scrollY;
          const heroH    = heroSection.offsetHeight;
          const progress = Math.min(scrollY / heroH, 1);

          if (heroSpray) {
            heroSpray.style.transform = `translateY(${scrollY * 0.18}px)`;
          }

          const heroContent = heroSection.querySelector('.container');
          if (heroContent) {
            heroContent.style.opacity  = String(1 - progress * 0.55);
            heroContent.style.transform = `translateY(${scrollY * 0.12}px)`;
          }

          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ── Artwork card 3-D tilt on mousemove ────────────────────────── */
  document.querySelectorAll('.artwork-card').forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const rect  = card.getBoundingClientRect();
      const dx    = (e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2);
      const dy    = (e.clientY - rect.top  - rect.height / 2) / (rect.height / 2);
      const rotX  = (-dy * 6).toFixed(2);
      const rotY  = ( dx * 6).toFixed(2);
      card.style.transform = `perspective(600px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-8px) scale(1.012)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  /* ── Ambient hero background drift ────────────────────────────── */
  if (heroSection) {
    let t = 0;
    const driftLoop = () => {
      t += 0.003;
      const x = 80 + Math.sin(t) * 8;
      const y = 50 + Math.cos(t * 0.7) * 6;
      /* Only update the first radial gradient, leave main gradient intact */
      heroSection.style.setProperty('--hero-drift-x', x + '%');
      heroSection.style.setProperty('--hero-drift-y', y + '%');
      requestAnimationFrame(driftLoop);
    };
    driftLoop();
  }

  /* ── Stagger cards inside reveal-group ─────────────────────────── */
  document.querySelectorAll('.reveal-group').forEach((group) => {
    group.querySelectorAll('.reveal').forEach((child, idx) => {
      child.style.transitionDelay = `${idx * 80}ms`;
    });
  });

})();
