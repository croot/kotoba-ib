<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Фукнции не попавшие в другие модули или эксперементальные.
 * @package api
 */

/* **************************
 * Начальная инициализация. *
 ****************************/

/***/
require Config::ABS_PATH . '/smarty/Smarty.class.php';
/**
 * Расширение класса для работы с шаблонизатором. Местоположение шаблонов
 * зависит от текущего языка, поэтому при его изменении по ходу работы скрипта
 * должен быть создан новый экзепляр класса шаблонизатора. При изменени таблицы
 * стилей по умолчанию, надо так же создать новый экземпляр шаблонизатора.
 *
 * В любом шаблоне всегда доступны по крайней мере две переменные. Это
 * $DIR_PATH и $STYLESHEET (смотри описание одноименных констант в config.php).
 * @package templates
 */
class SmartyKotobaSetup extends Smarty { // Java CC
    var $language;
    var $stylesheet;

    function SmartyKotobaSetup($language = Config::LANGUAGE,
            $stylesheet = Config::STYLESHEET) {
        // Try to fix warning on strftime.
        date_default_timezone_set(Config::DEFAULT_TIMEZONE);

        $this->Smarty();
        $this->language = $language;
        $this->stylesheet = $stylesheet;

        $this->template_dir = Config::ABS_PATH
                . "/smarty/kotoba/templates/locale/$language/";
        $this->compile_dir = Config::ABS_PATH
                . "/smarty/kotoba/templates_c/locale/$language/";
        $this->config_dir = Config::ABS_PATH
                . "/smarty/kotoba/config/$language/";
        $this->cache_dir = Config::ABS_PATH
                . "/smarty/kotoba/cache/$language/";
        $this->caching = 0;

        $this->assign('DIR_PATH', Config::DIR_PATH);
        $this->assign('STYLESHEET', $stylesheet);
    }
}
/**
 * Возобновляет сессию или начинает новую. Восстанавливает настройки
 * пользователя или устанавливает настройки по умолчанию. Изменяет язык
 * сообщений об ошибках, если язык пользователя отличается от языка по
 * умолчанию.
 */
function kotoba_session_start() { // Java CC
    ini_set('session.save_path', Config::ABS_PATH . '/sessions');
    ini_set('session.gc_maxlifetime', Config::SESSION_LIFETIME);
    ini_set('session.cookie_lifetime', Config::SESSION_LIFETIME);

    if (!session_start()) {
        throw new CommonException(CommonException::$messages['SESSION_START']);
    }

    // По умолчанию пользователь является Гостем.
    if (!isset($_SESSION['user']) || $_SESSION['user'] == Config::GUEST_ID) {
        $_SESSION['user'] = Config::GUEST_ID;
        $_SESSION['groups'] = array(Config::GST_GROUP_NAME);
        $_SESSION['threads_per_page'] = Config::THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = Config::POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = Config::LINES_PER_POST;
        $_SESSION['stylesheet'] = Config::STYLESHEET;
        $_SESSION['language'] = Config::LANGUAGE;
        $_SESSION['password'] = null;
        $_SESSION['goto'] = 'b'; // Переход к доске.
    }

    // Язык мог измениться на язык пользователя.
    require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
}
/**
 * Устанавливает язык и кодировку для фукнций работы с многобайтовыми
 * кодировками. Настраивает локаль.
 */
function locale_setup() { // Java CC
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
 * Загружает настройки пользователя по заданному ключевому слову.
 * @param keyword string <p>Хеш ключевого слова.</p>
 */
function load_user_settings($keyword) { // Java CC
    $user_settings = users_get_by_keyword($keyword);
    $_SESSION['user'] = $user_settings['id'];
    $_SESSION['groups'] = $user_settings['groups'];
    $_SESSION['threads_per_page'] = $user_settings['threads_per_page'];
    $_SESSION['posts_per_thread'] = $user_settings['posts_per_thread'];
    $_SESSION['lines_per_post'] = $user_settings['lines_per_post'];
    $_SESSION['stylesheet'] = $user_settings['stylesheet'];

    // Язык мог измениться на язык пользователя.
    if($_SESSION['language'] != $user_settings['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/errors.php";
    }
    $_SESSION['language'] = $user_settings['language'];
    $_SESSION['password'] = $user_settings['password'] == ''
        ? null : $user_settings['password'];
    $_SESSION['goto'] = $user_settings['goto'];
}
/**
 * Проверяет корректность номера страницы в постраничной разбивке просмотра
 * доски.
 * @param string $page Номер страницы.
 * @return string
 * Возвращает безопасный для использования номер страницы.
 */
function check_page($page) { // Java CC
    $length = strlen($page);
    $max_int_length = strlen('' . PHP_INT_MAX);
    if ($length <= $max_int_length && $length >= 1) {
        $page = RawUrlEncode($page);
        $length = strlen($page);
        if ($length > $max_int_length || (ctype_digit($page) === false)
                || $length < 1) {
            throw new FormatException(FormatException::$messages['PAGE']);
        }
    } else {
        throw new FormatException(FormatException::$messages['PAGE']);
    }
    return $page;
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
 * <p>Возвращает true, если текст корректный и false в противном случае.</p>
 */
function check_utf8($text) { // Java CC
    $len = strlen($text);
    for ($i = 0; $i < $len; $i++) {
        $c = ord($text[$i]);
        if ($c > 128) {
            if (($c > 247)) {
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
 * Создаёт имена для загружаемого файла и уменьшенной копии (если это
 * изображение), с которыми они будут сохранены.
 * @param string $ext Расширение файла, с которым он будет сохранён.
 * @return array
 * Возвращает имена файлов:<p>
 * 0 - новое имя загружаемого файла.<br>
 * 1 - имя уменьшенной копии (для изображений)<br>
 * 2 - новое имя файла без расширения.</p>
 */
function create_filenames($ext) {
	list($usec, $sec) = explode(' ', microtime());
	// Три знака после запятой.
	$saved_filename = $sec . substr($usec, 2, 5);
	// Имена всех миниатюр заканчиваются на t.
	$saved_thumbname = $saved_filename . 't.' . $ext;
	$raw_filename = $saved_filename;
	$saved_filename .= ".$ext";
	return array($saved_filename, $saved_thumbname, $raw_filename);
}
/**
 * Создаёт трипкод.
 * @param string $name Имя отправителя, которое может содержать ключевое слово
 * для создания трипкода.
 * @return array
 * Возвращает имя отправителя со сгенерированным трипкодом, если было задано
 * ключевое слово или просто имя отправителя, если ключевое слово задано не было.
 */
function calculate_tripcode($name) { // Java CC
    @list($first, $code) = @preg_split("/[#!]/", $name);
    if (!isset($code) || strlen($code) == 0) {
        return array($name, null);
    }
    $enc = mb_convert_encoding($code, 'Shift_JIS', Config::MB_ENCODING);
    $salt = substr($enc .'H..', 1, 2);
    $salt2 = preg_replace("/![\.-z]/", '.', $salt);
    $salt3 = strtr($salt2, ":;<=>?@[\]^_`", "ABCDEFGabcdef");
    $cr = crypt($code, $salt3);
    $trip = substr($cr, -10);
    return array($first, $trip);
}
/**
 * Перемещает загруженный файл на постоянное место хранения.
 * @param string $source Путь к загруженному файлу.
 * @param string $dest Путь, куда должен быть сохранён загруженный файл.
 */
function move_uploded_file($source, $dest) { // Java CC
    if (!@rename($source, $dest)) {
        throw new UploadException(UploadException::$messages['UPLOAD_SAVE']);
    }
}
/**
 * Удаляет из текста не нужные котобе управляющие символы.
 * @param string $text Текст.
 */
function purify_ascii(&$text) { // Java CC
    // Remove any ASCII control sequences except \t and \n.
    $text = str_replace(array("\0", "\x01", "\x02", "\x03", "\x04", "\x05",
            "\x06", "\a", "\b", "\v", "\f", "\r", "\x0E", "\x0F", "\x10",
            "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", "\x18",
            "\x19", "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F", "\x7F"),
            '', $text);
}
/**
 * Вычисляет md5 хеш файла.
 * @param string $path Путь к файлу.
 * @return string
 * Возвращает строку, содержащую md5 хеш.
 */
function calculate_file_hash($path) { // Java CC
	$hash = null;

	if (($hash = hash_file('md5', $path)) === false) {
		throw new UploadException(UploadException::$messages['UPLOAD_HASH']);
    }

	return $hash;
}
/**
 * Выводит информацию об одинаковых вложениях.
 * @param SmartyKotobaSetup $smarty Шаблонизатор.
 * @param string $board_name Имя доски.
 * @param array $same_attachments Одинаковые вложения.
 */
function show_same_attachments($smarty, $board_name, $same_attachments) { // Java CC
    $smarty->assign('same_uploads', $same_attachments);
    $smarty->assign('board_name', $board_name);
    $smarty->display('same_uploads.tpl');
}
/**
 * Проверяет, не произошло ли ошибки при загрузке файла.
 */
function check_upload_error($error) { // Java CC
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
 * Выделяет расширение файла из его имени.
 * @param string $name Имя файла.
 * @return string
 * Возвращает расширение файла.
 */
function get_extension($name) { // Java CC
    $parts = pathinfo($name);
    return $parts['extension'];
}
/**
 * Создает жесткую ссылку или копию файла, если жесткие ссылки не
 * поддерживаются.
 * @param source string <p>Файл источник.</p>
 * @param dest string <p>Файл назначения.</p>
 */
function link_file($source, $dest)
{
	if(function_exists('link'))
	{
		if(!link($source, $dest))
			throw new CommonException(CommonException::$messages['LINK_FAILED']);
	}
	else
		if(!copy($source, $dest))
			throw new CommonException(CommonException::$messages['COPY_FAILED']);
}
/**
 * Проверяет, является ли пользователь администратором.
 * @return boolean
 * Возвращает true, если пользователь является администратором и false в
 * противном случае.
 */
function is_admin() { // Java CC
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
 * Проверяет, является ли пользователь модератором.
 * @return boolean
 * Возвращает true, если пользователь является модератором и false в противном
 * случае.
 */
function is_mod()
{
	if(isset($_SESSION['groups']) && is_array($_SESSION['groups']))
		foreach($_SESSION['groups'] as $group_name)
			if(in_array($group_name, Config::$MOD_GROUPS))
				return true;
	return false;
}

/* *************************
 * Работа с изображениями. *
 ***************************/

/**
 * Вычисляет размеры загружаемого изображения.
 * @param array $upload_type Тип загружаемого файла.
 * @param string $file Загружаемый файл.
 * @return array
 * Возвращает размеры:<p>
 * 'x' - ширина.<br>
 * 'y' - высота.</p>
 */
function image_get_dimensions($upload_type, $file) { // Java CC
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
 * Создаёт уменьшенную копию изображения.
 * @param string $source Исходное изображение.
 * @param string $dest Файл, куда должна быть помещена уменьшенная копия.
 * @param array $source_dimensions Размеры исходного изображения.
 * @param array $type Тип файла изображения.
 * @param int $resize_x Ширина уменьшенной копии изображения.
 * @param int $resize_y Высота уменьшенной копии изображения.
 * @param boolean $force Создавать уменьшенную копию, даже если изображение мало.
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function create_thumbnail($source, $dest, $source_dimensions, $type,
        $resize_x, $resize_y, $force) {
    $result = array();

    // small image doesn't need to be thumbnailed
    if (!$force && $source_dimensions['x'] < $resize_x
            && $source_dimensions['y'] < $resize_y) {
        // big file but small image is some kind of trolling
        if (filesize($source) > Config::SMALLIMAGE_LIMIT_FILE_SIZE) {
            throw new LimitException(LimitException::$messages['MAX_SMALL_IMG_SIZE']);
        }
        $result['x'] = $source_dimensions['x'];
        $result['y'] = $source_dimensions['y'];
        link_file_new($source, $dest); // TODO oops I lost this function.
        return $result;
    }

    return $type['upload_handler_name']($source, $dest, $source_dimensions,
            $type, $resize_x, $resize_y);
}
?>