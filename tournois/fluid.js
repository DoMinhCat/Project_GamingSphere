document.addEventListener("DOMContentLoaded", function () {
  function attachEventListeners() {
    const participerButtons = document.querySelectorAll(".participer-btn");
    const desinscrireButtons = document.querySelectorAll(".desinscrire-btn");

    // Gestion de l'inscription
    participerButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const idTournoi = this.getAttribute("data-id");
        this.disabled = true;
        this.textContent = "Inscription en cours...";

        fetch("participation.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id_tournoi=${idTournoi}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.textContent = "Inscrit";
              this.classList.remove("btn-outline-warning");
              this.classList.add("btn-outline-success");
              this.classList.add("desinscrire-btn");
              this.classList.remove("participer-btn");
              this.disabled = false;
              attachEventListeners(); // Réattache les écouteurs
            } else {
              this.disabled = false;
              this.textContent = "Participer";
              alert(data.message || "Une erreur est survenue.");
            }
          })
          .catch((error) => {
            console.error("Erreur:", error);
            this.disabled = false;
            this.textContent = "Participer";
            alert("Une erreur est survenue. Veuillez réessayer.");
          });
      });
    });

    // Gestion de la désinscription
    desinscrireButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const idTournoi = this.getAttribute("data-id");
        this.disabled = true;
        this.textContent = "Désinscription en cours...";

        fetch("desinscription.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id_tournoi=${idTournoi}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.textContent = "Participer";
              this.classList.remove("btn-outline-danger");
              this.classList.add("btn-outline-warning");
              this.classList.add("participer-btn");
              this.classList.remove("desinscrire-btn");
              this.disabled = false;
              attachEventListeners(); // Réattache les écouteurs
            } else {
              this.disabled = false;
              this.textContent = "Se désinscrire";
              alert(data.message || "Une erreur est survenue.");
            }
          })
          .catch((error) => {
            console.error("Erreur:", error);
            this.disabled = false;
            this.textContent = "Se désinscrire";
            alert("Une erreur est survenue. Veuillez réessayer.");
          });
      });
    });
  }

  // Attache les écouteurs d'événements au chargement de la page
  attachEventListeners();
});
