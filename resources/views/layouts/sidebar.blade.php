<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 start-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 ltr:border-r rtl:border-l border-gray-200 w-[90px] [.sidebar-expanded_&]:min-w-[290px]"
    x-data="{
    }"
    :class="{
        'translate-x-0': $store.sidebar.isMobileOpen,
        'max-xl:-translate-x-full max-xl:rtl:translate-x-full': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex items-center gap-2" :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'justify-center' : 'justify-between'">
        <a href="/" class="flex items-center gap-3">
            <img src="/images/logo/logo_kalsel.svg" alt="Logo Kalsel" class="size-10 shrink-0 object-contain" />
            <span class="hidden text-2xl font-bold text-gray-900 [.sidebar-expanded_&]:block dark:text-white">Kalimantan Selatan</span>
        </a>
    </div>

    <!-- Navigation Menu -->
<div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
    <nav class="mb-6">

        <h2
            class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
            :class="(!$store.sidebar.isExpanded &&
                     !$store.sidebar.isHovered &&
                     !$store.sidebar.isMobileOpen)
                     ? 'lg:justify-center'
                     : 'justify-start'"
        >
            <span
                x-show="$store.sidebar.isExpanded ||
                        $store.sidebar.isHovered ||
                        $store.sidebar.isMobileOpen"
            >
                Menu
            </span>
        </h2>

        <ul class="flex flex-col gap-1">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="menu-item group
                    {{ request()->routeIs('admin.dashboard')
                        ? 'menu-item-active'
                        : 'menu-item-inactive' }}"

                    :class="(!$store.sidebar.isExpanded &&
                            !$store.sidebar.isHovered &&
                            !$store.sidebar.isMobileOpen)
                            ? 'xl:justify-center'
                            : 'xl:justify-start'">

                    <span
                        class="{{ request()->routeIs('admin.dashboard')
                            ? 'menu-item-icon-active'
                            : 'menu-item-icon-inactive' }}">

                        <x-heroicon-o-home class="size-6" />

                    </span>

                    <span
                        x-show="$store.sidebar.isExpanded ||
                                $store.sidebar.isHovered ||
                                $store.sidebar.isMobileOpen"
                        class="menu-item-text"
                    >
                        Dashboard
                    </span>

                </a>
            </li>
            <li>
                <a href="{{ route('admin.map.edit') }}"
                    class="menu-item group {{ request()->routeIs('admin.map.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                    :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'xl:justify-start'">
                    <span class="{{ request()->routeIs('admin.map.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                        <x-heroicon-o-map class="size-6" />
                    </span>
                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Manajemen Peta</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.locations.index') }}"
                    class="menu-item group {{ request()->routeIs('admin.locations.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                    :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'xl:justify-start'">
                    <span class="{{ request()->routeIs('admin.locations.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21C12 21 19 15.5 19 9.5C19 5.36 15.87 2 12 2C8.13 2 5 5.36 5 9.5C5 15.5 12 21 12 21Z" fill="currentColor" opacity="0.35"/>
                            <circle cx="12" cy="9.5" r="2.5" fill="currentColor"/>
                        </svg>
                    </span>
                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Data Wisata</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}"
                    class="menu-item group {{ request()->routeIs('admin.categories.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                    :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'xl:justify-start'">
                    <span class="{{ request()->routeIs('admin.categories.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 5.5C4 4.67 4.67 4 5.5 4H10V10H4V5.5ZM14 4H18.5C19.33 4 20 4.67 20 5.5V10H14V4ZM4 14H10V20H5.5C4.67 20 4 19.33 4 18.5V14ZM14 14H20V18.5C20 19.33 19.33 20 18.5 20H14V14Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Kategori Wisata</span>
                </a>
            </li>
            @if (auth()->user()?->role === 'admin')
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="menu-item group {{ request()->routeIs('admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'xl:justify-start'">
                        <span class="{{ request()->routeIs('admin.users.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                            <x-heroicon-o-users class="size-6" />
                        </span>
                        <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Pengguna</span>
                    </a>
                </li>
            @endif
        </ul>

    </nav>
</div>
</aside>
