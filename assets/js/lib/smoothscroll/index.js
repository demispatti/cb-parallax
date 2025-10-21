/**
 * SmoothScroll Module Entry Point
 *
 * @package CbParallax
 * @subpackage JavaScript/Lib
 */

export { SmoothScroll, SmoothScroll as default } from './SmoothScroll.js';

// Auto-initialize polyfill if not in module context
if (typeof window !== 'undefined' && !window.__CB_SMOOTHSCROLL_MODULE__) {
    import('./SmoothScroll.js').then(({ SmoothScroll }) => {
        SmoothScroll.polyfill();
    });
}
