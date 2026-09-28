(function () {
  "use strict";

  // Mobile / full-screen nav toggle
  var toggle = document.getElementById("nav-toggle");
  if (toggle) {
    toggle.addEventListener("click", function () {
      var isOpen = document.documentElement.classList.toggle("nav-open");
      document.body.style.overflow = isOpen ? "hidden" : "";
      var label = toggle.querySelector(".nav-toggle-label");
      if (label) {
        label.textContent = isOpen ? "بستن" : "منو";
      }
    });

    document.querySelectorAll(".nav-overlay a").forEach(function (link) {
      link.addEventListener("click", function () {
        document.documentElement.classList.remove("nav-open");
        document.body.style.overflow = "";
      });
    });
  }

  // Reveal-on-scroll for elements marked with .reveal-up
  var revealEls = document.querySelectorAll(".reveal-up");
  if (revealEls.length) {
    if ("IntersectionObserver" in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add("is-visible");
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15, rootMargin: "0px 0px -60px 0px" }
      );
      revealEls.forEach(function (el) {
        observer.observe(el);
      });
    } else {
      revealEls.forEach(function (el) {
        el.classList.add("is-visible");
      });
    }
  }
})();
