<ul class="nav pcoded-inner-navbar">

    <li class="nav-item @if(Request::route()->getName() == 'email_marketing_executive.dashboard') active @endif">
        <a href="{{ route('email_marketing_executive.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('email_marketing_executive.campaign.list', 'email_marketing_executive.campaign.show'))) active @endif">
        <a href="{{ route('email_marketing_executive.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns (RA)</span>
        </a>
    </li>

    <li class="nav-item pcoded-menu-caption">
        <label>Promotion</label>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('email_marketing_executive.promotion_campaign.list', 'email_marketing_executive.promotion_campaign.show'))) active @endif">
        <a href="{{ route('email_marketing_executive.promotion_campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item pcoded-menu-caption">
        <label>Other</label>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('email_marketing_executive.campaign_management.list', 'email_marketing_executive.campaign_management.show'))) active @endif">
        <a href="{{ route('email_marketing_executive.campaign_management.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">Campaign Management</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'tutorial.list') active @endif">
        <a href="{{ route('tutorial.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
            <span class="pcoded-mtext">Tutorials</span>
        </a>
    </li>

</ul>

