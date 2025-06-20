 <!-- Sidebar -->
 <div class="sidebar">


     <!-- Sidebar Menu -->
     <nav class="mt-2">
         <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
             <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
             <li class="nav-item">
                 <a href="{{ route('teknisi.dashboard') }}" class="nav-link {{ $active_menu == 'dashboard' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-tachometer-alt"></i>
                     <p>Dashboard</p>
                 </a>
             </li>

             <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-file"></i>
                <p>
                    Tugas
                    <i class="right fas fa-angle-left"></i> <!-- Icon dropdown -->
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('/teknisi/pemeriksaan') }}" class="nav-link {{ $active_menu == 'pemeriksaan' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pemeriksaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/teknisi/perbaikan') }}" class="nav-link {{ $active_menu == 'perbaikan' ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Perbaikan</p>
                    </a>
                </li>
            </ul>
        </li>
             <li class="nav-item">
                 <a href="{{ route('teknisi.riwayat') }}" class="nav-link {{ $active_menu == 'riwayat' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-business-time"></i>
                     <p>Riwayat Tugas</p>
                 </a>
             </li>
         </ul>
     </nav>
     <!-- /.sidebar-menu -->
 </div>
 <!-- /.sidebar -->
