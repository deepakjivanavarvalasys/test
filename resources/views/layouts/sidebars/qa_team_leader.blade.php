<ul class="nav pcoded-inner-navbar">


    <li class="nav-item @if(Request::route()->getName() == 'qa_team_leader.dashboard') active @endif">
        <a href="{{ route('qa_team_leader.dashboard') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-home"></i></span>
            <span class="pcoded-mtext">Dashboard</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('qa_team_leader.campaign.list', 'qa_team_leader.campaign.show'))) active @endif">
        <a href="{{ route('qa_team_leader.campaign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-list"></i></span>
            <span class="pcoded-mtext">My Campaigns</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('qa_team_leader.campaign_assign.list'))) active @endif">
        <a href="{{ route('qa_team_leader.campaign_assign.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-shuffle"></i></span>
            <span class="pcoded-mtext">Campaign Assign</span>
        </a>
    </li>

    <li class="nav-item @if(in_array(Request::route()->getName(), array('qa_team_leader.team.list', 'qa_team_leader.team.show'))) active @endif">
        <a href="{{ route('qa_team_leader.team.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-users"></i></span>
            <span class="pcoded-mtext">My Team</span>
        </a>
    </li>

    <li class="nav-item @if(Request::route()->getName() == 'tutorial.list') active @endif">
        <a href="{{ route('tutorial.list') }}" class="nav-link">
            <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
            <span class="pcoded-mtext">Tutorials</span>
        </a>
    </li>

</ul>

