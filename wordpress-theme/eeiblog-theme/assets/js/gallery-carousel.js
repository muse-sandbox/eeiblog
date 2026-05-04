/**
 * EEi Blog — Squarespace-style gallery carousel.
 *
 * Converts every multi-image WordPress gallery
 * (figure.wp-block-gallery.has-nested-images) inside .entry-content /
 * .post-content-body into a "big preview + thumbnails" carousel mirroring
 * the original Squarespace site:
 *
 *   - Click a thumbnail   → main image swaps (brief fade).
 *   - Click ‹ / › buttons → cycle thumbnails (wrap around).
 *   - Click main image    → opens existing lightbox at the right index.
 *   - ArrowLeft/Right when carousel has focus → cycle thumbs.
 *   - No auto-scroll / no auto-rotation.
 *
 * Lightbox compatibility:
 *   The original <figure class="wp-block-image"> nodes are moved into a
 *   hidden `.eei-carousel__source` wrapper but kept in the DOM, so
 *   lightbox.js's container-wide image indexing (it greps the whole
 *   .entry-content for `figure.wp-block-image img`) still works and the
 *   counter shows correct positions. The carousel's main-image click
 *   dispatches a synthetic click on the matching hidden source <img>,
 *   which lightbox.js's already-bound handler then catches.
 *
 * Galleries with linkTo:custom — every image is wrapped in a non-image
 * <a href> — are NOT carousel-ified (clicks are meant to navigate, not
 * preview). They keep the existing grid layout but get an `eei-no-crop`
 * class so portrait images aren't cropped to a square aspect ratio.
 */
(function () {
    'use strict';

    var GALLERY_SEL = '.entry-content figure.wp-block-gallery.has-nested-images, '
                    + '.post-content-body figure.wp-block-gallery.has-nested-images';
    var IMG_HREF_RE = /\.(jpe?g|png|webp|gif|avif)(\?|#|$)/i;

    function isCustomLinkGallery(galleryFig) {
        var items = galleryFig.querySelectorAll(':scope > figure.wp-block-image');
        if (items.length === 0) return false;
        return Array.prototype.every.call(items, function (it) {
            var a = it.querySelector(':scope > a');
            if (!a || !a.href) return false;
            var raw = a.getAttribute('href');
            if (!raw || raw === '#') return false;
            // image-targeted <a> = lightbox-style link, NOT custom navigation
            return !IMG_HREF_RE.test(a.href);
        });
    }

    function copyImgAttrs(from, to) {
        to.src = from.currentSrc || from.src;
        if (from.srcset) { to.srcset = from.srcset; } else { to.removeAttribute('srcset'); }
        if (from.sizes)  { to.sizes  = from.sizes;  } else { to.removeAttribute('sizes');  }
        to.alt = from.alt || '';
    }

    function scrollThumbIntoView(btn, container) {
        var t = btn.getBoundingClientRect();
        var c = container.getBoundingClientRect();
        if (t.left < c.left || t.right > c.right) {
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function buildCarousel(galleryFig) {
        var figures = Array.prototype.slice.call(
            galleryFig.querySelectorAll(':scope > figure.wp-block-image')
        );
        if (figures.length < 2) {
            // Single-image gallery — no carousel, just stop the cropping.
            galleryFig.classList.add('eei-no-crop');
            galleryFig.dataset.eeiCarousel = 'skipped-single';
            return;
        }

        // 1) Hidden source wrapper — keeps originals (with lightbox handlers
        //    already bound) in the DOM for lightbox.js to discover.
        var sourceWrap = document.createElement('div');
        sourceWrap.className = 'eei-carousel__source';
        sourceWrap.hidden = true;
        figures.forEach(function (f) { sourceWrap.appendChild(f); });

        // 2) Main viewer
        var main = document.createElement('div');
        main.className = 'eei-carousel__main';

        var prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'eei-carousel__nav eei-carousel__nav--prev';
        prevBtn.setAttribute('aria-label', 'Previous image');
        prevBtn.innerHTML = '‹';

        var nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'eei-carousel__nav eei-carousel__nav--next';
        nextBtn.setAttribute('aria-label', 'Next image');
        nextBtn.innerHTML = '›';

        var mainImg = document.createElement('img');
        mainImg.className = 'eei-carousel__main-img';
        mainImg.alt = '';

        main.appendChild(prevBtn);
        main.appendChild(mainImg);
        main.appendChild(nextBtn);

        // 3) Thumbnails
        var thumbs = document.createElement('div');
        thumbs.className = 'eei-carousel__thumbs';
        thumbs.setAttribute('role', 'tablist');

        var thumbButtons = figures.map(function (fig, i) {
            var srcImg = fig.querySelector('img');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'eei-carousel__thumb' + (i === 0 ? ' is-active' : '');
            btn.setAttribute('role', 'tab');
            btn.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
            btn.dataset.index = String(i);
            var img = document.createElement('img');
            if (srcImg) {
                img.src = srcImg.currentSrc || srcImg.src;
                img.alt = srcImg.alt || '';
            }
            img.loading = 'lazy';
            img.decoding = 'async';
            btn.appendChild(img);
            thumbs.appendChild(btn);
            return btn;
        });

        // 4) Mount
        galleryFig.classList.add('eei-carousel');
        galleryFig.setAttribute('data-eei-carousel', '');
        galleryFig.setAttribute('tabindex', '0');
        galleryFig.appendChild(main);
        galleryFig.appendChild(thumbs);
        galleryFig.appendChild(sourceWrap);

        // 5) Initial state
        var active = 0;
        var firstImg = figures[0].querySelector('img');
        if (firstImg) copyImgAttrs(firstImg, mainImg);

        function show(idx) {
            var n = figures.length;
            var next = ((idx % n) + n) % n;
            if (next === active) return;
            var sourceImg = figures[next].querySelector('img');
            if (!sourceImg) return;

            mainImg.classList.add('is-swapping');
            window.setTimeout(function () {
                copyImgAttrs(sourceImg, mainImg);
                mainImg.classList.remove('is-swapping');
            }, 120);

            thumbButtons[active].classList.remove('is-active');
            thumbButtons[active].setAttribute('aria-selected', 'false');
            thumbButtons[next].classList.add('is-active');
            thumbButtons[next].setAttribute('aria-selected', 'true');
            scrollThumbIntoView(thumbButtons[next], thumbs);
            active = next;
        }

        thumbButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                show(parseInt(btn.dataset.index, 10));
            });
        });
        prevBtn.addEventListener('click', function () { show(active - 1); });
        nextBtn.addEventListener('click', function () { show(active + 1); });

        // Click main image → trigger the existing lightbox via the matching
        // hidden source <img> (lightbox.js bound a click handler on it during
        // its own init pass).
        mainImg.style.cursor = 'zoom-in';
        mainImg.addEventListener('click', function () {
            var sourceImg = figures[active].querySelector('img');
            if (sourceImg && typeof sourceImg.click === 'function') {
                sourceImg.click();
            }
        });

        // Keyboard nav — only when carousel itself is focused and lightbox
        // is closed (lightbox handles its own keys via document listener).
        galleryFig.addEventListener('keydown', function (e) {
            if (document.querySelector('.eei-lightbox.is-open')) return;
            if (e.key === 'ArrowLeft')  { e.preventDefault(); show(active - 1); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); show(active + 1); }
        });
    }

    function processGallery(galleryFig) {
        if (galleryFig.classList.contains('eei-carousel')) return;
        if (galleryFig.dataset.eeiCarousel) return;

        if (isCustomLinkGallery(galleryFig)) {
            // Keep grid layout for navigation galleries, but stop the
            // is-cropped aspect-ratio squashing on portrait images.
            galleryFig.classList.add('eei-no-crop');
            galleryFig.dataset.eeiCarousel = 'skipped-custom-link';
            return;
        }

        buildCarousel(galleryFig);
    }

    function init() {
        document.querySelectorAll(GALLERY_SEL).forEach(processGallery);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
