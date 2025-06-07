 <!-- Sidebar -->
 <div class="sidebar">
     <!-- Sidebar user panel (optional) -->
     <div class="user-panel mt-3 pb-3 mb-3 d-flex">
         <div class="image">
             <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                 alt="User Image">
         </div>
         <div class="info">
             <a href="{{ route('sarpras.profile.show') }}" class="d-block"><strong>{{ Auth::user()->name }}</strong></a>
         </div>
     </div>

     <!-- Sidebar Menu -->
     <nav class="mt-2">
         <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
             <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
             <li class="nav-header">Opsi</li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.dashboard') }}" class="nav-link {{ $active_menu == 'dashboard' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-tachometer-alt"></i>
                     <p>Dashboard</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ url('logout') }}" class="nav-link">
                     <i class="nav-icon fas fa-sign-out-alt"></i>
                     <p>Logout</p>
                 </a>
             </li>
             <li class="nav-header">Laporan</li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.verifikasi') }}" class="nav-link {{ $active_menu == 'verifikasi laporan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-file"></i>
                     <p>Verifikasi Laporan</p>
                 </a>
             </li>

             <li class="nav-header">Manajemen Data Pengguna</li>
             <li class="nav-item">
                 <a href="{{ url('/') }}" class="nav-link">
                     <i class="nav-icon fas fa-layer-group"></i>
                     <p>Role User</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ url('/') }}" class="nav-link">
                     <i class="nav-icon far fa-user"></i>
                     <p>Data User</p>
                 </a>
             </li>
             <li class="nav-header">Manajemen Data Fasilitas</li>
             <li class="nav-item">
                 <a href="{{ url('/') }}" class="nav-link">
                     <i class="nav-icon fas fa-home"></i>
                     <p>Ruangan</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ url('/') }}" class="nav-link">
                     <i class="nav-icon fas fa-plug"></i>
                     <p>Fasilitas</p>
                 </a>
             </li>
         </ul>
     </nav>
     <!-- /.sidebar-menu -->
 </div>
 <!-- /.sidebar -->
