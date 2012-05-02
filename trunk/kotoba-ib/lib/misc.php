<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Experimental or unsorted functions.
 * @package api
 */

/**
 *
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';

/* **************************
 * Начальная инициализация. *
 ****************************/

require Config::ABS_PATH . '/smarty/libs/Smarty.class.php';

/**
 * Smarty class extension.
 * @package templates
 */
class SmartyKotobaSetup extends Smarty {

    function SmartyKotobaSetup() {

        // Fix warning on strftime.
        parent::__construct();

        $language = isset($_SESSION['language'])
                    ? $_SESSION['language']
                    : Config::LANGUAGE;
        $stylesheet = isset($_SESSION['stylesheet']) 
                      ? $_SESSION['stylesheet']
                      : Config::STYLESHEET;

        $this->template_dir = Config::ABS_PATH
                              . "/smarty/kotoba/templates/locale/$language/";
        $this->compile_dir = Config::ABS_PATH
                             . "/smarty/kotoba/templates_c/locale/$language/";
        $this->config_dir = Config::ABS_PATH
                            . "/smarty/kotoba/config/$language/";
        $this->cache_dir = Config::ABS_PATH
                           . "/smarty/kotoba/cache/$language/";
        $this->caching = 0;

        // Variables what available by default in any template.
        $this->assign('DIR_PATH', Config::DIR_PATH);
        $this->assign('STYLESHEET', $stylesheet);
        $this->assign('INVISIBLE_BOARDS', Config::$INVISIBLE_BOARDS);
    }
}
/**
 * intval() wrapper.
 */
function kotoba_intval($var, $throw = TRUE) {
    if (!is_object($var)) {
        return intval($var);
    }

    if ($throw) {
        throw new IntvalException();
    } else {
        return NULL;
    }
}
/**
 * strval() wrapper.
 */
function kotoba_strval($var) {
    if (is_object($var) && method_exists($var, '__toString') || !is_array($var)) {
        return strval($var);
    }

    throw new StrvalException();
}
/**
 * Restores php session or create new one. In case of new session default
 * settings will be apply.
 */
function kotoba_session_start() {
    session_set_cookie_params(Config::SESSION_LIFETIME);

    if (!session_start()) {
        throw new SessionStartException();
    }

    if (!isset($_SESSION['kotoba_session_start_time'])) {
        $_SESSION['kotoba_session_start_time'] = time();
    }

    // Apply default settings.
    if (!isset($_SESSION['user']) || $_SESSION['user'] == Config::GUEST_ID) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['password'] = NULL;
        }
        $_SESSION['user'] = Config::GUEST_ID;
        $_SESSION['groups'] = array(Config::GST_GROUP_NAME);
        $_SESSION['threads_per_page'] = Config::THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = Config::POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = Config::LINES_PER_POST;
        $_SESSION['stylesheet'] = Config::STYLESHEET;
        $_SESSION['language'] = Config::LANGUAGE;
        $_SESSION['goto'] = 'b';    // Redirection to board view.
        $_SESSION['name'] = NULL;
    }
}
/**
 * Sets locale for script exection and for mb_* functions also.
 */
function locale_setup() {
    mb_language(Config::MB_LANGUAGE);
    mb_internal_encoding(Config::MB_ENCODING);
    if (!setlocale(LC_ALL, Config::$LOCALE_NAMES)) {
        //throw new SetLocaleException();
    }
}

/* *********
 * Разное. *
 ***********/

/**
 * Returns remote client IP address converted to long value or throw
 * RemoteAddressException exception if address not defined or invalid.
 */
function get_remote_addr() {
    $ip = FALSE;

    if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = ip2long($_SERVER['REMOTE_ADDR']);
    }

    if ($ip == FALSE) {
        throw new RemoteAddressException();
    } else {
        return $ip;
    }
}
/**
 * Makes category-boards tree for navigation panel.
 */
function make_category_boards_tree(&$categories, $boards) {
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id']
                    && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {

                array_push($c['boards'], $b);
            }
        }
    }
}
/**
 * Clone array by value.
 * @param array $array Array to clone.
 * @return array
 * clone.
 */
function kotoba_array_clone($array) {
    $new_array = NULL;

    foreach ($array as $key => $val) {
        if (!is_array($val)) {
            $new_array[$key] = $val;
        } else {
            $new_array[$key] = kotoba_array_clone($val);
        }
    }

    return $new_array;
}
/**
 * Get certain column of 2nd dimensinal array.
 * @param array $src Source array.
 * @param mixed $col Column index or name.
 */
function kotoba_array_column($src, $col) {
    $result = array();
    foreach ($src as $row) {
        array_push($result, $row[$col]);
    }
    return $result;
}
/**
 * Load user settings.
 * @param string $keyword Keyword hash.
 * @return boolean
 * TURE if user settings loaded successfully or FALSE otherwise. In case of
 * error last error will contain appropriate error object.
 */
function load_user_settings($keyword) {
    if ( ($_ = users_get_by_keyword($keyword)) === FALSE) {
        return FALSE;
    }

    $_SESSION['user'] = kotoba_intval($_['id']);
    $_SESSION['groups'] = $_['groups'];
    $_SESSION['threads_per_page'] = kotoba_intval($_['threads_per_page']);
    $_SESSION['posts_per_thread'] = kotoba_intval($_['posts_per_thread']);
    $_SESSION['lines_per_post'] = kotoba_intval($_['lines_per_post']);
    $_SESSION['stylesheet'] = $_['stylesheet'];
    $_SESSION['language'] = $_['language'];
    $_SESSION['password'] = $_['password'] == '' ? NULL : $_['password'];
    $_SESSION['goto'] = $_['goto'];

    return TRUE;
}
/**
 * Check page number.
 * @param int $page Page number.
 * @param boolean $throw Default value is TRUE. Throw exception or return NULL.
 * @return int Returns safe page number.
 */
function check_page($page, $throw = TRUE) {
    return kotoba_intval($page, $throw);
}
/**
 * Проверяет, загружено ли расширение php.
 * @param name string <p>Имя расширения</p>
 * @return bool
 * <p>Возвращает true, если расширение загружено и false в противном случае.</p>
 */
function check_module($name)
{
	return extension_loaded($name);
}
/**
 * Проверяет корректность юникода в UTF-8.
 * Thanks to javalc6@gmail.com <a href="http://ru2.php.net/manual/en/function.mb-check-encoding.php#95289">http://ru2.php.net/manual/en/function.mb-check-encoding.php#95289</a>
 * @param string $text Текст UTF-8
 * @return bool
 * TRUE if unicod is valid and FALSE otherwise.
 */
function check_utf8($text) {
    $len = strlen($text);
    for ($i = 0; $i < $len; $i++) {
        $c = ord($text[$i]);
        if ($c > 128) {
            if ($c > 247) {
                return FALSE;
            } elseif ($c > 239) {
                $bytes = 4;
            } elseif ($c > 223) {
                $bytes = 3;
            } elseif ($c > 191) {
                $bytes = 2;
            } else {
                return FALSE;
            }
            if (($i + $bytes) > $len) {
                return FALSE;
            }
            while ($bytes > 1) {
                $i++;
                $b = ord($text[$i]);
                if ($b < 128 || $b > 191) {
                    return FALSE;
                }
                $bytes--;
            }
        }
    }
    return TRUE;
}
/**
 * Create names for uploaded file and thumbnail.
 * @param string $ext Extension.
 * @return array
 * names.
 */
function create_filenames($ext) {
	list($usec, $sec) = explode(' ', microtime());

	// 3 digits after comma.
	$filename = $sec . substr($usec, 2, 5);

	// All thumbnails names ends on t.
	return array("$filename.$ext",
                 "{$filename}t.$ext",
                 $filename);
}
/**
 * Creates tripcode.
 * @param string $name Message author name what can contains tripcode.
 * @return array
 * array of name and created tripcode.
 */
function calculate_tripcode($name) {
    @list($first, $code) = @preg_split("/[#!]/", $name);
    if (!isset($code) || strlen($code) == 0) {
        return array($name, null);
    }
    $enc = mb_convert_encoding($code, 'Shift_JIS', Config::MB_ENCODING);
    $salt = mb_substr($enc . 'H..', 1, 2);
    $salt2 = preg_replace("/![\.-z]/", '.', $salt);
    $salt3 = strtr($salt2, ":;<=>?@[\]^_`", "ABCDEFGabcdef");
    $cr = crypt($code, $salt3);
    $trip = mb_substr($cr, -10);
    return array($first, $trip);
}
/**
 * Move file.
 * @param string $source Source path.
 * @param string $dest Destination path.
 */
function move_uploded_file($source, $dest) {
    if (!rename($source, $dest)) {
        throw new MoveFileException($source, $dest);
    }
}
/**
 * Copy file.
 * @param string $source Source path.
 * @param string $dest Destination path.
 */
function copy_uploded_file($source, $dest) {
    if (!copy($source, $dest)) {
        throw new UploadException('Cannot copy file.');
    }
}
/**
 * Remove any ASCII control sequences except \t and \n.
 * @param string $text Text.
 */
function purify_ascii(&$text) {
    $text = str_replace(
        array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
              "\x08", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F", "\x10", "\x11",
              "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19",
              "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F", "\x7F"),
        '',
        $text
    );
}
/**
 * Calculate md5 hash of file.
 * @param string $path File path.
 * @return string
 * md5 hash.
 */
function calculate_file_hash($path) {
	$hash = NULL;

	if ( ($hash = hash_file('md5', $path)) === false) {
		throw new FileHashException($path);
    }

	return $hash;
}
/**
 * Check if post attachments is a oekaki picture.
 * @return boolean
 * TRUE if it ELSE otherwise.
 */
function use_oekaki() {
    return isset($_REQUEST['use_oekaki'])
           && $_REQUEST['use_oekaki'] == '1'
           && isset($_SESSION['oekaki'])
           && is_array($_SESSION['oekaki']);
}
/**
 * Check error on file uploading.
 * @param int $err File upload error.
 * @return boolean
 * TRUE if no error and FALSE otherwise. In case of error set last error to
 * appropriate error object.
 */
function upload_check_error($err) {
    switch ($err) {
        case UPLOAD_ERR_INI_SIZE:
            kotoba_set_last_error(new UploadIniSizeError());
            return FALSE;
        case UPLOAD_ERR_FORM_SIZE:
            kotoba_set_last_error(new UploadFormSizeError());
            return FALSE;
        case UPLOAD_ERR_PARTIAL:
            kotoba_set_last_error(new UploadPartialError());
            return FALSE;
        case UPLOAD_ERR_NO_TMP_DIR:
            kotoba_set_last_error(new UploadNoTmpDirError());
            return FALSE;
        case UPLOAD_ERR_CANT_WRITE:
            kotoba_set_last_error(new UploadCantWriteError());
            return FALSE;
        case UPLOAD_ERR_EXTENSION:
            kotoba_set_last_error(new UploadExtensionError());
            return FALSE;
    }

    return TRUE;
}
/**
 * Get file extension.
 * @param string $name File path.
 * @return string
 * file extension.
 */
function get_extension($name) {
    $parts = pathinfo($name);
    return $parts['extension'];
}
/**
 * Create hard link or copy file if link() not availiable.
 * @param string $source Source filename.
 * @param string $dest Destination filename.
 */
function link_file($source, $dest) {
    if (function_exists('link')) {
        if (!link($source, $dest)) {
            throw new CreateLinkException($source, $dest);
        }
    } else {
        if(!copy($source, $dest)) {
            throw new CopyFileException($source, $dest);
        }
    }
}
/**
 * Check if user is admin.
 * @return boolean
 * TRUE if user is admin and FALSE otherwise.
 */
function is_admin() {
    if (isset($_SESSION['groups']) && is_array($_SESSION['groups'])
            && in_array(Config::ADM_GROUP_NAME, $_SESSION['groups'])) {
        if (count(Config::$ADMIN_IPS) > 0) {
            if (isset($_SERVER['REMOTE_ADDR'])
                    && in_array($_SERVER['REMOTE_ADDR'], Config::$ADMIN_IPS)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
    return false;
}
/**
 * Check if attachments enabled on board.
 * @param array $board Board.
 * @return
 * TRUE if attachments enabled and FALSE otherwise.
 */
function is_attachments_enabled($board) {
    return $board['with_attachments'] == TRUE;
}
/**
 * Check if geoip enabled.
 * @param array $board Board.
 * @return
 * TRUE if geoip enabled or FALSE otherwise.
 */
function is_geoip_enabled($board) {
    return $board['enable_geoip'] === NULL ? Config::ENABLE_GEOIP : $board['enable_geoip'];
}
/**
 * Проверяет, является ли пользователь гостем.
 * @return boolean
 * Возвращает true, если пользователь является гостем и false в противном
 * случае.
 */
function is_guest()
{
	if(isset($_SESSION['user']))
	{
		if($_SESSION['user'] == Config::GUEST_ID)
			return true;
        }
	else
		throw new CommonException('');
	if(isset($_SESSION['groups']) && is_array($_SESSION['groups']))
	{
		if(in_array(Config::GST_GROUP_NAME, $_SESSION['groups']))
			return true;
    }
	else
		throw new CommonException('');
	return false;
        }
/**
 * Check if macrochan integration enabled on board.
 * @param array $board Board.
 * @return boolean
 * return TRUE if macrochan integration enabled and FALSE otherwise.
 */
function is_macrochan_enabled($board) {
    return $board['enable_macro'] === NULL
           ? Config::ENABLE_MACRO
           : $board['enable_macro'];
}
/**
 * Check if user is moderator.
 * @return boolean
 * TRUE if user is moderator and FALSE otherwise.
 */
function is_mod() {
    if (isset($_SESSION['groups']) && is_array($_SESSION['groups'])) {
        foreach ($_SESSION['groups'] as $group_name) {
            if (in_array($group_name, Config::$MOD_GROUPS)) {
                return true;
            }
        }
    }

    return false;
}
/**
 * Check if postid enabled.
 * @param array $board Board.
 * @return
 * TRUE if postid enabled and FALSE otherwise.
 */
function is_postid_enabled($board) {
    return $board['enable_postid'] === NULL ? Config::ENABLE_POSTID : $board['enable_postid'];
}
/**
 * Check if shi plugin enabled.
 * @param array $board Board.
 * @return boolean
 * TRUE if shi plugin and FALSE otherwise.
 */
function is_shi_enabled($board) {
    $_ = Config::ENABLE_SHI;
    if (isset($board['enable_shi']) && $board['enable_shi'] !== NULL) {
        $_ = $board['enable_shi'];
    }

    return $_ == TRUE;
}
/**
 * Check if translation enabled.
 * @param array $board Board.
 * @return
 * TRUE if translation enabled and FALSE otherwise.
 */
function is_translation_enabled($board) {
    return $board['enable_translation'] === null ? Config::ENABLE_TRANSLATION : $board['enable_translation'];
}
/**
 * Check if youtube video attachments enabled.
 * @param array $board Board.
 * @return boolean
 * TRUE if youtube video attachments enabled and FALSE otherwise.
 */
function is_youtube_enabled($board) {
    $_ = Config::ENABLE_YOUTUBE;
    if (isset($board['enable_youtube']) && $board['enable_youtube'] !== NULL) {
        $_ = $board['enable_youtube'];
    }

    return $_ == TRUE;
}
/**
 * Check if captcha enabled.
 * @param array $board Board.
 * @return boolean
 * TRUE if captcha enabled or FALSE otherwise.
 */
function is_captcha_enabled($board) {
    if (is_admin()) {
        return FALSE;
    }

    $_ = Config::ENABLE_CAPTCHA;
    if (isset($board['enable_captcha']) && $board['enable_captcha'] !== NULL) {
        $_ = $board['enable_captcha'];
    }

    return $_ == TRUE;
}
/**
 * Check if captcha valid.
 * @return boolean
 * TRUE if captcha valid or FALSE otherwise.
 */
function is_captcha_valid() {
    return isset($_REQUEST['captcha_code'])
           && isset($_SESSION['captcha_code'])
           && mb_strtolower($_REQUEST['captcha_code'],
                            Config::MB_ENCODING) === $_SESSION['captcha_code'];
}
/**
 * Check if animaptcha valid.
 * @return boolean
 * TRUE if animaptcha valid or FALSE otherwise.
 */
function is_animaptcha_valid() {
    return isset($_REQUEST['animaptcha_code'])
           && isset($_SESSION['animaptcha_code'])
           && in_array(mb_strtolower($_REQUEST['animaptcha_code'],
                                     Config::MB_ENCODING),
                       $_SESSION['animaptcha_code'], TRUE);
}

/* *********
 * Images. *
 ***********/

/**
 * Get image dimensions.
 * @param array $upload_type File type.
 * @param string $file File path.
 * @return array
 * dimensions.
 */
function image_get_dimensions($upload_type, $file) {
	$result = array();

	if ((check_module('gd') | check_module('gd2')) & Config::TRY_IMAGE_GD) {
        //gd library formats
		$dimensions = getimagesize($file);
		$result['x'] = $dimensions[0];
		$result['y'] = $dimensions[1];
		return $result;
	} elseif(check_module('imagick') & Config::TRY_IMAGE_IM) {
        //image magick library
		$image = new Imagick($file);
		if (!$image->setImageFormat($upload_type['extension'])) {
			throw new ImagemagicFiletypeException($upload_type['extension']);
		}
		$result['x'] = $image->getImageWidth();
		$result['y'] = $image->getImageHeight();
		$image->clear();
		$image->destroy();
		return $result;
	} else {
		throw new NoImageLibraryException();
	}
}
/**
 * Create thumbnail of image.
 * @param string $source Source image.
 * @param string $dest Thumbnail path.
 * @param array $source_dimensions Source image dimensions.
 * @param array $type Image file type.
 * @param int $resize_x Thumbnail width.
 * @param int $resize_y Thumbnail height.
 * @return array|boolean
 * thumbnail dimensions or FALSE if any error occurred. Also in case of error
 * set last error to appropriate error object.
 */
function create_thumbnail($source, $dest, $source_dimensions, $type, $resize_x,
                          $resize_y) {

    $result = array();

    // small image doesn't need to be thumbnailed
    if ($source_dimensions['x'] < $resize_x
            && $source_dimensions['y'] < $resize_y) {

        // big file but small image is some kind of trolling
        if (filesize($source) > Config::SMALLIMAGE_LIMIT_FILE_SIZE) {
            kotoba_set_last_error(new MaxSmallImgSizeError());
            return FALSE;
        }
        $result['x'] = $source_dimensions['x'];
        $result['y'] = $source_dimensions['y'];
        link_file($source, $dest);
        return $result;
    }

    return $type['upload_handler_name']($source, $dest, $source_dimensions,
                                        $type, $resize_x, $resize_y);
}
?>
