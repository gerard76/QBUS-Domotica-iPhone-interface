<?php
include('/home/domotica/www/libs/global.lib.php');
@session_start();
if(empty($_SESSION['ses_user_id']))
{
  $_SESSION['ses_msg'] = 'Please login to continue';
  header('Location: index.php');
  exit();
}

qbus_update_status();
$pages = get_pages();
include('header.inc.php');
?>

    <ul id="home" title="Home" selected="true">
      <?php foreach($pages AS $page) { ?>
        <li><a href="#<?= $page['name']; ?>"><?= $page['name']; ?></a></li>
      <?php } ?>
      <li><a href="#camera" onclick="refreshSnapshot();">Camera</a></li>
      <li><a href="index.php?logout=1" target="_self">Logout</a></li>
    </ul>
    
    <?php foreach($pages AS $page) { 
      $modules = get_modules_by_page($page['id']); ?>
      <div id="<?= $page['name']; ?>" title="<?= $page['name']; ?>" class="panel">
        <fieldset>
          <?php foreach($modules AS $module)  {
            ?> 
            <?php switch($module['type']) {
              case 'dim': ?>
                <div class="row">
                <label><?= $module['name']; ?></label>
                <div class="dim" dimstate="<?= $module['status'] ?>"> 
                  <span class="thumb"></span>
                  <span class="dimOn" onclick="dim(<?= $module['id'] ?>, '1000', this);">100%</span>
                  <span class="dimHalf" onclick="dim(<?= $module['id'] ?>, '1115', this);">50%</span>
                  <span class="dimOff" onclick="dim(<?= $module['id'] ?>, '1255', this);">0%</span>
                </div>
                <? break;
              case 'toggle': ?>
                <div class="row">
                <label><?= $module['name']; ?></label>
                <div class="toggle" 
                     onclick="if(this.getAttribute('toggled') == 'true') 
                                postNumber(<?= $module['id']?>, '0255');
                              else
                                postNumber(<?= $module['id']?>, '0000');"
                     toggled="<?= $module['status'] == '0255'? 'true':'false'; ?>">
                  <span class="thumb"></span>
                  <span class="toggleOn">ON</span>
                  <span class="toggleOff">OFF</span>
                </div>
               <?
               break;
             case 'scene': ?>
               <div class="row" onclick="javascript:postNumber(<?= $module['id']?>, 7000)" >
               <label><?= $module['name']; ?></label>
            <? } ?>
            </div>
          <?php } ?>          
        </fieldset>
      </div>
    <?php } ?>
    <div id="camera" title="Camera" class="camera">
      <img id="snapshot" src="">
    </div>
<?php include('footer.inc.php'); ?>
