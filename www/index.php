<?php
  include('libs/global.lib.php');
  session_start(); 
  $_SESSION['ses_msg'] = 'Please enter your credentials';
  if($_REQUEST['logout'])
  {
    @session_destroy();
    $_SESSION['ses_msg'] = 'Logged out';
  }
  elseif($_SESSION['ses_user_id'])
    header('Location: domotica.php');
  elseif($_REQUEST['username'] || $_REQUEST['password'])
  {
    // prevent session highjacking
    @session_destroy();
    session_start();
    if($user = verify_user($_REQUEST['username'], $_REQUEST['password']))
    {
      $_SESSION['ses_user_id'] = $user['id'];
      qbus_login();
      header('Location: domotica.php');
    }
    else
    {
      $_SESSION['ses_msg'] = 'Invalid username or password';
      unset($_SESSION['ses_user_id']);
    }
  }

  include('header.inc.php');
?>

<body>
  <div class="toolbar">
    <h1 id="pageTitle"></h1>
  </div>
  <?php include('login_form.php'); ?>
</body>
</html>

