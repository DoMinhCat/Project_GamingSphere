document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;

  function updateButtonText(button) {
    if (!button) return;
    button.textContent = body.classList.contains("dark")
      ? "Désactiver mode nuit"
      : "Activer mode nuit";
  }

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  updateButtonText(document.getElementById("theme-btn"));
  updateButtonText(document.getElementById("theme-btn-mobile"));

  document.addEventListener("click", (e) => {
    const target = e.target;

    if (
      target &&
      (target.id === "theme-btn" || target.id === "theme-btn-mobile")
    ) {
      const isDark = body.classList.toggle("dark");
      localStorage.setItem("theme", isDark ? "dark" : "light");

      updateButtonText(document.getElementById("theme-btn"));
      updateButtonText(document.getElementById("theme-btn-mobile"));
    }
  });
});
