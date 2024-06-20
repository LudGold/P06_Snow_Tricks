document.addEventListener("DOMContentLoaded", function () {
  const newItem = (e) => {
    const collectionHolder = document.querySelector(
      e.currentTarget.dataset.collection
    );
    const item = document.createElement("div");
    item.innerHTML = collectionHolder.dataset.prototype.replace(
      /__name__/g,
      collectionHolder.dataset.index
    );
    item
      .querySelector(".btn-remove")
      .addEventListener("click", () => item.remove());
    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;
  };

  document
    .querySelectorAll(".btn-new")
    .forEach((btn) => btn.addEventListener("click", newItem));
  document
    .querySelectorAll(".btn-remove")
    .forEach((btn) =>
      btn.addEventListener("click", (e) =>
        e.currentTarget.closest(".input-media").remove()
      )
    );
});
