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
             <li class="nav-header">Penugasan</li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.penugasan') }}" class="nav-link {{ $active_menu == 'penugasan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-tasks"></i>
                     <p>Penugasan</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.riwayat.penugasan') }}" class="nav-link {{ $active_menu == 'riwayat penugasan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-history"></i>
                     <p>Riwayat Penugasan</p>
                 </a>
             </li>
             <li class="nav-header">Laporan</li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.laporan') }}" class="nav-link {{ $active_menu == 'laporan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-file"></i>
                     <p>Laporan</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('sarpras.riwayat') }}" class="nav-link {{ $active_menu == 'riwayat laporan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-archive"></i>
                     <p>Riwayat Laporan</p>
                 </a>
             </li>
         </ul>
     </nav>
     <!-- /.sidebar-menu -->
 </div>
 <!-- /.sidebar -->
