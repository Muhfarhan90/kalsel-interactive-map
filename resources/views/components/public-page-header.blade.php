@props(['settings', 'backgroundColor' => '#183832'])

<header
    class="flex h-16 items-center justify-between border-b border-black/10 px-4 py-1 text-[26px] font-bold text-white uppercase max-[520px]:px-2 max-[520px]:text-sm"
    style="background-color: {{ $backgroundColor }}">
    <div class="flex min-w-0 flex-1 items-center gap-2.5">
        @if ($settings->header_logo)
            <img src="{{ asset($settings->header_logo) }}" alt="Logo {{ $settings->header_logo_text ?: $settings->header_title }}" class="h-10 max-w-32 object-contain">
        @endif
        @if ($settings->header_logo_text)
            <span class="text-2xl font-bold max-[520px]:text-[9px] max-[520px]:tracking-normal">{{ $settings->header_logo_text }}</span>
        @endif
    </div>
    <div class="min-w-0 flex-1 truncate text-center tracking-wide max-[520px]:hidden">{{ $settings->header_title }}</div>
    <time class="min-w-0 flex-1 text-right tabular-nums" id="clock" aria-label="Waktu sekarang">--:--</time>
</header>

<script>
    const clock = document.getElementById('clock');
    const updateClock = () => {
        clock.textContent = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false
        }).format(new Date());
    };
    updateClock();
    setInterval(updateClock, 30000);
</script>
