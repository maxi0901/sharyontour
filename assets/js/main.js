(function () {
  'use strict';

  // Reveal animations
  const revealItems = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealItems.length) {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('show');
        obs.unobserve(entry.target);
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    revealItems.forEach((el) => io.observe(el));
  } else {
    revealItems.forEach((el) => el.classList.add('show'));
  }

  // Mobile nav
  const navToggle = document.querySelector('.nav-toggle');
  const mainNav = document.querySelector('.main-nav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      const isOpen = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', String(isOpen));
    });
    mainNav.querySelectorAll('a').forEach((a) => {
      a.addEventListener('click', () => {
        mainNav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
    document.addEventListener('click', (event) => {
      if (mainNav.contains(event.target) || navToggle.contains(event.target)) return;
      mainNav.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  }

  // Legacy carousel
  document.querySelectorAll('.carousel-btn[data-scroll]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const wrap = btn.closest('.carousel-wrap');
      const target = wrap && wrap.querySelector('.' + btn.dataset.scroll);
      if (target) target.scrollBy({ left: target.clientWidth, behavior: 'smooth' });
    });
  });

  // Event slider — dots match slide count, prev/next, sync on scroll
  document.querySelectorAll('[data-event-slider]').forEach((slider) => {
    const track = slider.querySelector('.event-slider-track');
    const slides = slider.querySelectorAll('.event-slide');
    const dots = slider.querySelectorAll('.event-slider-dot');
    const prevBtn = slider.querySelector('.event-slider-nav.prev');
    const nextBtn = slider.querySelector('.event-slider-nav.next');
    if (!track || !slides.length) return;

    const scrollTo = (idx) => {
      const slide = slides[idx];
      if (!slide) return;
      const left = slide.offsetLeft - track.offsetLeft;
      track.scrollTo({ left, behavior: 'smooth' });
    };

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => scrollTo(idx));
    });

    if (prevBtn) prevBtn.addEventListener('click', () => {
      const current = currentIndex();
      scrollTo(Math.max(0, current - 1));
    });
    if (nextBtn) nextBtn.addEventListener('click', () => {
      const current = currentIndex();
      scrollTo(Math.min(slides.length - 1, current + 1));
    });

    function currentIndex() {
      const center = track.scrollLeft + track.clientWidth / 2;
      let best = 0;
      let bestDist = Infinity;
      slides.forEach((slide, idx) => {
        const slideCenter = slide.offsetLeft - track.offsetLeft + slide.clientWidth / 2;
        const dist = Math.abs(center - slideCenter);
        if (dist < bestDist) { bestDist = dist; best = idx; }
      });
      return best;
    }

    let scrollTimer = null;
    track.addEventListener('scroll', () => {
      if (scrollTimer) clearTimeout(scrollTimer);
      scrollTimer = setTimeout(() => {
        const idx = currentIndex();
        dots.forEach((d, i) => d.classList.toggle('is-active', i === idx));
      }, 80);
    });
  });
})();
