import './bootstrap.js';
import '@popperjs/core';
import * as bootstrap from 'bootstrap';

const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement("div");
    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    item.querySelector(".btn-remove").addEventListener("click", () => item.remove());

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-new').forEach(btn => btn.addEventListener('click', newItem));

import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadLessBtn = document.getElementById('load-less-btn');
    const container = document.getElementById('figures-container');
const initialHTML = container.innerHTML;

    let offset = 6; // Initial offset set to the number of figures initially displayed

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            fetch(`/load-more-figures?offset=${offset}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('figures-container');
                    container.insertAdjacentHTML('beforeend', data.html);
                    offset += 6; 
                    if (offset > 6) {
                        loadLessBtn.style.display = 'block';
                    }
                    // Hide button if no more figures to load
                    if (data.remaining <= 0) {
                        loadMoreBtn.style.display = 'none';
                    }
                });
        });
    }
    if (loadLessBtn) {
        loadLessBtn.addEventListener('click', function () {
            // Revert to initial HTML and reset offset
            container.innerHTML = initialHTML;
            offset = 6;
            loadMoreBtn.style.display = 'block';
            loadLessBtn.style.display = 'none';
        });
    }
});
