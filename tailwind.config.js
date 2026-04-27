/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",      // Para sa CodeIgniter Views
    "./public/**/*.php",         // Kung may PHP sa public folder
    "./public/assets/js/**/*.js" // Para sa Alpine.js logic
  ],
  theme: {
    extend: {
      fontFamily: {
        'plus-jakarta': ['"Plus Jakarta Sans"', 'sans-serif'],
      },
    },
  },
  plugins: [
    require("daisyui"), // I-activate ang daisyUI plugin
  ],
  daisyui: {
    themes: ["light"], // I-force ang light theme para hindi maging itim ang modal
    darkTheme: "light",
  },
}