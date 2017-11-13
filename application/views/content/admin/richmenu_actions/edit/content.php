<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>>修改<?php echo $title;?></h1>

<div class='panel'>
  <form class='form-type1 loading' action='<?php echo base_url ($uri_1, $parent->id, $uri_2, $obj->id);?>' method='post' enctype='multipart/form-data'>
    <input type='hidden' name='_method' value='put' />

    <div class='row'>
      <b class='need'>X 點座標</b>
      <input type='number' name='x' value='<?php echo isset ($posts['x']) ? $posts['x'] : $obj->x;?>' placeholder='請輸入 X 點座標..' maxlength='4' min='0' max='2500' required title='輸入 X 點座標!' autofocus />
    </div>

    <div class='row'>
      <b class='need'>Y 點座標</b>
      <input type='number' name='y' value='<?php echo isset ($posts['y']) ? $posts['y'] : $obj->y;?>' placeholder='請輸入 Y 點座標..' maxlength='4' min='0' max='1686' required title='輸入 Y 點座標!' />
    </div>

    <div class='row'>
      <b class='need'>寬度範圍</b>
      <input type='number' name='width' value='<?php echo isset ($posts['width']) ? $posts['width'] : $obj->width;?>' placeholder='請輸入 寬度範圍..' maxlength='4' min='0' max='2500' required title='輸入 寬度範圍!' />
    </div>
    
    <div class='row'>
      <b class='need'>高度範圍</b>
      <input type='number' name='height' value='<?php echo isset ($posts['height']) ? $posts['height'] : $obj->height;?>' placeholder='請輸入 高度範圍..' maxlength='4' min='0' max='1686' required title='輸入 高度範圍!' />
    </div>

    <div class='row'>
      <b class='need'>事件類型</b>
      <select name='action_type' id='action_type' data-picks='<?php echo json_encode (RichmenuAction::$actionPickTypeNames);?>' data-text='<?php echo isset ($posts['text']) ? $posts['text'] : $obj->text;?>' data-uri='<?php echo isset ($posts['uri']) ? $posts['uri'] : $obj->uri;?>' data-data='<?php echo isset ($posts['data']) ? $posts['data'] : $obj->data;?>' data-action_pick_type='<?php echo isset ($posts['action_pick_type']) ? $posts['action_pick_type'] : $obj->action_pick_type;?>'>
  <?php foreach (RichmenuAction::$actionTypeNames as $key => $name) { ?>
          <option value='<?php echo $key;?>'<?php echo (isset ($posts['action_type']) ? $posts['action_type'] : $obj->action_type) == $key ? ' selected' : '';?>><?php echo $name;?></option>
  <?php } ?>
      </select>
    </div>

    <div class='row area' id='action_type_area'></div>

    <div class='row'>
      <button type='submit'>確定送出</button>
      <button type='reset'>重新填寫</button>
      <a href='<?php echo base_url ($uri_1, $parent->id, $uri_2);?>'>回列表頁</a>
    </div>
  </form>
</div>
