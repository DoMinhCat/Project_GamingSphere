document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const body = document.body;

  console.log("toggleBtn:", toggleBtn);
  console.log("body:", body);

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  setTimeout(updateButtonText, 0);

  toggleBtn?.addEventListener("click", () => {
    console.log("Bouton cliqué, body:", body);
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateButtonText();
  });
});
