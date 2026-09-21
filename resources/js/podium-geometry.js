// Normalized bounds let the small analysis canvas describe the original photo.
export function visibleBounds(pixels, width, height) {
    let left = width, top = height, right = -1, bottom = -1;
    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            if (pixels[(y * width + x) * 4 + 3] <= 8) continue;
            left = Math.min(left, x);
            top = Math.min(top, y);
            right = Math.max(right, x);
            bottom = Math.max(bottom, y);
        }
    }
    if (right < left) return { left: 0, top: 0, width: 1, height: 1 };
    return { left: left / width, top: top / height, width: (right - left + 1) / width, height: (bottom - top + 1) / height };
}

export function fitSilhouette(imageWidth, imageHeight, bounds, maxWidth, maxHeight) {
    const scale = Math.min(maxWidth / (imageWidth * bounds.width), maxHeight / (imageHeight * bounds.height));
    return {
        width: imageWidth * bounds.width * scale,
        height: imageHeight * bounds.height * scale,
        imageWidth: imageWidth * scale,
        imageHeight: imageHeight * scale,
        left: -imageWidth * bounds.left * scale,
        top: -imageHeight * bounds.top * scale,
    };
}
