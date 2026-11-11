'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const script = fs.readFileSync(path.join(__dirname, '../assets/js/maatek.js'), 'utf8');

test('viewer uses responsive sources and announces each selected photograph', () => {
	const handlers = {};
	const statusContainer = { appendChild(node) { this.status = node; } };
	const counter = { parentNode: statusContainer, textContent: '', removeAttribute(name) { this.removed = name; } };
	const viewer = { style: {} };
	const title = {};
	const description = {};
	const next = { addEventListener(name, handler) { handlers.next = handler; } };
	const prev = { addEventListener(name, handler) { handlers.prev = handler; } };
	const makeCard = (image, cardTitle) => {
		const media = { appendChild(node) { this.button = node; }, querySelector() { return null; } };
		return {
			classList: { contains() { return false; } },
			closest() { return null; },
			getAttribute() { return null; },
			querySelector(selector) {
				return ({ img: image, '.mtk-photo-card-media': media, '.mtk-photo-card-open': media.button,
					'.mtk-card-title': { textContent: cardTitle }, '.mtk-card-desc': { textContent: 'Description' } })[selector] || null;
			},
			addEventListener(name, handler) { if (name === 'click') this.click = handler; },
		};
	};
	const first = makeCard({ src: '/full.jpg', currentSrc: '/thumb.jpg', srcset: '/thumb.jpg 320w, /full.jpg 1600w', alt: 'Panel', closest() { return null; } }, 'Main panel');
	const second = makeCard({ src: '/second.jpg', currentSrc: '/second.jpg', srcset: '', alt: 'Conveyor', closest() { return null; } }, 'Conveyor');
	const dialog = {
		open: false,
		querySelector(selector) { return ({ '.mtk-lightbox-img': viewer, '.mtk-lightbox-title': title,
			'.mtk-lightbox-desc': description, '.mtk-lightbox-counter': counter, '.mtk-lightbox-next': next,
			'.mtk-lightbox-prev': prev })[selector] || null; },
		showModal() { this.open = true; },
		addEventListener() {},
	};
	const document = {
		readyState: 'complete', body: { classList: { contains() { return false; } } },
		querySelectorAll(selector) { return selector === '.mtk-photo-card' ? [first, second] : []; },
		getElementById() { return dialog; },
		createElement() {
			return { className: '', attrs: {}, setAttribute(name, value) { this.attrs[name] = value; },
				focus() { document.activeElement = this; },
				get classList() { return { contains: (name) => this.className.split(' ').includes(name) }; } };
		},
	};
	vm.runInNewContext(script, { URL, window: { location: new URL('https://example.test/') }, document });
	const button = first.querySelector('.mtk-photo-card-open');
	first.click({ target: { closest: () => button } });
	assert.equal(viewer.src, '/full.jpg', 'the card thumbnail currentSrc is not used as the viewer source');
	assert.equal(viewer.srcset, '/thumb.jpg 320w, /full.jpg 1600w');
	assert.match(viewer.sizes, /980px/);
	assert.equal(counter.removed, 'aria-live');
	assert.equal(counter.textContent, '· 1 / 2');
	assert.equal(statusContainer.status.attrs.role, 'status');
	assert.equal(statusContainer.status.textContent, 'Main panel. Photograph 1 of 2.');
	handlers.next({ stopPropagation() {} });
	assert.equal(viewer.src, '/second.jpg');
	assert.equal(viewer.srcset, '', 'source candidates from the previous image are cleared');
	assert.equal(viewer.sizes, '');
	assert.equal(counter.textContent, '· 2 / 2');
	assert.equal(statusContainer.status.textContent, 'Conveyor. Photograph 2 of 2.');
});
