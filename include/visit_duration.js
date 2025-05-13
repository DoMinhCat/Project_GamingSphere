let startTime = Date.now();
let totalTime = 0;

async function sendTime(duration) {
  const data = new FormData();
  data.append("category", pageCategory);
  data.append("duration", duration);

  navigator.sendBeacon("/include/visit_duration.php", data);
}

async function pauseTimer() {
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
  if (startTime) {
    pauseTimer();
  }
});

document.getElementById("btn-deconnexion").addEventListener("click", () => {
  pauseTimer();
});
