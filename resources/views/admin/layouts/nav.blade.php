<div id="nav" class="nav-container d-flex">
    <div class="nav-content d-flex">
    <!-- Logo Start -->
    <div class="logo position-relative">
        <a href="{{ route('admin.dashboard.index') }}">
        <!-- Logo can be added directly -->
        {{-- <img src="ass" alt="logo" /> --}}

        <!-- Or added via css to provide different ones for different color themes -->
        {{-- <div class="img"></div> --}}
        </a>
    </div>
    <!-- Logo End -->

    <!-- Language Switch Start -->
    {{-- <div class="language-switch-container">
        <button class="btn btn-empty language-button dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">EN</button>
        <div class="dropdown-menu">
        <a href="#" class="dropdown-item">DE</a>
        <a href="#" class="dropdown-item active">EN</a>
        <a href="#" class="dropdown-item">ES</a>
        </div>
    </div> --}}
    <!-- Language Switch End -->

    <!-- User Menu Start -->
    <div class="user-container d-flex">
        <a href="#" class="d-flex user position-relative" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <img class="profile" alt="profile" src="https://3.bp.blogspot.com/-84AZcdvvo6k/XxcAS-ve2mI/AAAAAAAAatg/MsweQPwt57oqf95KhA5Qg-Y2GUnqeqp4gCLcBGAsYHQ/s1600/Lambang-Kabupaten-Madiun_237-design.png" />
        <div class="name">{{Auth::user()->name}}</div>
        </a>
        <div class="dropdown-menu dropdown-menu-end user-menu wide">
        <div class="row mb-3 ms-0 me-0">
            <div class="col-12 ps-1 mb-2">
            <div class="text-extra-small text-primary">ACCOUNT</div>
            </div>
            <div class="col-6 ps-1 pe-1">
            <ul class="list-unstyled">
                <li>
                <a href="#">User Info</a>
                </li>
                {{-- <li>
                <a href="#">Preferences</a>
                </li>
                <li>
                <a href="#">Calendar</a>
                </li> --}}
            </ul>
            </div>
            {{-- <div class="col-6 pe-1 ps-1">
            <ul class="list-unstyled">
                <li>
                <a href="#">Security</a>
                </li>
                <li>
                <a href="#">Billing</a>
                </li>
            </ul>
            </div> --}}
        </div>
        {{-- <div class="row mb-1 ms-0 me-0">
            <div class="col-12 p-1 mb-2 pt-2">
            <div class="text-extra-small text-primary">APPLICATION</div>
            </div>
            <div class="col-6 ps-1 pe-1">
            <ul class="list-unstyled">
                <li>
                <a href="#">Themes</a>
                </li>
                <li>
                <a href="#">Language</a>
                </li>
            </ul>
            </div>
            <div class="col-6 pe-1 ps-1">
            <ul class="list-unstyled">
                <li>
                <a href="#">Devices</a>
                </li>
                <li>
                <a href="#">Storage</a>
                </li>
            </ul>
            </div>
        </div> --}}
        <div class="row mb-1 ms-0 me-0">
            <div class="col-12 p-1 mb-3 pt-3">
            <div class="separator-light"></div>
            </div>
            <div class="col-6 ps-1 pe-1">
            {{-- <ul class="list-unstyled">
                <li>
                <a href="#">
                    <i data-acorn-icon="help" class="me-2" data-acorn-size="17"></i>
                    <span class="align-middle">Help</span>
                </a>
                </li>
                <li>
                <a href="#">
                    <i data-acorn-icon="file-text" class="me-2" data-acorn-size="17"></i>
                    <span class="align-middle">Docs</span>
                </a>
                </li>
            </ul> --}}
            </div>
            <div class="col-6 pe-1 ps-1">
            <ul class="list-unstyled">
                {{-- <li>
                <a href="#">
                    <i data-acorn-icon="gear" class="me-2" data-acorn-size="17"></i>
                    <span class="align-middle">Settings</span>
                </a>
                </li> --}}
                <li>
                <a href="{{ route('admin.logout') }}">
                    <i data-acorn-icon="logout" class="me-2" data-acorn-size="17"></i>
                    <span class="align-middle">Logout</span>
                </a>
                </li>
            </ul>
            </div>
        </div>
        </div>
    </div>
    <!-- User Menu End -->

    <!-- Icons Menu Start -->
    {{-- <ul class="list-unstyled list-inline text-center menu-icons">
        <li class="list-inline-item">
        <a href="#" data-bs-toggle="modal" data-bs-target="#searchPagesModal">
            <i data-acorn-icon="search" data-acorn-size="18"></i>
        </a>
        </li>
        <li class="list-inline-item">
        <a href="#" id="pinButton" class="pin-button">
            <i data-acorn-icon="lock-on" class="unpin" data-acorn-size="18"></i>
            <i data-acorn-icon="lock-off" class="pin" data-acorn-size="18"></i>
        </a>
        </li>
        <li class="list-inline-item">
        <a href="#" id="colorButton">
            <i data-acorn-icon="light-on" class="light" data-acorn-size="18"></i>
            <i data-acorn-icon="light-off" class="dark" data-acorn-size="18"></i>
        </a>
        </li>
        <li class="list-inline-item">
        <a href="#" data-bs-toggle="dropdown" data-bs-target="#notifications" aria-haspopup="true" aria-expanded="false" class="notification-button">
            <div class="position-relative d-inline-flex">
            <i data-acorn-icon="bell" data-acorn-size="18"></i>
            <span class="position-absolute notification-dot rounded-xl"></span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end wide notification-dropdown scroll-out" id="notifications">
            <div class="scroll">
            <ul class="list-unstyled border-last-none">
                <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                <img src="{{ asset('acorn/acorn-elearning-portal/img/profile/profile-1.webp') }}" class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                <div class="align-self-center">
                    <a href="#">Joisse Kaycee just sent a new comment!</a>
                </div>
                </li>
                <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                <img src="{{ asset('acorn/acorn-elearning-portal/img/profile/profile-2.webp') }}" class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                <div class="align-self-center">
                    <a href="#">New order received! It is total $147,20.</a>
                </div>
                </li>
                <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                <img src="{{ asset('acorn/acorn-elearning-portal/img/profile/profile-3.webp') }}" class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                <div class="align-self-center">
                    <a href="#">3 items just added to wish list by a user!</a>
                </div>
                </li>
                <li class="pb-3 pb-3 border-bottom border-separator-light d-flex">
                <img src="{{ asset('acorn/acorn-elearning-portal/img/profile/profile-6.webp') }}" class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                <div class="align-self-center">
                    <a href="#">Kirby Peters just sent a new message!</a>
                </div>
                </li>
            </ul>
            </div>
        </div>
        </li>
    </ul> --}}
    <!-- Icons Menu End -->

    <!-- Menu Start -->
    <div class="menu-container flex-grow-1">
        <ul id="menu" class="menu">
            <li>
                @if (request()->routeIs('admin.dashboard.index'))
                    <a href="{{ route('admin.dashboard.index') }}" class="active">
                @else
                    <a href="{{ route('admin.dashboard.index') }}">
                @endif
                    <i data-acorn-icon="home-garage" class="icon" data-acorn-size="18"></i>
                    <span class="label">Dashboard</span>
                </a>
            </li>
            <li>
                @if (request()->routeIs('admin.nomenklatur.index'))
                    <a href="{{ route('admin.nomenklatur.index') }}" class="active">
                @else
                    <a href="{{ route('admin.nomenklatur.index') }}">
                @endif
                    <i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i>
                    <span class="label"> Nomenklatur</span>
                </a>
            </li>
            <li>
                @if (request()->routeIs('admin.perencanaan.index'))
                    <a href="{{ route('admin.perencanaan.index') }}" class="active">
                @else
                    <a href="{{ route('admin.perencanaan.index') }}">
                @endif
                    <i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i>
                    <span class="label"> Perencanaan</span>
                </a>
            </li>
            {{-- <li>
                @if (request()->routeIs('admin.urusan.index') ||
                request()->routeIs('admin.program.index') ||
                request()->routeIs('admin.kegiatan.index') ||
                request()->routeIs('admin.sub-kegiatan.index'))
                    <a href="#nomenklatur" class="active">
                @else
                    <a href="#nomenklatur">
                @endif
                    <i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i>
                    <span class="label">Nomenklatur</span>
                </a>
                <ul id="nomenklatur">
                    <li>
                        @if (request()->routeIs('admin.urusan.index'))
                            <a href="{{ route('admin.urusan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.urusan.index') }}">
                        @endif
                            <span class="label">Urusan</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.program.index'))
                            <a href="{{ route('admin.program.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.program.index') }}">
                        @endif
                            <span class="label">Program</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.kegiatan.index'))
                            <a href="{{ route('admin.kegiatan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.kegiatan.index') }}">
                        @endif
                            <span class="label">Kegiatan</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.sub-kegiatan.index'))
                            <a href="{{ route('admin.sub-kegiatan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.sub-kegiatan.index') }}">
                        @endif
                            <span class="label">Sub Kegiatan</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li>
                @if (request()->routeIs('admin.visi.index') ||
                request()->routeIs('admin.misi.index') ||
                request()->routeIs('admin.tujuan.index') ||
                request()->routeIs('admin.sasaran.index') ||
                request()->routeIs('admin.program-rpjmd.index'))
                    <a href="#rpjmd" class="active">
                @else
                    <a href="#rpjmd">
                @endif
                    <i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i>
                    <span class="label">RPJMD</span>
                </a>
                <ul id="rpjmd">
                    <li>
                        @if (request()->routeIs('admin.visi.index'))
                            <a href="{{ route('admin.visi.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.visi.index') }}">
                        @endif
                            <span class="label">Visi</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.misi.index'))
                            <a href="{{ route('admin.misi.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.misi.index') }}">
                        @endif
                            <span class="label">Misi</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.tujuan.index'))
                            <a href="{{ route('admin.tujuan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.tujuan.index') }}">
                        @endif
                            <span class="label">Tujuan</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.sasaran.index'))
                            <a href="{{ route('admin.sasaran.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.sasaran.index') }}">
                        @endif
                            <span class="label">Sasaran</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.program-rpjmd.index'))
                            <a href="{{ route('admin.program-rpjmd.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.program-rpjmd.index') }}">
                        @endif
                            <span class="label">Program RPJMD</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li>
                @if (request()->routeIs('admin.rkpd.index'))
                    <a href="{{ route('admin.rkpd.index') }}" class="active">
                @else
                    <a href="{{ route('admin.rkpd.index') }}">
                @endif
                    <i data-acorn-icon="file-data" class="icon" data-acorn-size="18"></i>
                    <span class="label">RKPD</span>
                </a>
            </li>
            <li>
                @if (request()->routeIs('admin.laporan.tc-14.index') ||
                request()->routeIs('admin.laporan.tc-19.index') ||
                request()->routeIs('admin.laporan.e-79.index'))
                    <a href="#laporan" class="active">
                @else
                    <a href="#laporan">
                @endif
                    <i data-acorn-icon="align-justify" class="icon" data-acorn-size="18"></i>
                    <span class="label">Laporan</span>
                </a>
                <ul id="laporan">
                    <li>
                        @if (request()->routeIs('admin.laporan.tc-14.index'))
                            <a href="{{ route('admin.laporan.tc-14.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.laporan.tc-14.index') }}">
                        @endif
                            <span class="label">TC 14</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.laporan.tc-19.index'))
                            <a href="{{ route('admin.laporan.tc-19.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.laporan.tc-19.index') }}">
                        @endif
                            <span class="label">TC 19</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.laporan.e-79.index'))
                            <a href="{{ route('admin.laporan.e-79.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.laporan.e-79.index') }}">
                        @endif
                            <span class="label">E 79</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.laporan.e-78.index'))
                            <a href="{{ route('admin.laporan.e-78.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.laporan.e-78.index') }}">
                        @endif
                            <span class="label">E 78</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                @if (request()->routeIs('admin.kecamatan.index') ||
                request()->routeIs('admin.kelurahan.index') ||
                request()->routeIs('admin.jenis-opd.index') ||
                request()->routeIs('admin.master-opd.index') ||
                request()->routeIs('admin.tahun-periode.index'))
                    <a href="#master_data" class="active">
                @else
                    <a href="#master_data">
                @endif
                    <i data-acorn-icon="align-justify" class="icon" data-acorn-size="18"></i>
                    <span class="label">Master Data</span>
                </a>
                <ul id="master_data">
                    <li>
                        @if (request()->routeIs('admin.kecamatan.index'))
                            <a href="{{ route('admin.kecamatan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.kecamatan.index') }}">
                        @endif
                            <span class="label">Kecamatan</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.kelurahan.index'))
                            <a href="{{ route('admin.kelurahan.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.kelurahan.index') }}">
                        @endif
                            <span class="label">Kelurahan</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.jenis-opd.index'))
                            <a href="{{ route('admin.jenis-opd.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.jenis-opd.index') }}">
                        @endif
                            <span class="label">Jenis OPD</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.master-opd.index'))
                            <a href="{{ route('admin.master-opd.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.master-opd.index') }}">
                        @endif
                            <span class="label">Master OPD</span>
                        </a>
                    </li>
                    <li>
                        @if (request()->routeIs('admin.tahun-periode.index'))
                            <a href="{{ route('admin.tahun-periode.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.tahun-periode.index') }}">
                        @endif
                            <span class="label">Tahun Periode</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                @if (request()->routeIs('admin.manajemen-akun.opd.index'))
                    <a href="#manajemen-akun" class="active">
                @else
                    <a href="#manajemen-akun">
                @endif
                    <i data-acorn-icon="user" class="icon" data-acorn-size="18"></i>
                    <span class="label">Manajemen Akun</span>
                </a>
                <ul id="manajemen-akun">
                    <li>
                        @if (request()->routeIs('admin.manajemen-akun.opd.index'))
                            <a href="{{ route('admin.manajemen-akun.opd.index') }}" class="active">
                        @else
                            <a href="{{ route('admin.manajemen-akun.opd.index') }}">
                        @endif
                            <span class="label">OPD</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- Menu End -->

    <!-- Mobile Buttons Start -->
    <div class="mobile-buttons-container">
        <!-- Scrollspy Mobile Button Start -->
        <a href="#" id="scrollSpyButton" class="spy-button" data-bs-toggle="dropdown">
        <i data-acorn-icon="menu-dropdown"></i>
        </a>
        <!-- Scrollspy Mobile Button End -->

        <!-- Scrollspy Mobile Dropdown Start -->
        <div class="dropdown-menu dropdown-menu-end" id="scrollSpyDropdown"></div>
        <!-- Scrollspy Mobile Dropdown End -->

        <!-- Menu Button Start -->
        <a href="#" id="mobileMenuButton" class="menu-button">
        <i data-acorn-icon="menu"></i>
        </a>
        <!-- Menu Button End -->
    </div>
    <!-- Mobile Buttons End -->
    </div>
    <div class="nav-shadow"></div>
</div>
