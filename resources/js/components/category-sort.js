import Sortable from "sortablejs";

function initCategorySort() {
    const list = document.querySelector("[data-category-sort-list]");
    const form = document.querySelector("[data-category-sort-form]");

    if (!list || !form) return;

    const saveButton = form.querySelector('button[type="submit"]');
    const orderInputs = form.querySelector("[data-order-inputs]");
    const sortable = new Sortable(list, {
        handle: "[data-category-drag-handle]",
        draggable: "tr[data-category-id]",
        dataIdAttr: "data-category-id",
        animation: 150,
        ghostClass: "opacity-40",
        onEnd: updateOrder,
    });
    const originalOrder = sortable.toArray().join(",");

    function updateOrder() {
        const unchanged = sortable.toArray().join(",") === originalOrder;
        form.hidden = unchanged;
        saveButton.disabled = unchanged;
    }

    list.addEventListener("keydown", (event) => {
        const handle = event.target.closest("[data-category-drag-handle]");
        if (!handle || (event.key !== "ArrowUp" && event.key !== "ArrowDown"))
            return;

        event.preventDefault();
        const row = handle.closest("tr[data-category-id]");
        const neighbor =
            event.key === "ArrowUp"
                ? row.previousElementSibling
                : row.nextElementSibling;
        if (!neighbor?.matches("tr[data-category-id]")) return;

        list.insertBefore(
            row,
            event.key === "ArrowUp" ? neighbor : neighbor.nextElementSibling,
        );
        handle.focus();
        updateOrder();
    });

    form.addEventListener("submit", (event) => {
        if (saveButton.disabled) {
            event.preventDefault();
            return;
        }

        orderInputs.replaceChildren(
            ...sortable.toArray().map((id) => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "order[]";
                input.value = id;
                return input;
            }),
        );
    });
}

function initLocationGallerySort() {
    const list = document.querySelector("[data-location-gallery-sort]");
    const toggleButton = document.querySelector(
        "[data-location-gallery-toggle]",
    );

    if (!list || !toggleButton) return;

    const toggleText = toggleButton.querySelector(
        "[data-location-gallery-toggle-text]",
    );

    const status = document.querySelector("[data-location-gallery-status]");

    function showStatus(message, type = "success") {
        if (!status) return;

        status.className =
            "rounded-lg border px-4 py-3 text-sm font-medium shadow-sm " +
            (type === "success"
                ? "border-green-300 bg-green-50 text-green-800"
                : "border-red-300 bg-red-50 text-red-800");

        status.innerHTML = "";

        const icon = document.createElement("i");
        icon.className =
            type === "success"
                ? "fas fa-circle-check mr-2"
                : "fas fa-circle-exclamation mr-2";

        const text = document.createElement("span");
        text.textContent = message;

        status.append(icon, text);
    }

    let sorting = false;
    let saving = false;

    const sortable = new Sortable(list, {
        draggable: "[data-location-gallery-item]",
        animation: 150,
        ghostClass: "opacity-40",
        disabled: true,
        onEnd: updateNumbers,
    });

    function updateNumbers() {
        list.querySelectorAll("[data-location-gallery-item]").forEach(
            (item, index) => {
                const number = item.querySelector(
                    "[data-location-gallery-number]",
                );

                if (number) {
                    number.textContent = index + 1;
                }
            },
        );
    }

    function setSorting(enabled) {
        sorting = enabled;
        sortable.option("disabled", !enabled);

        toggleButton.setAttribute("aria-pressed", String(enabled));
        toggleText.textContent = enabled ? "Simpan Urutan" : "Ubah Urutan";

        list.querySelectorAll("[data-location-gallery-overlay]").forEach(
            (overlay) => {
                overlay.classList.toggle("hidden", !enabled);
            },
        );
    }

    toggleButton.addEventListener("click", async () => {
        if (saving) return;

        // Klik pertama: aktifkan mode pengurutan.
        if (!sorting) {
            status?.classList.add("hidden");
            setSorting(true);
            return;
        }

        // Klik kedua: simpan urutan langsung ke database.
        const order = Array.from(
            list.querySelectorAll(
                '[data-location-gallery-item] input[name="gallery_order[]"]',
            ),
        ).map((input) => input.value);

        const token = toggleButton
            .closest("form")
            .querySelector('input[name="_token"]').value;

        saving = true;
        toggleButton.disabled = true;
        toggleText.textContent = "Menyimpan...";

        try {
            const response = await fetch(
                toggleButton.dataset.locationGallerySaveUrl,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                    body: JSON.stringify({ order }),
                },
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || "Gagal menyimpan urutan.");
            }

            setSorting(false);
            showStatus(result.message || 'Urutan media berhasil disimpan.');
        } catch (error) {
            showStatus(error.message, 'error');            
            toggleText.textContent = "Simpan Urutan";
        } finally {
            saving = false;
            toggleButton.disabled = false;
        }
    });
}

function initSortable() {
    initCategorySort();
    initLocationGallerySort();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSortable);
} else {
    initSortable();
}
