import './bootstrap.js';
import '@popperjs/core';
import * as bootstrap from 'bootstrap';
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

//new

document.addEventListener('DOMContentLoaded', function () {
   
    //     const newItem = (e) => {
    //         const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    //         const item = document.createElement("div");
    //         item.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    //         item.querySelector(".btn-remove").addEventListener("click", () => item.remove());
    //         collectionHolder.appendChild(item);
    //         collectionHolder.dataset.index++;
    //     };

    //     document.querySelectorAll('.btn-new').forEach(btn => btn.addEventListener('click', newItem));

    // });


    // //figures
    // // Gestion des figures avec "Load More" et "Load Less"
    // const loadMoreBtn = document.getElementById('load-more-btn');
    // const loadLessBtn = document.getElementById('load-less-btn');
    // const figuresContainer = document.getElementById('figures-container');
    // const initialFiguresHTML = figuresContainer.innerHTML;

    // let figuresOffset = 15;

    // if (loadMoreBtn) {
    //     loadMoreBtn.addEventListener('click', function () {
        
    //         fetch(`/load-more-figures?offset=${figuresOffset}`)
    //             .then(response => response.json())
    //             .then(data => {
    //                 figuresContainer.insertAdjacentHTML('beforeend', data.html);
    //                 figuresOffset += 15;
    //                 if (figuresOffset > 15) {
    //                     loadLessBtn.style.display = 'block';
    //                 }
    //                 if (data.remaining <= 0) {
    //                     loadMoreBtn.style.display = 'none';
    //                 }
    //             });
    //     });
    // }

    // if (loadLessBtn) {
    //     loadLessBtn.addEventListener('click', function () {
    //         figuresContainer.innerHTML = initialFiguresHTML;
    //         figuresOffset = 15;  // Reset to initial value
    //         loadMoreBtn.style.display = 'block';
    //         loadLessBtn.style.display = 'none';
    //     });
    // }


    // //comments
    // // Gestion des commentaires avec "Load More" et "Load Less"
    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreCommentsButton = document.getElementById('load-more-comments');
        const loadLessCommentsButton = document.getElementById('load-less-comments');
        const commentsContainer = document.getElementById('comments-container');
        const initialCommentsHTML = commentsContainer.innerHTML;
    
        let offset = 3; // Initial offset
    
        if (loadMoreCommentsButton) {
            loadMoreCommentsButton.addEventListener('click', function () {
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
});