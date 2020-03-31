  <header class="main-header">

    <!-- Logo -->
    <a href="./index.php" class="logo" >
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>P</b>DB</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Promobot</b>DB</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" >
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Навигация</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
    <div class="navbar-custom-menu">
     <ul class="nav navbar-nav">
         
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo ($userdata['avatar']!=0) ? './img/avatar/'.$userdata['avatar'] : './img/bot-32x32.png';?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $userdata['user_name']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="./img/bot-192x192.png" class="img-circle" alt="User Image">

                <p>
                  <?php echo $userdata['user_name']; ?> - <?php echo $userdata['group']; ?>
                
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Профиль</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat" onclick="delete_cookie('hash');">Выйти</a>
                  
                  <script>
                     function delete_cookie ( cookie_name )
                        {
                          var cookie_date = new Date ( );  // Текущая дата и время
                          cookie_date.setTime ( cookie_date.getTime() - 1 );
                          document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
                          window.location.href = "./login.php";  
                          
                        }

                  </script>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          
        </ul>
    </div>  
    </nav>
  </header>