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
	});
	// Panelet starter altid på første kategori, når det åbnes igen
	document.querySelectorAll('.mm__item[data-mm]').forEach(function (item) {
		item.addEventListener('mouseleave', function () {
			if (mobile.matches) return;
			var inner = item.querySelector('.mm__inner');
			if (inner) showPane(inner, '0');
		});
	});

	// Topbar på mobil: fordelene skifter roligt hvert 4. sekund
	var msgs = document.querySelectorAll('.topbar__msg');
	if (msgs.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		var cur = 0;
		setInterval(function () {
			if (document.hidden) return;
			msgs[cur].classList.remove('is-on');
			cur = (cur + 1) % msgs.length;
			msgs[cur].classList.add('is-on');
		}, 4000);
	}

	// Alle menupaneler får samme højde som det højeste, så panelet ikke hopper, når man skifter punkt.
	// Måles ved første hover (skjult), og igen hvis vinduet ændrer størrelse.
	if (menu) {
		var measured = false;
		var measurePanels = function () {
			if (mobile.matches) return;
			var max = 0;
			menu.querySelectorAll('.mm__item[data-mm]').forEach(function (item) {
				var panel = item.querySelector('.mm__panel');
				var inner = item.querySelector('.mm__inner');
				if (!panel || !inner) return;
				var was = panel.getAttribute('style') || '';
				panel.style.cssText = 'display:block;visibility:hidden;pointer-events:none';
				inner.style.minHeight = '0';
				var active = inner.querySelector('.mm__pane.is-active');
				inner.querySelectorAll('.mm__pane').forEach(function (pane) {
					inner.querySelectorAll('.mm__pane').forEach(function (x) { x.classList.toggle('is-active', x === pane); });
					max = Math.max(max, inner.offsetHeight);
				});
				inner.querySelectorAll('.mm__pane').forEach(function (x) { x.classList.toggle('is-active', x === active); });
				inner.style.minHeight = '';
				panel.setAttribute('style', was);
				if (!was) panel.removeAttribute('style');
			});
			if (max) menu.style.setProperty('--mm-h', max + 'px');
			measured = true;
		};
		menu.addEventListener('mouseenter', function () { if (!measured) measurePanels(); });
		window.addEventListener('resize', function () { measured = false; });
	}

	// Menuens faktiske bredde, så den folder sig jævnt sammen, når søgefeltet vokser
	if (menu && header) {
		menu.querySelectorAll(':scope > .mm__item').forEach(function (it, i) { it.style.setProperty('--i', i); });
		var measure = function () {
			if (header.classList.contains('is-searching')) return;
			header.style.setProperty('--mm-w', menu.scrollWidth + 'px');
		};
		measure();
		window.addEventListener('resize', measure);
		if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);
	}

	// Smal skærm: luppen folder søgefeltet ud under logoet
	var searchToggle = document.querySelector('.search-toggle');
	var closeSearch = function () {
		if (!header || !header.classList.contains('is-search-open')) return;
		header.classList.remove('is-search-open');
		if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false');
	};
	if (searchToggle && header) {
		searchToggle.addEventListener('click', function () {
			if (header.classList.contains('is-search-open')) { closeSearch(); return; }
			if (drawer && drawer.classList.contains('is-open')) toggle.click();
			header.classList.add('is-search-open');
			searchToggle.setAttribute('aria-expanded', 'true');
			var field = document.getElementById('site-search');
			if (field) field.focus();
		});
	}

	// Mobilmenu: skuffe med lag (menupunkt → kategori). Links med data-to åbner næste lag i stedet for siden.
	var toggle = document.querySelector('.menu-toggle');
	var drawer = document.getElementById('mobilmenu');
	var drawerBg = document.querySelector('[data-close-bg]');
	if (toggle && drawer) {
		var stack = ['root'];
		var view = function (id) { return drawer.querySelector('[data-view="' + id + '"]'); };
		var paint = function () {
			drawer.querySelectorAll('.dr__view').forEach(function (v) {
				var i = stack.indexOf(v.getAttribute('data-view'));
				v.classList.toggle('is-active', i === stack.length - 1);
				v.classList.toggle('is-behind', i > -1 && i < stack.length - 1);
			});
		};
		var openDrawer = function () {
			if (header) {
				header.classList.remove('is-tucked');
				closeSearch();
				document.documentElement.style.setProperty('--dr-top', Math.max(0, header.getBoundingClientRect().bottom) + 'px');
			}
			drawer.removeAttribute('inert');
			drawer.setAttribute('aria-hidden', 'false');
			drawer.classList.add('is-open');
			if (drawerBg) { drawerBg.hidden = false; requestAnimationFrame(function () { drawerBg.classList.add('is-open'); }); }
			document.documentElement.classList.add('dr-lock');
			toggle.setAttribute('aria-expanded', 'true');
			toggle.setAttribute('aria-label', 'Luk menu');
		};
		var closeDrawer = function () {
			drawer.classList.remove('is-open');
			if (drawerBg) { drawerBg.classList.remove('is-open'); setTimeout(function () { if (!drawer.classList.contains('is-open')) drawerBg.hidden = true; }, 300); }
			drawer.setAttribute('aria-hidden', 'true');
			drawer.setAttribute('inert', '');
			document.documentElement.classList.remove('dr-lock');
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-label', 'Åbn menu');
			// Start forfra næste gang, når skuffen er gledet ud
			setTimeout(function () { if (!drawer.classList.contains('is-open')) { stack = ['root']; paint(); drawer.querySelectorAll('.dr__view').forEach(function (v) { v.scrollTop = 0; }); } }, 350);
		};
		toggle.addEventListener('click', function () { if (drawer.classList.contains('is-open')) closeDrawer(); else openDrawer(); });
		if (drawerBg) drawerBg.addEventListener('click', closeDrawer);
		drawer.addEventListener('click', function (e) {
			var to = e.target.closest('[data-to]');
			if (to && view(to.getAttribute('data-to'))) {
				e.preventDefault();
				var next = view(to.getAttribute('data-to'));
				next.scrollTop = 0;
				stack.push(to.getAttribute('data-to'));
				paint();
				var b = next.querySelector('[data-back]');
				if (b) setTimeout(function () { b.focus({ preventScroll: true }); }, 50);
				return;
			}
			if (e.target.closest('[data-back]') && stack.length > 1) { stack.pop(); paint(); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape' || !drawer.classList.contains('is-open')) return;
			if (stack.length > 1) { stack.pop(); paint(); } else closeDrawer();
		});
		mobile.addEventListener('change', function () { if (!mobile.matches) closeDrawer(); });
		// Kommer man tilbage til siden med browserens tilbage-knap, er skuffen lukket
		window.addEventListener('pageshow', function () { if (drawer.classList.contains('is-open')) closeDrawer(); });
	}

	// Smal skærm: headeren gemmer sig, når man scroller ned, og kommer frem igen, når man scroller op
	if (header) {
		var lastY = window.scrollY, ticking = false;
		window.addEventListener('scroll', function () {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(function () {
				var y = window.scrollY;
				var tuck = header.classList.contains('is-tucked');
				if (!mobile.matches || y < 160 || header.classList.contains('is-searching')) tuck = false;
				else if (y > lastY + 4) tuck = true;
				else if (y < lastY - 4) tuck = false;
				header.classList.toggle('is-tucked', tuck);
				lastY = y;
				ticking = false;
			});
		}, { passive: true });
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
