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



  function initSliderDots(config) {
    const { root, track, slideSelector, dotsWrap, dotClass, prevBtn, nextBtn, hideIfSingle } = config;
    if (!root || !track || !dotsWrap) return;

    const slides = Array.from(track.querySelectorAll(slideSelector));
    if (!slides.length) return;

    dotsWrap.innerHTML = '';
    const dots = slides.map((_, idx) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = dotClass;
      dot.setAttribute('aria-label', `Event ${idx + 1} anzeigen`);
      dotsWrap.appendChild(dot);
      return dot;
    });

    dotsWrap.hidden = Boolean(hideIfSingle && slides.length <= 1);

    const currentIndex = () => {
      const center = track.scrollLeft + track.clientWidth / 2;
      let best = 0;
      let bestDist = Infinity;
      slides.forEach((slide, idx) => {
        const slideCenter = slide.offsetLeft - track.offsetLeft + slide.clientWidth / 2;
        const dist = Math.abs(center - slideCenter);
        if (dist < bestDist) { bestDist = dist; best = idx; }
      });
      return best;
    };

    const setActive = (idx) => dots.forEach((dot, i) => dot.classList.toggle('is-active', i === idx));
    const scrollToSlide = (idx) => {
      const slide = slides[idx];
      if (!slide) return;
      track.scrollTo({ left: slide.offsetLeft - track.offsetLeft, behavior: 'smooth' });
      setActive(idx);
    };

    dots.forEach((dot, idx) => dot.addEventListener('click', () => scrollToSlide(idx)));

    if (prevBtn) prevBtn.addEventListener('click', () => scrollToSlide(Math.max(0, currentIndex() - 1)));
    if (nextBtn) nextBtn.addEventListener('click', () => scrollToSlide(Math.min(slides.length - 1, currentIndex() + 1)));

    let timer = null;
    track.addEventListener('scroll', () => {
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => setActive(currentIndex()), 80);
    });
    window.addEventListener('resize', () => setActive(currentIndex()));

    setActive(currentIndex());
  }

  document.querySelectorAll('.carousel-wrap').forEach((wrap) => {
    initSliderDots({
      root: wrap,
      track: wrap.querySelector('.events-scroll'),
      slideSelector: '.event-card, .card',
      dotsWrap: wrap.parentElement && wrap.parentElement.querySelector('[data-event-dots]'),
      dotClass: 'event-dot',
      hideIfSingle: true
    });
  });

  document.querySelectorAll('[data-event-slider]').forEach((slider) => {
    initSliderDots({
      root: slider,
      track: slider.querySelector('.event-slider-track'),
      slideSelector: '.event-slide',
      dotsWrap: slider.querySelector('.event-slider-dots'),
      dotClass: 'event-slider-dot',
      prevBtn: slider.querySelector('.event-slider-nav.prev'),
      nextBtn: slider.querySelector('.event-slider-nav.next'),
      hideIfSingle: true
    });
  });

  // Location modal
  const modal = document.getElementById('eventModal');
  if (modal) {
    const titleEl = modal.querySelector('.js-modal-title');
    const locationEl = modal.querySelector('.js-modal-location');
    const addressEl = modal.querySelector('.js-modal-address');

    const close = () => { modal.hidden = true; };
    modal.querySelectorAll('.js-modal-close').forEach((el) => el.addEventListener('click', close));

    function openLocation(btn) {
      const isTicketOpening = btn.dataset.eventTicketOpening === '1';
      titleEl.textContent = btn.dataset.eventName || 'Event';
      if (isTicketOpening) {
        locationEl.textContent = 'Kassel';
        addressEl.textContent = 'Der genaue Standort wird rechtzeitig bekanntgegeben.';
      } else {
        locationEl.textContent = btn.dataset.eventLocation || '';
        addressEl.textContent = btn.dataset.eventAddress || '';
      }
      modal.hidden = false;
    }

    document.querySelectorAll('.js-location-btn').forEach((btn) => btn.addEventListener('click', () => openLocation(btn)));
  }

  document.querySelectorAll('.js-ticket-btn').forEach((btn) => btn.addEventListener('click', () => {
    window.location.href = '/ticket-buchen.php';
  }));

  document.querySelectorAll('.js-ticket-stock').forEach(async (el) => {
    const id = el.dataset.eventId;
    try {
      const res = await fetch(`/api/get-ticket-count.php?event_id=${encodeURIComponent(id)}`);
      const data = await res.json();
      const remaining = Math.max(0, 600 - Number(data.count || 0));
      el.textContent = remaining <= 0 ? 'Ausverkauft' : `Noch ${remaining} Tickets verfügbar`;
      if (remaining < 100) el.classList.add('text-danger');
      if (remaining <= 0) {
        const ticketBtn = document.querySelector(`.js-ticket-btn[data-event-id="${id}"]`);
        if (ticketBtn) { ticketBtn.disabled = true; ticketBtn.textContent = 'Ausverkauft'; }
      }
    } catch (err) { /* ignore */ }
  });
})();
