let timeoutDuration = 1000;
let timeout;

function resetTimer() {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    const path = window.location.pathname;
    const page = path.substring(path.lastIndexOf("/") + 1);
    if (page === "index.php") {
      window.location.href = "connexion/session_timeout.php";
    } else {
      window.location.href = "../connexion/session_timeout.php";
    }
  }, timeoutDuration);
}

window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeydown = resetTimer;
document.onclick = resetTimer;
