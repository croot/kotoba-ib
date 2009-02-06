<?php
/* Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable. */
/**
 * Faster method than only calling imagecopyresampled()
 *
 * @return boolean Success/fail 
 */ 
function fastImageCopyResampled(&$dst_image, &$src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3)
{
	/*
	Optional "quality" parameter (defaults is 3).  Fractional values are allowed, for example 1.5.
	1 = Up to 600 times faster.  Poor results, just uses imagecopyresized but removes black edges.
	2 = Up to 95 times faster.  Images may appear too sharp, some people may prefer it.
	3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled.
	4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
	5 = No speedup.  Just uses imagecopyresampled, highest quality but no advantage over imagecopyresampled.
	*/
	
	if (empty($src_image) || empty($dst_image))
		return false;

	if ($quality <= 1)
	{
		$temp = imagecreatetruecolor ($dst_w + 1, $dst_h + 1);
		
		imagecopyresized ($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
		imagecopyresized ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
		imagedestroy ($temp);
	} 
	elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h))
	{
		$tmp_w = $dst_w * $quality;
		$tmp_h = $dst_h * $quality;
		$temp = imagecreatetruecolor ($tmp_w + 1, $tmp_h + 1);
		
		imagecopyresized ($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
		imagecopyresampled ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
		imagedestroy ($temp);
	} 
	else
	{
		imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	}

	return true;
}

function createThumbnail($src_filename, $thumb_filename, $ext, $new_w, $new_h)
{
	switch($ext)
	{
		case 'jpg':
			$src_img = imagecreatefromjpeg($src_filename);
			break;
		case 'gif':
			$src_img = imagecreatefromgif($src_filename);
			break;
		case 'png':
			$src_img = imagecreatefrompng($src_filename);
			break;
		default:
			return false;
    }
		
	if (!$src_img)
	{
		die('Unable to read uploaded file during thumbnailing.');
	}

	$old_x = imageSX($src_img);
	$old_y = imageSY($src_img);

	if ($old_x > $old_y)
	{
		$percent = $new_w / $old_x;
	}
	else
	{
		$percent = $new_h / $old_y;
	}

	$thumb_w = round($old_x * $percent);
	$thumb_h = round($old_y * $percent);

	$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	fastImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

	switch($ext)
	{
		case 'jpg':
			if (!imagejpeg($dst_img, $thumb_filename, 70))
			{
				echo 'unable to imagejpg.';
				return false;
			}
			break;
		case 'gif':
			if (!imagegif($dst_img, $thumb_filename))
			{
				echo 'unable to imagegif.';
				return false;
			}
			break;
		case 'png':
			if (!imagepng($dst_img, $thumb_filename))
			{
				echo 'unable to imagepng.';
				return false;
			}
			break;
    }

	imagedestroy($dst_img);
	imagedestroy($src_img);

	return true;
}
?>
