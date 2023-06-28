<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="javascript:void(0);"><span></span></a>
        <a href="{{route($module->route_name)}}" class="b-brand">
            <span class="b-title">Valasys Media - CRM</span>
        </a>
    </div>
    <a class="mobile-menu" id="mobile-header" href="#!">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li>
                <span class="text-secondary">
                    <small>Version 2.0</small>
                </span>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li style="display: none;">
                <a href="javascript:;">
                    <button type="button" @class('btn btn-outline-dark btn-sm m-0')>Time-In: 23:29</button>
                </a>
            </li>

            <li>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="icon feather icon-bell"></i> @if(!empty($notifications) && $notifications->count()) <span id="new-notification-count" class="badge badge-warning" style="position: relative;bottom: 10px;z-index: -1;">{{ $notifications->count() }}</span> @endif </a>
                    <div class="dropdown-menu dropdown-menu-right notification">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notifications</h6>
                            <div class="float-right">
                                @if(!empty($notifications) && $notifications->count())
                                <a id="notification-mark-all-as-read-button" href="javascript:void(0);" onclick="notificationMarkAllAsRead();" class="m-r-10">mark all as read</a>
                                @endif
                            </div>
                        </div>
                        <ul class="noti-body" style="max-height: 400px;overflow-y: scroll;overflow-x: hidden;">

                            @if(!empty($notifications) && $notifications->count())
                                <li class="n-title new-notification">
                                    <p class="m-b-0">NEW</p>
                                </li>
                                @foreach($notifications as $key => $notification)
                                <li class="notification new-notification border-bottom border-light" style="padding: 10px 30px 10px 15px;">
                                    <div class="media">
                                        <img class="img-radius" src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>{{ $notification->sender->full_name }}</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span></p>
                                            <p>{{ $notification->message }} <a href="{{ route('notification.view_details', base64_encode($notification->id)) }}" class="float-right p-0" style="cursor: pointer;"><small>View Details</small></a></p>

                                        </div>
                                    </div>
                                </li>
                                @endforeach
                                <li class="notification no-new-notification" style="display: none;">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>No new notifications</p>
                                        </div>
                                    </div>
                                </li>
                            @else
                            <li class="notification no-new-notification">
                                <div class="media">
                                    <div class="media-body">
                                        <p>No new notifications</p>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                        <div class="noti-footer" style="display: none;">
                            <a href="javascript:void(0);" onclick="alert('feature in progress!!!');">show all</a>
                        </div>
                    </div>
                </div>
            </li>

            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon feather icon-settings"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification" style="line-height: 0.4;">
                        <div class="pro-head">
                            <img src="@if(!empty(Auth::user()->profile)){{asset('public/storage/user/'.Auth::user()->employee_code.'/profile/'.Auth::user()->profile)}}@else{{asset('public/template/assets/images/user/avatar-2.jpg')}}@endif" class="img-radius" alt="User-Profile-Image">
                            <span>
                                {{Auth::user()->full_name}}
                                <br>
                                <small class="text-dark" style="margin-left: 55px;">Reporting: {{Auth::user()->reporting->full_name}}</small>
                            </span>
                            <a href="{{ route('logout') }}" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">
                            <li><a href="@if(Request::route()->getName() != 'agent.user.my_profile') {{ route($module->slug.'.user.my_profile') }} @else javascript:; @endif" class="dropdown-item"><i class="feather icon-user"></i> My Profile</a></li>
                            <li><a href="{{ route('logout') }}" class="dropdown-item"><i class="feather icon-log-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
