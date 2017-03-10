<div class='panel'>
  <header>
    <h2>修改文章分類</h2>
    <a href='<?php echo base_url ($uri_1);?>' class='icon-x'></a>
  </header>


  <form class='form' method='post' action='<?php echo base_url ($uri_1, $obj->id);?>'>
    <input type='hidden' name='_method' value='put' />
    <div class='row n2'>
      <label>* 關鍵字 Pattern</label>
      <div>
        <input type='text' name='pattern' value='<?php echo isset ($posts['pattern']) ? $posts['pattern'] : $obj->pattern;?>' placeholder='請輸入關鍵字或 正規Pattern..' maxlength='200' pattern='.{1,200}' required title='輸入關鍵字!' autofocus />
      </div>
    </div>
    <div class='row n2'>
      <label>* 回應類型</label>
      <div>
        <select name='method' id='method'>
    <?php if ($methodNames = Keyword::$methodNames) {
            foreach ($methodNames as $method => $name) { ?>
              <option value='<?php echo $method;?>'<?php echo (isset ($posts['method']) ? $posts['method'] : $obj->method) == $method ? ' selected': '';?>><?php echo $name;?></option>
      <?php }
          }?>
        </select>
        <span id='tip'>範例：我想找\s*<span>(</span><i>?P<?php echo htmlentities ('<keyword>');?></i>哈哈*<span>)</span><br/>Sub Pattern 前加 <b>?P<?php echo htmlentities ('<keyword>');?></b> 系統會把 Sub Pattern 當關鍵字<br/>如此一來 我想找哈哈*，<b>哈哈*</b>就會是關鍵字</span>
      </div>
    </div>
    <div class='row n2' id='keyowrd_content'>
      <label>* 回覆文字內容</label>
      <div>
        <div class='contents'><button type='button' class='_add' data-val='<?php echo json_encode ($contents);?>'>+</button></div>
      </div>
    </div>
    <div class='row n2'>
      <label>* 權重</label>
      <div>
        <input type='number' name='weight' value='<?php echo isset ($posts['weight']) ? $posts['weight'] : $obj->weight;?>' placeholder='請輸入權重..' maxlength='200' pattern='.{1,200}' required title='輸入權重!' />
      </div>
    </div>

    <div class='row n2'>
      <label>* 針對類型</label>
      <div>
        <select name='type'>
    <?php if ($typeNames = Keyword::$typeNames) {
            foreach ($typeNames as $type => $name) { ?>
              <option value='<?php echo $type;?>'<?php echo (isset ($posts['type']) ? $posts['type'] : $obj->type) == $type ? ' selected': '';?>><?php echo $name;?></option>
      <?php }
          }?>
        </select>
      </div>
    </div>

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
