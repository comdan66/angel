<!DOCTYPE html>
<html lang="zh">
  <head>
    <?php echo isset ($meta_list) ? $meta_list : ''; ?>

    <title><?php echo isset ($title) ? $title : ''; ?></title>

<?php echo isset ($css_list) ? $css_list : ''; ?>

<?php echo isset ($js_list) ? $js_list : ''; ?>

  </head>
  <body lang="zh-tw">
    <?php echo isset ($hidden_list) ? $hidden_list : ''; ?>

    <div id='container' class=''>
      <div id='main_row'>
        <div id='left_side'>
          
          <header>
            <a href='<?php echo base_url ();?>'>👼</a>
            <span>小添屎管理後台!</span>
          </header>

          <div id='login_user'>
            <figure class='_i'>
              <img src="<?php echo User::current ()->avatar ();?>">
            </figure>
            <div>
              <span>Hi, 鏟屎官 您好!</span>
              <span><?php echo User::current ()->name;?></span>
            </div>
          </div>

          <ul id='main_menu'>
      <?php if (User::current ()->in_roles (array ('keyword'))) { ?>
            <li>
              <label>
                <input type='checkbox' />
                <span class='icon-f'>添屎關鍵字</span>
                <ul>
                  <li><a href="<?php echo $url = base_url ('admin', 'keywords');?>" class='icon-fa<?php echo $now_url == $url ? ' active' : '';?>'>關鍵字管理</a></li>
                </ul>
              </label>
            </li>
      <?php }
            if (User::current ()->in_roles (array ('admin'))) { ?>
              <li>
                <label>
                  <input type='checkbox' />
                  <span class='icon-u'>權限系統</span>
                  <ul>
                    <li><a href="<?php echo $url = base_url ('admin', 'admins');?>" class='icon-u<?php echo $now_url == $url ? ' active' : '';?>'>管理員列表</a></li>
                  </ul>
                </label>
              </li>
      <?php } ?>
          </ul>

        </div>
        <div id='right_side'>
          <div id='top_side'>
            <button type='button' id='hamburger' class='icon-m'></button>
            <span>
              <a href='<?php echo base_url ('logout');?>' class='icon-o'></a>
            </span>
          </div>
          <div id='main'>
      <?php if ($_flash_danger = Session::getData ('_flash_danger', true)) { ?>
              <div id='_flash_danger'><?php echo $_flash_danger;?></div>
      <?php } else if ($_flash_info = Session::getData ('_flash_info', true)) { ?>
              <div id='_flash_info'><?php echo $_flash_info;?></div>
      <?php }?>
      <?php echo isset ($content) ? $content : ''; ?>
          </div>
          <div id='bottom_side'>
            後台版型設計 by <a href='http://www.ioa.tw/' target='_blank'>OA Wu</a>
          </div>
        </div>
      </div>
    </div>
    
    <div id='loading'>
      <div class='cover'></div>
      <div class='contant'>編譯中，請稍候..</div>
    </div>
  </body>
</html>