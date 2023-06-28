<ul class="nav pcoded-inner-navbar">


    <li class="nav-item @if(Request::route()->getName() == 'admin.dashboard') active @endif">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'admin.user.list') active @endif">
        <a href="{{ route('admin.user.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-user"></i></span>
            <span class="pcoded-mtext">User Management</span>
        </a>
    </li>

    <li class="nav-item pcoded-hasmenu @if(Request::route()->getName() == 'admin.role.list' || Request::route()->getName() == 'admin.user_settings.department.list' || Request::route()->getName() == 'admin.user_settings.designation.list') active pcoded-trigger @endif">
        <a href="javascript:void(0);" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-users"></i></span>
            <span class="pcoded-mtext">User Settings</span>
        </a>
        <ul class="pcoded-submenu">
            <li class="@if(Request::route()->getName() == 'admin.role.list') active @endif"><a href="{{ route('admin.role.list') }}" class="">Roles</a></li>
            <li class="@if(Request::route()->getName() == 'admin.user_settings.department.list') active @endif"><a href="{{ route('admin.user_settings.department.list') }}" class="">Departments</a></li>
            <li class="@if(Request::route()->getName() == 'admin.user_settings.designation.list') active @endif"><a href="{{ route('admin.user_settings.designation.list') }}" class="">Designations</a></li>
        </ul>
    </li>

    <li class="nav-item pcoded-hasmenu @if(Request::route()->getName() == 'admin.campaign_settings.campaign_type.list' || Request::route()->getName() == 'admin.campaign_settings.campaign_filter.list' || Request::route()->getName() == 'admin.campaign_settings.campaign_status.list' || Request::route()->getName() == 'admin.campaign_settings.agent_work_type.list') active pcoded-trigger @endif">
        <a href="javascript:void(0);" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layers"></i></span><span class="pcoded-mtext">Campaign Settings</span></a>
        <ul class="pcoded-submenu">
            <li class="@if(Request::route()->getName() == 'admin.campaign_settings.campaign_filter.list') active @endif"><a href="{{ route('admin.campaign_settings.campaign_filter.list') }}" class="">Campaign Filters</a></li>
            <li class="@if(Request::route()->getName() == 'admin.campaign_settings.campaign_type.list') active @endif"><a href="{{ route('admin.campaign_settings.campaign_type.list') }}" class="">Campaign Types</a></li>
            <li class="@if(Request::route()->getName() == 'admin.campaign_settings.campaign_status.list') active @endif"><a href="{{ route('admin.campaign_settings.campaign_status.list') }}" class="">Campaign Statuses</a></li>
            <li class="@if(Request::route()->getName() == 'admin.campaign_settings.agent_work_type.list') active @endif"><a href="{{ route('admin.campaign_settings.agent_work_type.list') }}" class="">Agent Work Types</a></li>
        </ul>
    </li>

    <li class="nav-item pcoded-hasmenu @if(Request::route()->getName() == 'admin.geo.region.list' || Request::route()->getName() == 'admin.geo.region.list') active pcoded-trigger @endif">
        <a href="javascript:void(0);" class="nav-link"><span class="pcoded-micon"><i class="feather icon-map"></i></span><span class="pcoded-mtext">Geo Management</span></a>
        <ul class="pcoded-submenu">
            <li class="@if(Request::route()->getName() == 'admin.geo.country.list') active @endif"><a href="{{ route('admin.geo.country.list') }}" class="">Countries</a></li>
            <li class="@if(Request::route()->getName() == 'admin.geo.region.list') active @endif"><a href="{{ route('admin.geo.region.list') }}" class="">Regions</a></li>
        </ul>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'admin.holiday.list') active @endif">
        <a href="{{ route('admin.holiday.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-calendar"></i></span>
            <span class="pcoded-mtext">Holidays</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'admin.site_settings.list') active @endif">
        <a href="{{ route('admin.site_settings.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-settings"></i></span>
            <span class="pcoded-mtext">Site Settings</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'admin.tutorial.list') active @endif">
        <a href="{{ route('admin.tutorial.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
            <span class="pcoded-mtext">Tutorial Management</span>
        </a>
    </li>

    <li class="nav-item pcoded-menu-caption">
        <label>Admin Menu</label>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'admin.cron_trigger.list') active @endif">
        <a href="{{ route('admin.cron_trigger.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-sliders"></i></span>
            <span class="pcoded-mtext">Cron Triggers</span>
        </a>
    </li>

</ul>

