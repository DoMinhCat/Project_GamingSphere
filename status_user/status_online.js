setInterval(() => {
  fetch("/status_user/ping_online.php", {
    method: "POST",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  });
}, 10000); //10sec
