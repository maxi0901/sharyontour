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

  // Location + ticket modals
  const modal = document.getElementById('eventModal');
  if (modal) {
    const titleEl = modal.querySelector('.js-modal-title');
    const locationEl = modal.querySelector('.js-modal-location');
    const addressEl = modal.querySelector('.js-modal-address');
    const form = modal.querySelector('#ticketModalForm');
    const responseEl = modal.querySelector('.js-ticket-response');
    const ticketEventId = modal.querySelector('#ticketEventId');

    const close = () => { modal.hidden = true; form.hidden = true; responseEl.textContent = ''; };
    modal.querySelectorAll('.js-modal-close').forEach((el) => el.addEventListener('click', close));

    function openLocation(btn) {
      const isOpening = btn.dataset.eventIsOpening === '1';
      titleEl.textContent = btn.dataset.eventName || 'Event';
      if (isOpening) {
        locationEl.textContent = 'Kassel';
        addressEl.textContent = 'Der genaue Standort wird rechtzeitig bekanntgegeben.';
      } else {
        locationEl.textContent = btn.dataset.eventLocation || '';
        addressEl.textContent = btn.dataset.eventAddress || '';
      }
      form.hidden = true;
      modal.hidden = false;
    }

    document.querySelectorAll('.js-location-btn').forEach((btn) => btn.addEventListener('click', () => openLocation(btn)));
    document.querySelectorAll('.js-ticket-btn').forEach((btn) => btn.addEventListener('click', () => {
      titleEl.textContent = 'Gratis Ticket sichern';
      locationEl.textContent = 'Container Opening Kassel';
      addressEl.textContent = '';
      ticketEventId.value = btn.dataset.eventId || '';
      responseEl.textContent = '';
      form.hidden = false;
      modal.hidden = false;
    }));

    if (form) {
      form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const fd = new FormData(form);
        const res = await fetch('/api/create-ticket.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
          responseEl.textContent = `Ticket erfolgreich erstellt (${data.ticket_id})`;
          const activeBtn = document.querySelector(`.js-ticket-btn[data-event-id="${fd.get('event_id')}"]`);
          if (activeBtn) { activeBtn.disabled = true; activeBtn.textContent = 'Ticket erstellt'; }
        } else {
          responseEl.textContent = data.message || 'Fehler beim Erstellen';
        }
      });
    }

    document.querySelectorAll('.js-ticket-stock').forEach(async (el) => {
      const id = el.dataset.eventId;
      const res = await fetch(`/api/get-ticket-count.php?event_id=${encodeURIComponent(id)}`);
      const data = await res.json();
      const remaining = Math.max(0, 600 - Number(data.count || 0));
      el.textContent = remaining <= 0 ? 'Ausverkauft' : `Noch ${remaining} Tickets verfügbar`;
      if (remaining < 100) el.classList.add('text-danger');
      if (remaining <= 0) {
        const btn = document.querySelector(`.js-ticket-btn[data-event-id="${id}"]`);
        if (btn) { btn.disabled = true; btn.textContent = 'Ausverkauft'; }
      }
    });
  }
})();
