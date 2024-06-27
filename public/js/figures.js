document.addEventListener("DOMContentLoaded", function () {
  const loadMoreBtn = document.getElementById("load-more-btn");
  const figuresContainer = document.getElementById("figures-container");
  let figuresOffset = 15; // Initial offset for loading more figures

  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", function () {
      fetch(`/load-more-figures?offset=${figuresOffset}`)
        .then((response) => response.json())
        .then((data) => {
          figuresContainer.insertAdjacentHTML("beforeend", data.html);
          figuresOffset += 5;
          if (data.remaining <= 0) {
            loadMoreBtn.style.display = "none"; // Hide button if no more figures to load
          }
        })
        .catch((error) => {
          console.error("Error fetching more figures:", error);
        });
    });
  }
});

