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
    if (container) {
       
    } else {
        console.error("L'élément figure n'a pas été trouvé.");
    }
const initialHTML = container.innerHTML;

    let offset = 6; 

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

document.addEventListener('DOMContentLoaded', function() {
    const loadMoreCommentsButton = document.getElementById('load-more-comments');
    const loadLessCommentsButton = document.getElementById('load-less-comments');
    const commentsContainer = document.getElementById('comments-container');
    const initialCommentsHTML = commentsContainer.innerHTML;

    let offset = 3; // Initial offset

    if (loadMoreCommentsButton) {
        loadMoreCommentsButton.addEventListener('click', function() {
            const figureSlug = this.getAttribute('data-figure-slug');
            fetch(`/load-more-comments?figureSlug=${figureSlug}&offset=${offset}`)
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    while (tempDiv.firstChild) {
                        commentsContainer.appendChild(tempDiv.firstChild);
                    }
                    offset += 3; // Increase offset
                    // Hide button if no more comments to load
                    if (html.trim() === '') {
                        loadMoreCommentsButton.style.display = 'none';
                    }
                    loadLessCommentsButton.style.display = 'block';
                });
        });
    }

    if (loadLessCommentsButton) {
        loadLessCommentsButton.addEventListener('click', function () {
            // Revert to initial HTML and reset offset
            commentsContainer.innerHTML = initialCommentsHTML;
            offset = 3;
            loadMoreCommentsButton.style.display = 'block';
            loadLessCommentsButton.style.display = 'none';
        });
    }
});










