document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form[action*='conversation']");
  const textarea = document.querySelector("#messageTextarea");
  const messageContainer = document.querySelector("#message-container");

  if (!form || !textarea || !messageContainer) {
    console.log("Chat elements not found - refresh.js skipped");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

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
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("d-flex", "mb-3", "justify-content-end");

        messageDiv.innerHTML = `
          <div class="order-2" style="max-width: 75%;">
            <!-- Message Bubble -->
            <div class="bg-primary text-white rounded-3 p-3 shadow-sm position-relative">
              <p class="mb-0">${message.replace(/\n/g, "<br>")}</p>
            </div>
            
            <!-- Reactions -->
            <div class="d-flex align-items-center mt-1 justify-content-end">
              <div class="reactions-container d-flex align-items-center">
                <div class="reactions me-2" data-message-id="new-${Date.now()}">
                </div>
                
                <!-- React -->
                <button class="btn btn-light btn-sm rounded-circle p-1 react-btn border-0 shadow-sm" 
                        data-message-id="new-${Date.now()}"
                        style="width: 28px; height: 28px; font-size: 0.8rem;"
                        title="Ajouter une réaction">
                    <i class="bi bi-emoji-smile"></i>
                </button>
              </div>
            </div>
            
            <!-- Message Time -->
            <div class="text-end mt-1">
              <small class="text-muted">${result.time}</small>
            </div>
          </div>
        `;

        messageContainer.appendChild(messageDiv);

        messageContainer.scrollTop = messageContainer.scrollHeight;

        textarea.value = "";
        textarea.style.height = "auto";

        if (typeof window.attachReactionListeners === "function") {
          window.attachReactionListeners();
        }
      }
    } catch (error) {
      console.error("Erreur lors de l'envoi du message :", error);
    }
  });
});
