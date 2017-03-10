<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Admin_controller extends Oa_controller {

  public function __construct () {
    parent::__construct ();

    if (!(User::current () && User::current ()->is_login ()))
      return redirect_message (array ('login'), array ());

    $this
         ->set_componemt_path ('component', 'admin')
         ->set_frame_path ('frame', 'admin')
         ->set_content_path ('content', 'admin')
         ->set_public_path ('public')

         ->set_title ("小添屎管理後台")

         ->_add_meta ()
         ->_add_css ()
         ->_add_js ()
         ->add_param ('now_url', base_url ('admin'));
         ;
  }

  protected function _get_pagination ($limit, $total, $configs) {
    $this->load->library ('pagination');
    return $this->pagination->initialize (array_merge (array ('total_rows' => $total, 'num_links' => 3, 'per_page' => $limit, 'uri_segment' => 0, 'base_url' => '', 'page_query_string' => false, 'first_link' => '第一頁', 'last_link' => '最後頁', 'prev_link' => '上一頁', 'next_link' => '下一頁', 'full_tag_open' => '<ul>', 'full_tag_close' => '</ul>', 'first_tag_open' => '<li class="f">', 'first_tag_close' => '</li>', 'prev_tag_open' => '<li class="p">', 'prev_tag_close' => '</li>', 'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'cur_tag_open' => '<li class="active"><a href="#">', 'cur_tag_close' => '</a></li>', 'next_tag_open' => '<li class="n">', 'next_tag_close' => '</li>', 'last_tag_open' => '<li class="l">', 'last_tag_close' => '</li>'), $configs))->create_links ();
  }
  protected function _build_excel ($objs, $infos) {
    $excel = new OAExcel ();
    
    $excel->getActiveSheet ()->getRowDimension (1)->setRowHeight (20);
    $excel->getActiveSheet ()->freezePaneByColumnAndRow (0, 2);
    $excel->getActiveSheet ()->getStyle ('A1:' . chr (65 + count ($infos) - 1) . '1')->applyFromArray (array ('fill' => array ('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'fff3ca'))));

    foreach ($objs as $i => $obj) {
      $j = 0;
      foreach ($infos as $info) {
        if ($i == 0) {
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_TOP);
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 1))->getFont ()->setName ('新細明體');
          $excel->getActiveSheet ()->SetCellValue (chr (65 + $j) . ($i + 1), $info['title']);
        }
        eval ('$val = ' . $info['exp'] . ';');
        
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_TOP);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getAlignment ()->setHorizontal (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getFont ()->setName ("新細明體");
        $excel->getActiveSheet ()->SetCellValue (chr (65 + $j) . ($i + 2), $val);
        $excel->getActiveSheet ()->getStyle (chr (65 + $j) . ($i + 2))->getNumberFormat ()->setFormatCode ($info['format']);
        $j++;
      }
    }
    return $excel;
  }

  private function _add_meta () {
    return $this->add_meta (array ('name' => 'robots', 'content' => 'noindex,nofollow'))
                ;
  }

  private function _add_css () {
    return $this->add_css ('https://fonts.googleapis.com/css?family=Open+Sans:400italic,400,700', false)
                ;
  }

  private function _add_js () {
    return $this->add_js ('https://www.gstatic.com/charts/loader.js', false)
                ->add_js (res_url ('res', 'js', 'admin.js'))
                ->add_js (res_url ('res', 'js', 'autosize_v3.0.8', 'autosize.min.js'))
                ->add_js (res_url ('res', 'js', 'ckeditor_d2015_05_18', 'ckeditor.js'), false)
                ->add_js (res_url ('res', 'js', 'ckeditor_d2015_05_18', 'config.js'), false)
                ->add_js (res_url ('res', 'js', 'ckeditor_d2015_05_18', 'adapters', 'jquery.js'), false)
                ;
  }
}