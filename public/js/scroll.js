document.addEventListener("DOMContentLoaded", function () {
  // Scroll to figures
  document.getElementById("scroll-down").addEventListener("click", function () {
    document
      .getElementById("figures-container")
      .scrollIntoView({ behavior: "smooth" });
  });
  
  document.getElementById("scroll-up").addEventListener("click", function () {
    document.getElementById("banner").scrollIntoView({ behavior: "smooth" });
  });
});

