/**
 * assets/js/script.js
 * X-RPL 2 Class Portal — Main JavaScript
 */

"use strict";

/* ── Theme Switcher ── */
const ThemeManager = (() => {
  const KEY = "xrpl2-theme";
  const html = document.documentElement;
  const btn  = document.getElementById("themeToggle");
  const iconEl = btn ? btn.querySelector("i") : null;

  function apply(theme) {
    html.setAttribute("data-theme", theme);
    localStorage.setItem(KEY, theme);
    if (iconEl) {
      iconEl.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon";
    }
  }

  function init() {
    const saved = localStorage.getItem(KEY) || "dark";
    apply(saved);
    if (btn) {
      btn.addEventListener("click", () => {
        const current = html.getAttribute("data-theme") || "dark";
        apply(current === "dark" ? "light" : "dark");
      });
    }
  }

  return { init };
})();

/* ── Navbar Scroll Effect ── */
function initNavbar() {
  const nav = document.getElementById("mainNav");
  if (!nav) return;
  window.addEventListener("scroll", () => {
    nav.classList.toggle("scrolled", window.scrollY > 50);
  });
}

/* ── Smooth Scroll for anchor links ── */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener("click", e => {
      const target = document.querySelector(a.getAttribute("href"));
      if (target) {
        e.preventDefault();
        const offset = document.getElementById("mainNav")?.offsetHeight || 70;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: "smooth" });
      }
    });
  });
}

/* ── Student Search & Filter ── */
function initStudentFilter() {
  const searchInput = document.getElementById("studentSearch");
  const filterBtns  = document.querySelectorAll(".filter-btn");
  const cards       = document.querySelectorAll(".student-card-wrap");
  if (!searchInput || !cards.length) return;

  let currentFilter = "all";

  function filterCards() {
    const query = searchInput.value.toLowerCase().trim();
    cards.forEach(wrap => {
      const name    = wrap.dataset.name  || "";
      const jabatan = wrap.dataset.jabatan || "";
      const matchSearch  = name.includes(query);
      const matchFilter  = currentFilter === "all" || jabatan === currentFilter;
      wrap.style.display = matchSearch && matchFilter ? "" : "none";
    });
  }

  searchInput.addEventListener("input", filterCards);

  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      currentFilter = btn.dataset.filter;
      filterCards();
    });
  });
}

/* ── Lightbox ── */
function initLightbox() {
  const overlay  = document.getElementById("lightbox");
  const lbImg    = document.getElementById("lightboxImg");
  const lbTitle  = document.getElementById("lightboxTitle");
  const lbDate   = document.getElementById("lightboxDate");
  const lbClose  = document.getElementById("lightboxClose");
  if (!overlay) return;

  document.querySelectorAll(".gallery-item").forEach(item => {
    item.addEventListener("click", () => {
      const src   = item.dataset.src;
      const title = item.dataset.title;
      const date  = item.dataset.date;
      lbImg.src         = src;
      lbTitle.textContent = title;
      lbDate.textContent  = date;
      overlay.classList.add("active");
      document.body.style.overflow = "hidden";
    });
  });

  function closeLightbox() {
    overlay.classList.remove("active");
    document.body.style.overflow = "";
    lbImg.src = "";
  }

  lbClose.addEventListener("click", closeLightbox);
  overlay.addEventListener("click", e => {
    if (e.target === overlay) closeLightbox();
  });
  document.addEventListener("keydown", e => {
    if (e.key === "Escape") closeLightbox();
  });
}

/* ── Schedule Day Tabs ── */
function initScheduleTabs() {
  const tabs   = document.querySelectorAll(".day-tab");
  const panels = document.querySelectorAll(".day-panel");
  if (!tabs.length) return;

  function activate(hari) {
    tabs.forEach(t => t.classList.toggle("active", t.dataset.hari === hari));
    panels.forEach(p => p.classList.toggle("active", p.dataset.hari === hari));
  }

  tabs.forEach(tab => {
    tab.addEventListener("click", () => activate(tab.dataset.hari));
  });

  // Auto-activate today's tab
  const todayTab = document.querySelector(".day-tab.today-tab");
  if (todayTab) {
    activate(todayTab.dataset.hari);
  } else if (tabs.length) {
    activate(tabs[0].dataset.hari);
  }
}

/* ── Scroll Reveal ── */
function initReveal() {
  const els = document.querySelectorAll(".reveal");
  if (!els.length) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add("visible");
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  els.forEach(el => observer.observe(el));
}

/* ── Active Nav Link on Scroll ── */
function initActiveNav() {
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".navbar-nav .nav-link");
  if (!sections.length || !navLinks.length) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(link => {
          link.classList.toggle(
            "active",
            link.getAttribute("href") === "#" + entry.target.id
          );
        });
      }
    });
  }, { rootMargin: "-40% 0px -55% 0px" });

  sections.forEach(s => observer.observe(s));
}

/* ── Admin Sidebar Toggle (Mobile) ── */
function initAdminSidebar() {
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar   = document.getElementById("adminSidebar");
  if (!toggleBtn || !sidebar) return;

  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
  });
}

/* ── Admin: Image Preview on Upload ── */
function initImagePreview() {
  const input   = document.getElementById("fotoInput");
  const preview = document.getElementById("fotoPreview");
  if (!input || !preview) return;

  input.addEventListener("change", () => {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  });
}

/* ── Admin: Confirm Delete ── */
function initDeleteConfirm() {
  document.querySelectorAll(".btn-delete-confirm").forEach(btn => {
    btn.addEventListener("click", e => {
      if (!confirm("Yakin ingin menghapus data ini? Tindakan tidak dapat dibatalkan.")) {
        e.preventDefault();
      }
    });
  });
}

/* ── Auto-dismiss alerts ── */
function initAlertDismiss() {
  document.querySelectorAll(".admin-alert.auto-dismiss").forEach(el => {
    setTimeout(() => {
      el.style.transition = "opacity .5s";
      el.style.opacity = "0";
      setTimeout(() => el.remove(), 500);
    }, 4000);
  });
}

/* ── INIT ── */
document.addEventListener("DOMContentLoaded", () => {
  ThemeManager.init();
  initNavbar();
  initSmoothScroll();
  initStudentFilter();
  initLightbox();
  initScheduleTabs();
  initReveal();
  initActiveNav();
  initAdminSidebar();
  initImagePreview();
  initDeleteConfirm();
  initAlertDismiss();
});
