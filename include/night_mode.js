document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const offcanvasEl = document.getElementById("offcanvasWithBothOptions");
  const body = document.body;

  function updateButtonText(button) {
    if (!button) return;
    button.textContent = body.classList.contains("dark")
      ? "Désactiver mode nuit"
      : "Activer mode nuit";
  }

  function toggleTheme() {
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText(toggleBtn);
    const mobileBtn = document.getElementById("theme-btn-mobile");
    updateButtonText(mobileBtn);
  }

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  updateButtonText(toggleBtn);
  toggleBtn?.addEventListener("click", toggleTheme);

  if (offcanvasEl) {
    offcanvasEl.addEventListener("shown.bs.offcanvas", () => {
      const toggleBtn_mobile = document.getElementById("theme-btn-mobile");
      if (toggleBtn_mobile && !toggleBtn_mobile.dataset.bound) {
        toggleBtn_mobile.addEventListener("click", toggleTheme);
        toggleBtn_mobile.dataset.bound = "true";
        updateButtonText(toggleBtn_mobile);
      }
    });
  }
});
