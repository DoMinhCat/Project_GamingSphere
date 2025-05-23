document
  .getElementById("globalSearchForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();

    const query = document.getElementById("query").value.trim();
    const resultDiv = document.getElementById("results");

    if (!query) {
      resultDiv.innerHTML = "<p>Veuillez entrer une requête de recherche.</p>";
      return;
    }

    resultDiv.innerHTML = `<p>Chargement des résultats...</p>`;

    try {
      const response = await fetch("/search.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `query=${encodeURIComponent(query)}`,
      });

      if (!response.ok)
        throw new Error(`HTTP error! status: ${response.status}`);

      const data = await response.json();
      renderResults(data, resultDiv);
    } catch (error) {
      console.error("Erreur:", error);
      resultDiv.innerHTML =
        "<p>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
    }
  });

function renderResults(data, resultDiv) {
  const sections = [
    { key: "users", title: "Pseudos", renderItem: (item) => item.username },
    {
      key: "articles",
      title: "Articles",
      renderItem: (item) => `<a href="/article/${item.id}">${item.title}</a>`,
    },
    {
      key: "games",
      title: "Jeux",
      renderItem: (item) => `<a href="/game/${item.id}">${item.name}</a>`,
    },
    {
      key: "tournois",
      title: "Tournois",
      renderItem: (item) =>
        `<a href="/tournois/tournois_details.php?id_tournoi=${item.id}">${item.nom_tournoi}</a>`,
    },
    {
      key: "teams",
      title: "Équipes",
      renderItem: (item) =>
        `<a href="/equipe/details.php?id_equipe=${item.id}">${item.nom_equipe}</a>`,
    },
  ];

  const resultsHTML = sections
    .map(({ key, title, renderItem }) => {
      if (!data[key] || data[key].length === 0) return "";
      const itemsHTML = data[key]
        .map(renderItem)
        .map((item) => `<li>${item}</li>`)
        .join("");
      return `<h3>${title} :</h3><ul>${itemsHTML}</ul>`;
    })
    .join("");

  resultDiv.innerHTML = resultsHTML || "<p>Aucun résultat trouvé.</p>";
}
