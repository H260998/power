import test from 'node:test';
import assert from 'node:assert/strict';
import { fitSilhouette, visibleBounds } from '../../resources/js/podium-geometry.js';

test('transparent padding is excluded while opaque black pixels remain part of the product', () => {
    const pixels = new Uint8ClampedArray(10 * 10 * 4);
    for (let y = 2; y < 8; y++) for (let x = 3; x < 9; x++) pixels[(y * 10 + x) * 4 + 3] = 255;
    assert.deepEqual(visibleBounds(pixels, 10, 10), { left: 0.3, top: 0.2, width: 0.6, height: 0.6 });
});

test('empty and fully opaque photos have a safe full-image fallback', () => {
    for (const alpha of [0, 255]) {
        const pixels = new Uint8ClampedArray(16).fill(alpha);
        assert.deepEqual(visibleBounds(pixels, 2, 2), { left: 0, top: 0, width: 1, height: 1 });
    }
});

test('different silhouettes fit the same platform and their visible bottom matches the anchor', () => {
    for (const bounds of [
        { left: 0.07, top: 0.28, width: 0.87, height: 0.34 },
        { left: 0.27, top: 0.06, width: 0.46, height: 0.87 },
        { left: 0.1, top: 0.15, width: 0.8, height: 0.65 },
    ]) {
        for (const [maxWidth, maxHeight] of [[300, 364], [170, 294]]) {
            const fit = fitSilhouette(1086, 1448, bounds, maxWidth, maxHeight);
            assert.ok(fit.width <= maxWidth + 1e-9 && fit.height <= maxHeight + 1e-9);
            assert.ok(Math.abs(fit.imageWidth / fit.imageHeight - 1086 / 1448) < 1e-9);
            assert.ok(Math.abs(fit.top + fit.imageHeight * (bounds.top + bounds.height) - fit.height) < 1e-9);
            assert.ok(Math.abs(fit.left + fit.imageWidth * bounds.left) < 1e-9);
        }
    }
});
