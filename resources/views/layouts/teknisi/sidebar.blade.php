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
                  <a href="{{ route('teknisi.index') }}" class="nav-link {{ $active_menu == 'index'  ? 'active' : '' }}">
                     <i class="nav-icon fas fa-layer-group"></i>
                     <p>Tugas</p>
                 </a>
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
