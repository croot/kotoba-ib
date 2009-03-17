<?
/* checkLoadModule function:
 * checking installed module, tries to load module if not loaded
 * return is boolean:
 * true if module already loaded or loaded successfuly 
 * false if module failed to load or absent (not installed)
 * argumens:
 * $module_name is module name in php
 * $module_library is module library file name TODO: cross-platform
*/
function checkLoadModule($module_name, $module_library) {
	if(extension_loaded($module_name)) {
		return true;
	}
	else {
		if(!dl($module_library)) {
			return false;
		}
		else {
			return true;
		}
	}
}


/*
 * createThumbnail routine is
 * TODO creating thumbnail image using libgd as default
 * return value is boolean:
 * true on success
 * false otherwise
 * argumens: $source is source image file
 * $destination is thumbnail image file
 * $type is source image file extension (TODO isn't better to use mime type?)
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
*/
function createThumbnail($source, $destination, $type, $resize_x, $resize_y) {
	echo sprintf("%s, %s, %s, %s, %s", $source, $destination, $type, $resize_x, $resize_y);
	$has_gd = checkLoadModule('gd', 'gd.so');
	$has_im = checkLoadModule('imagick', 'imagick.so');

	if($has_gd && $has_im) { //all image formats supported
		switch(strtolower($type)) {
			case 'jpg':
			case 'jpeg':
				echo "gd";
				gdCreateThumbnail($source, $destination, $type, $resize_x, $resize_y);
				return true;
				break;
			case 'png':
			case 'gif':
			case 'bmp':
				echo "imagemagick";
				imCreateThumbnail($source, $destination, $resize_x, $resize_y);
				return true;
				break;
			default:
				// unknown image format
				return false;
				break;
		}
	}
	elseif ($has_gd && ! $has_im ) {
		switch(strtolower($type)) {
			case 'jpg':
			case 'gif':
			case 'jpeg':
				echo "gd";
				gdCreateThumbnail($source, $destination, $type, $resize_x, $resize_y);
				return true;
				break;
			default:
				// unknown image format
				return false;
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
				echo "imagemagick";
				imCreateThumbnail($source, $destination, $resize_x, $resize_y);
				return true;
				break;
			default:
				// unknown image format
				return false;
				break;
		}
	}
	else { // there is no libraries known to handle images. Instant fail.
		return false;
	}
	return false;
}

/*
 * imCreateThumbnail procedure: creating thumnail using ImageMagick
 * return void TODO: errors?
 * argumens:
 * argumens: $source is source image file
 * $destination is thumbnail image file
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
*/

function imCreateThumbnail($source, $destination, $resize_x, $resize_y) {
	$thumbanil = new Imagick($source);
	$x = $thumbanil->getImageWidth();
	$y = $thumbanil->getImageHeight();
	if($x >= $y) {
		$thumbanil->thumbnailImage($resize_x, 0);
	}
	else {
		$thumbanil->thumbnailImage(0, $resize_y);
	}
	$thumbanil->writeImage($destination);
}
?>
