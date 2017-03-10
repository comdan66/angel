<div class='panel'>
  <header>
    <h2>修改權限</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>

  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>'>
    <input type='hidden' name='_method' value='put' />

<?php foreach ($roles as $key => $role) { ?>
        <label class='checkbox'>
          <input type='checkbox' name='roles[]' value='<?php echo $key;?>'<?php echo $obj->roles && in_array ($key, column_array ($obj->roles, 'name')) ? ' checked' : '';?> />
          <span></span>
          <?php echo $role;?>
        </label>
<?php } ?>

    <div class='btns'>
      <div class='row n2'>
        <label></label>
        <div>
          <button type='reset'>取消</button>
          <button type='submit'>送出</button>
        </div>
      </div>
    </div>
  </form>
</div>
