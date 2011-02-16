<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Фукнции не попавшие в другие модули или эксперементальные.
 * @package api
 */

/**
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'errors.php'; })) {
    throw new Exception('Error handing file <b>errors.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}

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
        date_default_timezone_set(Config::DEFAULT_TIMEZONE);
        parent::__construct();

        $language = isset($_SESSION['language']) ? $_SESSION['language'] : Config::LANGUAGE;
        $stylesheet = isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : Config::STYLESHEET;

        $this->template_dir = Config::ABS_PATH . "/smarty/kotoba/templates/locale/$language/";
        $this->compile_dir = Config::ABS_PATH . "/smarty/kotoba/templates_c/locale/$language/";
        $this->config_dir = Config::ABS_PATH . "/smarty/kotoba/config/$language/";
        $this->cache_dir = Config::ABS_PATH . "/smarty/kotoba/cache/$language/";
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
function kotoba_intval($var) {
    if (!is_object($var)) {
        return intval($var);
    }

    throw new FormatException(FormatException::$messages['KOTOBA_INTVAL']);
}
/**
 * strval() wrapper.
 */
function kotoba_strval($var) {
    if (is_object($var) && method_exists($var, '__toString') || !is_array($var)) {
        return strval($var);
    }

    throw new FormatException(FormatException::$messages['KOTOBA_STRVAL']);
}
/**
 * Restores php session or create new one. In case of new session default
 * settings will be apply.
 */
function kotoba_session_start() {
    ini_set('session.save_path', Config::ABS_PATH . '/sessions');
    session_cache_expire(Config::SESSION_LIFETIME / 60);
    session_set_cookie_params(Config::SESSION_LIFETIME);

    if (!session_start()) {
        throw new CommonException(CommonException::$messages['SESSION_START']);
    }

    if (!isset($_SESSION['kotoba_session_start_time'])) {
        date_default_timezone_set(Config::DEFAULT_TIMEZONE);
        $_SESSION['kotoba_session_start_time'] = time();
    }

    // Apply default settings.
    if (!isset($_SESSION['user']) || $_SESSION['user'] == Config::GUEST_ID) {
        $_SESSION['user'] = Config::GUEST_ID;
        $_SESSION['groups'] = array(Config::GST_GROUP_NAME);
        $_SESSION['threads_per_page'] = Config::THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = Config::POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = Config::LINES_PER_POST;
        $_SESSION['stylesheet'] = Config::STYLESHEET;
        $_SESSION['language'] = Config::LANGUAGE;
        if (!isset($_SESSION['user'])) {
            $_SESSION['password'] = null;
        }
        $_SESSION['goto'] = 'b';    // Redirection to board view.
        $_SESSION['name'] = null;
    }
}
/**
 * Sets locale for script exection and for mb_* functions also.
 */
function locale_setup() {
    mb_language(Config::MB_LANGUAGE);
    mb_internal_encoding(Config::MB_ENCODING);
    if (!setlocale(LC_ALL, Config::$LOCALE_NAMES)) {
        throw new CommonException(CommonException::$messages['SETLOCALE']);
    }
}

/* *********
 * Разное. *
 ***********/

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
 */
function load_user_settings($keyword) {
    $user_settings = users_get_by_keyword($keyword);
    $_SESSION['user'] = kotoba_intval($user_settings['id']);
    $_SESSION['groups'] = $user_settings['groups'];
    $_SESSION['threads_per_page'] = kotoba_intval($user_settings['threads_per_page']);
    $_SESSION['posts_per_thread'] = kotoba_intval($user_settings['posts_per_thread']);
    $_SESSION['lines_per_post'] = kotoba_intval($user_settings['lines_per_post']);
    $_SESSION['stylesheet'] = $user_settings['stylesheet'];
    $_SESSION['language'] = $user_settings['language'];
    $_SESSION['password'] = $user_settings['password'] == '' ? NULL : $user_settings['password'];
    $_SESSION['goto'] = $user_settings['goto'];
}
/**
 * Check page number.
 * @param int $page Page number.
 * @return int
 * safe page number.
 */
function check_page($page) {
    return kotoba_intval($page);
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
 * Возвращает true, если текст корректный и false в противном случае.
 */
function check_utf8($text) { // Java CC
    $len = strlen($text);
    for ($i = 0; $i < $len; $i++) {
        $c = ord($text[$i]);
        if ($c > 128) {
            if ($c > 247) {
                return false;
            } elseif ($c > 239) {
                $bytes = 4;
            } elseif ($c > 223) {
                $bytes = 3;
            } elseif ($c > 191) {
                $bytes = 2;
            } else {
                return false;
            }
            if (($i + $bytes) > $len) {
                return false;
            }
            while ($bytes > 1) {
                $i++;
                $b = ord($text[$i]);
                if ($b < 128 || $b > 191) {
                    return false;
                }
                $bytes--;
            }
        }
    }
    return true;
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
    $salt = mb_substr($enc .'H..', 1, 2);
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
    if (!@rename($source, $dest)) {
        throw new UploadException(UploadException::$messages['UPLOAD_SAVE']);
    }
}
/**
 * Copy file.
 * @param string $source Source path.
 * @param string $dest Destination path.
 */
function copy_uploded_file($source, $dest) {
    if (!@copy($source, $dest)) {
        throw new UploadException('Cannot copy file.');
    }
}
/**
 * Remove any ASCII control sequences except \t and \n.
 * @param string $text Text.
 */
function purify_ascii(&$text) {
    $text = str_replace(array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05",
            "\x06", "\x07", "\x08", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F", "\x10",
            "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", "\x18",
            "\x19", "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F", "\x7F"),
            '', $text);
}
/**
 * Calculate md5 hash of file.
 * @param string $path File path.
 * @return string
 * md5 hash.
 */
function calculate_file_hash($path) {
	$hash = null;

	if ( ($hash = hash_file('md5', $path)) === false) {
		throw new UploadException(UploadException::$messages['UPLOAD_HASH']);
    }

	return $hash;
}
/**
 * Check if post attachments is a oekaki picture.
 * @return boolean
 * TRUE if it ELSE otherwise.
 */
function use_oekaki() {
    return isset($_POST['use_oekaki']) && $_POST['use_oekaki'] == '1' && isset($_SESSION['oekaki']) && is_array($_SESSION['oekaki']);
}
/**
 * Check if error occurs in file uploading.
 * @param int $error File uploading error.
 */
function check_upload_error($error) {
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
            throw new UploadException::$messages['UPLOAD_ERR_INI_SIZE'];
            break;
        case UPLOAD_ERR_FORM_SIZE:
            throw new UploadException::$messages['UPLOAD_ERR_FORM_SIZE'];
            break;
        case UPLOAD_ERR_PARTIAL:
            throw new UploadException::$messages['UPLOAD_ERR_PARTIAL'];
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            throw new UploadException::$messages['UPLOAD_ERR_NO_TMP_DIR'];
            break;
        case UPLOAD_ERR_CANT_WRITE:
            throw new UploadException::$messages['UPLOAD_ERR_CANT_WRITE'];
            break;
        case UPLOAD_ERR_EXTENSION:
            throw new UploadException::$messages['UPLOAD_ERR_EXTENSION'];
            break;
    }
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
            throw new CommonException(CommonException::$messages['LINK_FAILED']);
        }
    } else {
        if(!copy($source, $dest)) {
            throw new CommonException(CommonException::$messages['COPY_FAILED']);
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
 * Check if translation enabled.
 * @param array $board Board.
 * @return
 * TRUE if translation enabled and FALSE otherwise.
 */
function is_translation_enabled($board) {
    return $board['enable_translation'] === null ? Config::ENABLE_TRANSLATION : $board['enable_translation'];
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
			throw new CommonException(CommonException::$messages['IMAGEMAGICK_FORMAT']);
		}
		$result['x'] = $image->getImageWidth();
		$result['y'] = $image->getImageHeight();
		$image->clear();
		$image->destroy();
		return $result;
	} else {
		throw new CommonException(CommonException::$messages['NO_IMG_LIB']);
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
 * @param boolean $force Create thumbnail anyway.
 * @return array
 * thumbnail dimensions.
 */
function create_thumbnail($source, $dest, $source_dimensions, $type, $resize_x, $resize_y, $force) {
    $result = array();

    // small image doesn't need to be thumbnailed
    if (!$force && $source_dimensions['x'] < $resize_x && $source_dimensions['y'] < $resize_y) {
        // big file but small image is some kind of trolling
        if (filesize($source) > Config::SMALLIMAGE_LIMIT_FILE_SIZE) {
            throw new LimitException(LimitException::$messages['MAX_SMALL_IMG_SIZE']);
        }
        $result['x'] = $source_dimensions['x'];
        $result['y'] = $source_dimensions['y'];
        link_file($source, $dest); // TODO oops I lost this function.
        return $result;
    }

    return $type['upload_handler_name']($source, $dest, $source_dimensions, $type, $resize_x, $resize_y);
}
?>
