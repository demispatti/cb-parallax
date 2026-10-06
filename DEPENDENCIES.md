# Bundled dependencies

| Library | Version | Source |
|---|---|---|
| WP Color Picker Alpha | 2.1.4 | https://github.com/kallookoo/wp-color-picker-alpha/commit/e5a6154 |
| SmoothScroll polyfill | 0.4.4 | https://www.npmjs.com/package/smoothscroll-polyfill/v/0.4.4 |
| jQuery UI Smoothness CSS and images | 1.14.2 | https://code.jquery.com/ui/1.14.2/themes/smoothness/jquery-ui.min.css |
| Font Awesome | 4.7.0 (unchanged) | https://www.npmjs.com/package/font-awesome/v/4.7.0 |

The alpha picker stays on 2.x; migrating to 3.0.4 needs separate testing of initialization and color serialization. FancySelect and the requestAnimationFrame polyfill retain their existing bundled copies; no replacement migration was attempted.

The unused Font Awesome stylesheet already references missing font files; this pre-existing issue remains. All updated jQuery UI image references resolve locally. The separate theme-only stylesheet preserves its relative image paths.

Checked on 2026-10-06. Runtime assets remain bundled; WordPress supplies jQuery and jQuery UI JavaScript through its registered handles. Versions are pinned in enqueue calls for cache invalidation. Plugin release versions and `Tested up to` are not changed without a real WordPress integration test.

Validation: modified PHP parsed with php-parser (PHP 7.4 grammar); minified and unminified NiceScroll, SmoothScroll, Tooltipster and Alertify assets initialized in jsdom with jQuery 3.7.1. These checks do not replace a real WordPress browser test. The alpha color picker requires a WordPress/Iris integration test.
