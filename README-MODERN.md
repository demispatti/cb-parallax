# CB Parallax - Modern Architecture

> **Premium WordPress Parallax Background Plugin**
> Custom backgrounds with stunning parallax effects for posts, pages, and products.

[![WordPress](https://img.shields.io/badge/WordPress-5.5%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%202.0%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## ✨ Features

- 🎨 **Custom Backgrounds** - Full-screen backgrounds for any post type
- 🌊 **Parallax Effects** - Smooth vertical & horizontal parallax scrolling
- 📱 **Responsive Design** - Mobile-optimized with optional mobile disable
- 🎯 **Pixel-Perfect Control** - Advanced positioning and alignment options
- 🎭 **Overlay Patterns** - 13 built-in overlay patterns with custom colors
- ⚡ **High Performance** - GPU-accelerated with modern optimization
- 🛠️ **Developer Friendly** - Clean API, extensible architecture
- ♿ **Accessible** - WCAG compliant with reduced motion support

## 🚀 Quick Start

### Installation

1. **Install Dependencies:**
```bash
composer install --no-dev
npm install
npm run build
```

2. **Activate Plugin:**
   - Upload to `/wp-content/plugins/cb-parallax/`
   - Activate through WordPress admin
   - Configure under Settings → CB Parallax

### Basic Usage

1. Go to **Settings → CB Parallax**
2. Upload a background image (recommended: 1920x1200px or larger)
3. Enable parallax effect
4. Configure direction and alignment
5. Save changes

## 📚 Documentation

### Requirements

- **WordPress:** 5.5 or higher
- **PHP:** 7.4 or higher
- **Browser:** Modern browsers with ES6 support
- **Theme:** Must support WordPress `custom-background` feature

### Architecture

```
Modern Stack:
├── PHP 7.4+ (Type hints, strict types)
├── PSR-4 Autoloading
├── Dependency Injection
├── ES6+ JavaScript Modules
├── Webpack Build System
├── SCSS Preprocessing
└── Composer & NPM
```

### Directory Structure

```
cb-parallax/
├── src/                    # PSR-4 PHP source
│   ├── Core/              # Framework (Container, HookManager)
│   ├── Admin/             # Admin functionality
│   ├── Public/            # Frontend functionality
│   ├── Services/          # Business logic
│   └── helpers.php        # Global helpers
├── assets/                # Source assets
│   ├── js/
│   │   ├── public/        # ParallaxEngine, etc.
│   │   ├── admin/         # Settings management
│   │   ├── lib/           # SmoothScroll library
│   │   └── utils/         # Utilities
│   └── scss/              # SCSS source
├── dist/                  # Built assets
├── config/                # Configuration
├── vendor/                # Composer dependencies
├── composer.json          # PHP dependencies
├── package.json           # JS dependencies
└── webpack.config.js      # Build config
```

## 💻 Development

### Setup Development Environment

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Start development build (with watch)
npm run dev

# Run tests
composer test
npm test

# Code quality
composer cs          # Check PHP standards
composer stan        # Static analysis
npm run lint:js      # Lint JavaScript
```

### Build for Production

```bash
npm run build        # Minify & optimize all assets
composer install --no-dev --optimize-autoloader
```

### Code Standards

- **PHP:** PSR-12, type hints, strict types
- **JavaScript:** ES6+, ESLint recommended
- **CSS:** BEM methodology, SCSS
- **Documentation:** PHPDoc, JSDoc

## 🎯 Usage Examples

### PHP API

```php
// Get plugin instance
$plugin = cb_parallax();

// Get/set options
$isEnabled = cb_parallax_get_option('parallax_enabled');
cb_parallax_set_option('parallax_enabled', true);

// Check status
if (cb_parallax_is_enabled()) {
    // Parallax is active
}

// Get asset URL
$cssUrl = cb_parallax_asset_url('dist/css/public.min.css');
```

### JavaScript API

```javascript
// Access debug API (when debug mode is on)
const debug = window.CbParallaxDebug;

debug.getConfig();           // Get configuration
debug.getDevice();           // Get device info
debug.getParallaxEngine();   // Get engine instance
debug.reload();              // Reinitialize
```

### Hooks & Filters

```php
// Add custom post type support
add_filter('cb_parallax_supported_post_types', function($postTypes) {
    $postTypes[] = 'my_custom_type';
    return $postTypes;
});

// Hook after plugin loads
add_action('cb_parallax_loaded', function($plugin) {
    // Custom initialization
});
```

## 🏗️ Modern Architecture

### Dependency Injection

```php
use CbParallax\Services\OptionsService;

class MyCustomController {
    public function __construct(
        OptionsService $optionsService
    ) {
        $this->optionsService = $optionsService;
    }
}
```

### Service Container

```php
$container = cb_parallax()->getContainer();

// Register service
$container->singleton(MyService::class, function($container) {
    return new MyService();
});

// Resolve service
$service = $container->get(MyService::class);
```

### Hook Manager

```php
$hookManager = new \CbParallax\Core\Hooks\HookManager();

$hookManager
    ->addAction('init', [$this, 'initialize'])
    ->addFilter('the_content', [$this, 'filterContent']);
```

## 🎨 Customization

### Custom Parallax Settings

```php
// Override parallax factor
add_filter('cb_parallax_config', function($config) {
    $config['parallaxFactor'] = 1.5;
    return $config;
});
```

### Custom Overlay

```php
// Add custom overlay pattern
add_filter('cb_parallax_overlay_patterns', function($patterns) {
    $patterns['my-pattern'] = 'My Custom Pattern';
    return $patterns;
});
```

### SCSS Variables

```scss
// Override in your theme
@import 'cb-parallax/variables';

$parallax-z-index: 0 !default;
$overlay-opacity: 0.3 !default;
```

## 🔧 Configuration

### Plugin Settings

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `parallax_enabled` | boolean | false | Enable parallax effect |
| `disable_on_mobile` | boolean | true | Disable on mobile devices |
| `preserve_scrolling` | boolean | true | Enable smooth scrolling |
| `direction` | string | 'vertical' | Parallax direction |
| `parallax_factor` | number | 1.2 | Parallax intensity |

### Image Recommendations

- **Minimum Size:** 1920x1200px
- **Recommended:** 2560x1600px for 4K displays
- **Format:** JPG (optimized) or WebP
- **File Size:** < 500KB for best performance
- **Aspect Ratio:** 16:10 or wider for vertical parallax

## 🚀 Performance

### Optimizations

- ✅ GPU-accelerated transforms (translate3d)
- ✅ RequestAnimationFrame for smooth animations
- ✅ Debounced resize handlers
- ✅ Lazy loading and code splitting
- ✅ Optimized asset delivery
- ✅ Reduced motion support

### Performance Tips

1. Use optimized images (WebP when possible)
2. Enable disable on mobile for better mobile performance
3. Use solid colors instead of gradients
4. Limit the number of overlay effects
5. Consider reduced motion preferences

## 🌐 Browser Support

| Browser | Version |
|---------|---------|
| Chrome | 90+ |
| Firefox | 88+ |
| Safari | 14+ |
| Edge | 90+ |
| Opera | 76+ |

### Fallbacks

- Graceful degradation for older browsers
- Static background fallback
- No JavaScript errors in unsupported environments

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests (`composer test && npm test`)
5. Commit changes (`git commit -m 'Add amazing feature'`)
6. Push to branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Reporting Issues

Please use the [GitHub Issues](https://github.com/demispatti/cb-parallax/issues) page.

## 📄 License

This plugin is licensed under the GPL-2.0-or-later license.

## 👨‍💻 Credits

- **Author:** Demis Patti
- **Architecture:** Modern WordPress Plugin Boilerplate
- **Inspiration:** WordPress Community

## 📞 Support

- **Documentation:** [docs/](docs/)
- **FAQ:** [FAQ.md](FAQ.md)
- **Support Forum:** [WordPress.org](https://wordpress.org/support/plugin/cb-parallax/)
- **Email:** demis@demispatti.ch

## 🎉 Changelog

### 0.9.7 - 2025-10-21

#### Major Refactoring
- ♻️ Complete architectural overhaul
- ✨ Modern PHP 7.4+ with type hints
- ✨ ES6+ JavaScript with webpack
- ✨ Dependency injection container
- ✨ Professional SmoothScroll implementation
- ✨ Modern ParallaxEngine with GPU acceleration
- ✨ Comprehensive documentation

#### Breaking Changes
- Requires PHP 7.4+ (previously 5.6+)
- New PSR-4 namespace structure
- Composer autoloading required

See [CHANGELOG.md](CHANGELOG.md) for complete history.

---

**Made with ❤️ by Demis Patti**

**Star ⭐ this repository if you find it helpful!**
