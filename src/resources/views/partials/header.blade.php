<header class="c-header">
    <div class="c-header_logo">
        <img src="{{ asset('images/logo.svg') }}" alt="">
    </div>
    <ul class="c-header_menuLists">
        @if (Auth::guard('web')->check())
        <li class="c-header_menuList"><a href="">勤怠</a></li>
        <li class="c-header_menuList"><a href="{{  route('user.attendance_list') }}">勤怠一覧</a></li>
        <li class="c-header_menuList"><a href="{{ route('stamp_correction_request') }}">申請</a></li>
        @elseif (Auth::guard('admin')->check())
        <li class="c-header_menuList"><a href="{{  route('admin.attendance_list') }}">勤怠一覧</a></li>
        <li class="c-header_menuList"><a href="{{  route('admin.staff_list') }}">スタッフ一覧</a></li>
        <li class="c-header_menuList"><a href="{{  route('stamp_correction_request') }}">申請一覧</a></li>

        @endif
        @if (Auth::guard('web')->check() || Auth::guard('admin')->check())
        <li class="c-header_menuList">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button>ログアウト</button>
            </form>
        </li>
        @endif
    </ul>
</header>
