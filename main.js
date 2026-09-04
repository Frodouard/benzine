(function () {
  'use strict';

  /* Mobile navigation */
  var navToggle = document.getElementById('navToggle');
  var nav = document.getElementById('mainNav');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') nav.classList.remove('open');
    });
  }

  /* Quantity steppers */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-qty]');
    if (!btn) return;
    var wrap = btn.closest('.qty-stepper');
    if (!wrap) return;
    var input = wrap.querySelector('input[name="quantity"], input[data-qty-input]');
    if (!input) return;
    var step = btn.getAttribute('data-qty') === 'up' ? 1 : -1;
    var val = (parseInt(input.value, 10) || 1) + step;
    if (val < 1) val = 1;
    input.value = val;
  });

  /* Toast helper */
  window.showToast = function (message, type) {
    var typeClass = type === 'success' ? 'success' : type === 'error' ? 'error' : '';
    var toast = document.createElement('div');
    toast.className = 'toast show ' + typeClass;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function () {
      toast.classList.remove('show');
      setTimeout(function () { toast.remove(); }, 350);
    }, 2600);
  };
})();
