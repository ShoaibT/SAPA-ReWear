// auth.js
function toggleTheme() {
  const body = document.body;
  const checkbox = document.getElementById("theme-checkbox");
  const isDark = checkbox.checked;
  body.classList.toggle('dark-mode', isDark);
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('theme');
  const isDark = savedTheme === 'dark';
  document.body.classList.toggle('dark-mode', isDark);
  const checkbox = document.getElementById("theme-checkbox");
  if (checkbox) checkbox.checked = isDark;
});
