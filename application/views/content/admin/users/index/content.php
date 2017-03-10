<header>
  <div class='title'>
    <h1>會員</h1>
    <p>會員管理</p>
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
              <option value='<?php echo $option['value'];?>'<?php echo (is_numeric ($column['value']) && ($column['value'] == $option['value'])) || ($option['value'] === $column['value']) ? ' selected' : '';?>><?php echo $option['text'];?></option>
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
    <h2>會員列表</h2>
  </header>

  <div class='content'>

    <table class='table'>
      <thead>
        <tr>
          <th width='50' class='center'>#</th>
          <th width='130'>名稱</th>
          <th width='170'>信箱</th>
          <th >權限</th>
          <th width='90' class='right'>登入次數</th>
          <th width='90' class='right'>加入時間</th>
          <th width='50' class='right'>修改</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($objs) {
          foreach ($objs as $obj) { ?>
            <tr>
              <td class='center'><?php echo $obj->id;?></td>
              <td><?php echo anchor ('https://www.facebook.com/' . $obj->uid, $obj->name, 'target="_blank"');?></td>
              <td><?php echo $obj->email;?></td>
              <td><?php echo implode (',', $obj->role_names ());?></td>
              <td class='right'><?php echo $obj->login_count;?></td>
              <td class='right'><?php echo $obj->created_at->format ('Y-m-d H:i:s');?></td>
              <td class='right'>
                <a class='icon-e' href="<?php echo base_url ($uri_1, $obj->id, 'edit');?>"></a>
              </td>
            </tr>
    <?php }
        } else { ?>
          <tr>
            <td colspan='5' class='no_data'>沒有任何資料。</td>
          </tr>
  <?php } ?>
      </tbody>
    </table>

    <div class='pagination'>
      <?php echo $pagination;?>
    </div>

  </div>
</div>

