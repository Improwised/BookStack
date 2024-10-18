import * as DOM from '../services/dom';
import {scrollAndHighlightElement} from '../services/util';
import {Component} from './component';

let currentHighlighElement = null;

function checkForCurrentHeading(element) {
    const pageNavItem = document.querySelectorAll('.page-nav-item');
    const pageNavItemCurrentHeadingCount = Array.from(pageNavItem).filter(navItem => navItem.classList.contains('current-heading')).length;
    if (pageNavItemCurrentHeadingCount === 0) {
        currentHighlighElement = element;
        element.closest('li').classList.toggle('current-heading', true);
    }
}

function toggleAnchorHighlighting(elementId, shouldHighlight) {
    if (shouldHighlight && currentHighlighElement !== null) {
        currentHighlighElement.closest('li').classList.toggle('current-heading', false);
        currentHighlighElement = null;
    }
    DOM.forEach(`#page-navigation a[href="#${elementId}"]`, anchor => {
        anchor.closest('li').classList.toggle('current-heading', shouldHighlight);
        checkForCurrentHeading(anchor);
    });
}

function headingVisibilityChange(entries) {
    for (const entry of entries) {
        const isVisible = (entry.intersectionRatio === 1);
        toggleAnchorHighlighting(entry.target.id, isVisible);
    }
}

function addNavObserver(headings) {
    const intersectOpts = {
        rootMargin: '0px 0px 0px 0px',
        threshold: 1.0,
    };
    const pageNavObserver = new IntersectionObserver(headingVisibilityChange, intersectOpts);

    for (const heading of headings) {
        pageNavObserver.observe(heading);
    }
}

export class PageDisplay extends Component {
    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;

        window.importVersioned('code').then(Code => Code.highlight());
        this.setupNavHighlighting();

        if (window.location.hash) {
            const text = window.location.hash.replace(/%20/g, ' ').substring(1);
            this.goToText(text);
        }

        const sidebarPageNav = document.querySelector('.sidebar-page-nav');
        if (sidebarPageNav) {
            DOM.onChildEvent(sidebarPageNav, 'a', 'click', (event, child) => {
                event.preventDefault();
                window.$components.first('tri-layout').showContent();
                const contentId = child.getAttribute('href').substr(1);
                this.goToText(contentId);
                window.history.pushState(null, null, `#${contentId}`);
            });
        }
    }

    goToText(text) {
        const idElem = document.getElementById(text);

        DOM.forEach('.page-content [data-highlighted]', elem => {
            elem.removeAttribute('data-highlighted');
            elem.style.backgroundColor = null;
        });

        if (idElem !== null) {
            scrollAndHighlightElement(idElem);
        } else {
            const textElem = DOM.findText('.page-content > div > *', text);
            if (textElem) {
                scrollAndHighlightElement(textElem);
            }
        }
    }

    setupNavHighlighting() {
        const pageNav = document.querySelector('.sidebar-page-nav');
        const headings = document.querySelector('.page-content').querySelectorAll('h1, h2, h3, h4, h5, h6');
        if (headings.length > 0 && pageNav !== null) {
            addNavObserver(headings);
        }
    }
}
