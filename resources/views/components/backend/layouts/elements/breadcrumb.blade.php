{{-- Enhanced Premium Breadcrumb --}}
<div class="breadcrumb-header animate-fade-in">
    <div class="row align-items-center justify-content-between py-3 px-4">
        <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
            {{-- Left Section - Additional Actions --}}
            <div class="breadcrumb-actions">
                {{ $add ?? '' }}
            </div>
        </div>

        <div class="col-lg-6 col-md-4 text-center mb-3 mb-md-0">
            {{-- Center Section - Page Title --}}
            <h1 class="page-title mb-0">
                {{-- <span class="title-icon">
                    <i class="fas fa-{{ $icon ?? 'file-alt' }}"></i>
                </span> --}}
                {{ $pageHeader }}
            </h1>
        </div>

        <div class="col-lg-3 col-md-4">
            {{-- Right Section - User Profile Dropdown --}}
            <div class="user-profile-section">
                <ul class="navbar-nav ms-auto justify-content-end">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown-link" id="navbarDropdown" href="#"
                            role="button" data-bs-toggle="dropdown" data-bs-popper="static" aria-expanded="false">
                            <div class="user-avatar-wrapper">
                                <img src="{{ asset('images/users/' . auth()->user()->picture) }}" class="user-avatar"
                                    alt="{{ auth()->user()->name }}">
                                <span class="user-status-indicator"></span>
                            </div>
                            <span class="user-name d-none d-lg-inline-block">{{ auth()->user()->name ?? '' }}</span>
                            <i class="fas fa-chevron-down ms-2 dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu shadow-lg"
                            aria-labelledby="navbarDropdown" style="z-index: 9999;">
                            <li class="dropdown-header">
                                <div class="text-center py-2">
                                    <img src="{{ asset('images/users/' . auth()->user()->picture) }}"
                                        class="rounded-circle mb-2" width="60" height="60"
                                        alt="{{ auth()->user()->name }}">
                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            {{-- <li>
                                <a class="dropdown-item" href="#!">
                                    <i class="fas fa-user-circle me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#!">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li> --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item logout-item"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    /* ========== Breadcrumb Header Styles ========== */
    .breadcrumb-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        padding: 0 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 100;
        overflow: visible !important;
    }

    .animate-fade-in {
        animation: fadeInDown 0.6s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ========== Page Title ========== */
    .page-title {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        animation: titlePulse 3s ease-in-out infinite;
    }

    @keyframes titlePulse {

        0%,
        100% {
            filter: brightness(1);
        }

        50% {
            filter: brightness(1.15);
        }
    }

    .title-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* ========== Breadcrumb Actions ========== */
    .breadcrumb-actions h4 {
        margin: 0;
        font-size: 1rem;
    }

    /* ========== User Profile Section ========== */
    .user-profile-section {
        display: flex;
        justify-content: flex-end;
        position: relative;
        z-index: 1000;
    }

    .navbar-nav {
        position: relative;
        z-index: 1000;
    }

    .user-dropdown-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        background: rgba(102, 126, 234, 0.05);
        transition: all 0.3s ease;
        color: #4a5568 !important;
        font-weight: 600;
    }

    .user-dropdown-link:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .user-avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        object-fit: cover;
    }

    .user-dropdown-link:hover .user-avatar {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    }

    .user-status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: #38ef7d;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(56, 239, 125, 0.6);
        animation: pulse-status 2s ease-in-out infinite;
    }

    @keyframes pulse-status {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .user-name {
        font-size: 0.95rem;
        color: #2d3748;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dropdown-arrow {
        font-size: 0.75rem;
        transition: transform 0.3s ease;
    }

    .user-dropdown-link[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* ========== User Dropdown Menu ========== */
    .user-dropdown-menu {
        min-width: 280px;
        border: none;
        border-radius: 1rem;
        padding: 0.5rem;
        margin-top: 0.75rem;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        animation: dropdownSlideIn 0.3s ease-out;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        z-index: 10000 !important;
        position: relative;
    }

    @keyframes dropdownSlideIn {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .user-dropdown-menu .dropdown-header {
        padding: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .user-dropdown-menu .dropdown-header h6 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .user-dropdown-menu .dropdown-item {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        font-weight: 500;
        color: #4a5568;
        display: flex;
        align-items: center;
    }

    .user-dropdown-menu .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        transform: translateX(5px);
        color: #667eea;
    }

    .user-dropdown-menu .dropdown-item i {
        width: 20px;
        text-align: center;
    }

    .logout-item {
        color: #eb3349 !important;
    }

    .logout-item:hover {
        background: linear-gradient(135deg, rgba(235, 51, 73, 0.1) 0%, rgba(244, 92, 67, 0.1) 100%) !important;
        color: #eb3349 !important;
    }

    /* ========== Responsive Adjustments ========== */
    @media (max-width: 768px) {
        .breadcrumb-header {
            margin: -1rem -1rem 1rem -1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .title-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }

        .user-dropdown-link {
            padding: 0.5rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
        }

        .breadcrumb-actions h4 {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.25rem;
        }
    }
</style>
