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
  private $bottomWidth = 58;
  private $topWidth = 10;
  private $lineInfos = array ();

  public function __construct () {
  }

  public function setWidth ($w) { $this->width = $w; return $this; }
  public function setHeight ($h) { $this->height = $h; return $this; }
  public function setData ($d) {
    $this->data = $d;
    return $this; }
  public function setBgColor ($c) { $this->color = $c; return $this; }
  public function setPadding ($p) { $this->padding = $p; return $this; }
  public function setCntY ($cntY) { $this->cntY = $cntY; return $this; }
  public function setTimesY ($timesY) { $this->timesY = $timesY; return $this; }
  public function setLineInfos ($key, $lineInfos) { $this->lineInfos[$key] = $lineInfos; return $this; }
  

  public function save ($path, $format) {
    $imgk = new Imagick ();
    $imgk->newImage ($this->width, $this->height, $this->color);

    $draw = new ImagickDraw ();
    $draw->translate (0, 0);

    $h = $this->height - $this->padding * 2;
    $w = $this->width - $this->padding * 2;
    
    // $vs - array ()
    $data = ($data = $this->data) ? array_map (function ($k) use ($data) { return array (max (column_array ($this->data, $k)), min (column_array ($this->data, $k))); }, array_keys ($this->lineInfos)) : array ();

    $max = max (column_array ($data, 0)) + 1000;
    $min = min (column_array ($data, 1)) - 1000;
    
    $stepY = ($h - $this->bottomWidth - $this->topWidth) / $this->cntY;
    $unitY = ceil (($max - $min) / $this->cntY);
    $tmp = new Imagick ();

    for ($i = 0; $i < $this->cntY + 1; $i++) { 
      $draw->setFillColor ($i % $this->timesY ? 'rgba(230, 230, 230, 1.00)' : 'rgba(160, 160, 160, 1.00)');
      $draw->line ($this->padding + $this->leftWidth, $this->padding + $h - $this->bottomWidth - $i * $stepY, $this->padding + $w, $this->padding + $h - $this->bottomWidth - $i * $stepY);
      // if (!($i % $this->timesY)) {
        // $draw->line ($this->padding + $this->leftWidth, $this->padding + $h - $this->bottomWidth - $i * $stepY, $this->padding + $w, $this->padding + $h - $this->bottomWidth - $i * $stepY);
      // }
      $draw->setTextAlignment (Imagick::ALIGN_RIGHT);
      $tD = $tmp->queryFontMetrics ($draw, $i * $unitY + $min);
      if ($i % $this->timesY) continue;
      $draw->setFillColor ('rgba(39, 40, 34, 1)');
      $draw->annotation ($this->padding + $this->leftWidth - 4, ($this->padding + $h - $this->bottomWidth - $i * $stepY) + ($tD['textHeight'] / 3), number_format ($i * $unitY + $min));
    }

    $keys = array_values (column_array ($this->data, 'title'));
    $stepX = ($w - $this->leftWidth - 16 * 2) / (count ($keys) - 1);

    $polylines = array_combine (array_keys ($this->lineInfos), array_map (function () { return array (); }, $this->lineInfos));

    $data = ($data = $this->data) ? array_combine (array_keys ($this->lineInfos), array_map (function ($k) use ($data) { return column_array ($data, $k); }, array_keys ($this->lineInfos))) : array ();
    foreach ($keys as $i => $value) {

      $x = $this->padding + $this->leftWidth + $i * $stepX + 16;
      $draw->setFillColor ('rgba(39, 40, 34, .200)');
      $draw->setTextAlignment (Imagick::ALIGN_RIGHT);
      // $draw->setTextAlignment (Imagick::ALIGN_CENTER);
      if ((count ($keys) - $i - 1) % ceil (count ($keys) / 8)) {
        $value = '';
      } else {
        $draw->line ($x, $this->padding, $x, $this->padding + $h - $this->bottomWidth);
      }
      $tD = $tmp->queryFontMetrics ($draw, $value);

      $draw->setFillColor ('rgba(39, 40, 34, 1)');
      
    // $draw->setFontSize (10);
      $imgk->annotateImage ($draw, $x + 8, $this->padding + $h - $this->bottomWidth + $tD['textHeight'] + 2 - 4, 0, $value);
      // $draw->annotation ($x - $tD['textWidth'] / 2, $this->padding + $h - $this->bottomWidth + $tD['textHeight'] + 2, $value);
      
      foreach ($this->lineInfos as $key => $lineInfo)
        array_push ($polylines[$key], array ('x' => $x, 'y' => $this->padding + $h - $this->bottomWidth - (($data[$key][$i] - $min) / ($max - $min)) * ($h - $this->bottomWidth - $this->topWidth)));
    }
    $draw->setFillColor ('transparent');
    $draw->setStrokeAntialias (true);
    foreach ($this->lineInfos as $key => $lineInfo) {
      $draw->setStrokeColor ($lineInfo['color']);
      $draw->polyLine ($polylines[$key]);
    }


    $draw->setTextAlignment (Imagick::ALIGN_LEFT);
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

    $polylines = array ();
    foreach (array_values ($this->lineInfos) as $i => $lineInfo) {
      $draw->setFontSize (14);
      $tD = $tmp->queryFontMetrics ($draw, $text = $lineInfo['title']);
      $draw->setFillColor ($lineInfo['color']);
      $draw->line ($this->padding + $this->leftWidth + array_sum ($polylines), $this->padding + $h - 10, $this->padding + $this->leftWidth + $tD['textWidth'] + 6 + array_sum ($polylines), $this->padding + $h - 10);
      $draw->line ($this->padding + $this->leftWidth + array_sum ($polylines), $this->padding + $h + 1 - 10, $this->padding + $this->leftWidth + $tD['textWidth'] + 6 + array_sum ($polylines), $this->padding + $h + 1 - 10);
      $imgk->annotateImage ($draw, $this->padding + $this->leftWidth + array_sum ($polylines) + 6 / 2, $this->padding + $h + $tD['textHeight'] + 2 - 8, 0, $text);
      array_push ($polylines, $tD['textWidth'] + 6 + 8);
    }

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