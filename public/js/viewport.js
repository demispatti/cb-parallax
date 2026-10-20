/*jshint esversion: 6 */
(function(root, factory) {
    "use strict";

    const api = factory();

    if (typeof module === 'object' && module.exports) {
        module.exports = api;
    }

    if (root) {
        root.CbParallaxViewport = api;
    }
})(typeof globalThis !== 'undefined' ? globalThis : this, function() {
    "use strict";

    function positiveNumber(value) {
        return typeof value === 'number' && Number.isFinite(value) && value > 0;
    }

    function getDimensions(targetWindow, targetDocument) {
        const visualViewport = targetWindow.visualViewport;
        const documentElement = targetDocument.documentElement || {};
        const width = visualViewport && positiveNumber(visualViewport.width)
            ? visualViewport.width
            : (positiveNumber(targetWindow.innerWidth) ? targetWindow.innerWidth : documentElement.clientWidth);
        const height = visualViewport && positiveNumber(visualViewport.height)
            ? visualViewport.height
            : (positiveNumber(targetWindow.innerHeight) ? targetWindow.innerHeight : documentElement.clientHeight);

        return {
            width: Math.max(1, Math.round(width || 0)),
            height: Math.max(1, Math.round(height || 0))
        };
    }

    return {
        getDimensions: getDimensions
    };
});
