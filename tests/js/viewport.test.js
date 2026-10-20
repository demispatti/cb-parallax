const assert = require('node:assert/strict');
const test = require('node:test');
const viewport = require('../../public/js/viewport.js');

test('prefers the visual viewport used by mobile browser chrome', () => {
    const dimensions = viewport.getDimensions({
        innerWidth: 390,
        innerHeight: 844,
        visualViewport: {width: 390, height: 759.4}
    }, {documentElement: {clientWidth: 390, clientHeight: 844}});

    assert.deepEqual(dimensions, {width: 390, height: 759});
});

test('falls back to the layout viewport', () => {
    const dimensions = viewport.getDimensions({
        innerWidth: 1024,
        innerHeight: 768
    }, {documentElement: {clientWidth: 1000, clientHeight: 700}});

    assert.deepEqual(dimensions, {width: 1024, height: 768});
});

test('uses document dimensions when window dimensions are unavailable', () => {
    const dimensions = viewport.getDimensions({}, {
        documentElement: {clientWidth: 320, clientHeight: 568}
    });

    assert.deepEqual(dimensions, {width: 320, height: 568});
});
