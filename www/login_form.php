<form id="home" method="post" title="Login" target="_self" 
      action="index.php" class="panel" selected="true">
  <div style="passing:5px; float: left; ">
    <img src="logo.png"/>
  </div>
  <div style="padding-left: 100px;padding-top: 20px;">
    <?php if(!empty($_SESSION['ses_msg']))
         echo $_SESSION['ses_msg'];
       else
         echo '&nbsp;';
    ?>
  </div>
  <fieldset style="clear: both;">
    <div class="row">
      <label>Username:</label>
      <input type="text" name="username" autocapitalize="off" />
    </div>
    <div class="row">
      <label>Password:</label>
      <input type="password" name="password" />
    </div>
  </fieldset>
  <input type="submit" value="Log in" class="whiteButton">
</form>
