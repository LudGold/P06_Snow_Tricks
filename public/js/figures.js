// Gestion des figures avec "Load More"
document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const figuresContainer = document.getElementById('figures-container');
    const initialFiguresHTML = figuresContainer.innerHTML;

    let figuresOffset = 15; // Initial offset set to 15 since you start with 15 figures

    // Check initial number of figures and show or hide the "Load More" button accordingly
    if (figuresContainer.children.length <= 15) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            fetch(`/load-more-figures?offset=${figuresOffset}`)
                .then(response => response.json())
                .then(data => {
                    figuresContainer.insertAdjacentHTML('beforeend', data.html);
                    figuresOffset += 5;
                    if (data.remaining <= 0) {
                        loadMoreBtn.style.display = 'none';
                    }
                });
        });
    }
});
