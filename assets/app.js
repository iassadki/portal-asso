import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/styles.css';
import './styles/general.css';
import './styles/header-footer.css';

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

const fleche = document.querySelector(".scrollTop"); 
const header = document.querySelector("body > header").offsetHeight; 

// Scroll top //
document.addEventListener("scroll", () => {
  let scroll = this.scrollY; 
  if (scroll >= header) { 
    fleche.classList.add("visible"); 
  } else {
    fleche.classList.remove("visible"); 
  }
});

document.addEventListener('DOMContentLoaded', () => {
    setupNavbar();
    setupSidebar();
    setupCheckboxImages();
    setupSingleCheckboxImage();
    setupScrollToTopButton();
});

function setupNavbar() {
    const navLinks = document.querySelectorAll('nav .nav-link');
    const currentPath = window.location.pathname;

    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;

        if (linkPath === currentPath ||
            (currentPath.endsWith('/') && linkPath.endsWith('/index.html')) ||
            (currentPath.endsWith('/') && linkPath.endsWith('/'))) {
            link.classList.add('active');
        }
    });
}

function setupSidebar() {
    const menuIcone = document.getElementById("menu-icon");
    const sidebar = document.getElementById("sidebar");

    if (sidebar) {
        sidebar.style.width = "0";
        menuIcone.addEventListener("click", () => {
            sidebar.style.width = (sidebar.style.width === "300px") ? "0" : "300px";
        });
    } else {
        console.error("L'élément avec l'ID 'sidebar' est introuvable.");
    }
}

function setupCheckboxImages() {
    const checkboxes = document.querySelectorAll('.grid-item input[type="checkbox"]');

    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('label');
        const img = label.querySelector('img');

        // Initialize image based on checkbox state
        updateCheckboxImage(checkbox, img);

        // Add event listener for change event
        checkbox.addEventListener('change', (event) => {
            updateCheckboxImage(event.target, img);
        });
    });
}

function updateCheckboxImage(checkbox, img) {
    if (checkbox.checked) {
        img.src = 'assets/icons/orange/checkbox-true.svg'; // Path to checked image
    } else {
        img.src = 'assets/icons/orange/checkbox-false.svg'; // Path to unchecked image
    }
}

function setupSingleCheckboxImage() {
    const checkboxImg = document.getElementById('checkbox-img');
    const checkbox = document.querySelector('.checkbox-connexion input[type="checkbox"]');

    if (checkboxImg && checkbox) {
        checkboxImg.addEventListener('click', () => {
            checkbox.checked = !checkbox.checked;
            checkboxImg.src = checkbox.checked ? 'assets/icons/black/checkbox-true.svg' : 'assets/icons/black/checkbox-false.svg';
        });
    } else {
        console.error("Les éléments 'checkbox-img' ou '.checkbox-connexion input[type=\"checkbox\"]' sont introuvables.");
    }
}

// function setupScrollToTopButton() {
//     const upArrow = document.getElementById("up-arrow");
//     if (upArrow) {
//         upArrow.addEventListener("click", () => {
//             window.scrollTo({
//                 top: 0,
//                 behavior: "smooth"
//             });
//         });
//     } else {
//         console.error("L'élément avec l'ID 'up-arrow' est introuvable.");
//     }
// }


