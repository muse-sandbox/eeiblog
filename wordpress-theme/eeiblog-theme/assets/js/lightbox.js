/**
 * EEi Blog — vanilla-JS lightbox.
 *
 * Click any image inside `.entry-content` or `.post-content-body` to open it
 * fullscreen with prev/next navigation. Keyboard shortcuts:
 *   Esc          — close
 *   ArrowLeft    — previous
 *   ArrowRight   — next
 *
 * Galleries: navigation cycles through images in the same .entry-content /
 * .post-content-body container (not just the same gallery block — feels nicer
 * for posts with multiple gallery sections).
 */
(function () {
    'use strict';

    const SELECTOR = '.entry-content figure.wp-block-image img, .post-content-body figure.wp-block-image img';

    let overlay, lbImg, counter, prevBtn, nextBtn, captionEl;
    let images = [];
    let current = 0;

    function buildOverlay() {
        overlay = document.createElement('div');
        overlay.className = 'eei-lightbox';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML = [
            '<button type="button" class="eei-lightbox__close" aria-label="Close">×</button>',
            '<button type="button" class="eei-lightbox__prev" aria-label="Previous image">‹</button>',
            '<button type="button" class="eei-lightbox__next" aria-label="Next image">›</button>',
            '<figure class="eei-lightbox__figure">',
            '  <img class="eei-lightbox__img" alt=""/>',
            '  <figcaption class="eei-lightbox__caption"></figcaption>',
            '</figure>',
            '<div class="eei-lightbox__counter"></div>'
        ].join('');
        document.body.appendChild(overlay);

        lbImg     = overlay.querySelector('.eei-lightbox__img');
        captionEl = overlay.querySelector('.eei-lightbox__caption');
        counter   = overlay.querySelector('.eei-lightbox__counter');
        prevBtn   = overlay.querySelector('.eei-lightbox__prev');
        nextBtn   = overlay.querySelector('.eei-lightbox__next');
        const closeBtn = overlay.querySelector('.eei-lightbox__close');

        closeBtn.addEventListener('click', close);
        prevBtn.addEventListener('click', () => navigate(-1));
        nextBtn.addEventListener('click', () => navigate(1));

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay || e.target === lbImg.parentNode) close();
        });

        document.addEventListener('keydown', (e) => {
            if (!overlay.classList.contains('is-open')) return;
            if (e.key === 'Escape') { e.preventDefault(); close(); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); navigate(-1); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); navigate(1); }
        });
    }

    function open(srcImg) {
        const container = srcImg.closest('.entry-content, .post-content-body');
        images = container ? Array.from(container.querySelectorAll(SELECTOR)) : [srcImg];
        current = Math.max(0, images.indexOf(srcImg));
        showCurrent();
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function navigate(direction) {
        if (images.length < 2) return;
        current = (current + direction + images.length) % images.length;
        showCurrent();
    }

    function showCurrent() {
        const src = images[current];
        lbImg.src = src.dataset.fullSrc || largestSrcsetUrl(src) || src.src;
        lbImg.alt = src.alt || '';
        captionEl.textContent = src.alt || '';
        captionEl.style.display = src.alt ? '' : 'none';
        const showNav = images.length > 1;
        prevBtn.style.display    = showNav ? '' : 'none';
        nextBtn.style.display    = showNav ? '' : 'none';
        counter.style.display    = showNav ? '' : 'none';
        if (showNav) counter.textContent = (current + 1) + ' / ' + images.length;
    }

    function largestSrcsetUrl(img) {
        const ss = img.getAttribute('srcset');
        if (!ss) return null;
        let best = null, bestW = 0;
        ss.split(',').forEach(part => {
            const m = part.trim().match(/^(\S+)\s+(\d+)w$/);
            if (m && parseInt(m[2], 10) > bestW) {
                bestW = parseInt(m[2], 10);
                best = m[1];
            }
        });
        return best;
    }

    function init() {
        buildOverlay();
        document.querySelectorAll(SELECTOR).forEach((node) => {
            const a = node.closest('a');
            const willNavigate = a && a.href && !/\.(jpe?g|png|webp|gif|avif)(\?|#|$)/i.test(a.href);
            // Only mark as zoomable when we'll actually open the lightbox.
            // Otherwise the wrapping <a>'s native pointer cursor wins.
            if (!willNavigate) {
                node.style.cursor = 'zoom-in';
            }
            node.addEventListener('click', (e) => {
                if (willNavigate) {
                    return;
                }
                e.preventDefault();
                open(node);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
