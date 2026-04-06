/* ============================================================
   FAMILY DASHBOARD — Location Map
   Mini-map + full-screen modal with PWD markers + routing.

   Depends on:
     • Leaflet 1.9.x           (loaded by family-dashboard.php)
     • fullmap-modal.js        (loaded by family-dashboard.php)
     • pwdMembersData          (injected by family-dashboard.php)
     • currentLat / currentLng (managed by family-dashboard.js)
   ============================================================ */

(function () {
    'use strict';

    /* ── State ── */
    var miniMap       = null;
    var selfMarker    = null;
    var pwdMiniMarkers = [];
    var gpsReady      = false;

    /* ── Colour palette for PWD markers (fallback if color missing) ── */
    var COLOURS = ['#e53935','#1976d2','#43a047','#9c27b0','#ef6c00','#fbc02d'];

    /* ── Wait for DOMContentLoaded, then hook into the GPS watch ── */
    document.addEventListener('DOMContentLoaded', function () {
        /* Poll until GPS coords are populated by family-dashboard.js */
        var attempts = 0;
        var poll = setInterval(function () {
            attempts++;
            var lat = (typeof currentLat !== 'undefined') ? currentLat : null;
            var lng = (typeof currentLng !== 'undefined') ? currentLng : null;

            if (lat && lng) {
                clearInterval(poll);
                onGpsReady(lat, lng);
            } else if (attempts > 60) {
                /* After 30 s give up quietly */
                clearInterval(poll);
                var badge = document.getElementById('familyMapBadge');
                if (badge) badge.textContent = 'GPS unavailable';
            }
        }, 500);

        /* Also watch for subsequent GPS updates */
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function (pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                if (!gpsReady) {
                    onGpsReady(lat, lng);
                } else {
                    updateSelfMarker(lat, lng);
                }
            }, null, { enableHighAccuracy: true });
        }
    });

    /* ── Called once we have the first valid GPS fix ── */
    function onGpsReady(lat, lng) {
        gpsReady = true;

        /* Update badge */
        var badge = document.getElementById('familyMapBadge');
        if (badge) badge.textContent = 'GPS active';

        /* Show "View Full Map" button */
        var btn = document.getElementById('familyViewFullMapBtn');
        if (btn) btn.style.display = 'inline-flex';

        /* Build / update mini-map */
        if (!miniMap) {
            buildMiniMap(lat, lng);
        } else {
            updateSelfMarker(lat, lng);
        }
    }

    /* ── Build the Leaflet mini-map ── */
    function buildMiniMap(lat, lng) {
        var placeholder = document.getElementById('familyMapPlaceholder');
        if (placeholder) placeholder.style.display = 'none';

        miniMap = L.map('familyMiniMapEl', {
            zoomControl: false,
            attributionControl: false,
            dragging: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom: false,
            keyboard: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(miniMap);

        /* Self (family member) marker */
        selfMarker = L.marker([lat, lng], { icon: selfIcon() })
            .addTo(miniMap)
            .bindPopup('<b>📍 You</b><br>Your current location');

        /* PWD markers */
        var allLatLngs = [[lat, lng]];
        var pwds = getPwdList();

        pwds.forEach(function (p, idx) {
            if (!p.latitude || !p.longitude || (p.latitude === 0 && p.longitude === 0)) return;
            var colour = p.color || COLOURS[idx % COLOURS.length];
            var mLat = parseFloat(p.latitude);
            var mLng = parseFloat(p.longitude);
            allLatLngs.push([mLat, mLng]);

            var mk = L.marker([mLat, mLng], { icon: pwdPinIcon(colour) })
                .addTo(miniMap)
                .bindTooltip(p.name, { permanent: true, direction: 'top', className: 'fm-mini-tooltip' });

            pwdMiniMarkers.push(mk);
        });

        /* Fit bounds to show everyone */
        if (allLatLngs.length > 1) {
            miniMap.fitBounds(L.latLngBounds(allLatLngs), { padding: [30, 30] });
        } else {
            miniMap.setView([lat, lng], 15);
        }
    }

    /* ── Update self marker on subsequent GPS fixes ── */
    function updateSelfMarker(lat, lng) {
        if (!selfMarker) return;
        selfMarker.setLatLng([lat, lng]);
    }

    /* ── Icon helpers ── */
    function selfIcon() {
        return L.divIcon({
            className: '',
            html: [
                '<div style="width:36px;height:36px;',
                'background:linear-gradient(135deg,#1565C0,#1976D2);',
                'border-radius:50%;border:3px solid #fff;',
                'box-shadow:0 2px 8px rgba(0,0,0,.3);',
                'display:flex;align-items:center;justify-content:center;',
                'color:#fff;font-size:16px;">',
                '👤</div>'
            ].join(''),
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -20]
        });
    }

    function pwdPinIcon(colour) {
        return L.divIcon({
            className: '',
            html: [
                '<div style="width:38px;height:46px;position:relative;">',
                '  <div style="width:38px;height:38px;background:' + colour + ';',
                '    border-radius:50% 50% 50% 0;transform:rotate(-45deg);',
                '    border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.25);"></div>',
                '  <div style="position:absolute;top:6px;left:9px;',
                '    color:#fff;font-size:16px;line-height:1;">♿</div>',
                '</div>'
            ].join(''),
            iconSize: [38, 46],
            iconAnchor: [19, 46],
            popupAnchor: [0, -48]
        });
    }

    /* ── Build the list of PWD members for the modal ── */
    function getPwdList() {
        if (typeof pwdMembersData === 'undefined' || !Array.isArray(pwdMembersData)) return [];
        return pwdMembersData;
    }

    /* ── Open the full-screen modal ── */
    window.openFamilyFullMap = function () {
        var lat = (typeof currentLat !== 'undefined') ? currentLat : null;
        var lng = (typeof currentLng !== 'undefined') ? currentLng : null;

        if (!lat || !lng) {
            alert('GPS location not yet acquired. Please wait a moment and try again.');
            return;
        }

        var pwds     = getPwdList();
        var members  = [];
        var fallbackOffsets = [
            { dLat:  0.003, dLng:  0.005 },
            { dLat: -0.004, dLng:  0.002 },
            { dLat:  0.001, dLng: -0.006 },
            { dLat: -0.002, dLng: -0.003 }
        ];

        pwds.forEach(function (p, idx) {
            var off  = fallbackOffsets[idx % fallbackOffsets.length];
            var mLat = (p.latitude  && parseFloat(p.latitude)  !== 0) ? parseFloat(p.latitude)  : lat + off.dLat;
            var mLng = (p.longitude && parseFloat(p.longitude) !== 0) ? parseFloat(p.longitude) : lng + off.dLng;
            members.push({
                name:  p.name  || ('PWD ' + (idx + 1)),
                lat:   mLat,
                lng:   mLng,
                color: p.color || COLOURS[idx % COLOURS.length]
            });
        });

        /* fullmap-modal.js treats the first arg as "home" (the family member)
           and the members array as the destinations (PWDs). The modal's
           "Guide Me There" button routes FROM home TO the selected member,
           and "Guide Them to Me" does the reverse — exactly what we need. */
        FullMapModal.open(lat, lng, members);

        /* Patch the modal header title for context */
        var h3 = document.querySelector('#fullMapHeader h3');
        if (h3) h3.textContent = 'PWD Locations Map';

        /* Patch the action bar labels */
        var btn1 = document.getElementById('fmBtnGuideMe');
        var btn2 = document.getElementById('fmBtnGuideThem');
        if (btn1) btn1.innerHTML = '<i class="ri-navigation-line"></i> Guide Me to Them';
        if (btn2) btn2.innerHTML = '<i class="ri-walk-line"></i> Guide Them to Me';
    };

}());
