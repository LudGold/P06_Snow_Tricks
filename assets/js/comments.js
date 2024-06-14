// public/js/comments.js

document.addEventListener('DOMContentLoaded', function() {
    const loadMoreCommentsButton = document.getElementById('load-more-comments');
    const loadLessCommentsButton = document.getElementById('load-less-comments');
    const commentsContainer = document.getElementById('comments-container');
    let offset = 3;
    let initialComments = commentsContainer.innerHTML;

    loadMoreCommentsButton.addEventListener('click', function() {
        const figureSlug = this.dataset.figureSlug;

        fetch(`/figure/load-more-comments?figureSlug=${figureSlug}&offset=${offset}`)
           
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newComments = doc.querySelectorAll('#comments-container li');
               
                for (let i = 0; i < newComments.length; i++) {
                    commentsContainer.appendChild(newComments[i]);
                }
                newComments.forEach(comment => commentsContainer.appendChild(comment));
                offset += newComments.length;
               
                if (newComments.length < 3) {
                    loadMoreCommentsButton.style.display = 'none';
                    loadLessCommentsButton.style.display = 'inline-block';
                }
            });
    });

    loadLessCommentsButton.addEventListener('click', function() {
        commentsContainer.innerHTML = initialComments;
        offset = 3;
        loadMoreCommentsButton.style.display = 'inline-block';
        loadLessCommentsButton.style.display = 'none';
    });
});