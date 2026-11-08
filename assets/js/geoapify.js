// ============================================================
// Silent Signal — Geoapify helpers (shared by the family
// dashboard map and the SOS page map)
//
//  • Map tiles        → Leaflet tile layer from Geoapify
//  • Reverse geocoding → coordinates to a readable address
//  • Places           → hospitals, clinics, police, fire stations
//  • Routing          → road route drawn on the Leaflet map
//
// Needs Leaflet loaded first and window.GEOAPIFY_KEY set by the
// page (it comes from GEOAPIFY_API_KEY in config/config.php).
// If the key is missing, the map falls back to plain OSM tiles
// and Nominatim addresses so nothing breaks.
// ============================================================
(function (w) {
    'use strict';

    const KEY     = String(w.GEOAPIFY_KEY || '').trim();
    const HAS_KEY = KEY.length >= 16;
    const API     = 'https://api.geoapify.com';

    const RADIUS_M = 5000;

    const TYPES = {
        hospital: { cat: 'healthcare.hospital',         label: 'Hospital',     chip: 'Hospitals', icon: 'ri-hospital-fill',      color: '#e53935' },
        clinic:   { cat: 'healthcare.clinic_or_praxis', label: 'Clinic',       chip: 'Clinics',   icon: 'ri-first-aid-kit-fill', color: '#d81b60' },
        police:   { cat: 'service.police',              label: 'Police',       chip: 'Police',    icon: 'ri-police-car-fill',    color: '#1e4e79' },
        fire:     { cat: 'service.fire_station',        label: 'Fire station', chip: 'Fire',      icon: 'ri-fire-fill',          color: '#ef6c00' },
    };
    const TYPE_ORDER = ['hospital', 'clinic', 'police', 'fire'];

    // ---------- small utils ----------
    function esc(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function distanceM(lat1, lng1, lat2, lng2) {
        const R = 6371000, toRad = d => d * Math.PI / 180;
        const dLat = toRad(lat2 - lat1), dLng = toRad(lng2 - lng1);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
        return 2 * R * Math.asin(Math.sqrt(a));
    }

    function fmtDistance(m) {
        if (m == null || isNaN(m)) return '';
        if (m < 1000) return Math.max(10, Math.round(m / 10) * 10) + ' m';
        return (m / 1000).toFixed(m < 10000 ? 1 : 0) + ' km';
    }

    function fmtTime(s) {
        if (s == null || isNaN(s)) return '';
        const min = Math.max(1, Math.round(s / 60));
        if (min < 60) return min + ' min';
        return Math.floor(min / 60) + ' hr ' + (min % 60) + ' min';
    }

    function fetchJson(url, ms = 9000) {
        const ctrl = ('AbortController' in w) ? new AbortController() : null;
        const t = ctrl ? setTimeout(() => ctrl.abort(), ms) : null;
        return fetch(url, ctrl ? { signal: ctrl.signal } : undefined)
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .finally(() => { if (t) clearTimeout(t); });
    }

    // localStorage cache (safe if storage is blocked)
    const cache = {
        get(k) {
            try {
                const v = JSON.parse(localStorage.getItem('ssgeo:' + k));
                if (!v || (v.exp && v.exp < Date.now())) return null;
                return v.data;
            } catch (e) { return null; }
        },
        set(k, data, ttlMs) {
            try { localStorage.setItem('ssgeo:' + k, JSON.stringify({ data, exp: Date.now() + ttlMs })); } catch (e) { /* ignore */ }
        }
    };

    // ---------- 1. Map tiles ----------
    function addTiles(map) {
        const osm = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            maxZoom: 19,
        });
        if (!HAS_KEY) return osm().addTo(map);

        const retina = (L.Browser && L.Browser.retina) ? '@2x' : '';
        const geo = L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}${retina}.png?apiKey=${KEY}`, {
            attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            maxZoom: 20,
        });

        // If Geoapify tiles keep failing (bad key / daily limit), switch to OSM
        let errors = 0, swapped = false;
        geo.on('tileerror', () => {
            errors++;
            if (errors >= 4 && !swapped) {
                swapped = true;
                map.removeLayer(geo);
                osm().addTo(map);
                console.warn('[SilentSignal] Geoapify tiles failed, using OpenStreetMap tiles instead');
            }
        });
        return geo.addTo(map);
    }

    // ---------- 2. Reverse geocoding ----------
    function shortAddress(p) {
        const line1 = p.address_line1 || p.street || p.name || '';
        const city  = p.city || p.county || p.state || '';
        if (line1 && city && line1.indexOf(city) === -1) return line1 + ', ' + city;
        return line1 || p.formatted || '';
    }

    function reverse(lat, lng) {
        lat = parseFloat(lat); lng = parseFloat(lng);
        if (!isFinite(lat) || !isFinite(lng) || (lat === 0 && lng === 0)) return Promise.resolve(null);

        const ck = 'rv:' + lat.toFixed(4) + ',' + lng.toFixed(4);
        const hit = cache.get(ck);
        if (hit) return Promise.resolve(hit);

        const viaGeoapify = () => fetchJson(`${API}/v1/geocode/reverse?lat=${lat}&lon=${lng}&format=json&apiKey=${KEY}`)
            .then(d => {
                const p = (d.results && d.results[0]) || (d.features && d.features[0] && d.features[0].properties);
                if (!p || !p.formatted) throw new Error('no result');
                return { short: shortAddress(p), full: p.formatted, source: 'geoapify' };
            });

        const viaNominatim = () => fetchJson(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(d => {
                if (!d || !d.display_name) throw new Error('no result');
                return { short: d.display_name.split(',').slice(0, 3).join(',').trim(), full: d.display_name, source: 'nominatim' };
            });

        return (HAS_KEY ? viaGeoapify().catch(viaNominatim) : viaNominatim())
            .then(r => { cache.set(ck, r, 7 * 24 * 3600 * 1000); return r; })
            .catch(err => { console.warn('[SilentSignal] Address lookup failed:', err.message); return null; });
    }

    // ---------- 3. Nearby places ----------
    function typeOf(categories) {
        const cats = categories || [];
        for (const t of TYPE_ORDER) {
            const c = TYPES[t].cat;
            if (cats.some(x => x === c || x.indexOf(c + '.') === 0)) return t;
        }
        return null;
    }

    function nearby(lat, lng, radius = RADIUS_M) {
        if (!HAS_KEY) return Promise.reject(new Error('no-key'));
        lat = parseFloat(lat); lng = parseFloat(lng);

        const ck = 'nb:' + lat.toFixed(3) + ',' + lng.toFixed(3) + ':' + radius;
        const hit = cache.get(ck);
        if (hit) return Promise.resolve(hit);

        const cats = TYPE_ORDER.map(t => TYPES[t].cat).join(',');
        const url = `${API}/v2/places?categories=${cats}&filter=circle:${lng},${lat},${radius}&bias=proximity:${lng},${lat}&limit=100&apiKey=${KEY}`;

        return fetchJson(url).then(d => {
            const seen = new Set();
            const places = (d.features || []).map(f => {
                const p = f.properties || {};
                const g = f.geometry && f.geometry.coordinates;
                const plat = p.lat != null ? p.lat : (g ? g[1] : null);
                const plng = p.lon != null ? p.lon : (g ? g[0] : null);
                const type = typeOf(p.categories);
                if (plat == null || plng == null || !type) return null;
                const id = p.place_id || (plat.toFixed(5) + ',' + plng.toFixed(5));
                if (seen.has(id)) return null;
                seen.add(id);
                const phone = (p.contact && p.contact.phone) || (p.datasource && p.datasource.raw && p.datasource.raw.phone) || '';
                return {
                    id, type,
                    name: p.name || TYPES[type].label,
                    lat: plat, lng: plng,
                    address: p.address_line2 || p.formatted || '',
                    street: p.address_line1 && p.address_line1 !== p.name ? p.address_line1 : '',
                    phone,
                    distance: distanceM(lat, lng, plat, plng),
                };
            }).filter(Boolean).filter(p => p.distance <= radius + 50);
            places.sort((a, b) => a.distance - b.distance);
            cache.set(ck, places, 12 * 3600 * 1000);
            return places;
        });
    }

    // ---------- 4. Routing ----------
    function route(from, to, mode = 'drive') {
        if (!HAS_KEY) return Promise.reject(new Error('no-key'));
        const k = 'rt:' + [from.lat, from.lng, to.lat, to.lng].map(v => (+v).toFixed(4)).join(',') + ':' + mode;
        const hit = cache.get(k);
        if (hit) return Promise.resolve(hit);

        const url = `${API}/v1/routing?waypoints=${from.lat},${from.lng}|${to.lat},${to.lng}&mode=${mode}&apiKey=${KEY}`;
        return fetchJson(url, 12000).then(d => {
            const f = d.features && d.features[0];
            if (!f || !f.geometry) throw new Error('no route');
            const g = f.geometry;
            const lines = g.type === 'MultiLineString' ? g.coordinates : [g.coordinates];
            const latlngs = lines.map(line => line.map(c => [c[1], c[0]]));
            const r = { latlngs, distance: f.properties.distance, time: f.properties.time, mode };
            cache.set(k, r, 30 * 60 * 1000);   // reuse for 30 minutes
            return r;
        });
    }

    // ---------- Leaflet bits ----------
    function placeIcon(type) {
        const t = TYPES[type];
        return L.divIcon({
            className: '',
            html: `<div class="ss-place-pin" style="--c:${t.color}"><i class="${t.icon}"></i></div>`,
            iconSize: [32, 38],
            iconAnchor: [16, 38],
            popupAnchor: [0, -36],
        });
    }

    function personIcon(label, color = '#1e4e79', pulse = true) {
        return L.divIcon({
            className: '',
            html: `<div class="ss-me-pin" style="--c:${color}">${pulse ? '<span class="ss-me-pin__pulse"></span>' : ''}<span class="ss-me-pin__dot">${esc(label || '')}</span></div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -18],
        });
    }

    // ============================================================
    // Nearby panel: markers + filter chips + list + route bar
    // ============================================================
    function NearbyPanel(opts) {
        this.map      = opts.map;
        this.chipsEl  = opts.chipsEl;
        this.listEl   = opts.listEl;
        this.routeEl  = opts.routeEl;
        this.showCall = !!opts.showCall;
        this.maxList  = opts.maxList || 30;
        this.onStatus = opts.onStatus || function () {};
        this.layer    = L.layerGroup().addTo(this.map);
        this.routeLayer = L.layerGroup().addTo(this.map);
        this.places   = [];
        this.markers  = {};
        this.filter   = 'all';
        this.origin   = null;   // {lat, lng, label}
        this.current  = null;   // last routed {from, to, toLabel, mode}
        this._token   = 0;
        const self = this;

        if (this.chipsEl) {
            this.chipsEl.addEventListener('click', e => {
                const b = e.target.closest('[data-filter]');
                if (!b) return;
                self.filter = b.getAttribute('data-filter');
                self._render();
            });
        }
        if (this.listEl) {
            this.listEl.addEventListener('click', e => {
                const rb = e.target.closest('[data-route]');
                const it = e.target.closest('[data-place]');
                if (rb) { e.stopPropagation(); self.routeToPlace(rb.getAttribute('data-route')); return; }
                if (it) self.focusPlace(it.getAttribute('data-place'));
            });
        }
        if (this.routeEl) {
            this.routeEl.addEventListener('click', e => {
                const m = e.target.closest('[data-mode]');
                if (m && self.current) { self._drawRoute(self.current.from, self.current.to, self.current.toLabel, m.getAttribute('data-mode')); return; }
                if (e.target.closest('[data-clear]')) self.clearRoute();
            });
        }
        // Buttons inside Leaflet popups
        this.map.on('popupopen', ev => {
            const el = ev.popup.getElement();
            if (!el) return;
            el.querySelectorAll('[data-route]').forEach(btn => {
                btn.onclick = () => { self.map.closePopup(); self.routeToPlace(btn.getAttribute('data-route')); };
            });
        });
    }

    NearbyPanel.prototype.load = function (lat, lng, label) {
        const self = this, token = ++this._token;
        this.origin = { lat: parseFloat(lat), lng: parseFloat(lng), label: label || '' };
        this.layer.clearLayers();
        this.markers = {};
        this.places = [];
        if (this.listEl) this.listEl.innerHTML = '<div class="geo-empty"><i class="ri-loader-4-line ri-spin"></i> Looking for hospitals, clinics, police and fire stations nearby…</div>';
        if (this.chipsEl) this.chipsEl.innerHTML = '';

        if (!HAS_KEY) {
            if (this.listEl) this.listEl.innerHTML = '<div class="geo-empty">Nearby places need the Geoapify API key in config.php.</div>';
            return Promise.resolve([]);
        }
        return nearby(lat, lng).then(places => {
            if (token !== self._token) return places;
            self.places = places;
            places.forEach(p => {
                const m = L.marker([p.lat, p.lng], { icon: placeIcon(p.type), riseOnHover: true })
                    .bindPopup(self._popup(p), { className: 'ss-geo-popup', maxWidth: 240 });
                self.markers[p.id] = m;
            });
            self._render();
            return places;
        }).catch(err => {
            if (token !== self._token) return [];
            console.warn('[SilentSignal] Nearby places failed:', err.message);
            if (self.listEl) self.listEl.innerHTML = '<div class="geo-empty">Couldn’t load nearby places. Check your connection and try again.</div>';
            return [];
        });
    };

    NearbyPanel.prototype._popup = function (p) {
        const t = TYPES[p.type];
        const from = this.origin && this.origin.label ? ' from ' + esc(this.origin.label) : '';
        return `
            <div class="ss-geo-pop">
                <div class="ss-geo-pop__type" style="color:${t.color}"><i class="${t.icon}"></i> ${t.label}</div>
                <div class="ss-geo-pop__name">${esc(p.name)}</div>
                ${p.street || p.address ? `<div class="ss-geo-pop__addr">${esc([p.street, p.address].filter(Boolean).join(', '))}</div>` : ''}
                <div class="ss-geo-pop__dist">${fmtDistance(p.distance)} away${from}</div>
                <div class="ss-geo-pop__actions">
                    <button type="button" class="ss-geo-btn ss-geo-btn--primary" data-route="${esc(p.id)}"><i class="ri-route-line"></i> Route</button>
                    ${this.showCall && p.phone ? `<a class="ss-geo-btn" href="tel:${esc(p.phone.replace(/[^\d+]/g, ''))}"><i class="ri-phone-line"></i> Call</a>` : ''}
                </div>
            </div>`;
    };

    NearbyPanel.prototype._render = function () {
        const self = this;
        const counts = { all: this.places.length };
        TYPE_ORDER.forEach(t => counts[t] = this.places.filter(p => p.type === t).length);

        if (this.chipsEl) {
            const chip = (key, label, color) =>
                `<button type="button" class="geo-chip ${self.filter === key ? 'is-active' : ''}" data-filter="${key}" ${color ? `style="--c:${color}"` : ''}>
                    ${label} <span>${counts[key]}</span></button>`;
            this.chipsEl.innerHTML = chip('all', 'All') + TYPE_ORDER.map(t => chip(t, TYPES[t].chip, TYPES[t].color)).join('');
        }

        const shown = this.places.filter(p => self.filter === 'all' || p.type === self.filter);
        this.layer.clearLayers();
        shown.forEach(p => self.layer.addLayer(self.markers[p.id]));

        if (!this.listEl) return;
        if (!this.places.length) {
            this.listEl.innerHTML = '<div class="geo-empty">No hospitals, clinics, police or fire stations found within 5 km.</div>';
            return;
        }
        if (!shown.length) {
            this.listEl.innerHTML = '<div class="geo-empty">None of these within 5 km.</div>';
            return;
        }
        this.listEl.innerHTML = shown.slice(0, this.maxList).map(p => {
            const t = TYPES[p.type];
            return `
                <div class="geo-item" data-place="${esc(p.id)}">
                    <span class="geo-item__icon" style="--c:${t.color}"><i class="${t.icon}"></i></span>
                    <span class="geo-item__text">
                        <span class="geo-item__name">${esc(p.name)}</span>
                        <span class="geo-item__meta">${t.label} · ${fmtDistance(p.distance)}${p.address ? ' · ' + esc(p.address) : ''}</span>
                    </span>
                    <button type="button" class="geo-item__route" data-route="${esc(p.id)}" title="Show route"><i class="ri-route-line"></i></button>
                </div>`;
        }).join('');
    };

    NearbyPanel.prototype.focusPlace = function (id) {
        const p = this.places.find(x => x.id === id);
        const m = this.markers[id];
        if (!p || !m) return;
        if (!this.layer.hasLayer(m)) this.layer.addLayer(m);
        this.map.setView([p.lat, p.lng], Math.max(this.map.getZoom(), 16), { animate: true });
        m.openPopup();
    };

    NearbyPanel.prototype.routeToPlace = function (id) {
        const p = this.places.find(x => x.id === id);
        if (!p || !this.origin) return;
        this._drawRoute(this.origin, { lat: p.lat, lng: p.lng }, p.name, this.current ? this.current.mode : 'drive');
    };

    // Route between any two points (used for "route from you to her")
    NearbyPanel.prototype.routeBetween = function (from, to, toLabel, mode) {
        return this._drawRoute(from, to, toLabel, mode || 'drive');
    };

    NearbyPanel.prototype._drawRoute = function (from, to, toLabel, mode) {
        const self = this;
        this.current = { from, to, toLabel, mode };
        this._routeBar('loading');
        return route(from, to, mode).then(r => {
            if (!self.current || self.current.to !== to || self.current.mode !== mode) return r;
            self.routeLayer.clearLayers();
            const casing = L.polyline(r.latlngs, { color: '#ffffff', weight: 9, opacity: 0.9, lineCap: 'round', lineJoin: 'round' });
            const line   = L.polyline(r.latlngs, { color: mode === 'walk' ? '#2e7d32' : '#1976d2', weight: 5, opacity: 0.95, lineCap: 'round', lineJoin: 'round', dashArray: mode === 'walk' ? '2 9' : null });
            self.routeLayer.addLayer(casing).addLayer(line);
            self.map.fitBounds(line.getBounds(), { padding: [40, 40], maxZoom: 17 });
            self._routeBar('ok', r);
            return r;
        }).catch(err => {
            console.warn('[SilentSignal] Route failed:', err.message);
            if (self.current && self.current.to === to) self._routeBar('error');
        });
    };

    NearbyPanel.prototype._routeBar = function (state, r) {
        if (!this.routeEl) return;
        const c = this.current;
        if (!c) { this.routeEl.hidden = true; return; }
        const modeBtn = (m, icon, label) => `<button type="button" class="geo-mode ${c.mode === m ? 'is-active' : ''}" data-mode="${m}"><i class="${icon}"></i> ${label}</button>`;
        let main;
        if (state === 'loading') main = `<span class="geo-route__main"><i class="ri-loader-4-line ri-spin"></i> Getting route…</span>`;
        else if (state === 'error') main = `<span class="geo-route__main geo-route__main--err"><i class="ri-error-warning-line"></i> Couldn’t get a route right now.</span>`;
        else main = `<span class="geo-route__main"><strong>${fmtDistance(r.distance)} · ${fmtTime(r.time)}</strong><span>to ${esc(c.toLabel)}</span></span>`;
        this.routeEl.innerHTML = `
            ${main}
            <span class="geo-route__modes">${modeBtn('drive', 'ri-car-line', 'Car')}${modeBtn('walk', 'ri-walk-line', 'Walk')}</span>
            <button type="button" class="geo-route__close" data-clear title="Clear route"><i class="ri-close-line"></i></button>`;
        this.routeEl.hidden = false;
    };

    NearbyPanel.prototype.clearRoute = function () {
        this.current = null;
        this.routeLayer.clearLayers();
        if (this.routeEl) { this.routeEl.hidden = true; this.routeEl.innerHTML = ''; }
    };

    NearbyPanel.prototype.nearest = function (type) {
        return this.places.find(p => p.type === type) || null;
    };

    w.SSGeo = {
        hasKey: HAS_KEY,
        TYPES, RADIUS_M,
        addTiles, reverse, nearby, route,
        placeIcon, personIcon,
        fmtDistance, fmtTime, distanceM, esc,
        NearbyPanel,
    };
})(window);
