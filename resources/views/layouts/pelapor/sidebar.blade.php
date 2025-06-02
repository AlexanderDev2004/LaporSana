<!-- Sidebar -->
 <div class="sidebar">
     <!-- Sidebar user panel (optional) -->
     <div class="user-panel mt-3 pb-3 mb-3 d-flex">
         <div class="image">
             <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                 alt="User Image">
         </div>
         <div class="info">
             <a href="#" class="d-block"><strong>{{ Auth::user()->name }}</strong></a>
         </div>
     </div>

     <!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview" role="menu" data-accordion="false">

        {{-- <li class="nav-header">Opsi</li> --}}

        <li class="nav-item">
            <a href="{{ url('/pelapor/dashboard') }}" class="nav-link {{ $active_menu == 'dashboard' ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-file"></i>
                <p>
                    Laporan
                    <i class="right fas fa-angle-left"></i> <!-- Icon dropdown -->
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/pelapor/laporan') }}" class="nav-link {{ $active_menu == 'laporan saya' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Laporan Saya</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/laporan/bersama') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Laporan Bersama</p>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link {{ $active_menu == 'feedback' ? '' : '' }}">
                <i class="nav-icon fas fa-star"></i>
                <p>Feedback</p>
            </a>
        </li>

        {{-- <li class="nav-header">Manajemen Data Pengguna</li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>
                    Pengguna
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/role-user') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Role User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ $active_menu == 'users' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Data User</p>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-header">Manajemen Data Fasilitas</li>

        <li class="nav-item">
            <a href="{{ url('/ruangan') }}" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>Ruangan</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/fasilitas') }}" class="nav-link">
                <i class="nav-icon fas fa-plug"></i>
                <p>Fasilitas</p>
            </a>
        </li> --}}

        <li class="nav-header">Logout</li>
        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </a>
        </li>
    </ul>
</nav>
<!-- /.sidebar-menu -->

 </div>
 <!-- /.sidebar -->