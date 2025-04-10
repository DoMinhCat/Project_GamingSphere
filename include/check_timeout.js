let timeoutDuration = 30*60*1000; 
let timeout;

async function update_session() {
  await fetch('/update_session.php', { method: 'POST' });
}

function resetTimer() {
  clearTimeout(timeout);
  update_session();

  timeout = setTimeout(() => {
    const root = window.location.origin;
    window.location.href = `${root}/connexion/session_timeout.php`;
  }, timeoutDuration);
}

window.onload = resetTimer;
document.addEventListener("mousemove", resetTimer);
document.addEventListener("keydown", resetTimer);
document.addEventListener("click", resetTimer);
