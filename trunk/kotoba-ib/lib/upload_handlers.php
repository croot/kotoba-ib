<?php
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Error messages in russian.
 * @package ?
 */

/**
 * Derp. PHPDoc sucks.
 */
require_once '../config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

// Скрипт обработчиков загружаемых файлов и вспомогательных фукнций.
/**
 * Стандартная функция (обработчик) создания уменьшенных копий изображений.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param source_dimensions array <p>Размеры исходного изображения.</p>
 * @param type array <p>Тип файла изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function thumb_default_handler($source, $dest, $source_dimensions, $type,
                               $resize_x, $resize_y) {
    if ((check_module('gd') | check_module('gd2')) & Config::TRY_IMAGE_GD) {
        return gd_create_thumbnail($source, $dest, $type,
                                   $source_dimensions['x'],
                                   $source_dimensions['y'], $resize_x,
                                   $resize_y);
    } elseif (check_module('imagick') & Config::TRY_IMAGE_IM) {
        return im_create_thumbnail($source, $dest, $source_dimensions['x'],
                                   $source_dimensions['y'], $resize_x,
                                   $resize_y, false);
    } else {
        throw new CommonException(CommonException::$messages['NO_IMG_LIB']);
    }
}
/**
 * Создает уменьшенную копию изображения с помощью GD.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param type array <p>Тип файла изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function gd_create_thumbnail($source, $dest, $type, $x, $y, $resize_x,
                             $resize_y) {

    $ext = strtolower($type['extension']);

    switch ($ext) {
        case 'gif':
            return gif_gd_create($source, $dest, $x, $y, $resize_x, $resize_y);
        case 'jpeg':
        case 'jpg':
            return jpg_gd_create($source, $dest, $x, $y, $resize_x, $resize_y);
        case 'png':
            return png_gd_create($source, $dest, $x, $y, $resize_x, $resize_y);
        default:
            throw new CommonException($EXCEPTIONS['GD_WRONG_FILETYPE']($ext));
    }
}
/**
 * Создаёт уменьшенную копию изображения с помощью ImageMagick.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @param animation boolean[false] <p>Флаг создания анимированной уменьшенной
 * копии.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
*/
function im_create_thumbnail($source, $dest, $x, $y, $resize_x, $resize_y,
   $animation = false)
{
	$thumbnail = new Imagick($source);
	if($x >= $y)	// resize width to 200, height is resized proportional
	{
		if($animation)
			;		// animation not supported
		else
			$thumbnail->thumbnailImage($resize_x, 0);
	}
	else			//resize height too 200, width resized proportional
	{
		if($animation)
			;		// animation not supported
		else
			$thumbnail->thumbnailImage(0, $resize_y);
	}
	if(!$animation)
	{
		// write image, ImageMagick object cleanup
		$dimensions = array();
		$dimensions['x'] = $thumbnail->getImageWidth();
		$dimensions['y'] = $thumbnail->getImageHeight();
		$thumbnail->writeImage($dest);
		$thumbnail->clear();
		$thumbnail->destroy();
		return $dimensions;
	}
}
/**
 * Создает уменьшенную копию gif файла с помощью GD.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function gif_gd_create($source, $dest, $x, $y, $resize_x, $resize_y)
{
	$gif = imagecreatefromgif($source);
	$dimensions = array();
	$thumbnail = gd_resize($gif, $x, $y, $resize_x, $resize_y, $source,
		$dimensions, true, false);
	imagegif($thumbnail, $dest);
	return $dimensions;
}
/**
 * Создает уменьшенную копию jpg файла с помощью GD.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function jpg_gd_create($source, $dest, $x, $y, $resize_x, $resize_y)
{
	$jpeg = imagecreatefromjpeg($source);
	$dimensions = array();
	$thumbnail = gd_resize($jpeg, $x, $y, $resize_x, $resize_y, $source,
		$dimensions, false, false);
	imagejpeg($thumbnail, $dest);
	return $dimensions;
}
/**
 * Создает уменьшенную копию pag файла с помощью GD.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function png_gd_create($source, $dest, $x, $y, $resize_x, $resize_y)
{
	if (($png = imagecreatefrompng($source)) === false) {
        throw new Exception('You PNG failed.');
    }
	$dimensions = array();
	$thumbnail = gd_resize($png, $x, $y, $resize_x, $resize_y, $source,
		$dimensions, true ,true);
	imagepng($thumbnail, $dest);
	return $dimensions;
}
/**
 * Пропорционально изменяет размеры GD изображения.
 * @param img resource <p>Ссылка на исходное GD изображение.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @param source string <p>Исходное изображение.</p>
 * @param dimensions array <p>Ссылка на массив для сохранения размеров созданной
 * уменьшенной копии изображения.</p>
 * @param fill boolean[false] <p>Заполнить изображение прозрачностью.</p>
 * @param blend boolean[false] <p>blend image with transparent color</p>
 * @return resource
 * Возвращает новое GD изображение.
 */
function gd_resize(&$img, $x, $y, $resize_x, $resize_y, $source, &$dimensions,
	$fill = false, $blend = false)
{
	if($x >= $y)
	{
		// calculate proportions of destination image
		$ratio = $y / $x;
		$resize_y = $resize_y * $ratio;
	}
	else
	{
		$ratio = $x / $y;
		$resize_x = $resize_x * $ratio;
	}
	$dimensions['x'] = round($resize_x);
	$dimensions['y'] = round($resize_y);
	$res = imagecreatetruecolor($resize_x, $resize_y);
	if($fill && $blend)
	{
		// png. slow on big images (need tests)
		imagealphablending($res, false);
		imagesavealpha($res, true);
		$transparent = imagecolorallocatealpha($res, 255, 255, 255, 127);
		imagefilledrectangle($res, 0, 0, $resize_x, $resize_y, $transparent);
	}
	elseif($fill && !$blend)
	{
		//gif
		$colorcount = imagecolorstotal($img);
		imagetruecolortopalette($res, true, $colorcount);
		imagepalettecopy($res, $img);
		$transparentcolor = imagecolortransparent($img);
		imagefill($res, 0, 0, $transparentcolor);
		imagecolortransparent($res, $transparentcolor);
	}
	imagecopyresampled($res, $img, 0, 0, 0, 0, $resize_x, $resize_y, $x, $y);
	return $res;
}
/**
 * Создаёт уменьшенную копию изображений разных форматов с помощью ImageMagick.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param source_dimensions array <p>Размеры исходного изображения.</p>
 * @param type array <p>Тип файла изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function thumb_internal_png($source, $dest, $source_dimensions, $type,
                            $resize_x, $resize_y) {
    if (check_module('imagick') & Config::TRY_IMAGE_IM) {
        return im_create_png_thumbnail($source, $dest, $source_dimensions['x'],
                                       $source_dimensions['y'], $resize_x,
                                       $resize_y);
    } else {
        throw new CommonException(CommonException::$messages['NO_IMG_LIB']);
    }
}
/**
 * Создаёт уменьшенную копию изображений разных форматов с помощью ImageMagick.
 * Уменьшенная копия будет в формате png.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param x mixed <p>Ширина исходного изображения.</p>
 * @param y mixed <p>Высота исходного изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения.
 */
function im_create_png_thumbnail($source, $dest, $x, $y, $resize_x, $resize_y) {
    $thumbnail = new Imagick($source);
    $resolution = $thumbnail->getImageResolution();
    $resolution_ratio_x = $resolution['x'] / $x;
    $resolution_ratio_y = $resolution['y'] / $y;
    // get background color of source image
    $color = $thumbnail->getImageBackgroundColor();
    if ($x >= $y) {
        // calculate proportions of destination image
        $ratio = $y / $x;
        $resize_y = $resize_y * $ratio;
    } else {
        $ratio = $x / $y;
        $resize_x = $resize_x * $ratio;
    }
    $thumbnail->removeImage();
    $thumbnail->setResolution($resize_x * $resolution_ratio_x,
                              $resize_y * $resolution_ratio_y);
    $thumbnail->readImage($source);
    if (!$thumbnail->setImageFormat('png')) {
        throw new CommonException($EXCEPTIONS['CONVERT_PNG']());
    }
    // fill destination image with source image background color
    // (for transparency in svg for example)
    $thumbnail->paintTransparentImage($color, 0.0, 0);
    $dimensions = array();
    $dimensions['x'] = $thumbnail->getImageWidth();
    $dimensions['y'] = $thumbnail->getImageHeight();
    $thumbnail->writeImage($dest);
    $thumbnail->clear();
    $thumbnail->destroy();
    return $dimensions;
}
?>