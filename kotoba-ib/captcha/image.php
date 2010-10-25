<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/**
 * Скрипт, создающий изображение для капчи.
 */

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/misc.php';

// <editor-fold defaultstate="collapsed" desc="Font">
$font = array('а' => array(4, array(0, 2, 1, 1, 2, 1, 3, 2, 3, 5, 4, 6), array(3, 3, 1, 3, 0, 4, 0, 5, 1, 6, 2, 6, 3, 5)),
              'б' => array(3, array(3, 0, 0, 0, 0, 6, 2, 6, 3, 5, 3, 4, 2, 3, 0, 3)),
              'в' => array(3, array(0, 0, 0, 6, 2, 6, 3, 5, 3, 4, 2, 3, 0, 3, 2, 3, 3, 2, 3, 1, 2, 0, 0, 0)),
              'г' => array(3, array(3, 0, 0, 0, 0, 6)),
              'д' => array(4, array(0, 6, 0, 5, 4, 5, 4, 6), array(1, 5, 2, 0, 3, 5)),
              'е' => array(3, array(3, 6, 1, 6, 0, 5, 0, 3, 1, 2, 3, 2, 3, 3, 2, 4, 0, 4)),
              'ж' => array(4, array(2, 0, 2, 6), array(0, 0, 4, 6), array(4, 0, 0, 6)),
              'з' => array(3, array(2, 6, 3, 5, 3, 4, 2, 3, 0, 3, 2, 3, 3, 2, 3, 1, 2, 0, 0, 0)),
              'и' => array(3, array(0, 0, 0, 6, 3, 0, 3, 6)),
              'й' => array(3, array(0, 0, 0, 6, 3, 0, 3, 6), array(1, 0, 2, 0)),
              'к' => array(3, array(0, 0, 0, 6), array(0, 3, 3, 0), array(0, 3, 3, 6)),
              'л' => array(3, array(0, 6, 0, 3, 1, 0, 2, 0, 3, 3, 3, 6)),
              'м' => array(4, array(0, 6, 1, 0, 2, 6, 3, 0, 4, 6)),
              'н' => array(3, array(0, 0, 0, 6, 0, 3, 3, 3, 3, 0, 3, 6)),
              'о' => array(3, array(0, 4, 1, 3, 2, 3, 3, 4, 3, 5, 2, 6, 1, 6, 0, 5, 0, 4)),
              'п' => array(3, array(0, 6, 0, 0, 3, 0, 3, 6)),
              'р' => array(3, array(0, 8, 0, 3, 2, 3, 3, 4, 3, 5, 2, 6, 0, 6)),
              'с' => array(3, array(3, 0, 2, 0, 0, 2, 0, 4, 2, 6, 3, 6)),
              'т' => array(4, array(0, 0, 4, 0), array(2, 0, 2, 6)),
              'у' => array(3, array(0, 3, 0, 5, 1, 6, 3, 6), array(3, 3, 3, 7, 2, 8, 0, 8)),
              'ф' => array(4, array(2, 0, 2, 6), array(2, 0, 1, 0, 0, 1, 0, 2, 1, 3, 3, 3, 4, 2, 4, 1, 3, 0, 2, 0)),
              'х' => array(3, array(0, 0, 3, 6), array(0, 6, 3, 0)),
              'ц' => array(4, array(0, 0, 0, 6, 3, 6, 3, 0), array(3, 6, 4, 6, 4, 7)),
              'ч' => array(4, array(0, 0, 0, 3, 3, 3, 3, 0, 3, 6)),
              'ш' => array(4, array(0, 0, 0, 6, 4, 6, 4, 0), array(2, 6, 2, 0)),
              'щ' => array(5, array(0, 0, 0, 6, 4, 6, 4, 0), array(2, 6, 2, 0), array(4, 6, 5, 6, 5, 7)),
              'ь' => array(3, array(0, 0, 0, 6, 2, 6, 3, 5, 3, 4, 2, 3, 0, 3)),
              'ы' => array(5, array(0, 0, 0, 6, 2, 6, 3, 5, 3, 4, 2, 3, 0, 3), array(5, 0, 5, 6)),
              'ъ' => array(4, array(0, 0, 1, 0, 1, 6, 3, 6, 4, 5, 4, 4, 3, 3, 1, 3)),
              'э' => array(3, array(0, 0, 2, 0, 3, 1, 3, 5, 2, 6, 0, 6), array(0, 3, 3, 3)),
              'ю' => array(5, array(1, 2, 2, 1, 3, 1, 4, 2, 4, 4, 3, 5, 2, 5, 1, 4, 1, 2), array(0, 1, 0, 5, 0, 3, 1, 3)),
              'я' => array(3, array(0, 6, 3, 3, 1, 3, 0, 2, 0, 1, 1, 0, 3, 0, 3, 6)),
              ' ' => array(3));
// </editor-fold>

function drawtext($image, $xshift, $yshift, $text, $color) {
    global $font;

    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');
    $text = preg_split('/(?<!^)(?!$)/u', mb_strtolower($text));
    $char_space = 7;

    for ($i = 0; $i < count($text); $i++) {
        if (isset($font[$text[$i]])) {
            $curves = array_slice($font[$text[$i]], 1, count($font[$text[$i]]));
            foreach ($curves as $curve) {
                for ($k = 0; $k < count($curve) - 2; $k += 2) {
                    imageline($image,
                              $curve[$k] * 2 + $xshift,
                              $curve[$k + 1] * 2 + $yshift,
                              $curve[$k + 2] * 2 + $xshift,
                              $curve[$k + 3] * 2 + $yshift,
                              $color);
                }
            }
            $xshift += $font[$text[$i]][0] + $char_space;
        }
    }
}

function getrandchar() {
    global $font;
    $chars = array_keys($font);
    return $chars[rand(0, count($font) - 2)];
}

kotoba_session_start();

$im = imagecreate(100, 30);

if (isset($_SESSION['stylesheet']) && $_SESSION['stylesheet'] == 'kusaba.css') {
    $bg = imagecolorallocate($im, 238, 255, 238);
} elseif (isset($_SESSION['stylesheet']) && $_SESSION['stylesheet'] == 'futaba.css') {
    $bg = imagecolorallocate($im, 255, 255, 238);
} else {
    $bg = imagecolorallocate($im, 255, 100, 255);
}
$textcolor = imagecolorallocate($im, 0, 0, 0);

$word = "";
for ($i = 0; $i < 5; $i++) {
    $word .= getrandchar();
}
drawtext($im, 10, 10, $word, $textcolor);
imageline($im, rand(0, 100), rand(0, 30), rand(0, 100), rand(0, 30), $textcolor);

header('Content-type: image/png');

imagepng($im);
imagedestroy($im);
$_SESSION['captcha_code'] = $word;
?>