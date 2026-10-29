/* Fitbase – lidt JavaScript til sliders, menu, sortering og foldbar tekst. Siden virker også uden. */
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

	var header = document.querySelector('.site-header');
	var menu = document.getElementById('hovedmenu');
	var mobile = window.matchMedia('(max-width: 1080px)');

	// Menupanel: kategorien man holder over (eller fokuserer) styrer midter- og priskolonnen.
	// En lille forsinkelse gør, at man kan bevæge musen skråt over til midterkolonnen uden at skifte.
	function showPane(inner, idx) {
		inner.querySelectorAll('[data-pane]').forEach(function (el) {
			el.classList.toggle('is-active', el.getAttribute('data-pane') === idx);
		});
	}
	document.querySelectorAll('.mm__cat').forEach(function (cat) {
		var timer;
		var inner = cat.closest('.mm__inner');
		cat.addEventListener('mouseenter', function () {
			if (mobile.matches) return;
			clearTimeout(timer);
			timer = setTimeout(function () { showPane(inner, cat.getAttribute('data-pane')); }, 90);
		});
		cat.addEventListener('mouseleave', function () { clearTimeout(timer); });
		cat.addEventListener('focus', function () { if (!mobile.matches) showPane(inner, cat.getAttribute('data-pane')); });
		// Mobil: klik dig ind på kategorien i stedet for at gå til siden
		cat.addEventListener('click', function (e) {
			if (!mobile.matches) return;
			e.preventDefault();
			showPane(inner, cat.getAttribute('data-pane'));
			inner.classList.add('has-pane');
		});
	});
	// Panelet starter altid på første kategori, når det åbnes igen
	document.querySelectorAll('.mm__item[data-mm]').forEach(function (item) {
		item.addEventListener('mouseleave', function () {
			if (mobile.matches) return;
			var inner = item.querySelector('.mm__inner');
			if (inner) showPane(inner, '0');
		});
	});

	// Mobilmenu
	var toggle = document.querySelector('.menu-toggle');
	var back = menu ? menu.querySelector('.mm__back') : null;
	function resetDrill() {
		if (!menu) return;
		menu.classList.remove('is-drill');
		menu.querySelectorAll('.mm__item.is-open').forEach(function (i) { i.classList.remove('is-open'); });
		menu.querySelectorAll('.mm__inner.has-pane').forEach(function (i) { i.classList.remove('has-pane'); });
		if (back) back.hidden = true;
	}
	if (toggle && menu) {
		toggle.addEventListener('click', function () {
			var open = menu.classList.toggle('is-open');
			if (!open) resetDrill();
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.setAttribute('aria-label', open ? 'Luk menu' : 'Åbn menu');
		});
		menu.querySelectorAll('.mm__item[data-mm] > .mm__link').forEach(function (link) {
			link.addEventListener('click', function (e) {
				if (!mobile.matches) return;
				e.preventDefault();
				link.parentNode.classList.add('is-open');
				menu.classList.add('is-drill');
				back.hidden = false;
			});
		});
		if (back) back.addEventListener('click', function () {
			var inner = menu.querySelector('.mm__item.is-open .mm__inner');
			if (inner && inner.classList.contains('has-pane')) inner.classList.remove('has-pane');
			else resetDrill();
		});
		mobile.addEventListener('change', function () { menu.classList.remove('is-open'); resetDrill(); });
	}

	// Søgning: feltet vokser, menuen toner ud, forslag mens man skriver
	var form = document.querySelector('[data-search]');
	if (form && header) {
		var input = form.querySelector('input[name="s"]');
		var drop = form.querySelector('.search__drop');
		var popular = form.querySelector('.search__popular');
		var results = form.querySelector('.search__results');
		var endpoint = form.getAttribute('data-endpoint');
		var debounce, latest = 0;

		function el(tag, cls, text) {
			var n = document.createElement(tag);
			if (cls) n.className = cls;
			if (text) n.textContent = text;
			return n;
		}
		var placeholder = input.placeholder;
		function open() {
			header.classList.add('is-searching');
			input.placeholder = 'Hvad leder du efter?';
			drop.hidden = false;
		}
		function close() {
			header.classList.remove('is-searching');
			input.placeholder = placeholder;
			drop.hidden = true;
		}
		function render(d, q) {
			results.textContent = '';
			if (d.cats && d.cats.length) {
				var g = el('div', 'search__group');
				g.appendChild(el('span', 'mm__title', 'Kategorier'));
				var wrap = el('div', 'search__cats');
				d.cats.forEach(function (c) { var a = el('a', '', c.name); a.href = c.url; wrap.appendChild(a); });
				g.appendChild(wrap);
				results.appendChild(g);
			}
			if (d.products && d.products.length) {
				var gp = el('div', 'search__group');
				gp.appendChild(el('span', 'mm__title', 'Produkter'));
				var list = el('div', 'search__prods');
				d.products.forEach(function (p) {
					var a = el('a', 'search__prod');
					a.href = p.url;
					if (p.image) { var img = el('img'); img.src = p.image; img.alt = ''; img.loading = 'lazy'; a.appendChild(img); }
					else a.appendChild(el('span', 'search__ph'));
					var name = el('span', 'search__name');
					name.appendChild(el('b', '', p.name));
					if (p.brand) name.appendChild(el('span', '', p.brand));
					a.appendChild(name);
					a.appendChild(el('span', 'search__price', p.price));
					list.appendChild(a);
				});
				gp.appendChild(list);
				results.appendChild(gp);
				var all = el('a', 'search__all', 'Se alle resultater for "' + q + '" →');
				all.href = d.all;
				results.appendChild(all);
			}
			if (!results.childNodes.length) results.appendChild(el('p', 'search__empty', 'Ingen produkter matcher "' + q + '". Prøv et andet ord.'));
			popular.hidden = true;
			results.hidden = false;
		}
		function suggest() {
			var q = input.value.trim();
			if (q.length < 2) { popular.hidden = false; results.hidden = true; return; }
			var id = ++latest;
			fetch(endpoint + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } })
				.then(function (r) { return r.json(); })
				.then(function (d) { if (id === latest) render(d, q); })
				.catch(function () {});
		}
		input.addEventListener('focus', open);
		form.addEventListener('click', function () { if (!header.classList.contains('is-searching')) input.focus(); });
		input.addEventListener('input', function () { clearTimeout(debounce); debounce = setTimeout(suggest, 180); });
		document.addEventListener('click', function (e) { if (!form.contains(e.target)) close(); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && header.classList.contains('is-searching')) { close(); input.blur(); }
		});
		form.addEventListener('submit', function (e) { if (!input.value.trim()) e.preventDefault(); });
		if (input.value.trim()) suggest();
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
