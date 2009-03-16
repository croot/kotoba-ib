<?
/*
 * createThumbnail routine is
 * TODO creating thumbnail image using libgd as default
 * return value is boolean:
 * true on success
 * false otherwise
 *
 * argumens: $source is source image file
 * $destination is thumbnail image file
 * $type is source image file extension (TODO isn't better to use mime type?)
 * $resize_x is thumbnal width
 * $resize_y is thumbnal height
 *
*/

function createThumbnail($source, $destination, $type, $resize_x, $resize_y) {
	echo sprintf("%s, %s, %s, %s, %s", $source, $destination, $type, $resize_x, $resize_y);
	return false;
}
?>
