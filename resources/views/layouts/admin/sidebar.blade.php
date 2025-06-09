 <!-- Sidebar -->
 <div class="sidebar">

     <!-- Sidebar Menu -->
     <nav class="mt-2">
         <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
             <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
             <li class="nav-item">
                 <a href="{{ route('admin.dashboard') }}"
                     class="nav-link {{ $active_menu == 'dashboard' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-tachometer-alt"></i>
                     <p>Dashboard</p>
                 </a>
             </li>
             <li class="nav-header">Manajemen Data Pengguna</li>
             <li class="nav-item">
                 <a href="{{ route('admin.roles.index') }}"
                 class="nav-link {{ $active_menu == 'roles' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-layer-group"></i>
                     <p>Role User</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('admin.users.index') }}"
                     class="nav-link {{ $active_menu == 'users' ? 'active' : '' }}">
                     <i class="nav-icon far fa-user"></i>
                     <p>Data User</p>
                 </a>
             </li>
             <li class="nav-header">Manajemen Data Fasilitas</li>
             <li class="nav-item">
                 <a href="{{ route('admin.lantai.index') }}"
                     class="nav-link {{ $active_menu == 'lantai' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-square"></i>
                     <p>Lantai</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('admin.ruangan.index') }}"
                     class="nav-link {{ $active_menu == 'ruangan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-home"></i>
                     <p>Ruangan</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('admin.fasilitas.index') }}"
                     class="nav-link {{ $active_menu == 'fasilitas' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-plug"></i>
                     <p>Fasilitas</p>
                 </a>
             </li>
             <li class="nav-header">Arsip</li>
             <li class="nav-item">
                 <a href="{{ route('admin.laporan.index') }}"
                 class="nav-link  {{ $active_menu == 'laporan' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-file"></i>
                     <p>Laporan</p>
                 </a>
             </li>
         </ul>
     </nav>
     <!-- /.sidebar-menu -->
 </div>
 <!-- /.sidebar -->
