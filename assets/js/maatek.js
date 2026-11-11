/**
 * Maatek Front-End Interactions
 * - Active navigation indicator (#64C2F4 bar) for core/navigation links
 * - Accessible Project Photo Card Lightbox modal with Prev/Next navigation,
 *   counter badge, smooth image transition, and per-card/section disable support.
 */
(function () {
	'use strict';

	// WordPress plain permalinks distinguish pages by query parameters, while
	// tracking parameters and fragments do not change which page is current.
	var pageQueryKeys = ['page_id', 'p', 'pagename', 'name', 'post_type', 'cat', 'category_name', 'tag', 'author', 's'];

	function pageIdentity(urlOrPath) {
		try {
			var u = new URL(urlOrPath, window.location.href);
			var query = pageQueryKeys.map(function (key) {
				return [key, u.searchParams.getAll(key).sort()];
			});
			return JSON.stringify([u.origin, u.pathname.replace(/\/+$/, '') || '/', query]);
		} catch (e) {
			return null;
		}
	}

	function initNavigation() {
		var currentPage = pageIdentity(window.location.href);
		var navLinks = document.querySelectorAll('.mtk-nav .wp-block-navigation-item__content, [data-nav-slug]');

		navLinks.forEach(function (link) {
			var href = link.getAttribute('href');
			// A fragment links to a section, not the current page in the main navigation.
			if (href && href.charAt(0) !== '#' && pageIdentity(href) === currentPage) {
				link.classList.add('is-active');
				link.setAttribute('aria-current', 'page');
			}
		});
	}

	function isCardLightboxDisabled(card) {
		if (document.body.classList.contains('mtk-lightbox-disabled')) {
			return true;
		}
		if (
			card.classList.contains('is-style-mtk-no-lightbox') ||
			card.classList.contains('mtk-no-lightbox')
		) {
			return true;
		}
		if (card.closest('.is-style-mtk-no-lightbox, .mtk-no-lightbox')) {
			return true;
		}
		return false;
	}

	function initLightbox() {
		var dialog = document.getElementById('mtk-lightbox');
		if (!dialog || document.body.classList.contains('mtk-lightbox-disabled')) {
			return;
		}

		var imgEl = dialog.querySelector('.mtk-lightbox-img');
		var titleEl = dialog.querySelector('.mtk-lightbox-title');
		var descEl = dialog.querySelector('.mtk-lightbox-desc');
		var counterEl = dialog.querySelector('.mtk-lightbox-counter');
		var prevBtn = dialog.querySelector('.mtk-lightbox-prev');
		var nextBtn = dialog.querySelector('.mtk-lightbox-next');
		var closeBtn = dialog.querySelector('.mtk-lightbox-close');
		var statusEl = null;
		if (counterEl && counterEl.parentNode) {
			// Keep the visible count short while announcing the changed image title too.
			// The empty region exists before its first update so assistive technology
			// can observe later changes when Next or Previous is activated.
			counterEl.removeAttribute('aria-live');
			statusEl = document.createElement('span');
			statusEl.className = 'mtk-sr-only';
			statusEl.setAttribute('role', 'status');
			statusEl.setAttribute('aria-live', 'polite');
			counterEl.parentNode.appendChild(statusEl);
		}

		var allCards = Array.prototype.slice
			.call(document.querySelectorAll('.mtk-photo-card'))
			.filter(function (card) {
				return !isCardLightboxDisabled(card) && !!card.querySelector('img');
			});

		if (!allCards.length) {
			return;
		}

		var currentIndex = 0;

		var renderCardAt = function (index) {
			if (!allCards.length) {
				return;
			}
			currentIndex = (index + allCards.length) % allCards.length;
			var card = allCards[currentIndex];
			var cardImg = card.querySelector('img');
			if (!cardImg || !imgEl) {
				return;
			}

			var titleNode = card.querySelector('.mtk-card-title');
			var descNode = card.querySelector('.mtk-card-desc');

			// Re-trigger smooth image fade animation
			imgEl.style.animation = 'none';
			void imgEl.offsetWidth;
			imgEl.style.animation = '';

			// currentSrc is selected for the small card and can be a thumbnail. Let
			// the browser select a candidate for the larger viewer instead, clearing
			// the previous card's candidates when the next image has no srcset.
			imgEl.sizes = cardImg.srcset ? '(max-width: 1060px) calc(100vw - 80px), 980px' : '';
			imgEl.srcset = cardImg.srcset || '';
			imgEl.src = cardImg.src || cardImg.currentSrc;
			imgEl.alt = cardImg.alt || (titleNode ? titleNode.textContent : '');

			if (titleEl) {
				titleEl.textContent = titleNode ? titleNode.textContent : (cardImg.alt || '');
			}
			if (descEl) {
				descEl.textContent = descNode ? descNode.textContent : '';
			}
			if (counterEl) {
				counterEl.textContent = allCards.length > 1 ? '· ' + (currentIndex + 1) + ' / ' + allCards.length : '';
			}
			if (statusEl) {
				var photoTitle = titleNode ? titleNode.textContent.trim() : (cardImg.alt || '').trim();
				statusEl.textContent = (photoTitle ? photoTitle + '. ' : '') + 'Photograph ' + (currentIndex + 1) + ' of ' + allCards.length + '.';
			}

			var showNav = allCards.length > 1;
			if (prevBtn) {
				prevBtn.hidden = !showNav;
			}
			if (nextBtn) {
				nextBtn.hidden = !showNav;
			}
		};

		var openCard = function (card) {
			if (isCardLightboxDisabled(card)) {
				return;
			}
			var idx = allCards.indexOf(card);
			renderCardAt(idx >= 0 ? idx : 0);

			if (typeof dialog.showModal === 'function') {
				if (!dialog.open) {
					// Clicking the card body may not focus the button. Give the native
					// dialog a consistent trigger to return to when it closes.
					var trigger = card.querySelector('.mtk-photo-card-open');
					if (trigger && document.activeElement !== trigger) {
						trigger.focus({ preventScroll: true });
					}
					dialog.showModal();
				}
			} else {
				dialog.setAttribute('open', '');
			}
		};

		var closeLightbox = function () {
			if (typeof dialog.close === 'function' && dialog.open) {
				dialog.close();
			} else {
				dialog.removeAttribute('open');
			}
		};

		allCards.forEach(function (card) {
			// Remove legacy whole-card button semantics from older markup/versions.
			if (card.getAttribute('role') === 'button') {
				card.removeAttribute('role');
				card.removeAttribute('tabindex');
			}

			// Leave linked images clickable; their separate viewer button occupies only
			// the zoom badge, while an unlinked photo uses the whole media as a target.
			var media = card.querySelector('.mtk-photo-card-media') || card;
			if (!media.querySelector('.mtk-photo-card-open')) {
				var titleNode = card.querySelector('.mtk-card-title');
				var cardImg = card.querySelector('img');
				var imageLink = cardImg && cardImg.closest('a[href]');
				var label = (titleNode && titleNode.textContent.trim()) || (cardImg && cardImg.alt) || 'project photograph';
				var openBtn = document.createElement('button');
				openBtn.type = 'button';
				openBtn.className = 'mtk-photo-card-open' + (imageLink ? ' mtk-photo-card-open--linked' : '');
				openBtn.setAttribute('aria-haspopup', 'dialog');
				openBtn.setAttribute('aria-label', 'View larger photo: ' + label);
				media.appendChild(openBtn);
			}

			// Mouse/touch convenience: clicking anywhere on the card opens the viewer,
			// except when the click came from a link or another control inside the card.
			card.addEventListener('click', function (e) {
				var interactive = e.target.closest('a, button, input, select, textarea, [role="button"]');
				if (interactive && !interactive.classList.contains('mtk-photo-card-open')) {
					return;
				}
				if (isCardLightboxDisabled(card)) {
					return;
				}
				openCard(card);
			});
		});

		if (prevBtn) {
			prevBtn.addEventListener('click', function (e) {
				e.stopPropagation();
				renderCardAt(currentIndex - 1);
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function (e) {
				e.stopPropagation();
				renderCardAt(currentIndex + 1);
			});
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', closeLightbox);
		}

		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) {
				closeLightbox();
			}
		});

		dialog.addEventListener('keydown', function (e) {
			if (allCards.length > 1) {
				if (e.key === 'ArrowLeft') {
					e.preventDefault();
					renderCardAt(currentIndex - 1);
				} else if (e.key === 'ArrowRight') {
					e.preventDefault();
					renderCardAt(currentIndex + 1);
				}
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initNavigation();
			initLightbox();
		});
	} else {
		initNavigation();
		initLightbox();
	}
})();
