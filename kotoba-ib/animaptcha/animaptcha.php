<?php
/* ********************************
 * This file is part of Kotoba.   *
 * See license.txt for more info. *
 **********************************/

/**
 * Create animaptcha image script.
 */

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';

// Initialization.
kotoba_session_start();
if (Config::LANGUAGE != $_SESSION['language']) {
    require Config::ABS_PATH . "/locale/{$_SESSION['language']}/messages.php";
}
locale_setup();

// Words register independet.
$data = array(array('name' => 'boxxy001.png',
                    'width' => 100,
                    'height' => 50,
                    'words'=> array('boxxy', 'бокси', 'catie')));

// Read image.
$n = rand(0, count($data) - 1);
$im = imagecreatefrompng($data[$n]['name']);
imageline($im,
          rand(0, $data[$n]['width'] - 1),
          rand(0, $data[$n]['height'] - 1),
          rand(0, $data[$n]['width'] - 1),
          rand(0, $data[$n]['height'] - 1),
          imagecolorallocate($im, 0, 0, 0));

// Save keywords.
$_SESSION['animaptcha_code'] = $data[$n]['words'];

// Show image.
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>
