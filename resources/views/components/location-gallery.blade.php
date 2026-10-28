<style>
    #detailMedia {
        border: 1px solid #e4e7ec;
        background: white;
        background-clip: padding-box;
    }

    .location-gallery .plyr {
        height: 100%;
        width: 100%;
    }

    .location-gallery {
        --plyr-color-main: var(--page-header-background, #da251d);
    }

    .location-gallery .location-gallery-thumbnails img,
    .location-gallery .location-gallery-thumbnails video {
        position: static;
        inset: auto;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script>
    function renderMediaGallery(location) {
        const container = document.getElementById('detailMedia');

        const medias = Array.isArray(location.medias) && location.medias.length ?
            location.medias :
            location.media ? [location.media] : [];

        detailPlayer?.destroy?.();
        detailPlayer = null;
        container.replaceChildren();

        if (!medias.length) {
            container.textContent = 'Media belum tersedia';
            return;
        }

        const gallery = document.createElement('div');
        gallery.className =
            'location-gallery flex h-full w-full min-h-0 flex-col overflow-hidden bg-white';
        const stage = document.createElement('div');
        stage.className = 'relative min-h-0 w-full flex-1 bg-black';

        const thumbnails = document.createElement('div');
        thumbnails.className =
            'location-gallery-thumbnails flex shrink-0 gap-2 overflow-x-auto bg-white p-2';
        gallery.append(stage);

        if (medias.length > 1) {
            gallery.append(thumbnails);
        }

        container.append(gallery);

        let activeIndex = 0;

        function showMedia(index) {
            activeIndex = (index + medias.length) % medias.length;
            const url = medias[activeIndex];

            detailPlayer?.pause?.();
            detailPlayer?.destroy?.();
            detailPlayer = null;

            stage.replaceChildren();

            if (/\.mp4(?:\?.*)?$/i.test(url)) {
                const video = document.createElement('video');
                video.src = url;
                video.playsInline = true;
                video.preload = 'metadata';
                video.className = 'h-full w-full object-contain';
                video.setAttribute('controlsList', 'nodownload');
                stage.append(video);

                if (typeof window.Plyr === 'function') {
                    video.controls = false;

                    detailPlayer = new window.Plyr(video, {
                        controls: [
                            'play-large',
                            'restart',
                            'rewind',
                            'play',
                            'fast-forward',
                            'progress',
                            'current-time',
                            'duration',
                            'mute',
                            'volume',
                            'settings',
                            'pip',
                            'fullscreen'
                        ],
                        settings: ['speed'],
                        seekTime: 10,
                        fullscreen: {
                            enabled: true,
                            fallback: true,
                            iosNative: false
                        }
                    });

                    detailPlayer.on('ready', () => {
                        console.log('Plyr berhasil diinisialisasi');
                    });
                } else {
                    video.controls = true;
                    detailPlayer = video;

                    console.error('Plyr tidak tersedia');
                }
            } else {
                const image = document.createElement('img');
                image.src = url;
                image.alt = `${location.name} - Foto ${activeIndex + 1}`;
                image.draggable = false;
                image.className =
                    'absolute inset-0 h-full w-full object-contain';
                stage.append(image);
            }

            if (medias.length > 1) {
                [-1, 1].forEach(direction => {
                    const button = document.createElement('button');

                    button.type = 'button';
                    button.innerHTML = direction < 0 ?
                        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>' :
                        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';
                    button.setAttribute(
                        'aria-label',
                        direction < 0 ? 'Sebelumnya' : 'Berikutnya'
                    );

                    button.className =
                        'absolute top-1/2 z-50 flex size-11 shrink-0 -translate-y-1/2 items-center justify-center rounded-full bg-black/60 p-0 text-white shadow-md transition hover:bg-[var(--page-header-background)] ' +
                        (direction < 0 ? 'left-3' : 'right-3');

                    button.onclick = () => showMedia(activeIndex + direction);
                    stage.append(button);
                });

                thumbnails.replaceChildren();

                medias.forEach((mediaUrl, i) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.setAttribute('aria-label', `Media ${i + 1}`);

                    button.className =
                        'h-14 w-20 shrink-0 overflow-hidden rounded border-2 bg-white';

                    button.style.borderColor =
                        i === activeIndex ?
                        'var(--page-header-background, #da251d)' :
                        '#e4e7ec';

                    if (/\.mp4(?:\?.*)?$/i.test(mediaUrl)) {
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-video text-xl text-gray-600';

                        button.classList.add(
                            'flex',
                            'items-center',
                            'justify-center',
                            'bg-gray-100'
                        );

                        button.append(icon);
                    } else {
                        const img = document.createElement('img');
                        img.src = mediaUrl;
                        img.alt = `Thumbnail ${i + 1}`;
                        img.draggable = false;
                        img.className = 'h-full w-full object-cover';

                        button.append(img);
                    }

                    button.onclick = () => showMedia(i);
                    thumbnails.append(button);
                });
            }

            const source = location.source_media || 'Sumber belum tersedia';
            document.getElementById('detailSource').textContent = source;
            document.getElementById('fullscreenDetailSource').textContent = source;
        }

        showMedia(0);
    }
</script>
