import { fitSilhouette, visibleBounds } from './podium-geometry';

const photos = new Map();

function measurePhoto(url) {
    if (!photos.has(url)) {
        const promise = (async () => {
            let image = new Image();
            image.crossOrigin = 'anonymous';
            image.src = url;
            try {
                await image.decode();
            } catch {
                // External hosts without CORS can still display an uncropped photo.
                image = new Image();
                image.src = url;
                await image.decode();
            }
            let bounds = { left: 0, top: 0, width: 1, height: 1 };
            try {
                const canvas = document.createElement('canvas');
                const ratio = Math.min(1, 512 / Math.max(image.naturalWidth, image.naturalHeight));
                canvas.width = Math.max(1, Math.round(image.naturalWidth * ratio));
                canvas.height = Math.max(1, Math.round(image.naturalHeight * ratio));
                const context = canvas.getContext('2d', { willReadFrequently: true });
                context.drawImage(image, 0, 0, canvas.width, canvas.height);
                bounds = visibleBounds(context.getImageData(0, 0, canvas.width, canvas.height).data, canvas.width, canvas.height);
            } catch {
                // Preserve image display when a browser cannot inspect its pixels.
            }
            return { width: image.naturalWidth, height: image.naturalHeight, bounds };
        })();
        photos.set(url, promise);
        promise.catch(() => photos.delete(url));
    }
    return photos.get(url);
}

export async function initPodium() {
    const data = document.getElementById('podium-slides');
    const image = document.getElementById('hero-product-img');
    const link = document.getElementById('hero-product-link');
    const podium = document.getElementById('hero-podium');
    if (!data || !image || !link || !podium) return;
    const slides = JSON.parse(data.textContent);
    if (!slides.length) return;
    const layer = link.parentElement;
    const scene = layer.parentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let currentPhoto;
    let currentScale = slides[0].scale ?? 1;

    function position() {
        if (!currentPhoto) return;
        // Reserve mobile headroom throughout the rotation so the text never overlaps a larger item.
        scene.style.marginTop = window.innerWidth < 768 && slides.some(slide => slide.scale > 1) ? '32px' : '0px';
        const base = podium.getBoundingClientRect();
        const parent = layer.getBoundingClientRect();
        const stage = scene.getBoundingClientRect();
        if (!base.width || !base.height || !stage.height) return;
        // The upper ellipse of podium-transparent.png is centered at 64.8% of its height.
        const surfaceY = base.top + base.height * 0.648;
        const maxWidth = Math.min(base.width * 0.54 * 0.92, stage.width * 0.9);
        const maxHeight = Math.max(1, Math.min(stage.height * 0.7, surfaceY - stage.top - 24));
        const fit = fitSilhouette(currentPhoto.width, currentPhoto.height, currentPhoto.bounds, maxWidth, maxHeight);
        // A larger product can use the space above the scene without revealing its clipped decorations.
        const headroom = Math.ceil(Math.max(0, fit.height * currentScale - (surfaceY - stage.top))) + 2;
        scene.style.overflow = currentScale > 1 ? 'visible' : 'hidden';
        scene.style.clipPath = currentScale > 1 ? `inset(-${headroom}px 0 0)` : 'none';
        Object.assign(link.style, {
            left: `${base.left + base.width / 2 - parent.left}px`, top: `${surfaceY - parent.top}px`,
            width: `${fit.width}px`, height: `${fit.height}px`, maxHeight: 'none',
            transform: `translate(-50%, -100%) scale(${currentScale})`, transformOrigin: '50% 100%', overflow: 'hidden',
        });
        Object.assign(image.style, {
            position: 'absolute', width: `${fit.imageWidth}px`, height: `${fit.imageHeight}px`,
            maxWidth: 'none', maxHeight: 'none', left: `${fit.left}px`, top: `${fit.top}px`, objectFit: 'fill',
        });
        link.dataset.fitted = 'true';
    }

    link.style.opacity = '0';
    if (reducedMotion.matches) link.style.transition = 'none';
    try {
        const [photo] = await Promise.all([measurePhoto(slides[0].image), podium.decode()]);
        currentPhoto = photo;
        position();
    } catch {
        // The server-rendered first slide remains usable if preparation fails.
    } finally {
        link.style.opacity = '1';
    }
    const observer = new ResizeObserver(position);
    observer.observe(scene);
    observer.observe(podium);
    if (slides.length < 2 || reducedMotion.matches) return;

    let index = 0;
    let changing = false;
    setInterval(async () => {
        if (changing || document.hidden || link.matches(':hover, :focus-within')) return;
        changing = true;
        const next = (index + 1) % slides.length;
        try {
            const photo = await measurePhoto(slides[next].image);
            link.style.opacity = '0';
            await new Promise(resolve => setTimeout(resolve, 500));
            image.src = slides[next].image;
            image.alt = slides[next].name;
            link.href = slides[next].url;
            currentPhoto = photo;
            currentScale = slides[next].scale ?? 1;
            position();
        } catch {
            // Skip an unavailable photo and keep the last successful product visible.
        } finally {
            index = next;
            link.style.opacity = '1';
            changing = false;
        }
    }, 4000);
}
