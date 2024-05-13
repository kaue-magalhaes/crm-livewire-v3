import form from  '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
		"./resources/**/*.blade.php",
		 "./resources/**/*.js",
		 "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
	],
  theme: {
    extend: {},
  },
  plugins: [
		form,
		typography,
		require("daisyui")
	],
}

