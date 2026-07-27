/**
 * MusicOfEveryone - main scripts
 */
(function () {
  'use strict';

  /* ---------------- Mobile navigation ---------------- */
  var toggle = document.querySelector('[data-nav-toggle]');
  var nav = document.getElementById('mainNav');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    nav.addEventListener('click', function (e) {
      if (e.target.closest('a') && window.innerWidth <= 980) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ---------------- Sticky header shadow ---------------- */
  var header = document.getElementById('siteHeader');
  var toTop = document.querySelector('[data-to-top]');

  function onScroll() {
    var y = window.pageYOffset || document.documentElement.scrollTop;
    if (header) {
      header.classList.toggle('is-scrolled', y > 10);
    }
    if (toTop) {
      toTop.classList.toggle('is-visible', y > 400);
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------------- Reveal on scroll ---------------- */
  var revealables = document.querySelectorAll('.reveal');

  if (revealables.length) {
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      revealables.forEach(function (el) { observer.observe(el); });
    } else {
      revealables.forEach(function (el) { el.classList.add('is-visible'); });
    }
  }

  /* ---------------- Animated counters ---------------- */
  var counters = document.querySelectorAll('[data-count]');

  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var duration = 1400;
    var start = null;

    function step(ts) {
      if (start === null) { start = ts; }
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(target * eased).toLocaleString() + suffix;
      if (progress < 1) { requestAnimationFrame(step); }
    }
    requestAnimationFrame(step);
  }

  if (counters.length && 'IntersectionObserver' in window) {
    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    counters.forEach(function (el) { counterObserver.observe(el); });
  } else {
    counters.forEach(animateCounter);
  }

  /* ---------------- Delete confirmation ---------------- */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm]');
    if (!el) { return; }
    if (!window.confirm(el.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  });

  /* ---------------- Auto slug generation (admin) ---------------- */
  var slugSources = document.querySelectorAll('[data-slug-source]');

  function slugify(value) {
    return value
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/đ/g, 'd')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  slugSources.forEach(function (source) {
    var targetName = source.getAttribute('data-slug-source');
    var target = document.querySelector('[name="' + targetName + '"]');
    if (!target) { return; }
    source.addEventListener('input', function () {
      if (!target.dataset.touched && !target.value.trim().length || target.dataset.auto === '1') {
        target.value = slugify(source.value);
        target.dataset.auto = '1';
      }
    });
    target.addEventListener('input', function () {
      target.dataset.touched = '1';
      target.dataset.auto = '0';
    });
  });

  /* ---------------- Image preview on upload ----------------
     The chosen file is decoded and painted onto a <canvas>; no blob/data URL
     is ever assigned to an element attribute. */
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    var group = input.closest('.form-group') || input.parentElement;
    var canvas = group ? group.querySelector('canvas[data-preview-target]') : null;
    var current = group ? group.querySelector('img[data-preview-current]') : null;
    if (!canvas || typeof window.createImageBitmap !== 'function') { return; }

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file || file.type.indexOf('image/') !== 0) { return; }

      window.createImageBitmap(file).then(function (bitmap) {
        var ctx = canvas.getContext('2d');
        var scale = Math.min(canvas.width / bitmap.width, canvas.height / bitmap.height);
        var w = Math.round(bitmap.width * scale);
        var h = Math.round(bitmap.height * scale);

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(bitmap, Math.round((canvas.width - w) / 2), Math.round((canvas.height - h) / 2), w, h);
        bitmap.close();

        canvas.hidden = false;
        if (current) { current.hidden = true; }
      }).catch(function () { /* unsupported image: keep the current preview */ });
    });
  });
})();
