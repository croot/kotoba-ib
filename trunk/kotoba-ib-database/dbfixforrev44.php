<?php
/* fix database from releases up to 44 to new schema
 * (added new parameter to Post Settings)
 * place file in kotoba directory
 * run either from command line or web-browser
 */

require 'config.php';
require('databaseconnect.php');
require('common.php');

// I don't want design here damn html
header("Content-Type: text/plain");

// get all records from database
$query = "select id, `Post Settings` from posts";

if($posts = mysql_query($query)) { // query executed normally
	if(mysql_num_rows($posts) > 0) { // we're have posts
		while (($post = mysql_fetch_array($posts, MYSQL_ASSOC)) !== false) {
			$post_settings = get_settings('post', $post['Post Settings']);
			if(isset($post_settings['IMGNAME'])) { // post have an image
				if(!isset($post_settings['ORIGIMGEXT'])) { // post doesn't have new setting
					// create new bunch of settings.
					$new_post_settings = sprintf("ORIGIMGEXT:%s\n%s", 
						$post_settings['IMGEXT'], $post['Post Settings']);
					// create query for update
					$update_sql = sprintf("update posts set `Post settings` = '%s' where id = %d",
						$new_post_settings, $post['id']);
					// run update
					if(mysql_query($update_sql)) { // updated successfully
						echo sprintf("post #%d updated\n", $post['id']);
					}
					else {
						echo sprintf(">>> error %s\nexecuting:%s\n", mysql_error(),
							$update_sql);
					}
				}
			}
		}
	}
}
else {
	echo mysql_error();
}

?>
