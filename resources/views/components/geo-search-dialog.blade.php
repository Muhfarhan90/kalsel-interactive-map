<button type="button" data-google-open
    class="h-11 shrink-0 rounded-lg border border-[#da251d] bg-white px-3 text-sm font-semibold text-[#da251d] hover:bg-red-50 dark:bg-gray-900 dark:hover:bg-gray-800">
    <i class="fas fa-map-location-dot mr-1"></i>
    Google Maps
</button>

<dialog data-google-dialog aria-label="Pencarian lokasi Google Maps"
    class="fixed inset-0 m-auto w-[min(92vw,560px)] max-h-[90dvh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-0 text-gray-800 shadow-2xl backdrop:bg-black/60 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">

    <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-700">
        <h3 class="text-lg font-bold">
            Cari Lokasi di Google Maps
        </h3>

        <button type="button" data-google-close aria-label="Tutup" class="rounded-lg px-2 text-2xl text-gray-500">
            &times;
        </button>
    </div>

    <div class="space-y-4 p-5">
        <div>
            <label class="mb-2 block text-sm font-medium">
                Nama Tempat
            </label>

            <input type="search" data-google-query autocomplete="off" placeholder="Contoh: Pantai Takisung"
                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800">

            <button type="button" data-google-launch
                class="mt-3 w-full rounded-lg bg-[#da251d] px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">

                <i class="fas fa-up-right-from-square mr-2"></i>
                Buka Popup Google Maps
            </button>
        </div>

        <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <p class="mb-2 font-semibold">Cara sinkronisasi:</p>

            <ol class="list-inside list-decimal space-y-2">
                <li>Klik Buka Popup Google Maps.</li>
                <li>Cari dan pilih tempat yang sesuai.</li>
                <li>Salin URL lengkap tempat dari Google Maps.</li>
                <li>Kembali ke form, tempel URL Google Maps, dan Klik Sinkronkan Koordinat.</li>
            </ol>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">
                URL Google Maps atau Koordinat
            </label>

            <input type="text" data-google-url autocomplete="off" placeholder="https://www.google.com/maps/place/..."
                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800">

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Gunakan URL lengkap. Link pendek maps.app.goo.gl
                perlu dibuka terlebih dahulu.
            </p>
        </div>

        <p data-google-feedback hidden role="alert"
            class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
        </p>
    </div>

    <div class="flex justify-end gap-2 border-t border-gray-200 p-5 dark:border-gray-700">
        <button type="button" data-google-close
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium dark:border-gray-600">
            Batal
        </button>

        <button type="button" data-google-sync
            class="rounded-lg bg-[#da251d] px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
            Sinkronkan Koordinat
        </button>
    </div>
</dialog>
