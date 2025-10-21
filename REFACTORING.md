# CB Parallax - Refactoring Documentation

## Overview

This document describes the major architectural refactoring of the CB Parallax WordPress plugin, transforming it from a legacy codebase into a modern, industry-standard application following best practices.

## Version

**Refactored Version:** 0.9.7
**Refactoring Date:** 2025-10-21
**Target PHP Version:** 7.4+
**Target WordPress Version:** 5.5+

## Major Changes

### 1. Project Structure

#### Before (Legacy Structure):
```
cb-parallax/
├── admin/               # Unorganized admin files
├── public/              # Unorganized public files
├── includes/            # Mixed core files
├── vendor/              # Manual vendor files
└── cb-parallax.php      # Bootstrap file
```

#### After (Modern PSR-4 Structure):
```
cb-parallax/
├── src/                 # PSR-4 compliant source code
│   ├── Core/            # Core framework (Container, HookManager)
│   ├── Admin/           # Admin controllers
│   ├── Public/          # Public controllers
│   └── Services/        # Business logic services
├── assets/              # Source assets (not compiled)
│   ├── js/              # Modern ES6+ JavaScript modules
│   │   ├── public/      # Public JavaScript
│   │   ├── admin/       # Admin JavaScript
│   │   ├── lib/         # Libraries (SmoothScroll)
│   │   └── utils/       # Utility modules
│   └── scss/            # SCSS source files
├── dist/                # Compiled/built assets
├── config/              # Configuration files
├── vendor/              # Composer dependencies
├── node_modules/        # NPM dependencies
├── composer.json        # PHP dependency management
├── package.json         # JS dependency management
├── webpack.config.js    # Build configuration
└── cb-parallax-modern.php  # Modern bootstrap file
```

### 2. PHP Architecture

#### Dependency Injection Container
- **File:** `src/Core/Container.php`
- **Features:**
  - Service binding and resolution
  - Singleton pattern support
  - Auto-wiring with reflection
  - PSR-11 compatible design

#### Hook Manager
- **File:** `src/Core/Hooks/HookManager.php`
- **Purpose:** Centralized WordPress hook management
- **Benefits:**
  - Better testability
  - Hook tracking
  - Easy removal of hooks

#### Services Layer
- **File:** `src/Services/OptionsService.php`
- **Replaces:** The monolithic 991-line `class-options.php`
- **Features:**
  - Schema-based option definitions
  - Automatic sanitization
  - Type safety
  - Clean API

#### Modern Controllers
- **AdminController:** `src/Admin/AdminController.php`
- **PublicController:** `src/Public/PublicController.php`
- **Benefits:**
  - Type hints throughout
  - Constructor injection
  - Single responsibility principle
  - Clear separation of concerns

### 3. JavaScript Modernization

#### Before:
- jQuery-dependent prototype-based classes
- Global variable pollution
- No module system
- Manual script concatenation

#### After:
- ES6+ classes and modules
- Import/export syntax
- Webpack bundling
- Tree-shaking support
- Source maps for debugging

#### Key Files:

**SmoothScroll Library** (`assets/js/lib/smoothscroll/SmoothScroll.js`):
- Professional implementation replacing vendor polyfill
- Promise-based API
- Multiple easing functions
- Cancelable animations
- Touch device support

**ParallaxEngine** (`assets/js/public/ParallaxEngine.js`):
- Clean class-based architecture
- Performance optimizations
- GPU acceleration
- Modern device detection
- Responsive design support

**Utility Modules**:
- `DeviceDetector.js` - Modern feature-based device detection
- `ViewportObserver.js` - Intersection/Resize observer wrappers

### 4. Build System

#### Tools:
- **Webpack 5:** Modern JavaScript bundling
- **Babel:** ES6+ transpilation
- **Sass:** CSS preprocessing
- **ESLint:** Code quality
- **Prettier:** Code formatting

#### Scripts:
```bash
npm run dev          # Development build with watch
npm run build        # Production build
npm run lint:js      # JavaScript linting
npm run format       # Code formatting
```

### 5. Composer Integration

#### Features:
- PSR-4 autoloading
- Dev dependencies (PHPUnit, PHPStan, PHPCS)
- Optimized autoloader
- Vendor management

#### Scripts:
```bash
composer test        # Run PHPUnit tests
composer cs          # Check coding standards
composer cbf         # Fix coding standards
composer stan        # Static analysis
```

## Code Quality Improvements

### PHP Type Hints

**Before:**
```php
class cb_parallax_options {
    private $domain;

    public function get($key, $default = null) {
        // No type safety
    }
}
```

**After:**
```php
class OptionsService {
    private string $textDomain;

    public function get(string $key, $default = null) {
        // Type-safe with PHP 7.4+ features
    }
}
```

### Dependency Injection

**Before:**
```php
public function __construct() {
    $this->options = new cb_parallax_options($this->domain);
}
```

**After:**
```php
public function __construct(
    string $textDomain,
    OptionsService $optionsService
) {
    $this->textDomain = $textDomain;
    $this->optionsService = $optionsService;
}
```

### Modern JavaScript

**Before:**
```javascript
var cbPublicIsMobile = /Android|webOS/.test(navigator.userAgent) ? '1' : '0';

function CbParallaxPublic() {
    this.parallaxEnabled = '1';
}
```

**After:**
```javascript
import { DeviceDetector } from '../utils/DeviceDetector.js';

export class ParallaxEngine {
    constructor(container, options = {}) {
        this.device = DeviceDetector.detect();
        this.config = { ...ParallaxEngine.DEFAULT_CONFIG, ...options };
    }
}
```

## Breaking Changes

### For Developers:

1. **Namespace Change:**
   - Old: Mixed namespaces (`CbParallax\Includes`, `CbParallax\Admin\Menu\Includes`)
   - New: Clean PSR-4 (`CbParallax\Core`, `CbParallax\Services`)

2. **Bootstrap File:**
   - Old: `cb-parallax.php` (legacy compatible)
   - New: `cb-parallax-modern.php` (modern architecture)

3. **Class Names:**
   - Old: `cb_parallax_options`, `cb_parallax_admin`
   - New: `OptionsService`, `AdminController`

### For End Users:

- **No breaking changes** - The plugin maintains backward compatibility
- **Same functionality** with improved performance
- **Better error handling** and debugging

## Migration Guide

### Using the Modern Version:

1. **Install Dependencies:**
```bash
composer install --no-dev  # PHP dependencies
npm install                # JavaScript dependencies
npm run build              # Build assets
```

2. **Switch Bootstrap:**
   - Rename `cb-parallax.php` to `cb-parallax-legacy.php`
   - Rename `cb-parallax-modern.php` to `cb-parallax.php`
   - Reactivate the plugin

3. **Verify:**
   - Check admin settings page
   - Test parallax functionality
   - Review browser console for errors

## Performance Improvements

### PHP:
- ✅ Lazy loading with dependency injection
- ✅ Optimized autoloading
- ✅ Reduced memory footprint
- ✅ Better opcode cache utilization

### JavaScript:
- ✅ Code splitting and tree-shaking
- ✅ Minification and compression
- ✅ requestAnimationFrame optimization
- ✅ GPU-accelerated transforms
- ✅ Debounced resize handlers

### CSS:
- ✅ SCSS with variables and mixins
- ✅ Automatic vendor prefixing
- ✅ Minification
- ✅ Source maps for debugging

## Security Enhancements

1. **Input Sanitization:**
   - Schema-based sanitization in OptionsService
   - Type-safe method signatures
   - Automatic escaping

2. **Nonce Verification:**
   - Consistent nonce handling
   - CSRF protection

3. **Capability Checks:**
   - Proper capability verification
   - Role-based access control

4. **Error Handling:**
   - Try-catch blocks
   - Graceful degradation
   - Debug mode awareness

## Testing

### PHP Testing:
```bash
composer test              # Run all tests
composer test -- --filter TestName  # Run specific test
```

### JavaScript Testing:
```bash
npm test                   # Run Jest tests
npm run test:watch         # Watch mode
```

## Future Roadmap

### Phase 1: ✅ Completed
- Modern architecture
- Dependency injection
- ES6+ JavaScript
- Build system
- Type hints

### Phase 2: Planned
- Unit tests (PHPUnit, Jest)
- Integration tests
- E2E tests (Playwright)
- CI/CD pipeline

### Phase 3: Planned
- REST API endpoints
- Block editor integration
- React-based admin UI
- Enhanced accessibility

### Phase 4: Planned
- Performance monitoring
- Error tracking integration
- Advanced caching strategies
- CDN support

## Developer Resources

### Key Files to Understand:

1. **`src/Plugin.php`** - Main plugin class, entry point
2. **`src/Core/Container.php`** - Dependency injection container
3. **`src/Services/OptionsService.php`** - Options management
4. **`assets/js/public/ParallaxEngine.js`** - Parallax engine
5. **`webpack.config.js`** - Build configuration

### Helper Functions:

```php
cb_parallax()                          // Get plugin instance
cb_parallax_get_option($key, $default) // Get option value
cb_parallax_set_option($key, $value)   // Set option value
cb_parallax_is_enabled()               // Check if parallax is enabled
cb_parallax_asset_url($path)           // Get asset URL
```

### Hooks:

```php
// Actions
do_action('cb_parallax_loaded', $plugin);
do_action('cb_parallax_init', $plugin);

// Filters
apply_filters('cb_parallax_supported_post_types', $postTypes);
```

## Contributing

### Code Standards:

- **PHP:** PSR-12 coding standard
- **JavaScript:** ESLint with recommended rules
- **CSS:** Stylelint with standard config

### Before Submitting:

```bash
composer cs              # Check PHP standards
npm run lint:js          # Check JavaScript
npm run format           # Format code
composer test            # Run tests
```

## Credits

**Original Author:** Demis Patti
**Refactoring:** Claude Code (AI)
**Inspiration:** WordPress Plugin Boilerplate
**License:** GPL-2.0-or-later

## Support

- **Documentation:** `/docs` directory
- **Issues:** GitHub Issues
- **Discussions:** GitHub Discussions

---

**Last Updated:** 2025-10-21
**Version:** 0.9.7
