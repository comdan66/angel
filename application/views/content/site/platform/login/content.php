<div class='login' id='login'>
  <h1>👼 添屎管理後台 v1</h1>
<?php 
  if (User::current () && !User::current ()->is_login ()) { ?>
    <div class='m'>您已經登入成功。<br/>請<a href='mailto:teresa@zeusdesign.com.tw?subject=[添屎後台] 關於登入問題&body=Hi 管理員,%0D%0A%0D%0A 關於後台登入，請您幫我開啟登入的權限吧..'>管理員</a>為您開啟權限吧！</div>
<?php 
  } else if ($_flash_info = Session::getData ('_flash_info', true)) { ?>
    <div class='m'><?php echo $_flash_info;?></div>
<?php 
  } else if ($_flash_message = Session::getData ('_flash_message', true)) { ?>
    <div class='e'><?php echo $_flash_message;?></div>
<?php 
  } else { ?>
    <div class='r'>添屎管理後台系統是使用 Facebook 登入！<br/>如有任何問題歡迎洽詢工程人員或<a href='mailto:teresa@zeusdesign.com.tw?subject=[添屎後台] 關於登入問題&body=Hi 管理員,%0D%0A%0D%0A 關於後台登入，我有些問題..'>來信</a>告知。</div>
<?php 
  }?>
  <a id='fb-login' href='<?php echo Fb::loginUrl ('platform', 'fb_sign_in', 'admin');?>'>facebook 登入</a>
</div>
