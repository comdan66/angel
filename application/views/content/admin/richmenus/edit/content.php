<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row min'>
      <b class='need'>是否預設</b>
      <label class='switch'>
        <input type='checkbox' name='selected'<?php echo (isset ($posts['selected']) ? $posts['selected'] : $obj->selected) == Richmenu::SELECTED_2 ? ' checked' : '';?> value='<?php echo Richmenu::SELECTED_2;?>' />
        <span></span>
      </label>
    </div>

    <div class='row'>
      <b class='need'><?php echo $title;?>名稱</b>
      <input type='text' name='name' value='<?php echo isset ($posts['name']) ? $posts['name'] : $obj->name;?>' placeholder='請輸入<?php echo $title;?>名稱..' maxlength='300' pattern='.{1,300}' required title='輸入<?php echo $title;?>名稱!' autofocus />
    </div>

    <div class='row'>
      <b class='need'>顯示名稱</b>
      <input type='text' name='text' value='<?php echo isset ($posts['text']) ? $posts['text'] : $obj->text;?>' placeholder='請輸入顯示名稱..' maxlength='12' pattern='.{1,12}' required title='輸入顯示!' />
    </div>
    
    <div class='row'>
      <b class='need' data-title='預覽僅示意，未按比例(2500x1686 or 2500x843)。'><?php echo $title;?>封面</b>
      <div class='drop_img'>
        <img src='<?php echo $obj->cover->url ();?>' />
        <input type='file' name='cover' />
      </div>
    </div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1);?>'>回列表頁</a>
    </div>
  </form>
</div>
