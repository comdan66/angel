<header>
  <div class='title'>
    <h1>關鍵字</h1>
    <p>管理</p>
  </div>

  <form class='select'>
    <button type='submit' class='icon-s'></button>

<?php 
    if ($columns) { ?>
<?php foreach ($columns as $column) {
        if (isset ($column['select']) && $column['select']) { ?>
          <select name='<?php echo $column['key'];?>'>
            <option value=''>請選擇 <?php echo $column['title'];?>..</option>
      <?php foreach ($column['select'] as $option) { ?>
              <option value='<?php echo $option['value'];?>'<?php echo $option['value'] === $column['value'] ? ' selected' : '';?>><?php echo $option['text'];?></option>
      <?php } ?>
          </select>
  <?php } else { ?>
          <label>
            <input type='text' name='<?php echo $column['key'];?>' value='<?php echo $column['value'];?>' placeholder='<?php echo $column['title'];?>搜尋..' />
            <i class='icon-s'></i>
          </label>
<?php   }
      }?>
<?php 
    } ?>

  </form>
</header>


<div class='panel'>
  <header>
    <h2>關鍵字列表</h2>
    <a href='<?php echo base_url ($uri_1, 'add');?>' class='icon-r'></a>
  </header>

  <div class='content'>


    <table class='table'>
      <thead>
        <tr>
          <th width='80'>#</th>
          <th width='200'>關鍵字 Pattern</th>
          <th >回覆內容</th>
          <th width='150'>回應類型</th>
          <th width='100'>針對類型</th>
          <th width='100'>權重</th>
          <th width='85' class='right'>修改/刪除</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td><?php echo $obj->id;?></td>
              <td><?php echo htmlentities ($obj->pattern);?></td>
              <td><?php echo implode ('', array_map (function ($content) { return '<div class="munit">' . $content->text . '</div>';}, $obj->contents));?></td>
              <td><?php echo isset (Keyword::$methodNames[$obj->method]) ? Keyword::$methodNames[$obj->method] : '';?></td>
              <td><?php echo isset (Keyword::$typeNames[$obj->type]) ? Keyword::$typeNames[$obj->type] : '';?></td>
              <td><?php echo $obj->weight;?></td>
              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
                /
                <a class='icon-t' href="<?php echo base_url ($uri_1, $obj->id);?>" data-method='delete'></a>
              </td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='7' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

