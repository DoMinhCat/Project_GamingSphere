document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-btn");
  const body = document.body;

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
  }

  toggleBtn?.addEventListener("click", () => {
    const isDark = body.classList.toggle("dark");
    localStorage.setItem("theme", isDark ? "dark" : "light");
  });
});
