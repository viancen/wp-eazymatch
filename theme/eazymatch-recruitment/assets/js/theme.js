/**
 * EazyMatch Recruitment theme scripts.
 */
(function () {
	'use strict';

	function initMobileNav() {
		var toggle = document.querySelector('.emr-header__toggle');
		var nav = document.getElementById('emr-mobile-nav');

		if (!toggle || !nav) {
			return;
		}

		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	function initStickyHeader() {
		var header = document.getElementById('emr-header');

		if (!header) {
			return;
		}

		var ticking = false;

		function update() {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					window.requestAnimationFrame(update);
					ticking = true;
				}
			},
			{ passive: true }
		);

		update();
	}

	/**
	 * Makes the whole job card clickable, without breaking the apply and
	 * read-more links the plugin renders inside it.
	 */
	function initJobCards() {
		var cards = document.querySelectorAll('.emol-job-result-item');

		Array.prototype.forEach.call(cards, function (card) {
			var link = card.querySelector('.eazymatch_job_title a');

			if (!link) {
				return;
			}

			card.addEventListener('click', function (event) {
				if (event.target.closest('a, button, input, select, textarea')) {
					return;
				}

				if (window.getSelection && window.getSelection().toString()) {
					return;
				}

				link.click();
			});
		});
	}

	if (document.readyState !== 'loading') {
		initMobileNav();
		initStickyHeader();
		initJobCards();
	} else {
		document.addEventListener('DOMContentLoaded', function () {
			initMobileNav();
			initStickyHeader();
			initJobCards();
		});
	}
})();
