@php
    use App\Http\Controllers\Controller;
@endphp
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="/assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="/assets/images/logo-dark.png" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="/assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="/assets/images/logo-light.png" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ (request()->is('dashboard')) ? 'active' : '' }}" href="/dashboard">
                        <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                </li>
                @if (!empty(Controller::checkRole('35', 'VIEW')))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ (request()->is('orderlist')) ? 'active' : '' }}" href="/orderlist">
                            <i class="ri-shopping-cart-2-fill"></i> <span data-key="t-dashboards">การสั่งซื้อ</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link menu-link {{request()->is('product') || request()->is('productview') ? 'active' : ''}}" 
                        href="#sidebarProduct" data-bs-toggle="collapse" role="button" aria-controls="sidebarProduct">
                        <i class="las la-cubes"></i> <span data-key="t-landing">สินค้าหลัก</span>
                    </a>
                    <div class="collapse menu-dropdown {{request()->is('product') || request()->is('productview') || request()->is('option') ? 'show' : ''}}" id="sidebarProduct">
                        <ul class="nav nav-sm flex-column">
                            @if (!empty(Controller::checkRole('28', 'VIEW')))
                                <li class="nav-item">
                                    <a href="/product/index" class="nav-link {{ (request()->is('product')) ? 'active' : '' }}" data-key="t-one-page">สินค้า</a>
                                </li>
                            @endif
                            @if (!empty(Controller::checkRole('27', 'VIEW')))
                                <li class="nav-item {{ (request()->is('option')) ? 'active' : '' }}">
                                    <a href="/option" class="nav-link {{ (request()->is('option')) ? 'active' : '' }}" data-key="t-one-page">ตัวเลือกสินค้า</a>
                                </li>
                            @endif
                            <li class="nav-item {{ (request()->is('category')) ? 'active' : '' }}">
                                <a href="/category" class="nav-link {{ (request()->is('category')) ? 'active' : '' }}" data-key="t-one-page">หมวดหมู่สินค้า</a>
                            </li>
                            <li class="nav-item {{ (request()->is('brand')) ? 'active' : '' }}">
                                <a href="/brand" class="nav-link {{ (request()->is('brand')) ? 'active' : '' }}" data-key="t-one-page">แบรนด์</a>
                            </li>
                            <li class="nav-item {{ (request()->is('supplier')) ? 'active' : '' }}">
                                <a href="/supplier" class="nav-link {{ (request()->is('supplier')) ? 'active' : '' }}" data-key="t-one-page">ผู้ผลิต/จำหน่าย</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{request()->is('shelf/index') ? 'active' : ''}}" 
                        href="#sidebardisplay" data-bs-toggle="collapse" role="button" aria-controls="sidebardisplay">
                        <i class="mdi mdi-monitor"></i> <span data-key="t-landing">แสดงบนเว็บ</span>
                    </a>
                    <div class="collapse menu-dropdown {{request()->is('shelf/index') ? 'show' : ''}}" id="sidebardisplay">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item {{ (request()->is('shelf/index')) ? 'active' : '' }}">
                                <a href="/shelf/index" class="nav-link {{ (request()->is('shelf/index')) ? 'active' : '' }}" data-key="t-one-page">ชั้นวาง</a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    @php
                        $setting = "";
                        $show_set = "";
                        if(request()->is('usergroup') || request()->is('users')){
                            $setting = "active";
                            $show_set = "show";
                        }
                    @endphp
                    <a class="nav-link menu-link {{$setting}}" href="#sidebarLanding" data-bs-toggle="collapse" role="button" aria-controls="sidebarLanding">
                        <i class="ri-settings-4-fill"></i> <span data-key="t-landing">ตั้งค่า</span>
                    </a>
                    <div class="collapse menu-dropdown {{$show_set}}" id="sidebarLanding">
                        <ul class="nav nav-sm flex-column">
                            @if (!empty(Controller::checkRole('28', 'VIEW')))
                                <li class="nav-item">
                                    <a href="/usergroup" class="nav-link {{ (request()->is('usergroup')) ? 'active' : '' }}" data-key="t-one-page">สิทธิ์กลุ่มผู้ใช้งาน</a>
                                </li>
                            @endif
                            @if (!empty(Controller::checkRole('27', 'VIEW')))
                                <li class="nav-item {{ (request()->is('users')) ? 'active' : '' }}">
                                    <a href="/users" class="nav-link {{ (request()->is('users')) ? 'active' : '' }}" data-key="t-one-page">ผู้ใช้งาน</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->