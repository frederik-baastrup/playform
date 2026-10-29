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

	// Mobilmenu: gruppepunkter (fx Kondiudstyr) har ingen egen side, kun en undermenu
	document.querySelectorAll('.mm__item--group > .mm__link').forEach(function (label) {
		label.addEventListener('click', function () {
			var item = label.closest('.mm__item--group');
			var open = item.classList.toggle('is-open');
			label.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	});

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

	// Nyhedsbrev: sendes til vores endpoint, som videresender til Brevo/MailerLite (double opt-in)
	document.querySelectorAll('[data-newsletter]').forEach(function (form) {
		var note = form.querySelector('.news__note');
		var btn = form.querySelector('button');
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			btn.disabled = true;
			fetch(form.getAttribute('data-endpoint'), {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ email: form.email.value, website: form.website ? form.website.value : '' })
			}).then(function (r) { return r.json(); }).then(function (d) {
				note.textContent = d.message || 'Tak!';
				if (d.ok) form.email.value = '';
			}).catch(function () {
				note.textContent = 'Noget gik galt. Prøv igen senere.';
			}).finally(function () { btn.disabled = false; });
		});
	});
})();
