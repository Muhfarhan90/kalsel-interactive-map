<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 start-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 ltr:border-r rtl:border-l border-gray-200 w-[90px] [.sidebar-expanded_&]:min-w-[290px]"
    x-data="{}"
    :class="{
        'translate-x-0': $store.sidebar.isMobileOpen,
        'max-xl:-translate-x-full max-xl:rtl:translate-x-full': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex items-center gap-2"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'justify-center' :
        'justify-between'">
        <a href="/" class="flex items-center gap-3">
            <img src="/images/logo/logo_kalsel.svg" alt="Logo Kalsel" class="size-10 shrink-0 object-contain" />
            <span class="hidden text-2xl font-bold text-gray-900 [.sidebar-expanded_&]:block dark:text-white">Kalimantan
                Selatan</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">

            <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                :class="(!$store.sidebar.isExpanded &&
                    !$store.sidebar.isHovered &&
                    !$store.sidebar.isMobileOpen) ?
                'lg:justify-center' :
                'justify-start'">
                <span
                    x-show="$store.sidebar.isExpanded ||
                        $store.sidebar.isHovered ||
                        $store.sidebar.isMobileOpen">
                    Menu
                </span>
            </h2>

            <ul class="flex flex-col gap-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-item group
                    {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded &&
                            !$store.sidebar.isHovered &&
                            !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' :
                        'xl:justify-start'">

                        <span
                            class="{{ request()->routeIs('admin.dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">

                            <x-heroicon-o-home class="size-6" />

                        </span>

                        <span
                            x-show="$store.sidebar.isExpanded ||
                                $store.sidebar.isHovered ||
                                $store.sidebar.isMobileOpen"
                            class="menu-item-text">
                            Dashboard
                        </span>

                    </a>
                </li>
            </ul>

            <h2 class="mb-2 mt-5 flex text-xs uppercase leading-[20px] text-gray-400"
                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                'lg:justify-center' : 'justify-start'">
                <span
                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Pengaturan</span>
            </h2>
            <ul class="flex flex-col gap-1">
                {{-- <li>
                    <a href="{{ route('admin.headers.edit') }}"
                        class="menu-item group {{ request()->routeIs('admin.headers.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' : 'xl:justify-start'">
                        <span
                            class="{{ request()->routeIs('admin.headers.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><x-heroicon-o-cog-6-tooth
                                class="size-6" /></span>
                        <span
                            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                            class="menu-item-text">Pengaturan header</span>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('admin.homepage.edit') }}"
                        class="menu-item group {{ request()->routeIs('admin.homepage.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' :
                        'xl:justify-start'">
                        <span
                            class="{{ request()->routeIs('admin.homepage.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                            <x-heroicon-o-home class="size-6" />
                        </span>
                        <span
                            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                            class="menu-item-text">
                            Pengaturan Homepage
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.maps.edit') }}"
                        class="menu-item group {{ request()->routeIs('admin.maps.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' : 'xl:justify-start'">
                        <span
                            class="{{ request()->routeIs('admin.maps.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><x-heroicon-o-photo
                                class="size-6" /></span>
                        <span
                            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                            class="menu-item-text">Peta Dasar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.menus.index') }}"
                        class="menu-item group {{ request()->routeIs('admin.menus.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' : 'xl:justify-start'">
                        <span
                            class="{{ request()->routeIs('admin.menus.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"><x-heroicon-o-adjustments-horizontal
                                class="size-6" /></span>
                        <span
                            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                            class="menu-item-text">Menu</span>
                    </a>
                </li>
                @if (auth()->user()?->role === 'admin')
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="menu-item group {{ request()->routeIs('admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                            'xl:justify-center' : 'xl:justify-start'">
                            <span
                                class="{{ request()->routeIs('admin.users.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                                <x-heroicon-o-users class="size-6" />
                            </span>
                            <span
                                x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                class="menu-item-text">Pengguna</span>
                        </a>
                    </li>
                @endif
            </ul>
            <h2 class="mb-2 mt-5 flex text-xs uppercase leading-[20px] text-gray-400"
                :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                'lg:justify-center' : 'justify-start'">
                <span
                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">Menu</span>
            </h2>
            @foreach ($sidebarMenus as $menu)
                @php
                    $categoryActive =
                        request()->routeIs('admin.categories.*') && request()->query('menu') === $menu->slug;

                    $locationActive =
                        request()->routeIs('admin.locations.*') && request()->query('menu') === $menu->slug;

                    $menuActive = $categoryActive || $locationActive;
                @endphp

                <li x-data="{ open: @js($menuActive) }">
                    <button type="button" @click="open = !open" :aria-expanded="open"
                        aria-controls="submenu-{{ $menu->id }}"
                        class="menu-item group w-full {{ $menuActive ? 'menu-item-active' : 'menu-item-inactive' }}"
                        :class="(!$store.sidebar.isExpanded &&
                            !$store.sidebar.isHovered &&
                            !$store.sidebar.isMobileOpen) ?
                        'xl:justify-center' :
                        'xl:justify-start'">
                        <span class="{{ $menuActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                            <i class="fa-solid fa-{{ $menu->icon }} text-xl" aria-hidden="true"></i>
                        </span>

                        <span
                            x-show="$store.sidebar.isExpanded ||
                        $store.sidebar.isHovered ||
                        $store.sidebar.isMobileOpen"
                            class="menu-item-text flex-1 text-left">
                            {{ $menu->name }}
                        </span>

                        <i x-show="$store.sidebar.isExpanded ||
                        $store.sidebar.isHovered ||
                        $store.sidebar.isMobileOpen"
                            class="fa-solid fa-chevron-down ml-auto text-xs transition-transform"
                            :class="{ 'rotate-180': open }" aria-hidden="true"></i>
                    </button>

                    <ul id="submenu-{{ $menu->id }}" x-cloak
                        x-show="open && (
                $store.sidebar.isExpanded ||
                $store.sidebar.isHovered ||
                $store.sidebar.isMobileOpen
            )"
                        x-transition
                        class="ml-6 mt-1 flex flex-col gap-1 border-l border-gray-200 pl-2 dark:border-gray-700">
                        <li>
                            <a href="{{ route('admin.categories.index', ['menu' => $menu->slug]) }}"
                                class="block rounded-lg px-3 py-2 text-sm {{ $categoryActive ? 'menu-dropdown-item-active font-semibold' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                <i class="fa-solid fa-tag mr-2 text-xs" aria-hidden="true"></i>
                                Kategori
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.locations.index', ['menu' => $menu->slug]) }}"
                                class="block rounded-lg px-3 py-2 text-sm {{ $locationActive ? 'menu-dropdown-item-active font-semibold' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                <i class="fa-solid fa-map-location-dot mr-2 text-xs" aria-hidden="true"></i>
                                Lokasi
                            </a>
                        </li>
                    </ul>
                </li>
            @endforeach
            </ul>

        </nav>
    </div>
</aside>
