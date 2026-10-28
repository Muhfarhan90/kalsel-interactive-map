import L from "leaflet";
import "leaflet/dist/leaflet.css";

import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

const DEFAULT_CENTER = [-3.1, 115.4];

const DEFAULT_ZOOM = 8;
const GEOJSON_URL = "/geojson/regency_kalsel.geojson";

function normalizeRegion(name) {
    const normalized = String(name ?? "")
        .normalize("NFKD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim()
        .replace(/[-_]+/g, " ")
        .replace(/^(kabupaten|kab\.?|kota)\s+/, "")
        .replace(/[^a-z0-9]/g, "");

    return normalized === "baru" ? "kotabaru" : normalized;
}

function parseGoogleMapsUrl(value) {
    const text = value.trim();

    const coordinatePattern = /^(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)$/;

    function makePoint(lat, lng, source) {
        const latitude = Number(lat);
        const longitude = Number(lng);

        if (
            !Number.isFinite(latitude) ||
            !Number.isFinite(longitude) ||
            Math.abs(latitude) > 90 ||
            Math.abs(longitude) > 180
        ) {
            throw new Error("Koordinat tidak valid.");
        }

        return {
            point: L.latLng(latitude, longitude),
            source,
        };
    }

    const direct = text.match(coordinatePattern);

    if (direct) {
        return makePoint(direct[1], direct[2], "manual");
    }

    let url;

    try {
        url = new URL(text);
    } catch {
        throw new Error("Masukkan URL Google Maps yang valid.");
    }

    const hostname = url.hostname.toLowerCase();

    if (["maps.app.goo.gl", "goo.gl"].includes(hostname)) {
        throw new Error(
            "Buka link pendek terlebih dahulu dan salin URL lengkap Google Maps.",
        );
    }

    const validHost = ["google.com", "google.co.id"].some(
        (domain) => hostname === domain || hostname.endsWith(`.${domain}`),
    );

    if (
        url.protocol !== "https:" ||
        !validHost ||
        !url.pathname.startsWith("/maps")
    ) {
        throw new Error("URL harus berasal dari Google Maps.");
    }

    let decoded = url.href;

    try {
        decoded = decodeURIComponent(decoded);
    } catch {
        // Gunakan URL tanpa decode.
    }

    // Prioritas: koordinat tempat yang dipilih.
    const place = decoded.match(/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/i);

    if (place) {
        return makePoint(place[1], place[2], "place");
    }

    // Alternatif: URL yang menyimpan koordinat sebagai query.
    for (const key of ["query", "q", "ll", "center"]) {
        const match = url.searchParams.get(key)?.match(coordinatePattern);

        if (match) {
            return makePoint(match[1], match[2], key);
        }
    }

    // Fallback: titik pusat tampilan peta.
    const viewport = decoded.match(
        /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)(?:[,/?]|$)/,
    );

    if (viewport) {
        return makePoint(viewport[1], viewport[2], "viewport");
    }

    throw new Error(
        "URL tidak memuat koordinat. Pilih tempat lalu salin URL lengkap.",
    );
}

function initializeCoordinatePickers() {
    document.querySelectorAll("[data-geo-picker]").forEach((wrapper) => {
        const mapElement = wrapper.querySelector("[data-geo-map]");

        const latitudeInput = wrapper.querySelector('[name="latitude"]');
        const longitudeInput = wrapper.querySelector('[name="longitude"]');
        const regencySelect = wrapper.querySelector('[name="regency_id"]');

        if (!mapElement || !latitudeInput || !longitudeInput) return;

        function parseCoordinates(lat, lng) {
            if (
                String(lat ?? "").trim() === "" ||
                String(lng ?? "").trim() === ""
            ) {
                return null;
            }

            const latitude = Number(lat);
            const longitude = Number(lng);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return null;
            }

            if (Math.abs(latitude) > 90 || Math.abs(longitude) > 180) {
                return null;
            }

            return [latitude, longitude];
        }

        function selectedRegencyCoordinates() {
            const option = regencySelect?.selectedOptions[0];

            return parseCoordinates(
                option?.dataset.latitude,
                option?.dataset.longitude,
            );
        }

        const initialPoint = parseCoordinates(
            latitudeInput.value,
            longitudeInput.value,
        );

        const center =
            initialPoint ?? selectedRegencyCoordinates() ?? DEFAULT_CENTER;

        const map = L.map(mapElement, {
            center: center,
            zoom: initialPoint
                ? 15
                : selectedRegencyCoordinates()
                  ? 11
                  : DEFAULT_ZOOM,
            zoomSnap: 0.25,
            zoomDelta: 0.5,
            maxBoundsViscosity: 1.0,
            bounceAtZoomLimits: false,
            zoomControl: true,
            scrollWheelZoom: true,
            touchZoom: true,
            attributionControl: false,
        });

        L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        let regionLayer = null;
        let provinceBounds = null;

        const mapStatus = wrapper.querySelector("[data-geo-map-status]");

        function showMapStatus(message) {
            if (!mapStatus) return;

            mapStatus.textContent = message;
            mapStatus.classList.toggle("hidden", !message);
        }

        function updateMapDragging() {
            const isMinZoom = map.getZoom() <= map.getMinZoom() + 0.001;

            if (isMinZoom) {
                map.dragging.disable();
            } else {
                map.dragging.enable();
            }
        }

        map.on("zoomend", updateMapDragging);

        // Jalankan saat pertama kali peta ditampilkan.
        updateMapDragging();

        // Hitung ulang apabila ukuran peta berubah.
        map.on("resize", () => {
            focusSelectedRegion();
        });
        // Fokus ke wilayah yang dipilih, jika ada.

        function focusSelectedRegion() {
            if (!regionLayer || !provinceBounds?.isValid()) return;

            const selectedOption = regencySelect?.selectedOptions?.[0];

            // Gunakan slug dari dropdown Laravel.
            const selectedSlug = regencySelect?.value
                ? normalizeRegion(selectedOption?.dataset.slug ?? "")
                : "";

            let selectedBounds = null;

            regionLayer.eachLayer((layer) => {
                const feature = layer.feature;
                const properties = feature?.properties ?? {};

                const shapeName = normalizeRegion(properties.shapeName ?? "");

                const isSelected = selectedSlug && shapeName === selectedSlug;

                if (isSelected) {
                    // Tampilkan hanya garis batas wilayah terpilih.
                    layer.setStyle({
                        color: "#111",
                        weight: Math.max(
                            1,
                            Number(properties["stroke-width"]) || 1,
                        ),
                        opacity: 1,
                        fill: false,
                    });

                    // Ambil seluruh batas polygon wilayah terpilih.
                    const bounds = layer.getBounds();

                    if (bounds.isValid()) {
                        selectedBounds = selectedBounds
                            ? selectedBounds.extend(bounds)
                            : bounds;
                    }
                } else {
                    // Sembunyikan garis batas wilayah lainnya.
                    layer.setStyle({
                        color: "transparent",
                        weight: 0,
                        opacity: 0,
                        fill: false,
                    });
                }
            });

            // Jika wilayah dipilih tetapi tidak ada polygon cocok.
            if (regencySelect?.value && !selectedBounds) {
                console.warn(
                    "Polygon tidak ditemukan:",
                    selectedOption?.dataset.slug,
                );

                showMapStatus(
                    "Polygon kabupaten/kota tidak ditemukan di GeoJSON.",
                );

                return;
            }

            showMapStatus("");

            // Jika belum memilih wilayah, tampilkan area provinsi.
            const bounds = selectedBounds ?? provinceBounds;

            // Hapus batas dari wilayah sebelumnya.
            map.stop();
            map.setMaxBounds(null);
            map.setMinZoom(0);

            // Zoom otomatis mengikuti seluruh polygon terpilih.
            map.fitBounds(bounds, {
                padding: [20, 20],
                animate: false,
            });

            // Simpan zoom minimum berdasarkan polygon.
            const resetZoom = map.getZoom();

            map.setMinZoom(resetZoom);

            // Batasi pergerakan sesuai tampilan awal.
            map.setMaxBounds(map.getBounds());

            updateMapDragging();

            console.log("Wilayah aktif:", {
                nama: selectedOption?.textContent?.trim(),
                slug: selectedSlug,
                polygonDitemukan: Boolean(selectedBounds),
                zoom: resetZoom,
            });
        }

        // Memuat GeoJSON kabupaten/kota Kalimantan Selatan.
        fetch(wrapper.dataset.geojsonUrl || GEOJSON_URL)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`GeoJSON HTTP ${response.status}`);
                }

                return response.json();
            })
            .then((geojson) => {
                regionLayer = L.geoJSON(geojson, {
                    interactive: false,

                    style: (feature) => ({
                        color: "#111",
                        weight: 1.5,
                        fillColor: feature.properties?.fill || "#d6b98b",
                        fillOpacity: 0.05,
                    }),
                }).addTo(map);

                provinceBounds = regionLayer.getBounds();

                if (!provinceBounds.isValid()) {
                    throw new Error("Polygon GeoJSON tidak valid.");
                }

                // Ketika edit lokasi, jangan mengubah zoom marker tersimpan.
                focusSelectedRegion();

                updateMapDragging();
            })
            .catch((error) => {
                console.error("Gagal memuat GeoJSON:", error);
                showMapStatus("GeoJSON belum dapat dimuat.");
            });

        let marker = null;

        function setMarker(latlng) {
            const point = [latlng.lat, latlng.lng];

            latitudeInput.value = latlng.lat.toFixed(7);
            longitudeInput.value = latlng.lng.toFixed(7);

            if (!marker) {
                marker = L.marker(point, {
                    draggable: true,
                }).addTo(map);

                marker.on("dragend", () => {
                    setMarker(marker.getLatLng());
                });
            } else {
                marker.setLatLng(point);
            }
        }

        if (initialPoint) {
            setMarker({
                lat: initialPoint[0],
                lng: initialPoint[1],
            });
        }

        map.on("click", (event) => {
            setMarker(event.latlng);
        });

        const searchInput = wrapper.querySelector("[data-geo-search]");
        const searchButton = wrapper.querySelector("[data-geo-search-button]");
        const searchResults = wrapper.querySelector(
            "[data-geo-search-results]",
        );
        const searchMessage = wrapper.querySelector(
            "[data-geo-search-message]",
        );

        if (searchInput && searchButton && searchResults && searchMessage) {
            let results = [];
            let searching = false;
            let lastRequest = 0;

            const cache = new Map();

            function showMessage(message) {
                searchMessage.textContent = message;
                searchMessage.classList.toggle("hidden", !message);
            }

            function isKalsel(result) {
                const address = result.address ?? {};

                const code = String(
                    address["ISO3166-2-lvl4"] ?? "",
                ).toUpperCase();

                if (code) {
                    return code === "ID-KS";
                }

                const province = String(
                    address.state ?? address.province ?? "",
                ).toLowerCase();

                const name = String(result.display_name ?? "").toLowerCase();

                return (
                    province.includes("kalimantan selatan") ||
                    province.includes("south kalimantan") ||
                    name.includes("kalimantan selatan") ||
                    name.includes("south kalimantan")
                );
            }

            // Menampilkan hasil ke select bawaan browser.
            function renderResults(data) {
                results = data.slice(0, 50);

                searchResults.replaceChildren();
                searchResults.value = "";

                if (!results.length) {
                    searchResults.classList.add("hidden");
                    showMessage(
                        "Lokasi tidak ditemukan. Coba gunakan nama tempat dan kabupaten/kota.",
                    );
                    return;
                }

                const placeholder = new Option(
                    "-- Pilih lokasi dari hasil pencarian --",
                    "",
                );

                placeholder.disabled = true;
                placeholder.selected = true;

                searchResults.add(placeholder);

                results.forEach((result, index) => {
                    const option = new Option(
                        result.display_name,
                        String(index),
                    );

                    searchResults.add(option);
                });

                searchResults.classList.remove("hidden");

                // Pastikan belum ada hasil yang dipilih.
                searchResults.selectedIndex = 0;

                showMessage(
                    `${results.length} lokasi ditemukan. Pilih salah satu.`,
                );
            }

            // Event ketika lokasi dipilih.

            searchResults.addEventListener("change", (event) => {
                console.log("Dropdown berubah:", event.target.value);

                const index = event.target.value;

                if (index === "") return;

                const result = results[Number(index)];

                if (!result) {
                    console.error("Data lokasi tidak ditemukan:", index);
                    return;
                }

                const latitude = Number(result.lat);
                const longitude = Number(result.lon);

                if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                    console.error("Koordinat tidak valid:", result);
                    return;
                }

                const latlng = L.latLng(latitude, longitude);

                console.log("Lokasi dipilih:", result.display_name);
                console.log("Koordinat:", latitude, longitude);

                // Hentikan animasi map sebelumnya.
                map.stop();

                // Perbarui marker dan input latitude/longitude.
                setMarker(latlng);

                // Langsung pindahkan peta tanpa animasi.
                map.setView(latlng, 15, {
                    animate: false,
                });

                searchInput.value = result.display_name;
                searchResults.classList.add("hidden");
                showMessage("");

                console.log("Posisi peta sekarang:", map.getCenter());
            });

            async function fetchLocations(query) {
                // Jeda antar-request dalam halaman.
                const elapsed = Date.now() - lastRequest;
                const delay = Math.max(0, 1100 - elapsed);

                if (delay > 0) {
                    await new Promise((resolve) => setTimeout(resolve, delay));
                }

                const params = new URLSearchParams({
                    q: query,
                    format: "jsonv2",
                    addressdetails: "1",
                    limit: "20",
                    countrycodes: "id",
                    "accept-language": "id",
                });

                if (provinceBounds?.isValid()) {
                    params.set(
                        "viewbox",
                        [
                            provinceBounds.getWest(),
                            provinceBounds.getNorth(),
                            provinceBounds.getEast(),
                            provinceBounds.getSouth(),
                        ].join(","),
                    );

                    params.set("bounded", "0");
                }

                lastRequest = Date.now();

                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?${params.toString()}`,
                );

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            }

            async function searchLocation() {
                const query = searchInput.value.trim();

                if (query.length < 3) {
                    showMessage("Masukkan minimal 3 karakter.");
                    return;
                }

                if (searching) return;

                const cacheKey = query.toLowerCase();

                if (cache.has(cacheKey)) {
                    renderResults(cache.get(cacheKey));
                    return;
                }

                searching = true;
                searchButton.disabled = true;
                searchResults.classList.add("hidden");
                showMessage("Mencari lokasi...");

                try {
                    // Pencarian awal.
                    const data = await fetchLocations(query);

                    let filtered = data.filter(isKalsel);

                    // Jika belum ditemukan, coba dengan konteks wilayah.
                    if (!filtered.length) {
                        const selectedName =
                            regencySelect?.selectedOptions?.[0]?.textContent?.trim();

                        const area =
                            regencySelect?.value && selectedName
                                ? `${selectedName}, Kalimantan Selatan, Indonesia`
                                : "Kalimantan Selatan, Indonesia";

                        const secondData = await fetchLocations(
                            `${query}, ${area}`,
                        );

                        filtered = secondData.filter(isKalsel);
                    }

                    if (filtered.length) {
                        cache.set(cacheKey, filtered);
                    }

                    renderResults(filtered);
                } catch (error) {
                    console.error("Gagal mencari lokasi:", error);

                    showMessage(
                        error.message === "HTTP 429"
                            ? "Batas permintaan pencarian tercapai. Coba lagi nanti."
                            : "Gagal mencari lokasi. Periksa koneksi internet.",
                    );
                } finally {
                    searching = false;
                    searchButton.disabled = false;
                }
            }

            searchButton.addEventListener("click", searchLocation);

            searchInput.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    searchLocation();
                }
            });
        }

        [latitudeInput, longitudeInput].forEach((input) => {
            input.addEventListener("change", () => {
                const point = parseCoordinates(
                    latitudeInput.value,
                    longitudeInput.value,
                );

                if (!point) {
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                    return;
                }

                setMarker({
                    lat: point[0],
                    lng: point[1],
                });

                map.setView(point, 15);
            });
        });

        regencySelect?.addEventListener("change", () => {
            latitudeInput.value = "";
            longitudeInput.value = "";

            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }

            focusSelectedRegion();

            // Bersihkan pencarian sebelumnya.
            const searchInput = wrapper.querySelector("[data-geo-search]");
            const searchResults = wrapper.querySelector(
                "[data-geo-search-results]",
            );
            const searchMessage = wrapper.querySelector(
                "[data-geo-search-message]",
            );

            if (searchInput) searchInput.value = "";

            if (searchResults) {
                searchResults.replaceChildren();
                searchResults.classList.add("hidden");
            }

            if (searchMessage) {
                searchMessage.textContent = "";
                searchMessage.classList.add("hidden");
            }
        });

        // Pencarian Google Maps sebagai fitur tambahan.
        const googleDialog = wrapper.querySelector("[data-google-dialog]");
        const googleOpen = wrapper.querySelector("[data-google-open]");

        if (googleDialog && googleOpen) {
            const googleQuery = googleDialog.querySelector(
                "[data-google-query]",
            );
            const googleUrl = googleDialog.querySelector("[data-google-url]");
            const googleLaunch = googleDialog.querySelector(
                "[data-google-launch]",
            );
            const googleSync = googleDialog.querySelector("[data-google-sync]");
            const googleFeedback = googleDialog.querySelector(
                "[data-google-feedback]",
            );
            const leafletSearch = wrapper.querySelector("[data-geo-search]");

            function showGoogleError(message = "") {
                googleFeedback.textContent = message;
                googleFeedback.hidden = !message;
            }

            // Membuka dialog tanpa mengganggu pencarian Leaflet.
            googleOpen.addEventListener("click", () => {
                showGoogleError();

                const locationName = wrapper
                    .closest("form")
                    ?.querySelector('[name="name"]')
                    ?.value.trim();

                googleQuery.value =
                    leafletSearch?.value.trim() || locationName || "";

                googleDialog.showModal();
                googleQuery.focus();
            });

            googleDialog
                .querySelectorAll("[data-google-close]")
                .forEach((button) => {
                    button.addEventListener("click", () =>
                        googleDialog.close(),
                    );
                });

            // Buka Google Maps di tab browser baru.
            function openGoogleMaps() {
                const query = googleQuery.value.trim();

                if (!query) {
                    showGoogleError("Masukkan nama tempat terlebih dahulu.");
                    googleQuery.focus();
                    return;
                }

                showGoogleError();

                const regencyName = regencySelect?.value
                    ? regencySelect.selectedOptions[0]?.textContent.trim()
                    : "";

                const location = [
                    query,
                    regencyName,
                    "Kalimantan Selatan",
                    "Indonesia",
                ]
                    .filter(Boolean)
                    .join(", ");

                const url =
                    "https://www.google.com/maps/search/?api=1&query=" +
                    encodeURIComponent(location);

                const popup = window.open(
                    url,
                    "kalselGoogleMaps",
                    "popup=yes,width=1150,height=760,resizable=yes,scrollbars=yes",
                );

                if (!popup) {
                    showGoogleError(
                        "Popup diblokir browser. Izinkan pop-up untuk situs ini.",
                    );
                    return;
                }

                try {
                    popup.opener = null;
                    popup.focus();
                } catch {
                    // Browser dapat membatasi akses jendela lintas domain.
                }
            }

            googleLaunch.addEventListener("click", openGoogleMaps);

            googleQuery.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    openGoogleMaps();
                }
            });

            // Sinkronisasi koordinat dari URL Google Maps.
            function syncGoogleCoordinates() {
                try {
                    const { point, source } = parseGoogleMapsUrl(
                        googleUrl.value,
                    );

                    map.stop();

                    // Menggunakan fungsi marker Leaflet yang sudah ada.
                    setMarker(point);

                    map.setView(point, 16, {
                        animate: false,
                    });

                    showGoogleError();
                    googleDialog.close();

                    showMapStatus(
                        source === "viewport"
                            ? "Koordinat berasal dari pusat tampilan Google Maps. Periksa kembali marker."
                            : "Koordinat berhasil disinkronkan dari Google Maps.",
                    );

                    requestAnimationFrame(() => map.invalidateSize());
                } catch (error) {
                    showGoogleError(error.message);
                }
            }

            googleSync.addEventListener("click", syncGoogleCoordinates);

            googleUrl.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    syncGoogleCoordinates();
                }
            });
        }
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeCoordinatePickers);
} else {
    initializeCoordinatePickers();
}
