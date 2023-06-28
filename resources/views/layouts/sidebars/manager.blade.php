<ul class="nav pcoded-inner-navbar">


    <li class="nav-item @if(Request::route()->getName() == 'manager.dashboard') active @endif">
        <a href="{{ route('manager.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('manager.campaign.list', 'manager.campaign.show'))) active @endif">
        <a href="{{ route('manager.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">Campaign Management</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('manager.campaign_assign.list'))) active @endif">
        <a href="{{ route('manager.campaign_assign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-shuffle"></i></span>
            <span class="pcoded-mtext">Campaign Assign</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('manager.campaign_issue.list'))) active @endif">
        <a href="{{ route('manager.campaign_issue.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-info"></i></span>
            <span class="pcoded-mtext">Campaign Issue Management</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('manager.data.list'))) active @endif">
        <a href="{{ route('manager.data.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
            <span class="pcoded-mtext">Data Management</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('manager.reports'))) active @endif">
        <a href="{{ route('manager.reports') }}?page=1" class="nav-link">
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

