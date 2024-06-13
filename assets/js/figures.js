// Gestion des figures avec "Load More" et "Load Less"
const loadMoreBtn = document.getElementById('load-more-btn');
const loadLessBtn = document.getElementById('load-less-btn');
const figuresContainer = document.getElementById('figures-container');
const initialFiguresHTML = figuresContainer.innerHTML;

let figuresOffset = 15;

if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
        fetch(`/load-more-figures?offset=${figuresOffset}`)
            .then(response => response.json())
            .then(data => {
                figuresContainer.insertAdjacentHTML('beforeend', data.html);
                figuresOffset += 15;
                if (figuresOffset > 15) {
                    loadLessBtn.style.display = 'block';
                }
                if (data.remaining <= 0) {
                    loadMoreBtn.style.display = 'none';
                }
            });
    });
}

if (loadLessBtn) {
    loadLessBtn.addEventListener('click', function () {
        figuresContainer.innerHTML = initialFiguresHTML;
        figuresOffset = 15;  // Reset to initial value
        loadMoreBtn.style.display = 'block';
        loadLessBtn.style.display = 'none';
    });
}
