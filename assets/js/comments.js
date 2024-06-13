// Gestion des commentaires avec "Load More" et "Load Less"
const loadMoreCommentsButton = document.getElementById('load-more-comments');
const loadLessCommentsButton = document.getElementById('load-less-comments');
const commentsContainer = document.getElementById('comments-container');
const initialCommentsHTML = commentsContainer.innerHTML;

let commentsOffset = 3;

if (loadMoreCommentsButton) {
    loadMoreCommentsButton.addEventListener('click', function () {
        const figureSlug = this.getAttribute('data-figure-slug');
        fetch(`/load-more-comments?figureSlug=${figureSlug}&offset=${commentsOffset}`)
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                while (tempDiv.firstChild) {
                    commentsContainer.appendChild(tempDiv.firstChild);
                }
                commentsOffset += 3;
                if (html.trim() === '') {
                    loadMoreCommentsButton.style.display = 'none';
                }
                loadLessCommentsButton.style.display = 'block';
            });
    });
}

if (loadLessCommentsButton) {
    loadLessCommentsButton.addEventListener('click', function () {
        commentsContainer.innerHTML = initialCommentsHTML;
        commentsOffset = 3;
        loadMoreCommentsButton.style.display = 'block';
        loadLessCommentsButton.style.display = 'none';
    });
}