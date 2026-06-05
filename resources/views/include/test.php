<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

<div class="text-center navbar-brand-wrapper d-flex align-items-center">

  <a class="navbar-brand brand-logo" href="{{URL::to('')}}/home"><img src="<?= URL::to('img/logo.png') ?>"></a>

  <a class="navbar-brand brand-logo-mini" href="{{URL::to('')}}/home"><img src="<?= URL::to('img/logo.png') ?>"></a>

</div>

<div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

  <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

    <span class="mdi mdi-menu"></span>

  </button>

  <ul class="navbar-nav mr-lg-2">

    <li class="nav-item nav-search d-none d-lg-block">
      <form class="navbar-form navbar-left" method="GET" action="<?php echo URL::to('/'); ?>/search" role="search" id="searchAlls">
        <div class="input-group">

          <div class="input-group-prepend">

            <span class="input-group-text" id="search" onclick="$('#searchAlls').submit();">

              <i class="mdi mdi-magnify"></i>

            </span>

          </div>

          <input type="text" class="form-control" placeholder="Search for anything..." name="search_global" id="vsearch_id" value="<?php if (isset($search) && $search != '') {
                                                                                                                                     echo $search;
                                                                                                                                   } ?>" aria-label="search" aria-describedby="search">

        </div>
      </form>
    </li>

  </ul>

  <ul class="navbar-nav navbar-nav-right">

    <li class="nav-item dropdown">
      <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="true">
        <i class="mdi mdi-bell-outline mx-0"></i>
        <!--{{  auth()->user()->unreadNotifications->count()}}-->
        <span class="countidsnewNo"></span>

      </a>
      <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
        <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
        <?php foreach (auth()->user()->unreadNotifications as $notification) {  ?>
          <a class="dropdown-item preview-item" data-notif-id="{{$notification->id}}" href="<?php echo $notification['data']['action']; ?>">

            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal"> Just now #<?php echo $notification['data']['record_id']; ?>
              </h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                <?php echo $notification['data']['body']; ?>
              </p>

            </div>
          </a>
        <?php } ?>


      </div>
    </li>
    <li class="nav-item nav-profile dropdown">

      <a class="nav-link" href="#" data-toggle="dropdown" id="profileDropdown">

        <img src="<?= URL::to('assets/images/faces/face5.jpg') ?>" alt="profile" />

      </a>

      <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

        <a class="dropdown-item" href="<?php echo URL::to('/change-password') ?>">

          <i class="mdi mdi-settings "></i>

          Change Password</a>

        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();

                                              document.getElementById('logout-form').submit();">

          <i class="mdi mdi-logout"></i>

          Logout </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

          {{ csrf_field() }}

        </form>

      </div>

    </li>



    <!-- <li class="nav-item nav-settings d-none d-lg-flex">

     <a class="nav-link" href="#">

       <i class="mdi mdi-dots-horizontal"></i>

     </a>

   </li> -->

  </ul>

  <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">

    <span class="mdi mdi-menu"></span>

  </button>

</div>

</nav>