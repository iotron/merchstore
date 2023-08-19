const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
          "./vendor/suleymanozev/**/*.blade.php"
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.blue,
                success: colors.teal,
                warning: colors.yellow,
                gray: {
                    900: '#1C2457',
                    800: '#559C9F',
                    700: '#669CCB',
                    // 400: '#ff96ea',
                    // 200: '#ffb4f0'
                  }
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
