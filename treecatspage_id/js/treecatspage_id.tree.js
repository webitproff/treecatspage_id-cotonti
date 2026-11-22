/**
 * treecatspage_id.tree.js
 * Раскрывает активную категорию в дереве + запоминает раскрытые в localStorage.
 * Адаптировано из treecatspage / marketTreeScript.
 */
document.addEventListener('DOMContentLoaded', function () {
  var containers = document.querySelectorAll('.treecatspage-id-tree');
  if (!containers.length) return;

  var STORAGE_KEY = 'treecatspage-id-tree-open';

  function readOpen() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch (e) { return []; }
  }
  function writeOpen(arr) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
  }

  function showCollapse(container, collapseEl) {
    if (!collapseEl) return;
    try {
      if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
        bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).show();
      } else {
        collapseEl.classList.add('show');
      }
    } catch (e) {
      collapseEl.classList.add('show');
    }
    var id = collapseEl.id;
    if (!id) return;
    var btn = container.querySelector('[data-bs-target="#' + id + '"]');
    if (btn) btn.setAttribute('aria-expanded', 'true');
  }

  var pathSegments = window.location.pathname
    .replace(/\/+$/, '')
    .split('/')
    .filter(function (s) { return s !== ''; })
    .reverse();

  containers.forEach(function (container) {

    readOpen().forEach(function (catId) {
      var item = container.querySelector('[data-id="' + CSS.escape(catId) + '"]');
      if (!item) return;
      var collapse = item.closest('.collapse');
      if (collapse) showCollapse(container, collapse);
    });

    var currentCat = '';
    for (var i = 0; i < pathSegments.length; i++) {
      var seg = pathSegments[i];
      if (container.querySelector('[data-id="' + CSS.escape(seg) + '"]')) {
        currentCat = seg;
        break;
      }
    }

    if (currentCat) {
      var activeItem = container.querySelector('[data-id="' + CSS.escape(currentCat) + '"]');
      if (activeItem) {
        var activeLink = activeItem.querySelector('a[href]');
        if (activeLink) activeLink.classList.add('active');

        var collapse = activeItem.closest('.collapse');
        while (collapse && container.contains(collapse)) {
          showCollapse(container, collapse);
          var parentItem = collapse.closest('[data-id]');
          collapse = parentItem ? parentItem.closest('.collapse') : null;
        }
      }
    }

    container.querySelectorAll('.toggle-subcats').forEach(function (btn) {
      var targetSel = btn.getAttribute('data-bs-target');
      if (!targetSel) return;
      var collapseEl = container.querySelector(targetSel);
      if (!collapseEl) return;

      function sync() {
        var shown = collapseEl.classList.contains('show');
        btn.setAttribute('aria-expanded', shown ? 'true' : 'false');
        var item = collapseEl.closest('[data-id]');
        if (!item) return;
        var catId = item.dataset.id;
        if (!catId) return;
        var arr = readOpen();
        if (shown) {
          if (arr.indexOf(catId) === -1) { arr.push(catId); writeOpen(arr); }
        } else {
          writeOpen(arr.filter(function (id) { return id !== catId; }));
        }
      }

      collapseEl.addEventListener('shown.bs.collapse', sync);
      collapseEl.addEventListener('hidden.bs.collapse', sync);
      sync();
    });
  });
});
