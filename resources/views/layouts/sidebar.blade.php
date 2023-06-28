@php
    /*
    *
    * IT IS WORKING FINE, DONT TOUCH WITHOUT PRIOR INSTRUCTION
    * Code By: alam@valasys.com, sagar@valasys.com
    */

    $resultModules = \Modules\Permission\models\Permission::select('id','name', 'route', 'icon', 'sidebar_visibility')->whereStatus(\Modules\Dashboard\enum\Status::ACTIVE)->whereSidebar_visibility(\Modules\Dashboard\enum\SidebarVisibility::VISIBLE)->whereNull('parent_id')->orderBy('priority')->get();
    function getModules($module)
    {
        if ($module->subPermissions->where('sidebar_visibility', '1')->count() > 0) {
            if(Helper::hasPermission($module->route)) {
            echo '<li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-menu"></i></span><span class="pcoded-mtext">'.$module->name.'</span></a>
                    <ul class="pcoded-submenu">';
            foreach ($module->subPermissions->sortBy('priority') as $subModule) {
                //if(Helper::hasPermission($module->route)) {
                    getModules($subModule);
                //}
            }

            echo ' </ul> </li>';
            }

        } else {
            if(Helper::hasPermission($module->route)) {
                if($module->sidebar_visibility) {
                    $active = '';
                    if($module->route == \Request::route()->getName()) {
                        $active = 'active';
                    }
                    if(!empty($module->icon)) {
                        $icon = '<span class="pcoded-micon"><i class="'.$module->icon.'"></i></span>';
                    } else {
                        $icon = '';
                    }
                    if($module->slug == 'campaign_management_list' && (Auth::user()->role_id == '34' || Auth::user()->role_id == '31')) {

                    } else {
                        echo '<li class="nav-item '.$active.'"><a href="'.route($module->route).'" class="nav-link">'.$icon.'<span class="pcoded-mtext">'.ucfirst($module->name).'</span></a></li>';
                    }
                }
            }

        }
    }
@endphp

<ul class="nav pcoded-inner-navbar">
    {{--@forelse($resultModules as $module)
        @if(Helper::hasPermission($module->route))
            <li class="nav-item @if($module->route == \Request::route()->getName()) active @endif"><a href="{{route($module->route)}}" class="nav-link"><span class="pcoded-micon"><i class="{{$module->icon}}"></i></span><span class="pcoded-mtext">{{ ucfirst($module->name) }}</span></a></li>
        @endif
    @empty
    @endforelse--}}

    @forelse($resultModules as $module)
        @php
            getModules($module);
        @endphp
    @empty
    @endforelse



    <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu" style="display: none;">
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-menu"></i></span><span class="pcoded-mtext">Menu levels</span></a>
        <ul class="pcoded-submenu">
            <li class=""><a href="" class="">Menu Level 2.1</a></li>
            <li class="pcoded-hasmenu">
                <a href="#!" class="">Menu level 2.2</a>
                <ul class="pcoded-submenu">
                    <li class=""><a href="" class="">Menu level 3.1</a></li>
                    <li class=""><a href="" class="">Menu level 3.2</a></li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

