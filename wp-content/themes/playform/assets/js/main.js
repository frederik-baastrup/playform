/* Playform – lidt JavaScript til sliders, menu, sortering og foldbar tekst. Siden virker også uden. */
(function () {
	// Sliders: pilene scroller én kortbredde ad gangen
	document.querySelectorAll('[data-slider-arrows]').forEach(function (wrap) {
		var slider = document.getElementById(wrap.getAttribute('data-slider-arrows'));
		if (!slider) return;
		var buttons = wrap.querySelectorAll('button');
		function update() {
			buttons[0].disabled = slider.scrollLeft <= 2;
			buttons[1].disabled = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 2;
		}
		buttons.forEach(function (b) {
			b.addEventListener('click', function () {
				var item = slider.querySelector('.slider__item');
				var step = item ? item.getBoundingClientRect().width + 16 : slider.clientWidth / 4;
				slider.scrollBy({ left: step * parseInt(b.getAttribute('data-dir'), 10), behavior: 'smooth' });
			});
		});
		slider.addEventListener('scroll', update, { passive: true });
		update();
	});

	// Mobilmenu
	var toggle = document.querySelector('.menu-toggle');
	var menu = document.getElementById('hovedmenu');
	if (toggle && menu) {
		toggle.addEventListener('click', function () {
			var open = menu.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.setAttribute('aria-label', open ? 'Luk menu' : 'Åbn menu');
		});
	}

	// Sortering skifter med det samme
	document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
		el.addEventListener('change', function () { el.form.submit(); });
	});

	// Foldbar SEO-tekst
	document.querySelectorAll('[data-seo-toggle]').forEach(function (btn) {
		var text = btn.parentNode.querySelector('[data-seo]');
		if (!text) return;
		if (text.scrollHeight <= 240) { text.classList.remove('is-collapsed'); btn.hidden = true; return; }
		btn.addEventListener('click', function () {
			var collapsed = text.classList.toggle('is-collapsed');
			btn.textContent = collapsed ? 'Læs mere' : 'Vis mindre';
			btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
		});
	});

	// Filtre er foldet sammen på mobil
	if (window.matchMedia('(max-width: 900px)').matches) {
		var f = document.querySelector('.filters__mobile');
		if (f && !document.querySelector('.tags')) f.removeAttribute('open');
	}

	// Nyhedsbrev: midlertidig besked, indtil en rigtig tjeneste er koblet på
	document.querySelectorAll('[data-newsletter]').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			form.querySelector('.news__note').textContent = 'Tak! Tilmeldingen kobles på, når nyhedsbrevet er sat op.';
		});
	});
})();
