/**
 * Maatek Front-End Interactions
 * - Active navigation indicator (#64C2F4 bar) for core/navigation links
 * - Accessible Project Photo Card Lightbox modal with Prev/Next navigation,
 *   counter badge, smooth image transition, and per-card/section disable support.
 */
(function () {
	'use strict';

	function normalizePath(urlOrPath) {
		try {
			var u = new URL(urlOrPath, window.location.origin);
			return u.pathname.replace(/\/+$/, '') || '/';
		} catch (e) {
			return (urlOrPath || '/').replace(/\/+$/, '') || '/';
		}
	}

	function initNavigation() {
		var currentPath = normalizePath(window.location.pathname);
		var navLinks = document.querySelectorAll('.mtk-nav .wp-block-navigation-item__content, [data-nav-slug]');

		navLinks.forEach(function (link) {
			var linkPath = normalizePath(link.getAttribute('href') || '');
			if (linkPath === currentPath) {
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

			imgEl.src = cardImg.currentSrc || cardImg.src;
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
			if (!card.hasAttribute('tabindex')) {
				card.setAttribute('tabindex', '0');
				card.setAttribute('role', 'button');
			}

			card.addEventListener('click', function (e) {
				if (e.target.closest('a') || isCardLightboxDisabled(card)) {
					return;
				}
				openCard(card);
			});

			card.addEventListener('keydown', function (e) {
				if (isCardLightboxDisabled(card)) {
					return;
				}
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					openCard(card);
				}
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
