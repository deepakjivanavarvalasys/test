<ul class="nav pcoded-inner-navbar">


    <li class="nav-item @if(Request::route()->getName() == 'quality_analyst.dashboard') active @endif">
        <a href="{{ route('quality_analyst.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('quality_analyst.campaign.list', 'quality_analyst.campaign.show'))) active @endif">
        <a href="{{ route('quality_analyst.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('quality_analyst.reports'))) active @endif">
        <a href="{{ route('quality_analyst.reports') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">Reports</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'tutorial.list') active @endif">
        <a href="{{ route('tutorial.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
            <span class="pcoded-mtext">Tutorials</span>
        </a>
    </li>

</ul>

