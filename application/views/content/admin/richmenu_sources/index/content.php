<h1<?php echo isset ($icon) && $icon ? ' class="' . $icon . '"' : '';?>><?php echo $title;?>列表</h1>

<div class='panel back'>
  <a class='icon-keyboard_arrow_left' href='<?php echo $_url;?>'>上層表頁</a>
</div>

<div class='search'>
  <input type='checkbox' id='search_conditions' class='hckb'<?php echo $isSearch = array_filter (column_array ($searches, 'value'), function ($t) { return $t !== null; }) ? ' checked' : '';?> />
  
  <div class='left'>
    <label class='icon-search' for='search_conditions'></label>
    <span><b>搜尋條件：</b><?php echo $isSearch ? implode (',', array_filter (array_map (function ($search) { return $search['value'] !== null ? $search['text'] : null; }, $searches), function ($t) { return $t !== null; })) : '無';?>，共 <b><?php echo number_format ($total);?></b> 筆。</span>
  </div>

  <div class='right'>
    <a class='icon-r' href='<?php echo base_url ($uri_1, 'add');?>'>新增</a>
  </div>

  <form class='conditions'>
<?php
    foreach ($searches as $name => $search) {
      if ($search['el'] == 'input') { ?>
        <input type='<?php echo isset ($search['type']) ? $search['type'] : 'text';?>' name='<?php echo $name;?>' placeholder='依照<?php echo $search['text'];?>搜尋..' value='<?php echo $search['value'] === null ? '' : $search['value'];?>'>
<?php }
      if ($search['el'] == 'select' && $search['items']) { ?>
        <select name='<?php echo $name;?>'>
          <option value=''<?php echo $search['value'] === null ? '' : ' selected';?>>依照<?php echo $search['text'];?>搜尋</option>
    <?php foreach ($search['items'] as $item) { ?>
            <option value='<?php echo $item['value'];?>'<?php echo ($search['value'] !== null) && ($item['value'] == $search['value']) ? ' selected' : '';?>><?php echo $item['text'];?></option>
    <?php } ?>
        </select>
<?php }
      if ($search['el'] == 'checkbox' && $search['items']) { ?>
        <div class='checkboxs' title='依照<?php echo $search['text'];?>搜尋'>
<?php     foreach ($search['items'] as $item) { ?>
            <label class='checkbox'>
              <input type='checkbox' name='<?php echo $name;?>' value='<?php echo $item['value'];?>'<?php echo ($search['value'] !== null) && (!is_array ($search['value']) ? $item['value'] == $search['value'] : in_array ($item['value'], $search['value'])) ? ' checked' : '';?>>
              <span></span>
              <?php echo $item['text'];?>
            </label>
<?php     } ?>
        </div>
<?php }
    } ?>

    <div class='btns'>
      <button type='submit'>搜尋</button>
      <a href='<?php echo base_url ($uri_1);?>'>取消</a>
    </div>

  </form>
</div>

<div class='panel'>
  <table class='table-list'>
    <thead>
      <tr>
        <th width='60' class='center'>挑選</th>
        <th width='150' class='left'>名稱</th>
        <th class='left'>Richmenu 名稱</th>
        <th width='80' class='right'>移除設定</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($objs as $obj) { ?>
        <tr>
          <td class='center'>
            <label class='checkbox'>
              <input type='checkbox' data-id='<?php echo $obj->id;?>' data-title='<?php echo $obj->title;?>' />
              <span></span>
            </label>
          </td>
          <td class='left'><?php echo $obj->title;?></td>
          <td class='left'><?php echo $obj->richmenu ? $obj->richmenu->name : '';?></td>
          <td class='right'><?php echo $obj->richmenu ? '<a class="icon-minus" href="' . base_url ($uri_1, $parent->id, $uri_2, $obj->id) . '" data-alert="確定要 Richmenu 移除？" data-method="delete"></a>' : '';?></td>
        </tr>
<?php } ?>
    </tbody>
  </table>

</div>

<div class='pagination'><?php echo $pagination;?></div>

<input type='checkbox' id='_b' />

<form id='choice-box' method='post' data-id='<?php echo $parent->id;?>'>
  <header>
    <a class='icon-bin'></a>
    <div data-cnt='0'>選擇列表</div>
    <label for='_b'></label>
  </header>

  <div></div>
  <button type='submit'>設定<i class='icon-keyboard_arrow_right'></i></button>
</form>
