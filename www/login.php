<?php

if($_REQUEST['logout'])
{
  @session_destroy();
  $msg = 'Logout succesful';
  include('login_form.php');
}
elseif($_SESSION['ses_user_id'])
  include('domotica.php');
elseif($_REQUEST['username'] || $_REQUEST['password'])
{
  // prevent session highjacking
  @session_destroy();
  session_start();
  if($user = verify_user($_REQUEST['username'], $_REQUEST['password']))
  {
    $_SESSION['ses_user_id'] = $user['id'];
    include('domotica.php');
  }
  else
  {
    $msg = 'Invalid username or password';
    unset($_SESSION['ses_user_id']);
    include('login_form.php');
  }
}
else 


?>
