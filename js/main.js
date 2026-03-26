// Manejo del toggle light/dark.
// Nota: el diseño usa variables CSS en `css/style.css` que reaccionan a `body[data-theme="dark"]`.
(function () {
  const themeToggle = document.getElementById("themeToggle");
  if (!themeToggle) return;

  const iconEl = document.getElementById("themeIcon") || themeToggle.querySelector("span");

  const applyTheme = (theme) => {
    document.body.setAttribute("data-theme", theme);
    if (iconEl) iconEl.textContent = theme === "dark" ? "Light" : "Dark";
  };

  const savedTheme = localStorage.getItem("theme");
  const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
  let theme = savedTheme === "light" || savedTheme === "dark" ? savedTheme : prefersDark ? "dark" : "light";

  applyTheme(theme);

  themeToggle.addEventListener("click", () => {
    theme = document.body.getAttribute("data-theme") === "dark" ? "light" : "dark";
    localStorage.setItem("theme", theme);
    applyTheme(theme);
  });
})();

