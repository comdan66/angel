<div id='main'>
  <div id='users'>
    <?php
    foreach (Source::find ('all', array ('select' => 'title, sid, bio', 'conditions' => array ('status = ? AND title != ?', Source::STATUS_JOIN, ''))) as $source) { ?>
      <a data-id='<?php echo $source->sid;?>'>
        <i class='icon-r'></i>
        <figure><img src='<?php echo res_url ('res', 'image', 'avatar.png');?>'></figure>
        <b><?php echo $source->title;?></b>
        <span><?php echo $source->bio;?></span>
      </a>
    <?php
    } ?>
  </div>
  <div id='room'>
    <div id='title'></div>
    <div id='msgs'></div>
    <form id='fm'>
      <div>
        <!-- <a class='icon-gr'></a>
        <a class='icon-lo'></a>
        <a class='icon-me'></a> -->
      </div>
      <input type='text' id='in' placeholder='想說些什麼？' maxlength='2000' pattern='.{1,2000}' required title='輸入點內容吧!' autofocus />
    </form>
  </div>
</div>