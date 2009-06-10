<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

@require_once('config.php');
@require_once("common.php");

/* check_module function:
 * checking installed module and loaded module
 * XXX: dynamic loading removing due security and thread-safe.
 * return is boolean:
 * true if module already loaded
 * false if module not loaded
 * argumens:
 * $module_name is module name in php
*/
function check_module($module_name) {
	if(extension_loaded($module_name)) {
		return true;
	}
	else {
		return false;
	}
}
/* db_image_settings: get uploaded file type settings from database
 * return settings array
 * arguments:
 * $link is database link
 * $extension is file extension
 */
function db_image_settings($link, $extension) {
	$st = mysqli_prepare($link, "call sp_get_filetype(?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "s", $extension)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $image, $extension, $store_extension, $handler, $thumbnail_image);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	$settings = array('image' => $image,'extension' => $extension,
		'store_extension' => $store_extension, 'handler' => $handler,
		'thumbnail_image' => $thumbnail_image);
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $settings;
}

/*
 * thumb_check_image_type function checking is image format supported
 * also if fomat supported - calculates its dimensions
 * return true if supported
 * argumens: 
 * $link is database link
 * $ext is extension of uploaded file
 * $file is uploaded file
 * &$result is reference to array with resulting data:
 * 'extension' is thumbnail extension
 * 'orig_extension' is original file extension
 * 'x' is width of image
 * 'y' is height of image
 * 'force_thumbnail' create thumbnail even if dimensions too small
 */

function thumb_check_image_type($link, $ext, $file, &$result) {
//	echo sprintf("file %s with extension %s", $file, $ext);
	$has_gd = (check_module('gd') | check_module('gd2')) & KOTOBA_TRY_IMAGE_GD;
	$has_im = check_module('imagick') & KOTOBA_TRY_IMAGE_IM;
	$image_settings = db_image_settings($link, $ext);
	$result['force_thumbnail'] = false;
	if(count($image_settings) == 0) {
		return false;
	}
	$result['orig_extension'] = $ext;
	$result['extension'] = $image_settings['store_extension'];
	$result['x'] = 0;
	$result['y'] = 0;
	$result['image'] = $image_settings['image'];
	if($result['image'] == 0) {
		if(!isset($image_settings['thumbnail_image']) && 
			strlen($image_settings['thumbnail_image']) == 0)
		{
			return false;
		}
		$result['thumbnail'] = $image_settings['thumbnail_image'];
		return true;
	}

	if($has_gd) { //gd library formats
		// fill result
		if($image_settings['handler'] == 'internal' || $image_settings['handler'] == 'internal_png') {
			if($image_settings['image'] == 1) {
				$dimensions = getimagesize($file);
				$result['x'] = $dimensions[0];
				$result['y'] = $dimensions[1];
			}
		}
		return true;
	}
	elseif($has_im) {
		if($image_settings['handler'] == 'internal' || $image_settings['handler'] == 'internal_png') {
			if($image_settings['image'] == 1) {
				$image = new Imagick($file);
				if(!$image->setImageFormat($result['orig_extension'])) {
					kotoba_error("imagemagick: format failed");
				}
				$result['x'] = $image->getImageWidth();
				$result['y'] = $image->getImageHeight();
				$image->clear();
				$image->destroy();
			}
		}
		return true;
	}
	else {
		return false;
	}
}

/*
 * create_thumbnail routine is
 * TODO creating thumbnail image using all available allowed modules
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * argumens: $source is source image file
 * $link is database link
 * $destination is thumbnail image file
 * $type is source image file extension (TODO isn't better to use mime type?)
 * $x and $y - dimensions of original image
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
 * $force is forcing creating thumbnail
 * &$result is reference to array with thumbnail dimensions:
 * 'x' is width, 'y' is height
*/
function create_thumbnail($link, $source, $destination, $type, $x, $y, 
	$resize_x, $resize_y, $force = false, &$result) {
	//echo sprintf("%s, %s, %s, %d, %d, %d, %d", $source, $destination, $type, $x, $y, $resize_x, $resize_y);
	if(!$force && $x < $resize_x && $y < $resize_y) { // small image doesn't need to be thumbnailed
		if(filesize($source) > KOTOBA_SMALLIMAGE_LIMIT_FILE_SIZE) { // big file but small image is some kind of trolling
			return KOTOBA_THUMB_TOOBIG;
		}
		$result['x'] = $x;
		$result['y'] = $y;
		return link_file($source, $destination);
	}
	$has_gd = (check_module('gd') | check_module('gd2')) & KOTOBA_TRY_IMAGE_GD;
	$has_im = check_module('imagick') & KOTOBA_TRY_IMAGE_IM;
	$image_settings = db_image_settings($link, $type);
	if(count($image_settings) == 0) {
		return KOTOBA_THUMB_UNSUPPORTED;
	}
	if($image_settings['image'] == 1 && 
		$image_settings['handler'] == 'internal') 
	{
		if($has_gd) {
			return gd_create_thumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y, $result);
		}
		elseif($has_im) {
			return im_create_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, false, $result);
		}
		else {
			return KOTOBA_THUMB_NOLIBRARY;
		}
	}
	if($image_settings['image'] == 1 && 
		$image_settings['handler'] == 'internal_png') 
	{
		if($has_im) {
			return im_create_png_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $result);
		}
		else {
			return KOTOBA_THUMB_NOLIBRARY;
		}
	}
/*
	if($has_gd && $has_im) { //all image formats supported
		switch(strtolower($type)) {
			case 'jpg':
			case 'jpeg':
				return gd_create_thumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y, $result);
				break;
			case 'png':
			case 'bmp':
			case 'gif':
				return im_create_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $result);
				break;
			case 'svg':
				// svg format
				return im_create_png_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $result);
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
				return gd_create_thumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y, $result);
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
				return im_create_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $false, $result);
				break;
			case 'svg':
				// svg format
				return im_create_png_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, $result);
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
 */
}

/*
 * function link_file - creates hardlink or copy of file
 * return integer; 0 on success, 255 on unknown result
 * WARNING: dies
 * argumens:
 * $source is source filename
 * $destination is destination filename
 */
function link_file($source, $destination) {
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
 * im_create_png_thumbnail procedure: creating thumnail using ImageMagick from 
 *  other formats. Result in .png
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * argumens:
 * $source is source image file
 * $destination is thumbnail image file
 ** (dimensions of original image unknown)
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
 * $result: see description in create_thumbnail function
 */
function im_create_png_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y, &$result) {
//	echo "$source, $destination, $x, $y, $resize_x, $resize_y<br>\n";
	$thumbnail = new Imagick($source);
	$resolution = $thumbnail->getImageResolution();
	$resolution_ratio_x = $resolution['x'] / $x;
	$resolution_ratio_y = $resolution['y'] / $y;
	$color = $thumbnail->getImageBackgroundColor();
	if($x >= $y) { // calculate proportions of destination image
		$ratio = $y / $x;
		$resize_y = $resize_y * $ratio;
	}
	else {
		$ratio = $x / $y;
		$resize_x = $resize_x * $ratio;
	}
	$thumbnail->removeImage();

	$thumbnail->setResolution($resize_x * $resolution_ratio_x, $resize_y * $resolution_ratio_y);
	$thumbnail->readImage($source);
	if(!$thumbnail->setImageFormat('png')) {
		die("conversion failed");
	}
	$thumbnail->paintTransparentImage($color, 0.0, 0);
	$result['x'] = $thumbnail->getImageWidth();
	$result['y'] = $thumbnail->getImageHeight();
	$thumbnail->writeImage($destination);
	$thumbnail->clear();
	$thumbnail->destroy();
	return KOTOBA_THUMB_SUCCESS;
}
/*
 * im_create_thumbnail procedure: creating thumnail using ImageMagick
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * argumens:
 * $source is source image file
 * $destination is thumbnail image file
 * $x and $y - dimensions of original image
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
 * $animation is boulean: preserve animation?
 * $result: see description in create_thumbnail function
*/
function im_create_thumbnail($source, $destination, $x, $y, $resize_x, $resize_y,
   $animation = false, &$result) {
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
		$result['x'] = $thumbnail->getImageWidth();
		$result['y'] = $thumbnail->getImageHeight();
		$res = $thumbnail->writeImage($destination);
		$thumbnail->clear();
		$thumbnail->destroy();
	}
	if($res) {
		return KOTOBA_THUMB_SUCCESS;
	}
}

/*
 * gd_create_thumbnail: create thumbnail from image
 * return integer code error (0 or KOTOBA_THUMB_SUCCESS on success)
 * arguments
 * $source: source file name
 * $destination: destination file name
 * $type: file type
 * $x and $y: dimensions of source image
 * $resize_x and $resize_y: dimensions of destination image
 * $result: see description in create_thumbnail function
 */

function gd_create_thumbnail($source, $destination, $type, $x, $y, $resize_x, $resize_y, &$result) {
	switch(strtolower($type)) {
	case 'gif':
		return gif_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, $result);
		break;
	case 'jpeg':
	case 'jpg':
		return jpg_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, $result);
		break;
	case 'png':
		return png_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, $result);
		break;
	default:
		return KOTOBA_THUMB_UNKNOWN;
		break;
	}
}
/*
 * gd_resize: resize gd image object proportionaly
 * return new gd image object
 * arguments:
 * $img: source image gd object referenc
 * $x and $y: dimensions of source image
 * $size_x and $size_y: dimensions of destination image
 * $source: source file name
 * $destination: destination file name
 * $fill: fill image with transparent color
 * $blend: blend image with transparent color FIXME
 * $result: see description in create_thumbnail function
 */
function gd_resize(&$img, $x, $y, $size_x, $size_y, $source, $destination, $fill = false, $blend = false, &$result) {
	if($x >= $y) { // calculate proportions of destination image
		$ratio = $y / $x;
		$size_y = $size_y * $ratio;
	}
	else {
		$ratio = $x / $y;
		$size_x = $size_x * $ratio;
	}

	$result['x'] = $size_x;
	$result['y'] = $size_y;
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
 * functions xxx_gd_create: create resized file
 * one function for one image type (based on prefix)
 * return int FIXME
 * arguments:
 * $source: source file name
 * $destination: destination file name
 * $x and $y: dimensions of source image
 * $size_x and $size_y: dimensions of destination image
 * $result: see description in create_thumbnail function
 */

function gif_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, &$result) {
	$gif = imagecreatefromgif($source);
	$thumbnail = gd_resize($gif, $x, $y, $resize_x, $resize_y, $source, $destination, true, false, $result);
	imagegif($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
function jpg_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, &$result) {
	$jpeg = imagecreatefromjpeg($source);
	$thumbnail = gd_resize($jpeg, $x, $y, $resize_x, $resize_y, $source, $destination, false,false,$result);
	imagejpeg($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
function png_gd_create($source, $destination, $x, $y, $resize_x, $resize_y, &$result) {
	$png = imagecreatefrompng($source);
	$thumbnail = gd_resize($png, $x, $y, $resize_x, $resize_y, $source, $destination, true ,true, $result);
	imagepng($thumbnail, $destination);
	return KOTOBA_THUMB_SUCCESS;
}
?>
