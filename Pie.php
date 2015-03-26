<?php

Class Pie
{

﻿  public function __construct () {}

﻿  public $data = array ();
﻿  public $imgWidth;
﻿  public $imgHeight;﻿
﻿  public $pdfWidth;
﻿  public $title;
﻿  public $showPercent=true;
﻿  public $fontFile;
﻿  public $fontSize;
﻿  public $file;

﻿  public $colorpalette = array ('#FF5454', '#FFFF54', '#8CD446', '#4DC742', '#45D2B0', '#46ACD3', '#438CCB', '#4262C7', '#5240C3', '#8C3FC0', '#D145C1', '#E64C8D', '#FF5454', '#FF8054', '#FFA054', '#FFB554', '#66CCFF', '#669CFF', '#7D66FF', '#C966FF', '#FF66BA', '#FF7066', '#FFA466', '#FFC666', '#FFE566', '#FAFF66', '#D2F854', '#4DF45C', '#8CD446', '#AC422B', '#D36A4D', '#DEA573', '#EEAA40', '#F9D066', '#EFC346', '#B6AB79', '#9DB28B', '#969D8D', '#A5AEAD', '#829ED0', '#7B92B1', '#CCD4DC', '#4C5672', '#9C844A', '#B59C6B', '#DFD0A9', '#FDF3D3', '#E8E8E6');


﻿  public function draw ($format)
﻿  {
﻿  ﻿  if ($this->data==null) return;

﻿  ﻿  if (!$this->file) $this->file = "pie.$format";

﻿  ﻿  $gpscript = "";


﻿  ﻿  # --- calculate total & merge slices having pc<1 in one slice named `other` ---
﻿
﻿  ﻿  $total = 0;
﻿  ﻿  $slices = array ();
﻿  ﻿  $otherValue = 0;
﻿
﻿  ﻿  foreach ($this->data as $row) $total += $row[1];

﻿  ﻿  foreach ($this->data as $row) ( round($row[1]/$total*100)>1 ? $slices [$row[0]] = $row[1] : $otherValue += $row[1] );﻿

﻿  ﻿  arsort ($slices);
﻿  ﻿  if ($otherValue>0) $slices['other'] = $otherValue;
﻿

﻿  ﻿  # --- terminal ---

﻿  ﻿  switch ($format)
﻿  ﻿  {
﻿  ﻿  ﻿  case 'png':
﻿  ﻿  ﻿
﻿  ﻿  ﻿  ﻿  if (!$this->imgWidth)  $this->imgWidth  = 1000;
﻿  ﻿  ﻿  ﻿  if (!$this->imgHeight) $this->imgHeight = 1000;
﻿  ﻿  ﻿  ﻿  $font = ($this->fontFile!=null && $this->fontSize!=null ? "font '{$this->fontFile}, {$this->fontSize}'" : "");

﻿  ﻿  ﻿  ﻿  $gpscript .=
﻿  ﻿  ﻿  ﻿  ("
﻿  ﻿  ﻿  ﻿  ﻿  reset
﻿  ﻿  ﻿  ﻿  ﻿  set terminal pngcairo size {$this->imgWidth}, {$this->imgHeight} $font
﻿  ﻿  ﻿  ﻿  ﻿  set output '{$this->file}'
﻿  ﻿  ﻿  ﻿  ");

﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿
﻿  ﻿  ﻿  case 'tex':
﻿  ﻿  ﻿
﻿  ﻿  ﻿  ﻿  if (!$this->pdfWidth) $this->pdfWidth = 14;

﻿  ﻿  ﻿  ﻿  $gpscript .=
﻿  ﻿  ﻿  ﻿  ("
﻿  ﻿  ﻿  ﻿  ﻿  reset
﻿  ﻿  ﻿  ﻿  ﻿  set terminal epslatex color solid size {$this->pdfWidth}cm, {$this->pdfWidth}cm
﻿  ﻿  ﻿  ﻿  ﻿  set output '{$this->file}'
﻿  ﻿  ﻿  ﻿  ");

﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  }


﻿  ﻿  # --- graph global params ---
﻿
﻿  ﻿  $gpscript .=
﻿  ﻿  ("
﻿  ﻿  ﻿  pieradius=0.25
﻿  ﻿  ﻿  piecenter=0.5  # pie center for x&y with 0.5 pie is center in image

﻿  ﻿  ﻿  unset border
﻿  ﻿  ﻿  unset tics
﻿  ﻿  ﻿  unset key
﻿  ﻿  ﻿  set angles degree
﻿  ﻿  ﻿  set xrange [0:1]
﻿  ﻿  ﻿  set yrange [0:1]
﻿  ﻿  ﻿  set style fill solid 1.0 border rgb 'white'
﻿  ﻿  ");


﻿  ﻿  # --- draw pie ---
﻿  ﻿  ﻿
﻿  ﻿  list ($sliceid, $sliceAnglestart, $sliceAngleend) = array (0, 0, 0);
﻿  ﻿
﻿  ﻿  foreach ($slices as $label => $value)
﻿  ﻿  {
﻿  ﻿  ﻿  $sliceid++;
﻿  ﻿  ﻿  $pc = $value/$total*100;

﻿  ﻿  ﻿  #  draw slice
﻿  ﻿
﻿  ﻿  ﻿  $sliceAnglestart = $sliceAngleend;﻿
﻿  ﻿  ﻿  $sliceAngleend   = $sliceAnglestart + 360*$pc/100;

﻿  ﻿  ﻿  $color = $this->colorpalette[($sliceid-1)%sizeof($this->colorpalette)];
﻿  ﻿
﻿  ﻿  ﻿  $gpscript .=  ("set obj $sliceid circle at screen piecenter,piecenter size screen pieradius front arc [$sliceAnglestart:$sliceAngleend] fc rgbcolor '$color'\n");

﻿  ﻿  ﻿  # draw label
﻿  ﻿
﻿  ﻿  ﻿  $labelAngle   = $sliceAnglestart + 360*$pc/100/2;
﻿  ﻿
﻿  ﻿  ﻿  $justification = ( $labelAngle>90 && $labelAngle<270 ? 'right' : 'left' );
﻿  ﻿  ﻿  ﻿  ﻿  ﻿
﻿  ﻿  ﻿  if ($this->showPercent) $label = ( $labelAngle>90 && $labelAngle<270 ? round ($pc) .'% - '. $label : $label .' - '. round ($pc) .'%' );
﻿  ﻿  ﻿  if ($format=='tex')     $label = escapetex ($label);
﻿  ﻿
﻿  ﻿  ﻿  $gpscript .=  ("set label $sliceid \"$label\" at screen piecenter+(pieradius+0.03)*cos($labelAngle), piecenter+(pieradius+0.03)*sin($labelAngle) $justification front\n");
﻿  ﻿  }

﻿  ﻿  $gpscript .=
﻿  ﻿  ("
﻿  ﻿  ﻿  plot 2
﻿  ﻿  ﻿  set output
﻿  ﻿  ");


﻿  ﻿  # --- execute gpscript ---

﻿  ﻿  file_put_contents ( 'draw.gp', $gpscript );
﻿  ﻿  exec ("/usr/bin/gnuplot draw.gp 2> draw.log");

﻿  }

}



/*
   escape reserved latex characters
*/
function escapetex ($line)
{
﻿  $texreserved = array
﻿  (
﻿  ﻿  '\\' => '\\\textbackslash'﻿  ,
﻿  ﻿  '{'  => '\\\{',
﻿  ﻿  '}'  => '\\\}',
﻿  ﻿  '#'  => '\\\#',
﻿  ﻿  '%'  => '\\\%',
﻿  ﻿  '$'  => '\\\$',
﻿  ﻿  '^'  => '\\\textasciicircum',
﻿  ﻿  '&'  => '\\\&',
﻿  ﻿  '_'  => '\\\_',
﻿  ﻿  '~'  => '\\\textasciitilde',
﻿  );

﻿  foreach ($texreserved as $key => $value) $line = str_replace ($key, $value, $line);
﻿
﻿  return $line;
}
