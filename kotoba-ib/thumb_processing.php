<?
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

@require_once("./common.php");

/* checkLoadModule function:
 * checking installed module and loaded module
 * XXX: dynamic loading removing due security and thread-safe.
 * return is boolean:
 * true if module already loaded
 * false if module not loaded
 * argumens:
 * $module_name is module name in php
*/
function checkLoadModule($module_name) {
	if(extension_loaded($module_name)) {
		return true;
	}
	else {
		return false;
	}
}


/*
 * createThumbnail routine is
 * TODO creating thumbnail image using all available allowed modules
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * argumens: $source is source image file
 * $destination is thumbnail image file
 * $type is source image file extension (TODO isn't better to use mime type?)
 * $x and $y - dimensions of original image
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
*/
function createThumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y) {
//	echo sprintf("%s, %s, %s, %s, %s", $source, $destination, $type, $resize_x, $resize_y);
	if($x < $size_x && $y < $size_y) { // small image doesn't need to be thumbnailed
		if(filesize($source) > KOTOBA_SMALLIMAGE_LIMIT_FILE_SIZE) { // big file but small image is some kind of trolling
			return KOTOBA_THUMB_TOOBIG;
		}
		return linkfile($source, $destination);
	}
	$has_gd = (checkLoadModule('gd') | checkLoadModule('gd2')) & KOTOBA_TRY_IMAGE_GD;
	$has_im = checkLoadModule('imagick') & KOTOBA_TRY_IMAGE_IM;

	if($has_gd && $has_im) { //all image formats supported
		switch(strtolower($type)) {
			case 'jpg':
			case 'jpeg':
				return gdCreateThumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y);
				break;
			case 'png':
			case 'bmp':
			case 'gif':
				return imCreateThumbnail($source, $destination, $x, $y, $resize_x, $resize_y);
				break;
			default:
				// unknown image format
				return KOTOBA_THUMB_UNSUPPORTED;
				break;
		}
	}
	elseif ($has_gd && ! $has_im ) {
		switch(strtolower($type)) {
			case 'jpg':
			case 'gif':
			case 'jpeg':
			case 'png':
				return gdCreateThumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y);
				break;
			default:
				// unknown image format
				return KOTOBA_THUMB_UNSUPPORTED;
				break;
		}
	}
	elseif ($has_im && ! $has_gd ) {
		switch(strtolower($type)) {
			case 'jpg':
			case 'gif':
			case 'jpeg':
			case 'png':
			case 'bmp':
				return imCreateThumbnail($source, $destination, $x, $y, $resize_x, $resize_y);
				break;
			default:
				// unknown image format
				return KOTOBA_THUMB_UNSUPPORTED;
				break;
		}
	}
	else { // there is no libraries known to handle images. Instant fail.
		return KOTOBA_THUMB_NOLIBRARY;
	}
	return KOTOBA_THUMB_UNKNOWN;
}

/*
 * function linkfile - creates hardlink or copy of file
 * return integer; 0 on success, 255 on unknown result
 * WARNING: dies
 * argumens:
 * $source is source filename
 * $destination is destination filename
 */
function linkfile($source, $destination) {
	if(function_exists("link")) {
		if(link($source, $destination)) {
			return KOTOBA_THUMB_SUCCESS;
		}
		else {
			die($php_errormsg);
		}
	}
	else {
		if(copy($source, $destination)) {
			return KOTOBA_THUMB_SUCCESS;
		}
		else {
			die($php_errormsg);
		}
	}
	return KOTOBA_THUMB_UNKNOWN;
}

/*
 * imCreateThumbnail procedure: creating thumnail using ImageMagick
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * argumens:
 * $source is source image file
 * $destination is thumbnail image file
 * $x and $y - dimensions of original image
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
 * $animation is boulean: preserve animation?
*/
function imCreateThumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $animation = false) {
	$thumbnail = new Imagick($source);
	if($x >= $y) { // resize width to 200, height is resized proportional
		if($animation) { // animation not supported
			;
		}
		else {
			$thumbnail->thumbnailImage($resize_x, 0);
		}
	}
	else { //resize height too 200, width resized proportional
		if($animation) { // animation not supported
			;
		}
		else {
			$thumbnail->thumbnailImage(0, $resize_y);
		}
	}
	$res = false;
	if(! $animation) {
		// write image, ImageMagick object cleanup
		$res = $thumbnail->writeImage($destination);
		$thumbnail->clear();
		$thumbnail->destroy();
	}
	if($res) {
		return KOTOBA_THUMB_SUCCESS;
	}
}

function gdCreateThumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y) {
	switch(strtolower($type)) {
	case 'gif':
		return gifGdCreate($source, $destination, $x, $y, $resize_x, $resize_y);
		break;
	case 'jpeg':
	case 'jpg':
		return jpgGdCreate($source, $destination, $x, $y, $resize_x, $resize_y);
		break;
	case 'png':
		return pngGdCreate($source, $destination, $x, $y, $resize_x, $resize_y);
		break;
	default:
		return KOTOBA_THUMB_UNKNOWN;
		break;
	}
}
/*
 * gdResize: resize gd image object proportionaly
 * return new gd image object
 * arguments:
 * $img: source image gd object referenc
 * $x and $y: dimensions of source image
 * $size_x and $size_y: dimensions of destination image
 * $source: source file name
 * $destination: destination file name
 * $fill: fill image with transparent color
 * $blend: blend image with transparent color FIXME
 */
function gdResize(&$img, $x, $y, $size_x, $size_y, $source, $destination, $fill = false, $blend = false) {
	if($x >= $y) { // calculate proportions of destination image
		$ratio = $y / $x;
		$size_y = $size_y * $ratio;
	}
	else {
		$ratio = $x / $y;
		$size_x = $size_x * $ratio;
	}
	$res = imagecreatetruecolor($size_x, $size_y);
	if($fill && $blend) { // png. slow on big images (need tests)
		imagealphablending($res, false);
		imagesavealpha($res, true);
		$transparent = imagecolorallocatealpha($res, 255, 255, 255, 127);
		imagefilledrectangle($res, 0, 0, $size_x, $size_y, $transparent);
	}
	elseif($fill && !$blend) { //gif
		$colorcount = imagecolorstotal($img);
		imagetruecolortopalette($res, true, $colorcount);
		imagepalettecopy($res, $img);
		$transparentcolor = imagecolortransparent($img);
		imagefill($res, 0, 0, $transparentcolor);
		imagecolortransparent($res, $transparentcolor);
	}
	imagecopyresampled($res, $img, 0, 0, 0, 0, $size_x, $size_y, $x, $y);
	return $res;
}

/*
 * functions xxxGdCreate: create resized file
 * one function for one image type (based on prefix)
 * return int FIXME
 * arguments:
 * $source: source file name
 * $destination: destination file name
 * $x and $y: dimensions of source image
 * $size_x and $size_y: dimensions of destination image
 */

function gifGdCreate($source, $destination, $x, $y, $resize_x, $resize_y) {
	$gif = imagecreatefromgif($source);
	$thumbnail = gdResize($gif, $x, $y, $resize_x, $resize_y, $source, $destination, true, false);
	imagegif($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
function jpgGdCreate($source, $destination, $x, $y, $resize_x, $resize_y) {
	$jpeg = imagecreatefromjpeg($source);
	$thumbnail = gdResize($jpeg, $x, $y, $resize_x, $resize_y, $source, $destination);
	imagejpeg($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
function pngGdCreate($source, $destination, $x, $y, $resize_x, $resize_y) {
	$png = imagecreatefrompng($source);
	$thumbnail = gdResize($png, $x, $y, $resize_x, $resize_y, $source, $destination, true ,true);
	imagepng($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
?>
