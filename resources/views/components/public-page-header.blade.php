@props(['settings', 'backgroundColor' => '#183832'])

<header
    class="flex h-16 items-center justify-between border-b border-black/10 px-4 py-1 text-[26px] font-bold text-white uppercase max-[520px]:px-2 max-[520px]:text-sm"
    style="background: linear-gradient(110deg, rgb(0 0 0 / 35%), transparent), {{ $backgroundColor }}">
    <div class="flex min-w-0 flex-1 items-center gap-2.5">
        @if ($settings->header_logo)
            <img src="{{ asset($settings->header_logo) }}" alt="Logo {{ $settings->header_logo_text ?: $settings->header_title }}" class="h-10 max-w-32 object-contain">
        @endif
        @if ($settings->header_logo_text)
            <span class="text-2xl font-bold max-[520px]:text-[9px] max-[520px]:tracking-normal">{{ $settings->header_logo_text }}</span>
        @endif
    </div>
    <div class="min-w-0 flex-1 truncate text-center tracking-wide max-[520px]:hidden">{{ $settings->header_title }}</div>

    <div class="flex min-w-0 flex-1 flex-col items-end justify-center leading-tight">
        <time class="tabular-nums" id="clock" aria-label="Waktu sekarang">--:--</time>
        <span class="text-lg font-medium normal-case text-white/85 max-[520px]:text-[10px]" id="headerDate" aria-label="Tanggal hari ini">--</span>
    </div>
</header>

<script>
    const clock = document.getElementById('clock');
    const headerDate = document.getElementById('headerDate');
    const updateClock = () => {
        const now = new Date();
        clock.textContent = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false
        }).format(now);
        headerDate.textContent = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric'
        }).format(now);
    };
    updateClock();
    setInterval(updateClock, 30000);

    document.addEventListener('click', (event) => {
        const link = event.target instanceof Element ? event.target.closest('a[data-public-page-link]') : null;
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        event.preventDefault();
        if (document.body.classList.contains('page-leaving')) return;

        document.body.classList.add('page-leaving');
        setTimeout(() => window.location.assign(link.href), 180);
    });

    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;

        document.body.classList.remove('page-leaving', 'public-page');
        void document.body.offsetWidth;
        document.body.classList.add('public-page');
    });
</script>
