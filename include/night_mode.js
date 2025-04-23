/*document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const toggleBtn_mobile = document.getElementById("theme-btn-mobile");
  const body = document.body;

  console.log("toggleBtn:", toggleBtn);
  console.log("body:", body);

  function updateButtonText() {
    toggleBtn.textContent = body.classList.contains("dark")
      ? "Désactiver mode nuit"
      : "Activer mode nuit";
  }
  function updateButtonText_mobile() {
    toggleBtn_mobile.textContent = body.classList.contains("dark")
      ? "Désactiver mode nuit"
      : "Activer mode nuit";
  }

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  setTimeout(updateButtonText, 0);
  setTimeout(updateButtonText_mobile, 0);

  toggleBtn?.addEventListener("click", () => {
    console.log("Bouton cliqué, body:", body);
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText();
  });
  toggleBtn_mobile?.addEventListener("click", () => {
    console.log("Bouton cliqué, body:", body);
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText_mobile();
  });
});
*/

document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const toggleBtn_mobile = document.getElementById("theme-btn-mobile");
  const body = document.body;

  function updateButtonText(button) {
    if (!button) return;
    button.textContent = body.classList.contains("dark")
      ? "Désactiver mode nuit"
      : "Activer mode nuit";
  }

  // Load saved theme
  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  // Initial text setup
  updateButtonText(toggleBtn);
  updateButtonText(toggleBtn_mobile);

  // Event handlers
  const toggleTheme = () => {
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText(toggleBtn);
    updateButtonText(toggleBtn_mobile);
  };

  toggleBtn?.addEventListener("click", toggleTheme);
  toggleBtn_mobile?.addEventListener("click", toggleTheme);
});
