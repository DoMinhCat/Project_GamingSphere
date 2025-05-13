let startTime = Date.now();
let totalTime = 0;

function sendTime(seconds) {
  navigator.sendBeacon(
    "/track_time.php",
    JSON.stringify({
      category: pageCategory,
      seconds: seconds,
    })
  );
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
  if (startTime) {
    pauseTimer();
  }
});

document.getElementById("btn-deconnexion").addEventListener("click", () => {
  pauseTimer();
});
