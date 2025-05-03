document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".message-input-container");
  const textarea = form.querySelector("textarea");
  const messageList = document.querySelector(".message-list");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // empêcher le rechargement

    const message = textarea.value.trim();
    if (message === "") return;

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const result = await response.json();

      if (result.success) {
        // Crée le bloc du message à ajouter
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("message", "sent");

        const bubble = document.createElement("div");
        bubble.classList.add("message-bubble");

        const content = document.createElement("p");
        content.innerHTML = message.replace(/\n/g, "<br>");

        // Append the content inside the bubble
        bubble.appendChild(content);

        // Create the time div and append it outside the bubble
        const time = document.createElement("div");
        time.classList.add("message-time");
        time.textContent = result.time;

        // Append the bubble and time in order
        messageDiv.appendChild(bubble);
        messageDiv.appendChild(time);

        // Append the message to the message list
        messageList.appendChild(messageDiv);

        // Scroll automatique vers le bas
        messageList.scrollTop = messageList.scrollHeight;

        // Vide le champ
        textarea.value = "";
      }
    } catch (error) {
      console.error("Erreur lors de l'envoi du message :", error);
    }
  });
});
