import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";
// Carousel functionality
document.addEventListener("DOMContentLoaded", () => {
  const track = document.querySelector(".carousel-track");
  if (!track) return;

  const slides = track.querySelectorAll(".carousel-slide");
  const slideCount = slides.length;
  let index = 0;

  const firstSlideClone = slides[0].cloneNode(true);
  track.appendChild(firstSlideClone);

  function updateCarousel() {
    track.style.transform = `translateX(-${index * 100}%)`;
    if (index === slideCount) {
      setTimeout(() => {
        track.style.transition = "none";
        index = 0;
        track.style.transform = `translateX(0)`;
        setTimeout(() => {
          track.style.transition = "transform 700ms ease-in-out";
        }, 50);
      }, 700);
    }
  }

  setInterval(() => {
    index++;
    updateCarousel();
  }, 4000);
});

// Product grid pagination functionality
document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("product-grid");
  const items = grid.querySelectorAll(".product-item");
  const prevButton = document.getElementById("prev-page");
  const nextButton = document.getElementById("next-page");
  const pageNumbersContainer = document.getElementById("page-numbers");
  const itemsPerPage = 10;
  const maxPageButtons = 5; // Maximum number of page buttons to display
  let currentPage = 1;
  let filteredItems = Array.from(items);

  function updateGrid() {
    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
    items.forEach((item) => {
      item.style.display = "none"; // Hide all items initially
    });

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    filteredItems.forEach((item, index) => {
      item.style.display = index >= start && index < end ? "block" : "none";
    });

    // Update page number buttons with sliding window
    pageNumbersContainer.innerHTML = "";
    const halfMaxButtons = Math.floor(maxPageButtons / 2);
    let startPage = Math.max(1, currentPage - halfMaxButtons);
    let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);

    // Adjust startPage if endPage is at the maximum
    if (endPage - startPage + 1 < maxPageButtons) {
      startPage = Math.max(1, endPage - maxPageButtons + 1);
    }

    // Add ellipsis before if there are skipped pages
    if (startPage > 1) {
      const ellipsis = document.createElement("span");
      ellipsis.textContent = "...";
      ellipsis.className = "px-4 py-2 text-gray-700";
      pageNumbersContainer.appendChild(ellipsis);
    }

    // Add page number buttons
    for (let i = startPage; i <= endPage; i++) {
      const button = document.createElement("button");
      button.textContent = i;
      button.className = `px-4 py-2 rounded ${
        i === currentPage
          ? "bg-blue-950 text-white"
          : "bg-gray-200 text-gray-700 hover:bg-yellow-500"
      }`;
      button.addEventListener("click", () => {
        currentPage = i;
        updateGrid();
      });
      pageNumbersContainer.appendChild(button);
    }

    // Add ellipsis after if there are more pages
    if (endPage < totalPages) {
      const ellipsis = document.createElement("span");
      ellipsis.textContent = "...";
      ellipsis.className = "px-4 py-2 text-gray-700";
      pageNumbersContainer.appendChild(ellipsis);
    }

    // Update button states
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
  }

  prevButton.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      updateGrid();
    }
  });

  nextButton.addEventListener("click", () => {
    if (currentPage < Math.ceil(filteredItems.length / itemsPerPage)) {
      currentPage++;
      updateGrid();
    }
  });

  // Initial setup
  updateGrid();
});
