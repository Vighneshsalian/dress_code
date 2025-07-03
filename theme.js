// Theme toggle logic for all pages
function applyTheme(theme) {
  if (theme === "dark") {
    document.body.classList.add("dark-theme");
    const sidebar = document.getElementById("sidebar");
    if (sidebar) sidebar.classList.add("dark-theme");
    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle)
      themeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Theme';
  } else {
    document.body.classList.remove("dark-theme");
    const sidebar = document.getElementById("sidebar");
    if (sidebar) sidebar.classList.remove("dark-theme");
    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle)
      themeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Theme';
  }
}
function toggleTheme() {
  const isDark = document.body.classList.toggle("dark-theme");
  const sidebar = document.getElementById("sidebar");
  if (sidebar) sidebar.classList.toggle("dark-theme");
  const theme = isDark ? "dark" : "light";
  localStorage.setItem("theme", theme);
  applyTheme(theme);
}
document.addEventListener("DOMContentLoaded", function () {
  const savedTheme = localStorage.getItem("theme") || "light";
  applyTheme(savedTheme);
  const themeToggle = document.getElementById("theme-toggle");
  if (themeToggle) themeToggle.addEventListener("click", toggleTheme);
});
