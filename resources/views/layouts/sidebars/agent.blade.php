<ul class="nav pcoded-inner-navbar">


    <li class="nav-item @if(Request::route()->getName() == 'agent.dashboard') active @endif">
        <a href="{{ route('agent.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('agent.campaign.list', 'agent.campaign.show'))) active @endif">
        <a href="{{ route('agent.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('agent.reports'))) active @endif">
        <a href="{{ route('agent.reports') }}" class="nav-link">
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

