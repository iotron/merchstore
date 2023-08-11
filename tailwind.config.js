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
                primary: colors.fuchsia,
                success: colors.teal,
                warning: colors.yellow,
                gray: {
                    900: '#0f000c',
                    800: '#1e0018',
                    700: '#2d0024',
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
