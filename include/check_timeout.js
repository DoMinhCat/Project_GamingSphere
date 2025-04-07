let timeoutDuration = 1000;
let timeout;

function resetTimer() {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    const basePath = window.location.origin;
    window.location.href = `${basePath}/connexion/session_timeout.php`;
  }, timeoutDuration);
}

window.onload = resetTimer;
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keydown", resetTimer);
document.addEventListener("click", resetTimer);
