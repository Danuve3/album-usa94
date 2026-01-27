import { PageFlip } from 'page-flip';

/**
 * PageFlip wrapper for Livewire integration
 * Provides a realistic page-turning effect for the album
 */
class AlbumPageFlip {
    constructor(container, options = {}) {
        this.container = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!this.container) {
            console.error('PageFlip: Container element not found');
            return;
        }

        this.options = {
            width: options.width || 400,
            height: options.height || 500,
            size: options.size || 'stretch',
            minWidth: options.minWidth || 300,
            maxWidth: options.maxWidth || 600,
            minHeight: options.minHeight || 400,
            maxHeight: options.maxHeight || 800,
            drawShadow: options.drawShadow !== false,
            flippingTime: options.flippingTime || 800,
            usePortrait: options.usePortrait !== false,
            startZIndex: options.startZIndex || 0,
            autoSize: options.autoSize !== false,
            maxShadowOpacity: options.maxShadowOpacity || 0.5,
            showCover: options.showCover !== false,
            mobileScrollSupport: options.mobileScrollSupport !== false,
            swipeDistance: options.swipeDistance || 30,
            clickEventForward: options.clickEventForward !== false,
            useMouseEvents: options.useMouseEvents !== false,
            showPageCorners: options.showPageCorners !== false,
            disableFlipByClick: options.disableFlipByClick || false,
            ...options
        };

        this.pageFlip = null;
        this.eventListeners = {};
        this.livewireComponent = null;

        this.init();
    }

    init() {
        this.pageFlip = new PageFlip(this.container, this.options);

        const pages = this.container.querySelectorAll('.page');
        if (pages.length > 0) {
            this.pageFlip.loadFromHTML(pages);
        }

        this.bindEvents();
    }

    bindEvents() {
        this.pageFlip.on('flip', (e) => {
            this.emit('flip', { page: e.data });
            this.emitToLivewire('pageFlipped', { page: e.data });
        });

        this.pageFlip.on('changeOrientation', (e) => {
            this.emit('changeOrientation', { orientation: e.data });
            this.emitToLivewire('orientationChanged', { orientation: e.data });
        });

        this.pageFlip.on('changeState', (e) => {
            this.emit('changeState', { state: e.data });
            this.emitToLivewire('stateChanged', { state: e.data });
        });
    }

    setLivewireComponent(component) {
        this.livewireComponent = component;
    }

    emitToLivewire(event, data) {
        if (this.livewireComponent) {
            this.livewireComponent.call(event, data);
        }

        window.dispatchEvent(new CustomEvent(`album:${event}`, { detail: data }));
    }

    on(event, callback) {
        if (!this.eventListeners[event]) {
            this.eventListeners[event] = [];
        }
        this.eventListeners[event].push(callback);
        return this;
    }

    off(event, callback) {
        if (!this.eventListeners[event]) return this;
        if (!callback) {
            this.eventListeners[event] = [];
        } else {
            this.eventListeners[event] = this.eventListeners[event].filter(cb => cb !== callback);
        }
        return this;
    }

    emit(event, data) {
        if (!this.eventListeners[event]) return;
        this.eventListeners[event].forEach(callback => callback(data));
    }

    flipNext() {
        if (this.pageFlip) {
            this.pageFlip.flipNext();
        }
        return this;
    }

    flipPrev() {
        if (this.pageFlip) {
            this.pageFlip.flipPrev();
        }
        return this;
    }

    flip(pageIndex) {
        if (this.pageFlip) {
            this.pageFlip.flip(pageIndex);
        }
        return this;
    }

    turnToPage(pageIndex) {
        if (this.pageFlip) {
            this.pageFlip.turnToPage(pageIndex);
        }
        return this;
    }

    getCurrentPage() {
        return this.pageFlip ? this.pageFlip.getCurrentPageIndex() : 0;
    }

    getPageCount() {
        return this.pageFlip ? this.pageFlip.getPageCount() : 0;
    }

    getOrientation() {
        return this.pageFlip ? this.pageFlip.getOrientation() : 'landscape';
    }

    getState() {
        return this.pageFlip ? this.pageFlip.getState() : 'read';
    }

    loadFromHTML(pages) {
        if (this.pageFlip && pages.length > 0) {
            this.pageFlip.loadFromHTML(pages);
        }
        return this;
    }

    updateFromHTML(pages) {
        if (this.pageFlip && pages.length > 0) {
            this.pageFlip.updateFromHtml(pages);
        }
        return this;
    }

    update() {
        if (this.pageFlip) {
            this.pageFlip.update();
        }
        return this;
    }

    destroy() {
        if (this.pageFlip) {
            this.pageFlip.destroy();
            this.pageFlip = null;
        }
        this.eventListeners = {};
        this.livewireComponent = null;
    }
}

/**
 * Alpine.js directive for easy integration
 * Usage: x-page-flip="{options}"
 */
function initAlpineDirective() {
    if (typeof Alpine !== 'undefined') {
        Alpine.directive('page-flip', (el, { expression }, { evaluate, cleanup }) => {
            const options = expression ? evaluate(expression) : {};
            const instance = new AlbumPageFlip(el, options);

            el._pageFlip = instance;

            cleanup(() => {
                instance.destroy();
                delete el._pageFlip;
            });
        });

        Alpine.magic('pageFlip', (el) => {
            while (el && !el._pageFlip) {
                el = el.parentElement;
            }
            return el?._pageFlip || null;
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAlpineDirective);
} else {
    initAlpineDirective();
}

window.AlbumPageFlip = AlbumPageFlip;

export { AlbumPageFlip };
export default AlbumPageFlip;
