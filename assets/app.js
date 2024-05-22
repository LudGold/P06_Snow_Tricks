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
