/* ============================================================
   FULL-SCREEN MAP MODAL  –  shared by Emergency Alert &
   Family Check-In pages
   ============================================================
   Depends on:
     • Leaflet  1.9.x  (loaded by each page)
     • Leaflet Routing Machine  (loaded here via CDN if absent)

   Public API:
     FullMapModal.open(pwdLat, pwdLng, familyMembers)
       familyMembers: [{ name, lat, lng, color }]
     FullMapModal.close()
   ============================================================ */

(function (global) {
    'use strict';

    /* ── CDN loaders ── */
    function loadScript(src, integrity) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            if (integrity) { s.integrity = integrity; s.crossOrigin = 'anonymous'; }
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }
    function loadStyle(href) {
        if (document.querySelector('link[href="' + href + '"]')) return;
        var l = document.createElement('link');
        l.rel = 'stylesheet'; l.href = href;
        document.head.appendChild(l);
    }

    /* ── State ── */
    var map          = null;
    var routingCtrl  = null;
    var activeMembers = [];
    var pwdLatLng    = null;
    var selectedMember = null;    // { name, lat, lng }

    /* ── Build DOM ── */
    function buildDOM() {
        if (document.getElementById('fullMapModal')) return;

        var html = [
            '<div id="fullMapModal" role="dialog" aria-modal="true" aria-label="Full Map">',
            '  <div id="fullMapShell">',
            '    <div id="fullMapHeader">',
            '      <i class="ri-map-2-line" style="font-size:18px;"></i>',
            '      <h3>Family Location Map</h3>',
            '      <button id="fullMapCloseBtn" aria-label="Close map" onclick="FullMapModal.close()">',
            '        <i class="ri-close-line"></i>',
            '      </button>',
            '    </div>',
            '    <div id="fullMapCanvas"></div>',
            '    <div id="fullMapInfoBox"></div>',
            '    <div id="fullMapActionBar">',
            '      <button id="fmBtnGuideMe"   disabled onclick="FullMapModal.routeTo()">',
            '        <i class="ri-navigation-line"></i> Guide Me There',
            '      </button>',
            '      <button id="fmBtnGuideThem" disabled onclick="FullMapModal.routeBack()">',
            '        <i class="ri-walk-line"></i> Guide Them to Me',
            '      </button>',
            '      <button id="fmBtnClearRoute" onclick="FullMapModal.clearRoute()">',
            '        <i class="ri-delete-bin-line"></i> Clear',
            '      </button>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('\n');

        var wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.body.appendChild(wrapper.firstChild);

        /* close on overlay click */
        document.getElementById('fullMapModal').addEventListener('click', function (e) {
            if (e.target === this) FullMapModal.close();
        });

        /* ESC key */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') FullMapModal.close();
        });
    }

    /* ── PWD marker icon ── */
    function pwdIcon() {
        return L.divIcon({
            className: '',
            html: [
                '<div style="',
                '  width:44px;height:52px;position:relative;',
                '">',
                '  <div style="',
                '    width:44px;height:44px;background:linear-gradient(135deg,#1565C0,#1976D2);',
                '    border-radius:50% 50% 50% 0;transform:rotate(-45deg);',
                '    border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.3);',
                '  "></div>',
                '  <div style="',
                '    position:absolute;top:7px;left:8px;',
                '    color:#fff;font-size:18px;line-height:1;',
                '  ">♿</div>',
                '</div>'
            ].join(''),
            iconSize: [44, 52],
            iconAnchor: [22, 52],
            popupAnchor: [0, -54]
        });
    }

    /* ── Family member marker icon ── */
    function familyIcon(color) {
        return L.divIcon({
            className: '',
            html: [
                '<div style="',
                '  width:38px;height:46px;position:relative;',
                '">',
                '  <div style="',
                '    width:38px;height:38px;background:' + color + ';',
                '    border-radius:50% 50% 50% 0;transform:rotate(-45deg);',
                '    border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.25);',
                '  "></div>',
                '  <div style="',
                '    position:absolute;top:6px;left:9px;',
                '    color:#fff;font-size:16px;line-height:1;',
                '  ">👤</div>',
                '</div>'
            ].join(''),
            iconSize: [38, 46],
            iconAnchor: [19, 46],
            popupAnchor: [0, -48]
        });
    }

    /* ── Initialise (or reinitialise) the Leaflet map ── */
    function initMap(pwdLat, pwdLng, members) {
        pwdLatLng = [pwdLat, pwdLng];
        activeMembers = members;
        selectedMember = null;

        var canvas = document.getElementById('fullMapCanvas');

        /* destroy previous instance */
        if (map) {
            map.remove();
            map = null;
            routingCtrl = null;
        }

        map = L.map(canvas, { zoomControl: true });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        /* PWD marker */
        L.marker(pwdLatLng, { icon: pwdIcon() })
            .addTo(map)
            .bindPopup('<b>📍 You (PWD)</b><br>Your current location')
            .openPopup();

        /* family markers */
        var allLatLngs = [pwdLatLng];

        members.forEach(function (m) {
            var mLatLng = [m.lat, m.lng];
            allLatLngs.push(mLatLng);

            var popupContent = buildPopupHTML(m);
            var marker = L.marker(mLatLng, { icon: familyIcon(m.color || '#e91e63') })
                .addTo(map)
                .bindPopup(popupContent, { minWidth: 200 });

            marker.on('popupopen', function () {
                selectMember(m);
                /* wire popup buttons after popup is in DOM */
                setTimeout(function () {
                    var el = document.getElementById('fm-popup-' + slugify(m.name));
                    if (!el) return;
                    el.querySelector('.fm-popup-btn-guide-me').onclick   = function () { FullMapModal.routeTo();   };
                    el.querySelector('.fm-popup-btn-guide-them').onclick = function () { FullMapModal.routeBack(); };
                }, 50);
            });
        });

        /* fit bounds */
        if (allLatLngs.length > 1) {
            map.fitBounds(L.latLngBounds(allLatLngs), { padding: [40, 40] });
        } else {
            map.setView(pwdLatLng, 15);
        }

        /* reset buttons */
        setActionBtnsEnabled(false);
        hideInfoBox();
    }

    function slugify(str) {
        return str.replace(/[^a-zA-Z0-9]/g, '-');
    }

    function buildPopupHTML(m) {
        var id = 'fm-popup-' + slugify(m.name);
        return [
            '<div id="' + id + '">',
            '  <b>' + escHtml(m.name) + '</b>',
            '  <div class="fm-popup-btns">',
            '    <button class="fm-popup-btn fm-popup-btn-guide-me">',
            '      <i class="ri-navigation-line"></i> Guide Me There',
            '    </button>',
            '    <button class="fm-popup-btn fm-popup-btn-guide-them">',
            '      <i class="ri-walk-line"></i> Guide Them to Me',
            '    </button>',
            '  </div>',
            '</div>'
        ].join('');
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function selectMember(m) {
        selectedMember = m;
        setActionBtnsEnabled(true);
    }

    function setActionBtnsEnabled(enabled) {
        var btn1 = document.getElementById('fmBtnGuideMe');
        var btn2 = document.getElementById('fmBtnGuideThem');
        if (btn1) btn1.disabled = !enabled;
        if (btn2) btn2.disabled = !enabled;
    }

    /* ── Routing ── */
    function drawRoute(from, to, colour) {
        if (!map) return;
        clearRoutingCtrl();

        routingCtrl = L.Routing.control({
            waypoints: [
                L.latLng(from[0], from[1]),
                L.latLng(to[0],   to[1])
            ],
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1',
                profile: 'foot'
            }),
            lineOptions: {
                styles: [{ color: colour, weight: 5, opacity: 0.85 }]
            },
            show: false,
            addWaypoints: false,
            routeWhileDragging: false,
            fitSelectedRoutes: true,
            createMarker: function () { return null; }   /* suppress default markers */
        }).addTo(map);

        routingCtrl.on('routesfound', function (e) {
            var summary = e.routes[0].summary;
            var dist = summary.totalDistance;
            var time = summary.totalTime;
            showInfoBox(dist, time);
        });

        routingCtrl.on('routingerror', function () {
            showInfoBox(null, null, 'Route unavailable – try a different destination.');
        });
    }

    function clearRoutingCtrl() {
        if (routingCtrl && map) {
            try { map.removeControl(routingCtrl); } catch (e) { /* ignore */ }
            routingCtrl = null;
        }
        hideInfoBox();
    }

    /* ── Info box ── */
    function showInfoBox(distM, timeSec, errorMsg) {
        var box = document.getElementById('fullMapInfoBox');
        if (!box) return;
        if (errorMsg) {
            box.innerHTML = '<strong>⚠️ ' + escHtml(errorMsg) + '</strong>';
        } else {
            var km   = (distM / 1000).toFixed(2);
            var mins = Math.ceil(timeSec / 60);
            box.innerHTML = [
                '<strong>📍 Route:</strong>',
                ' &nbsp;' + km + ' km &nbsp;|&nbsp;',
                '<span>≈ ' + mins + ' min on foot</span>'
            ].join('');
        }
        box.classList.add('visible');
    }
    function hideInfoBox() {
        var box = document.getElementById('fullMapInfoBox');
        if (box) box.classList.remove('visible');
    }

    /* ── LRM loader ── */
    function ensureLRM() {
        return new Promise(function (resolve, reject) {
            if (typeof L !== 'undefined' && L.Routing) { resolve(); return; }

            loadStyle('https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css');
            loadScript('https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js')
                .then(resolve)
                .catch(reject);
        });
    }

    /* ── Public API ── */
    var FullMapModal = {

        open: function (pwdLat, pwdLng, familyMembers) {
            buildDOM();

            var modal = document.getElementById('fullMapModal');
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';

            /* Leaflet needs a moment after display:flex before sizing */
            setTimeout(function () {
                initMap(pwdLat, pwdLng, familyMembers || []);
            }, 80);
        },

        close: function () {
            var modal = document.getElementById('fullMapModal');
            if (modal) modal.classList.remove('open');
            document.body.style.overflow = '';
            clearRoutingCtrl();
        },

        routeTo: function () {
            if (!selectedMember) return;
            ensureLRM().then(function () {
                drawRoute(pwdLatLng, [selectedMember.lat, selectedMember.lng], '#1976D2');
            });
        },

        routeBack: function () {
            if (!selectedMember) return;
            ensureLRM().then(function () {
                drawRoute([selectedMember.lat, selectedMember.lng], pwdLatLng, '#388E3C');
            });
        },

        clearRoute: function () {
            clearRoutingCtrl();
        }
    };

    global.FullMapModal = FullMapModal;

}(window));
