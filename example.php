<?php
ini_set ('display_errors', true);

require_once 'Pie.php';

$p = new Pie();
$p->data[] = array ('day', 25);
$p->data[] = array ('night', 75);

$p->draw('png');

# $p->draw('tex');

# --- Other options --- #

# $p->file="";
# $p->title="";
# $p->showPercent=true;
# $p->fontFile=".../font.ttf";
# $p->fontSize=;
# $p->imgWidth=;﻿
# $p->imgHeight=;
# $p->pdfWidth=;
