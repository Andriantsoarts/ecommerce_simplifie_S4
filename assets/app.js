import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";
import "./styles/dashboard.css";

document.addEventListener("turbo:load", function () {
  const profileIcon = document.querySelector(".profile_icon");
  const dropdownMenu = document.querySelector(".dropdown_menu ul");
  if (!profileIcon || !dropdownMenu) {
    return;
  }

  profileIcon.addEventListener("click", function (e) {
    e.preventDefault();
    dropdownMenu.classList.toggle("show");
  });

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".profile")) {
      dropdownMenu.classList.remove("show");
    }
  });
});
