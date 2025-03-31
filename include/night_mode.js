document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const body = document.body;

  function updateButtonText() {
    toggleBtn.textContent = body.classList.contains("dark")
      ? "Désactiver le mode nuit"
      : "Activer le mode nuit";
  }

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  updateButtonText();

  toggleBtn?.addEventListener("click", () => {
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText();
  });
});
