<header class="c-header">
    <div class="c-header_logo">
        <img src="{{ asset('images/logo.svg') }}" alt="">
    </div>
    <ul class="c-header_menuLists">
        <li class="c-header_menuList"><a href="">勤怠</a></li>
        <li class="c-header_menuList"><a href="{{ Auth::guard('web')->check() ? route('user.attendance_list') : ''; }}">勤怠一覧</a></li>
        <li class="c-header_menuList"><a href="{{ Auth::guard('web')->check() ? route('user.stamp_correction_request') : ''; }}">申請</a></li>
        <li class="c-header_menuList">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button>ログアウト</button>
            </form>
        </li>
    </ul>
</header>
