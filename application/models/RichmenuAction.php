<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class RichmenuAction extends OaModel {

  static $table_name = 'richmenu_actions';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('richmenu', 'class_name' => 'Richmenu'),
  );
  
  static $after_save = array ('reset');
  static $after_destroy = array ('reset');

  const ACTION_TYPE_1 = 1;
  const ACTION_TYPE_2 = 2;
  const ACTION_TYPE_3 = 3;
  const ACTION_TYPE_4 = 4;

  static $actionTypeNames = array (
    self::ACTION_TYPE_1 => '訊息',
    self::ACTION_TYPE_2 => '網址',
    self::ACTION_TYPE_3 => '回傳',
    self::ACTION_TYPE_4 => '時間',
  );

  static $actionTypes = array (
    self::ACTION_TYPE_1 => 'message',
    self::ACTION_TYPE_2 => 'uri',
    self::ACTION_TYPE_3 => 'postback',
    self::ACTION_TYPE_4 => 'datetimepicker',
  );

  const ACTION_PICK_MODE_1 = 1;
  const ACTION_PICK_MODE_2 = 2;
  const ACTION_PICK_MODE_3 = 3;

  static $actionPickTypeNames = array (
    self::ACTION_PICK_MODE_1 => '日期時間 (Y-m-d H:i)',
    self::ACTION_PICK_MODE_2 => '日期 (Y-m-d)',
    self::ACTION_PICK_MODE_3 => '時間 (H:i)',
  );

  static $actionPickTypes = array (
    self::ACTION_PICK_MODE_1 => 'datetime',
    self::ACTION_PICK_MODE_2 => 'date',
    self::ACTION_PICK_MODE_3 => 'time',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function reset () {
    return !$this->richmenu || $this->richmenu->reset ();
  }
  public function destroy () {
    return $this->delete ();
  }
  public function format () {
    $action = array ('type' => RichmenuAction::$actionTypes[$this->action_type]);
    
    switch ($this->action_type) {
      case RichmenuAction::ACTION_TYPE_1:
        $action = array_merge ($action, array ('text' => $this->text));
        break;

      case RichmenuAction::ACTION_TYPE_2:
        $action = array_merge ($action, array ('uri' => $this->uri));
        break;

      case RichmenuAction::ACTION_TYPE_3:
        $action = array_merge ($action, array ('data' => $this->data));
        break;

      case RichmenuAction::ACTION_TYPE_4:
        $action = array_merge ($action, array ('mode' => RichmenuAction::$actionPickTypes[$this->action_pick_type], 'data' => $this->data));
        break;
      
      default: return array (); break;
    }
    return array (
      'bounds' => array (
          'x' => $this->x,
          'y' => $this->y,
          'width' => $this->width,
          'height' => $this->height,
        ),
      'action' => $action,
    );
  }
}