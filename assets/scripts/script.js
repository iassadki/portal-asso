// const fleche = document.querySelector(".scrollTop"); 
// const header = document.querySelector("body > header").offsetHeight; 

// // Scroll top //
// document.addEventListener("scroll", () => {
//   let scroll = this.scrollY; 
//   if (scroll >= header) { 
//     fleche.classList.add("visible"); 
//   } else {
//     fleche.classList.remove("visible"); 
//   }
// });

// document.addEventListener('DOMContentLoaded', () => {
//     setupSmartphoneNavbar();
//     setupSidebarSmartphone();
//     setupNavbar();
//     setupSidebar();
//     toggleCheckbox(document.getElementById('checkbox'));
// });

// function setupSmartphoneNavbar() {
//     const menuIcon = document.getElementById("menu-icon");
//     const navMenu = document.querySelector(".nav-menu");

//     menuIcon.addEventListener("click", function () {
//         navMenu.classList.toggle("active");
//     });
// }

// function setupSidebarSmartphone() {
//     const menuIcone = document.getElementById("burger-icon");
//     const sidebar_mobile = document.getElementById("sidebar-mobile");

//     if (sidebar_mobile) {
//         sidebar_mobile.style.width = "0";
//         menuIcone.addEventListener("click", () => {
//             sidebar_mobile.style.width = (sidebar_mobile.style.width === "100%") ? "0" : "100%";
//         });
//     } else {
//         console.error("L'élément avec l'ID 'sidebar_mobile' est introuvable.");
//     }
// }

// function setupNavbar() {
//     const navLinks = document.querySelectorAll('nav .nav-link');
//     const currentPath = window.location.pathname;

//     navLinks.forEach(link => {
//         const linkPath = new URL(link.href).pathname;

//         if (linkPath === currentPath ||
//             (currentPath.endsWith('/') && linkPath.endsWith('/index.html')) ||
//             (currentPath.endsWith('/') && linkPath.endsWith('/'))) {
//             link.classList.add('active');
//         }
//     });
// }


// function setupSidebar() {
//     const menuIcone = document.getElementById("menu-icon");
//     const sidebar = document.getElementById("sidebar");

//     if (sidebar) {
//         sidebar.style.width = "0";
//         menuIcone.addEventListener("click", () => {
//             sidebar.style.width = (sidebar.style.width === "300px") ? "0" : "300px";
//         });
//     } else {
//         console.error("L'élément avec l'ID 'sidebar' est introuvable.");
//     }
// }

// function toggleCheckbox(checkbox) {
//     const label = checkbox.parentElement;
//     const checkboxFalse = label.querySelector('.checkbox-false');
//     const checkboxTrue = label.querySelector('.checkbox-true');

//     if (checkbox.checked) {
//         checkboxFalse.style.display = 'none';
//         checkboxTrue.style.display = 'block';
//     } else {
//         checkboxFalse.style.display = 'block';
//         checkboxTrue.style.display = 'none';
//     }
// }






