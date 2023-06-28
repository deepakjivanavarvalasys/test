<ul class="nav pcoded-inner-navbar">

    <li class="nav-item @if(Request::route()->getName() == 'vendor_manager.dashboard') active @endif">
        <a href="{{ route('vendor_manager.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item pcoded-menu-caption">
        <label>RA Menu</label>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('vendor_manager.ra.campaign.list', 'vendor_manager.ra.campaign.show'))) active @endif">
        <a href="{{ route('vendor_manager.ra.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item pcoded-menu-caption">
        <label>VM Menu</label>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('vendor_manager.campaign.list', 'vendor_manager.campaign.show'))) active @endif">
        <a href="{{ route('vendor_manager.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('vendor_manager.campaign_assign.list','vendor_manager.campaign_assign.show'))) active @endif">
        <a href="{{ route('vendor_manager.campaign_assign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-shuffle"></i></span>
            <span class="pcoded-mtext">Campaign Assign</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('vendor_manager.vendor.list'))) active @endif">
        <a href="{{ route('vendor_manager.vendor.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-users"></i></span>
            <span class="pcoded-mtext">Vendor Management</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'tutorial.list') active @endif">
        <a href="{{ route('tutorial.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
            <span class="pcoded-mtext">Tutorials</span>
        </a>
    </li>

</ul>

