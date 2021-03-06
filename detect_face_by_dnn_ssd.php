<?php

use CV\Scalar, CV\Size;
use function CV\{imread, imwrite, rectangle};


$src = imread("images/faces.jpg");

$blob = \CV\DNN\blobFromImage($src, 1, new Size(300, 300), new Scalar(104, 177, 123), true, false);

$net = \CV\DNN\readNetFromCaffe('models/ssd/res10_300x300_ssd_deploy.prototxt', 'models/ssd/res10_300x300_ssd_iter_140000.caffemodel');

$net->setInput($blob, "");

$r = $net->forward();

var_export($r->shape);

$scalar = new Scalar(0, 0, 255);
for ($i = 0; $i < $r->shape[2]; $i++) {
    $confidence = $r->atIdx([0,0,$i,2]);
    //var_export($confidence);echo "\n";
    if ($confidence > 0.2) {
        $startX = $r->atIdx([0,0,$i,3]) * $src->cols;
        $startY = $r->atIdx([0,0,$i,4]) * $src->rows;
        $endX = $r->atIdx([0,0,$i,5]) * $src->cols;
        $endY = $r->atIdx([0,0,$i,6]) * $src->rows;

        rectangle($src, $startX, $startY, $endX, $endY, $scalar, 3);
    }
}


$data = [];

imwrite("results/_detect_face_by_dnn_ssd.jpg", $src);
