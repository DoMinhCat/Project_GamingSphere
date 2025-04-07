document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const body = document.body;

  if (!toggleBtn) {
    console.error("Le bouton avec l'ID 'theme-btn' est introuvable.");
    return;
  }

  function updateButtonText() {
    const isDark = body.classList.contains("dark");
    toggleBtn.textContent = isDark
      ? "Désactiver le mode nuit"
      : "Activer le mode nuit";
  }

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  updateButtonText();

  toggleBtn.addEventListener("click", () => {
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText();
  });
});
