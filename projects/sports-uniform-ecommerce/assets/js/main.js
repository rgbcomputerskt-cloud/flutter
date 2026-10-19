/* ==========================================================
   VANTAGE storefront — interactions
   ========================================================== */
(function () {
  'use strict';
  var BASE = (function () {
    var s = document.querySelector('script[src*="assets/js/main.js"]');
    return s ? s.getAttribute('src').replace(/assets\/js\/main\.js.*$/, '') : '';
  })();

  /* ---- sticky header shadow ---- */
  var header = document.getElementById('siteHeader');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('is-stuck', window.scrollY > 10);
    }, { passive: true });
  }

  /* ---- mobile nav ---- */
  var navToggle = document.getElementById('navToggle');
  var mainnav = document.getElementById('mainnav');
  if (navToggle && mainnav) {
    navToggle.addEventListener('click', function () { mainnav.classList.toggle('is-open'); });
  }

  /* ---- hero slider ---- */
  (function () {
    var slider = document.getElementById('slider');
    if (!slider) return;
    var slides = [].slice.call(slider.querySelectorAll('.slide'));
    var dots = [].slice.call(slider.querySelectorAll('[data-dot]'));
    if (slides.length < 2) return;
    var i = 0, timer;

    function go(n) {
      slides[i].classList.remove('is-active');
      if (dots[i]) dots[i].classList.remove('is-active');
      i = (n + slides.length) % slides.length;
      slides[i].classList.add('is-active');
      if (dots[i]) dots[i].classList.add('is-active');
    }
    function next() { go(i + 1); }
    function start() { timer = setInterval(next, 6000); }
    function reset() { clearInterval(timer); start(); }

    slider.querySelectorAll('[data-slide]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        go(i + (btn.dataset.slide === 'next' ? 1 : -1)); reset();
      });
    });
    dots.forEach(function (d) {
      d.addEventListener('click', function () { go(+d.dataset.dot); reset(); });
    });
    start();
  })();

  /* ---- product gallery: thumbnails + cloud zoom ---- */
  (function () {
    var stage = document.getElementById('zoom');
    if (!stage) return;
    var img = document.getElementById('zoomImg');
    var lens = document.getElementById('zoomLens');
    var result = document.getElementById('zoomResult');

    // thumbnails
    document.querySelectorAll('.gallery__thumb').forEach(function (t) {
      t.addEventListener('click', function () {
        var full = t.dataset.full;
        img.src = full;
        stage.dataset.zoom = full;
        document.querySelectorAll('.gallery__thumb').forEach(function (x) { x.classList.remove('is-active'); });
        t.classList.add('is-active');
      });
    });

    if (!lens || !result || window.matchMedia('(max-width:1080px)').matches) return;

    function ratios() {
      return { cx: result.offsetWidth / lens.offsetWidth, cy: result.offsetHeight / lens.offsetHeight };
    }
    function move(e) {
      e.preventDefault();
      var rect = img.getBoundingClientRect();
      var point = e.touches ? e.touches[0] : e;
      var x = point.clientX - rect.left;
      var y = point.clientY - rect.top;
      var lw = lens.offsetWidth / 2, lh = lens.offsetHeight / 2;
      var lx = Math.max(0, Math.min(x - lw, rect.width - lens.offsetWidth));
      var ly = Math.max(0, Math.min(y - lh, rect.height - lens.offsetHeight));
      lens.style.left = lx + 'px';
      lens.style.top = ly + 'px';
      var r = ratios();
      result.style.backgroundImage = 'url(' + stage.dataset.zoom + ')';
      result.style.backgroundSize = (rect.width * r.cx) + 'px ' + (rect.height * r.cy) + 'px';
      result.style.backgroundPosition = '-' + (lx * r.cx) + 'px -' + (ly * r.cy) + 'px';
    }
    function show() { lens.style.display = 'block'; result.style.display = 'block'; }
    function hide() { lens.style.display = 'none'; result.style.display = 'none'; }
    stage.addEventListener('mouseenter', show);
    stage.addEventListener('mouseleave', hide);
    stage.addEventListener('mousemove', move);
  })();

  /* ---- product info tabs ---- */
  document.querySelectorAll('.pinfo__tabs button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tab = btn.dataset.tab;
      document.querySelectorAll('.pinfo__tabs button').forEach(function (b) { b.classList.remove('is-active'); });
      document.querySelectorAll('.pinfo__panel').forEach(function (p) { p.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var panel = document.querySelector('.pinfo__panel[data-panel="' + tab + '"]');
      if (panel) panel.classList.add('is-active');
    });
  });

  /* ---- quantity steppers ---- */
  document.querySelectorAll('.qtybox').forEach(function (box) {
    var input = box.querySelector('input');
    box.querySelectorAll('[data-step]').forEach(function (b) {
      b.addEventListener('click', function () {
        var v = parseInt(input.value, 10) || 1;
        v += parseInt(b.dataset.step, 10);
        input.value = Math.max(parseInt(input.min, 10) || 1, v);
      });
    });
  });

  /* ---- modal (quick view + size chart) ---- */
  var modal = document.getElementById('quickview');
  var modalContent = document.getElementById('quickviewContent');
  function openModal(html) {
    if (!modal) return;
    modalContent.innerHTML = html;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
  }
  function loadInto(urlStr) {
    openModal('<div class="qv-loading">Loading…</div>');
    fetch(urlStr).then(function (r) { return r.text(); }).then(openModal)
      .catch(function () { openModal('<div class="qv-loading">Could not load. Please try again.</div>'); });
  }
  if (modal) {
    modal.querySelectorAll('[data-close]').forEach(function (el) { el.addEventListener('click', closeModal); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
  }

  document.addEventListener('click', function (e) {
    var qv = e.target.closest('[data-quickview]');
    if (qv) { e.preventDefault(); loadInto(BASE + 'quickview.php?id=' + qv.dataset.quickview); return; }
    var sc = e.target.closest('[data-sizechart]');
    if (sc) { e.preventDefault(); loadInto(BASE + 'size-chart.php?ajax=1&id=' + sc.dataset.sizechart); return; }
  });
})();
