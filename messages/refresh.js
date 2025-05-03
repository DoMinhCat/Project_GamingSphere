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
        // Create the message block to be added
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("message", "sent");

        // Create the message bubble
        const bubble = document.createElement("div");
        bubble.classList.add("message-bubble");

        // Create the message content and add it inside the bubble
        const content = document.createElement("p");
        content.innerHTML = message.replace(/\n/g, "<br>");
        bubble.appendChild(content);

        // Create the time div (outside the bubble) and append it after the bubble
        const time = document.createElement("div");
        time.classList.add("message-time");
        time.textContent = result.time;

        // Append both the bubble and time div to the messageDiv
        messageDiv.appendChild(bubble); // Bubble first
        messageDiv.appendChild(time); // Time outside and beneath the bubble

        // Append the message div to the message list
        messageList.appendChild(messageDiv);

        // Auto scroll to the bottom of the message list
        messageList.scrollTop = messageList.scrollHeight;

        // Clear the input field
        textarea.value = "";
      }
    } catch (error) {
      console.error("Erreur lors de l'envoi du message :", error);
    }
  });
});
