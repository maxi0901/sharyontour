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

  // Carousel arrow buttons (prev/next) with boundary auto-hide + click tracking
  function trackCarouselClick(direction) {
    try {
      const payload = JSON.stringify({
        type: 'carousel_arrow',
        direction: direction || 'next',
        page: location.pathname
      });
      if (navigator.sendBeacon) {
        navigator.sendBeacon('/api/track-click.php', new Blob([payload], { type: 'application/json' }));
      } else {
        fetch('/api/track-click.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: payload,
          keepalive: true
        });
      }
    } catch (err) { /* ignore */ }
  }

  function initSliderDots(config) {
    const { root, track, slideSelector, dotsWrap, dotClass, prevBtn, nextBtn, hideIfSingle, trackClicks } = config;
    if (!root || !track || !dotsWrap) return;

    const slides = Array.from(track.querySelectorAll(slideSelector));
    if (!slides.length) return;

    const prevBtns = Array.isArray(prevBtn) ? prevBtn : (prevBtn ? [prevBtn] : []);
    const nextBtns = Array.isArray(nextBtn) ? nextBtn : (nextBtn ? [nextBtn] : []);

    let pages = [];
    let dots = [];

    const isMobileViewport = () => window.innerWidth <= 768;

    const clampIndex = (idx) => Math.max(0, Math.min(idx, pages.length - 1));

    const buildPages = () => {
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      const starts = slides
        .map((slide) => Math.max(0, Math.min(maxScroll, slide.offsetLeft - track.offsetLeft)))
        .filter((value, index, arr) => index === 0 || Math.abs(value - arr[index - 1]) > 8);

      const unique = [];
      starts.forEach((start) => {
        if (!unique.length || Math.abs(start - unique[unique.length - 1]) > 8) unique.push(start);
      });

      pages = unique.filter((start, index) => {
        const isLast = Math.abs(start - maxScroll) <= 8;
        return index === 0 || !isLast || Math.abs(start - unique[index - 1]) > 8;
      });

      if (!pages.length) pages = [0];

      dotsWrap.innerHTML = '';
      dots = pages.map((_, idx) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = dotClass;
        dot.setAttribute('aria-label', `Seite ${idx + 1} anzeigen`);
        dotsWrap.appendChild(dot);
        return dot;
      });

      dots.forEach((dot, idx) => dot.addEventListener('click', () => scrollToPage(idx)));
      dotsWrap.hidden = Boolean(hideIfSingle && pages.length <= 1);
    };

    const currentIndex = () => {
      let best = 0;
      let bestDist = Infinity;
      pages.forEach((start, idx) => {
        const dist = Math.abs(track.scrollLeft - start);
        if (dist < bestDist) { bestDist = dist; best = idx; }
      });
      return clampIndex(best);
    };

    const updateControls = () => {
      const idx = currentIndex();
      dots.forEach((dot, i) => dot.classList.toggle('is-active', i === idx));
      const disablePrev = pages.length <= 1 || idx <= 0;
      const disableNext = pages.length <= 1 || idx >= pages.length - 1;
      prevBtns.forEach((btn) => {
        btn.classList.toggle('is-disabled', disablePrev);
        btn.classList.remove('is-hidden');
        btn.disabled = disablePrev;
      });
      nextBtns.forEach((btn) => {
        btn.classList.toggle('is-disabled', disableNext);
        btn.classList.remove('is-hidden');
        btn.disabled = disableNext;
      });
    };

    const scrollToPage = (idx) => {
      const safeIndex = clampIndex(idx);
      const left = pages[safeIndex];
      if (typeof left !== 'number') return;
      track.scrollTo({ left, behavior: 'smooth' });
      updateControls();
    };

    prevBtns.forEach((btn) => btn.addEventListener('click', () => {
      if (isMobileViewport()) return;
      const idx = currentIndex();
      if (idx <= 0) return;
      scrollToPage(idx - 1);
      if (trackClicks) trackCarouselClick('prev');
    }));
    nextBtns.forEach((btn) => btn.addEventListener('click', () => {
      if (isMobileViewport()) return;
      const idx = currentIndex();
      if (idx >= pages.length - 1) return;
      scrollToPage(idx + 1);
      if (trackClicks) trackCarouselClick('next');
    }));

    let timer = null;
    track.addEventListener('scroll', () => {
      if (timer) clearTimeout(timer);
      timer = setTimeout(updateControls, 80);
    });

    window.addEventListener('resize', () => {
      buildPages();
      scrollToPage(currentIndex());
      updateControls();
    });

    buildPages();
    scrollToPage(currentIndex());
    updateControls();
  }

  document.querySelectorAll('.carousel-wrap').forEach((wrap) => {
    initSliderDots({
      root: wrap,
      track: wrap.querySelector('.events-scroll'),
      slideSelector: '.event-card, .card',
      dotsWrap: wrap.parentElement && wrap.parentElement.querySelector('[data-event-dots]'),
      dotClass: 'event-dot',
      prevBtn: Array.from(wrap.querySelectorAll('.carousel-btn[data-direction="prev"]')),
      nextBtn: Array.from(wrap.querySelectorAll('.carousel-btn[data-direction="next"]')),
      hideIfSingle: true,
      trackClicks: true
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
      const count = Number(data.count || 0);
      const max = Number(data.max || 600);
      const threshold = Number(data.threshold || 150);
      const remaining = Math.max(0, max - count);
      el.textContent = data.label || (remaining <= 0 ? 'Ausverkauft' : `Noch ${remaining} Tickets`);
      if (remaining <= 0 || (count >= threshold && remaining < 100)) {
        el.classList.add('text-danger');
      }
      if (remaining <= 0) {
        const ticketBtn = document.querySelector(`.js-ticket-btn[data-event-id="${id}"]`);
        if (ticketBtn) { ticketBtn.disabled = true; ticketBtn.textContent = 'Ausverkauft'; }
      }
    } catch (err) { /* ignore */ }
  });

  // Event-card video autoplay coordinator
  function initEventVideos() {
    const videos = Array.from(document.querySelectorAll('video.event-video'));
    if (!videos.length) return;
    if (!('IntersectionObserver' in window)) return;

    const visibility = new WeakMap();
    const carouselActive = new WeakMap();
    let currentlyPlaying = null;

    const carouselSelector = '.events-scroll, [data-event-slider]';

    videos.forEach((v) => {
      visibility.set(v, false);
      // Outside any carousel: always treated as carousel-active.
      carouselActive.set(v, !v.closest(carouselSelector));
    });

    const tryPlay = (v) => {
      if (!visibility.get(v) || !carouselActive.get(v)) return;
      if (currentlyPlaying && currentlyPlaying !== v) {
        try { currentlyPlaying.pause(); } catch (_) { /* ignore */ }
      }
      currentlyPlaying = v;
      try {
        const p = v.play();
        if (p && typeof p.catch === 'function') {
          p.catch(() => { /* autoplay blocked, ignore */ });
        }
      } catch (_) { /* ignore */ }
    };

    const pause = (v) => {
      try { v.pause(); } catch (_) { /* ignore */ }
      if (currentlyPlaying === v) currentlyPlaying = null;
    };

    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        const v = entry.target;
        const visible = entry.intersectionRatio >= 0.6;
        visibility.set(v, visible);
        if (visible) tryPlay(v); else pause(v);
      });
    }, { threshold: [0, 0.6, 1] });

    videos.forEach((v) => io.observe(v));

    // Carousel coordination: only the active slide's video may play.
    const tracks = new Set();
    videos.forEach((v) => {
      const t = v.closest(carouselSelector);
      if (t) tracks.add(t);
    });

    tracks.forEach((track) => {
      const trackVideos = Array.from(track.querySelectorAll('video.event-video'));

      const evaluate = () => {
        const trackRect = track.getBoundingClientRect();
        const trackCenter = trackRect.left + trackRect.width / 2;
        const slideMap = new Map();

        trackVideos.forEach((v) => {
          const slide = v.parentElement && v.parentElement.closest(
            '.event-card, .card, .event-slide, .events-item, .gallery-overview-item'
          );
          if (!slide || !track.contains(slide)) return;
          slideMap.set(v, slide);
        });

        let activeSlide = null;
        let bestDist = Infinity;
        const uniqueSlides = new Set(slideMap.values());
        uniqueSlides.forEach((slide) => {
          const r = slide.getBoundingClientRect();
          const center = r.left + r.width / 2;
          const dist = Math.abs(center - trackCenter);
          if (dist < bestDist) { bestDist = dist; activeSlide = slide; }
        });

        slideMap.forEach((slide, v) => {
          const isActive = (slide === activeSlide);
          carouselActive.set(v, isActive);
          if (!isActive) {
            pause(v);
          } else if (visibility.get(v)) {
            tryPlay(v);
          }
        });
      };

      let scrollTimer = null;
      track.addEventListener('scroll', () => {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(evaluate, 80);
      }, { passive: true });

      window.addEventListener('resize', () => {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(evaluate, 120);
      });

      evaluate();
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        videos.forEach(pause);
      } else {
        videos.forEach((v) => {
          if (visibility.get(v) && carouselActive.get(v)) tryPlay(v);
        });
      }
    });
  }

  initEventVideos();
})();
