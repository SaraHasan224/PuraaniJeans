<div class="app-page-title ">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div>
                @hasSection('parent_module_title')
                    <div class="page-title-head center-elem">
                        <span class="d-inline-block pr-2">
                            <i class="@yield('parent_module_icon') opacity-6"></i>
                        </span>
                        <span class="d-inline-block">@yield('parent_module_title')</span>
                    </div>
                @endif
                @hasSection('has_child_breadcrumb_section')
                    <div class="page-title-subheading opacity-10 ">
                        <nav class="" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a>
                                        <i aria-hidden="true" class="fa fa-home"></i>
                                    </a>
                                </li>
                                @hasSection('parent_module_breadcrumb_title')
                                    <li class="breadcrumb-item">
                                        <a>@yield('parent_module_breadcrumb_title')</a>
                                    </li>
                                @endif
                                @hasSection('child_module_breadcrumb_title')
                                    <li class="active breadcrumb-item" aria-current="page">
                                        @yield('child_module_breadcrumb_title')
                                    </li>
                                @endif
                                @hasSection('sub_child_module_breadcrumb_title')
                                    <li class="active breadcrumb-item" aria-current="page">
                                        @yield('sub_child_module_breadcrumb_title')
                                    </li>
                                @endif
                            </ol>
                        </nav>
                    </div>
                @endif

            </div>
        </div>
        @yield('has_child_breadcrumb_actions')
    </div>
</div>