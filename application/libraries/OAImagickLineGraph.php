<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* @author      OA Wu <comdan66@gmail.com>
* @copyright   Copyright (c) 2016 OA Wu Design
*/

class OAImagickLineGraph {
  private $width = 0;
  private $height = 0;
  private $data = array ();
  private $color = 'white';
  private $padding = 16;

  private $cntY = 48 ;
  private $timesY = 5;
  private $leftWidth = 48 ;
  private $bottomWidth = 40;
  private $topWidth = 10;
  private $lineColor = 10;

  public function __construct () {
  }

  public function setWidth ($w) { $this->width = $w; return $this; }
  public function setHeight ($h) { $this->height = $h; return $this; }
  public function setData ($d) {
    $this->data = $d;
    return $this; }
  public function setKeyData ($d) {
    $tmp = array ();
    foreach ($d as $key => $value) array_push ($tmp, array ('title' => $key, 'value' => $value));
    $this->data = $tmp;
    return $this; }
  public function setBgColor ($c) { $this->color = $c; return $this; }
  public function setPadding ($p) { $this->padding = $p; return $this; }
  public function setCntY ($cntY) { $this->cntY = $cntY; return $this; }
  public function setTimesY ($timesY) { $this->timesY = $timesY; return $this; }
  public function setLineColor ($lineColor) { $this->lineColor = $lineColor; return $this; }
  

  public function save ($path, $format) {
    $imgk = new Imagick ();
    $imgk->newImage ($this->width, $this->height, $this->color);

    $draw = new ImagickDraw ();
    $draw->translate (0, 0);

    $h = $this->height - $this->padding * 2;
    $w = $this->width - $this->padding * 2;
    
    $polyline = array ();
    
    $values = column_array ($this->data, 'value');
    $keys = column_array ($this->data, 'title');

    $max = max ($values);
    $min = min ($values);
    
    $stepY = ($h - $this->bottomWidth - $this->topWidth) / $this->cntY;
    $unitY = ceil (($max - $min) / $this->cntY);
    $tmp = new Imagick ();

    for ($i = 0; $i < $this->cntY + 1; $i++) { 
      $draw->setFillColor($i % $this->timesY ? 'rgba(230, 230, 230, 1.00)' : 'rgba(160, 160, 160, 1.00)');
      $draw->line ($this->padding + $this->leftWidth, $this->padding + $h - $this->bottomWidth - $i * $stepY, $this->padding + $w, $this->padding + $h - $this->bottomWidth - $i * $stepY);
      $draw->setTextAlignment (Imagick::ALIGN_RIGHT);
      $tD = $tmp->queryFontMetrics ($draw, $i * $unitY + $min);
      if ($i % $this->timesY) continue;
      $draw->setFillColor('rgba(39, 40, 34, 1)');
      $draw->annotation ($this->padding + $this->leftWidth - 4, ($this->padding + $h - $this->bottomWidth - $i * $stepY) + ($tD['textHeight'] / 3), number_format ($i * $unitY + $min));
    }

    $data = array_values ($values);

    $keys = array_values ($keys);
    $fW = $tmp->queryFontMetrics ($draw, $keys[0]); $fW = $fW['textWidth'];
    $lW = $tmp->queryFontMetrics ($draw, $keys[count ($keys) - 1]); $lW = $lW['textWidth'];
    $stepX = ($w - $this->leftWidth - 16 * 2) / (count ($this->data) - 1);

    foreach ($keys as $i => $value) {
      $x = $this->padding + $this->leftWidth + $i * $stepX + 16;
      $draw->setFillColor('rgba(39, 40, 34, .200)');
      $draw->line ($x, $this->padding, $x, $this->padding + $h - $this->bottomWidth);

      $draw->setTextAlignment (Imagick::ALIGN_LEFT);
      $tD = $tmp->queryFontMetrics ($draw, $value);
      $draw->setFillColor('rgba(39, 40, 34, 1)');
      $draw->annotation ($x - $tD['textWidth'] / 2, $this->padding + $h - $this->bottomWidth + $tD['textHeight'] + 2, $value);
      array_push ($polyline, array ('x' => $x, 'y' => $this->padding + $h - $this->bottomWidth - (($data[$i] - $min) / ($max - $min)) * ($h - $this->bottomWidth - $this->topWidth)));
    }
    $draw->setFillColor ('transparent');
    $draw->setStrokeColor ($this->lineColor);
    $draw->setStrokeAntialias (true);
    $draw->polyLine ($polyline);

    $draw->setStrokeColor ('transparent');
    $draw->setFont(FCPATH . 'res' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . 'monaco.ttf');
    $draw->setFontSize (10);
    $draw->setFillColor ('rgba(0, 0, 0, .8)');
    $tD = $tmp->queryFontMetrics ($draw, $text = 'By OA Wu.');
    $imgk->annotateImage ($draw, $x = ($this->padding + $w - $tD['textWidth']), $this->height - $this->padding + 2, 0, 'By OA Wu.');
    $draw->setFont(FCPATH . 'res' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . 'cwTeXHei-zhonly.ttf');
    $draw->setFontSize (16);
    $draw->setFillColor ('rgba(0, 0, 0, .5)');
    $tD = $tmp->queryFontMetrics ($draw, $text = '小添屎比特幣');
    $imgk->annotateImage ($draw, $x - $tD['textWidth'] - 8, $this->height - $this->padding + 1 + 2, 0, $text);
    $imgk->drawImage ($draw);
    
    $format = $format ? $format : ($format = pathinfo ($path, PATHINFO_EXTENSION));
    $imgk->setImageFormat ($format);
    $imgk->setFormat ($format);
    return $imgk->writeImages ($path, true);
  }

  public static function create ($w = 0, $h = 0) {
    $img = new OAImagickLineGraph ();
    if ($w) $img->setWidth ($w);
    if ($h) $img->setHeight ($h);
    return $img;
  }
}