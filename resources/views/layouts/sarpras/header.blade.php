<!-- Preloader -->
<style>
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        width: 100%;
        height: 100%;
        background-color: #3b82f6; /* Sesuaikan tema */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .preloader img {
        width: 281px;
        height: 77px;
        border-radius: 8px;
        /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); */
        animation: shake 1.2s infinite;
    }

    .preloader p {
        margin-top: 16px;
        color: #ffee00;
        font-weight: bold;
        font-size: 16px;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-3px); }
        50% { transform: translateX(3px); }
        75% { transform: translateX(-2px); }
    }
</style>

<div class="preloader">
    <img src="{{ asset('LaporSana.png') }}" alt="Laporsana Logo">
    <p>Memuat LaporSana...</p>
</div>

  <!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- User Dropdown Menu -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->
          <li class="user-header bg-primary">
            <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            <p>
              {{ Auth::user()->name }}
              <small>{{ Auth::user()->role->roles_nama }}</small>
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer">
            <a href="{{ route('sarpras.profile.show') }}" class="btn btn-primary btn-flat">Edit Profile</a>
            <a href="{{ route('logout') }}" class="btn btn-danger btn-flat float-right">
              Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
</nav>
