/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        ...require('./rapida-ui/tokens/colors.cjs'),
        ...require('./rapida-ui/tokens/semantic-colors.cjs'),
        ...require('./rapida-ui/tokens/states.cjs'),
      },
      fontFamily: require('./rapida-ui/tokens/typography.cjs').fontFamily,
      fontSize:   require('./rapida-ui/tokens/typography.cjs').fontSize,
      spacing: {
        ...require('./rapida-ui/tokens/spacing.cjs').spacing,
        ...require('./rapida-ui/tokens/spacing-semantic.cjs'),
      },
      borderRadius: require('./rapida-ui/tokens/spacing.cjs').borderRadius,
      boxShadow:    require('./rapida-ui/tokens/spacing.cjs').boxShadow,
      transitionDuration:       require('./rapida-ui/tokens/spacing.cjs').transitionDuration,
      transitionTimingFunction: require('./rapida-ui/tokens/spacing.cjs').transitionTimingFunction,
    },
  },
  plugins: [],
}
