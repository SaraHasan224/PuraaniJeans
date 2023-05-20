<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                        data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
                        <span>
                            <button type="button"
                                    class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <li class="app-sidebar__heading">Account Management</li>
                <li class="{{ (Request::is('home') ||  Request::is('/')) ? 'mm-active' : '' }}">
                    <a href="{{ URL::to('/') }}">
                        <i class="metismenu-icon pe-7s-display2">
                        </i>Dashboard
                    </a>
                </li>
                <li class="{{ (Request::is('users') ||  Request::is('/users')) ? 'mm-active' : '' }}">
                    <a href="{{ URL::to('/users') }}">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Users
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="{{
                     (Request::is('users') ||  Request::is('/users')) ||
                     (Request::is('users.add') ||  Request::is('/users/add'))
                     ? 'mm-active' : '' }}">
                        <li>
                            <a href="{{ URL::to('/users') }}" class="mm-active">
                                <i class="metismenu-icon">
                                </i>View All
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ (Request::is('roles') ||  Request::is('/roles')) ? 'mm-active' : '' }}">
                    <a href="{{ URL::to('/roles') }}">
                        <i class="metismenu-icon pe-7s-safe"></i>
                        Roles
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="{{
                     (Request::is('roles') ||  Request::is('/roles')) ||
                     (Request::is('roles.add') ||  Request::is('/roles/add'))
                     ? 'mm-active' : '' }}">
                        <li>
                            <a href="{{ URL::to('/roles') }}" class="mm-active">
                                <i class="metismenu-icon">
                                </i>View All
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar__heading">Manage Puraani Jeans Website</li>
                <li class="
                {{  (Request::is('closet') ||  Request::is('/closet')) ||
                    (Request::is('closet-orders') ||  Request::is('/closet/orders')) ||
                    (Request::is('closet-pim') ||  Request::is('/closet/pim')) ||
                    (Request::is('customers') ||  Request::is('/customers'))
                ? 'mm-active' : '' }}">
                    <a href="#">
                        <i class="metismenu-icon pe-7s-diamond"></i>
                        Application
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="
                        {{  (Request::is('closet') ||  Request::is('/closet')) ||
                            (Request::is('closet-orders') ||  Request::is('/closet/orders')) ||
                            (Request::is('closet-pim') ||  Request::is('/closet/pim'))
                        ? 'mm-active' : '' }}">
                        <li class="{{ (Request::is('closet') ||  Request::is('/closet')) ? 'mm-active' : '' }}">
                            <a href="{{ URL::to('/closet') }}">
                                <i class="metismenu-icon"></i>
                                Closet Management
                                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                            </a>
                            <ul
                            >
                                <li class="{{ (Request::is('closet') ||  Request::is('/closet')) ? 'mm-active' : '' }}">
                                    <a href="{{ URL::to('/closet') }}">
                                        <i class="metismenu-icon">
                                        </i>Closets
                                    </a>
                                </li>
                                <li class="{{ (Request::is('closet/orders') ||  Request::is('/closet/orders')) ? 'mm-active' : '' }}">
                                    <a href="{{ URL::to('/closet/orders') }}">
                                        <i class="metismenu-icon">
                                        </i>Orders
                                    </a>
                                </li>
                                <li class="{{ (Request::is('closet/pim') ||  Request::is('/closet/pim')) ? 'mm-active' : '' }}">
                                    <a href="{{ URL::to('/closet/pim') }}">
                                        <i class="metismenu-icon">
                                        </i>Product Inventory
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ (Request::is('customers') ||  Request::is('/customers')) ? 'mm-active' : '' }}">
                            <a href="{{ URL::to('/customers') }}">
                                <i class="metismenu-icon">
                                </i>Customers
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>