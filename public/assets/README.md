# Public Assets Structure

## Directory Organization

### assets/css/
- `compiled/` - Compiled/minified CSS files
- `vendor/` - Third-party CSS libraries
- `components/` - Reusable component styles
- `roles/` - Role-specific stylesheets
- `main.css` - Main application styles

### assets/js/
- `compiled/` - Compiled/minified JS files
- `vendor/` - Third-party JS libraries
- `components/` - Reusable component scripts
- `roles/` - Role-specific dashboard scripts
  - admin-dashboard.js
  - cashier-dashboard.js
  - td-dashboard.js
  - ceo-dashboard.js
  - shareholder-dashboard.js
  - client-dashboard.js
- `main.js` - Main application script
- `main2.js` - Additional main script

### assets/images/
- `logo.png` - Application logo
- `icons/` - Icon assets
- `backgrounds/` - Background images

### uploads/
- Temporary upload directory

### storage/
- Symbolic link to storage/app/public
