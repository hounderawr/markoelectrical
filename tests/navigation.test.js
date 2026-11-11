'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const script = fs.readFileSync(path.join(__dirname, '../assets/js/maatek.js'), 'utf8');

function activeLinks(currentUrl, hrefs) {
	const links = hrefs.map((href) => {
		const classes = new Set();
		const attributes = {};
		return {
			href,
			classes,
			attributes,
			classList: { add: (name) => classes.add(name) },
			getAttribute: (name) => name === 'href' ? href : attributes[name],
			setAttribute: (name, value) => { attributes[name] = value; },
		};
	});
	const location = new URL(currentUrl);
	const document = {
		readyState: 'complete',
		querySelectorAll: () => links,
		getElementById: () => null,
	};
	vm.runInNewContext(script, { URL, window: { location }, document });
	return links.filter((link) => link.classes.has('is-active'));
}

test('plain permalinks identify only the requested WordPress page', () => {
	const matches = activeLinks('https://example.test/?page_id=42&utm_source=email', [
		'https://example.test/',
		'https://example.test/?page_id=42',
		'https://example.test/?page_id=73',
	]);
	assert.deepEqual(matches.map((link) => link.href), ['https://example.test/?page_id=42']);
	assert.equal(matches[0].attributes['aria-current'], 'page');
});

test('pretty permalinks ignore tracking and trailing slashes but not external origins', () => {
	const matches = activeLinks('https://example.test/services/?utm_campaign=test', [
		'https://example.test/services',
		'https://elsewhere.test/services/',
		'https://example.test/contact/',
	]);
	assert.deepEqual(matches.map((link) => link.href), ['https://example.test/services']);
});

test('home, section and search URLs are not confused with each other', () => {
	assert.deepEqual(activeLinks('https://example.test/?s=motors', [
		'https://example.test/', '#contact', 'https://example.test/?s=motors',
	]).map((link) => link.href), ['https://example.test/?s=motors']);
	assert.deepEqual(activeLinks('https://example.test/', [
		'https://example.test/', '#contact', 'https://example.test/?page_id=42',
	]).map((link) => link.href), ['https://example.test/']);
});

test('linked gallery images keep their link and receive a separate viewer button', () => {
	const handlers = {};
	const media = {
		button: null,
		querySelector: () => null,
		appendChild(button) { this.button = button; },
	};
	const link = { classList: { contains: () => false } };
	const image = { src: '/project.jpg', alt: 'Switchboard', closest: () => link };
	const card = {
		classList: { contains: () => false },
		closest: () => null,
		getAttribute: () => null,
		querySelector(selector) {
			return ({
				'img': image,
				'.mtk-photo-card-media': media,
				'.mtk-photo-card-open': media.button,
				'.mtk-card-title': { textContent: 'Switchboard' },
			})[selector] || null;
		},
		addEventListener(name, callback) { handlers[name] = callback; },
	};
	const viewerImage = { style: {} };
	const dialog = {
		open: false,
		querySelector(selector) { return selector === '.mtk-lightbox-img' ? viewerImage : null; },
		showModal() { this.open = true; },
		addEventListener() {},
	};
	const document = {
		readyState: 'complete',
		body: { classList: { contains: () => false } },
		querySelectorAll(selector) { return selector === '.mtk-photo-card' ? [card] : []; },
		getElementById: () => dialog,
		createElement() {
			return {
				className: '',
				setAttribute() {},
				focus() { document.activeElement = this; },
				get classList() { return { contains: (name) => this.className.split(' ').includes(name) }; },
			};
		},
	};
	vm.runInNewContext(script, { URL, window: { location: new URL('https://example.test/') }, document });
	assert.match(media.button.className, /mtk-photo-card-open--linked/);
	handlers.click({ target: { closest: () => link } });
	assert.equal(dialog.open, false, 'a link click must not open the viewer');
	handlers.click({ target: { closest: () => media.button } });
	assert.equal(dialog.open, true);
	assert.equal(document.activeElement, media.button, 'the viewer trigger is focused for dialog restoration');
});
