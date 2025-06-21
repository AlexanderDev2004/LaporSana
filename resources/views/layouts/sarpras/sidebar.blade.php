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
             {{-- <li class="nav-item">
                 <a href="{{ route('sarpras.spk.step_by_step') }}"
                     class="nav-link {{ $active_menu == 'spk_step_by_step' ? 'active' : '' }}">
                     <i class="nav-icon fas fa-calculator"></i>
                     <p>SPK</p>
                 </a>
             </li> --}}
             <li class="nav-header">Penugasan</li>
             <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                        Penugasan
                        <i class="right fas fa-angle-left"></i> <!-- Icon dropdown -->
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/sarpras/penugasan/pemeriksaan') }}" class="nav-link {{ $active_menu == 'pemeriksaan' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Pemeriksaan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/sarpras/penugasan/perbaikan') }}" class="nav-link {{ $active_menu == 'laporan bersama' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Perbaikan</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-history"></i>
                    <p>
                        Riwayat Penugasan
                        <i class="right fas fa-angle-left"></i> <!-- Icon dropdown -->
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/sarpras/pemeriksaan/riwayat') }}" class="nav-link {{ $active_menu == 'riwayat pemeriksaan' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Riwayat Pemeriksaan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/sarpras/perbaikan/riwayat') }}" class="nav-link {{ $active_menu == 'riwayat perbaikan' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Riwayat Perbaikan</p>
                        </a>
                    </li>
                </ul>
            </li>
             <li class="nav-header">laporan</li>
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
