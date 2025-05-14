document.addEventListener("DOMContentLoaded", () => {
  let startTime = Date.now();
  let totalTime = 0;

  function sendTime(duration) {
    const data = new URLSearchParams();
    data.append("category", pageCategory);
    data.append("duration", duration);

    const blob = new Blob([data.toString()], {
      type: "application/x-www-form-urlencoded;charset=UTF-8",
    });

    navigator.sendBeacon("/include/visit_duration.php", blob);
  }

  function pauseTimer() {
    const now = Date.now();
    const duration = Math.round((now - startTime) / 1000);
    totalTime += duration;
    sendTime(duration);
    startTime = null;
  }

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      pauseTimer();
    } else {
      startTime = Date.now();
    }
  });

  window.addEventListener("beforeunload", () => {
    if (startTime) pauseTimer();
  });

  const logoutBtn = document.getElementById("btn-deconnexion");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      pauseTimer();
    });
  }
});
