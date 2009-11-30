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
// Фукнции общего назначения, не попавшие в другие модули или эксперементальные.

/****************************
 * Начальная инициализация. *
 ****************************/

/*
 * Расширение класса для работы с шаблонизатором. Местоположение шаблонов
 * зависит от текущего языка, поэтому при его изменении по ходу работы скрипта
 * должен быть создан новый экзепляр класса шаблонизатора. При изменени таблицы
 * стилей по умолчанию, надо так же создать новый экземпляр шаблонизатора.
 *
 * В любом шаблоне всегда доступны по крайней мере две переменные. Это
 * $DIR_PATH и $STYLESHEET (смотри описание одноименных констант в config.php).
 */
require Config::ABS_PATH . '/smarty/Smarty.class.php';
class SmartyKotobaSetup extends Smarty
{
	var $language;
	var $stylesheet;

	function SmartyKotobaSetup($language = Config::LANGUAGE, $stylesheet = Config::STYLESHEET)
	{
		$this->Smarty();
		$this->language = $language;
		$this->stylesheet = $stylesheet;

		$this->template_dir = Config::ABS_PATH . "/smarty/kotoba/templates/$language/";
		$this->compile_dir = Config::ABS_PATH . "/smarty/kotoba/templates_c/$language/";
		$this->config_dir = Config::ABS_PATH . "/smarty/kotoba/config/$language/";
		$this->cache_dir = Config::ABS_PATH . "/smarty/kotoba/cache/$language/";
		$this->caching = 0;

		$this->assign('DIR_PATH', Config::DIR_PATH);
		$this->assign('STYLESHEET', $stylesheet);
	}
}
/**
 * Возобновляет сессию пользователя или начинает новую. Восстанавливает
 * настройки пользователя или устанавливает настройки по умолчанию. Изменяет
 * язык сообщений об ошибках, если язык пользователя отличается от языка по
 * умолчанию.
 */
function kotoba_session_start()
{
	ini_set('session.save_path', Config::ABS_PATH . '/sessions/');
	ini_set('session.gc_maxlifetime', Config::SESSION_LIFETIME);
	ini_set('session.cookie_lifetime', Config::SESSION_LIFETIME);
	if(! session_start())
		throw new CommonException(CommonException::$messages['SESSION_START']);
	/* По умолчанию пользователь является Гостем. */
	if(!isset($_SESSION['user']) || $_SESSION['user'] == Config::GUEST_ID)
	{
		$_SESSION['user'] = Config::GUEST_ID;
		$_SESSION['groups'] = array(Config::GST_GROUP_NAME);
		$_SESSION['threads_per_page'] = Config::THREADS_PER_PAGE;
		$_SESSION['posts_per_thread'] = Config::POSTS_PER_THREAD;
		$_SESSION['lines_per_post'] = Config::LINES_PER_POST;
		$_SESSION['stylesheet'] = Config::STYLESHEET;
		$_SESSION['language'] = Config::LANGUAGE;
		$_SESSION['rempass'] = null;
	}
	/* Язык мог измениться на язык пользователя. */
	require Config::ABS_PATH . "/modules/lang/{$_SESSION['language']}/errors.php";
}
/**
 * Устанавливает язык и кодировку для фукнций работы с многобайтовыми
 * кодировками. Настраивает локаль.
 */
function locale_setup()
{
	mb_language(Config::MB_LANGUAGE);
	mb_internal_encoding(Config::MB_ENCODING);
	if(!setlocale(LC_ALL, Config::$LOCALE_NAMES))
		throw new CommonException(CommonException::$messages['SETLOCALE']);
}

/***********
 * Разное. *
 ***********/

/**
 * Загружает настройки пользователя с ключевым словом $keyword.
 */
function load_user_settings($keyword)
{
	$user_settings = users_get_settings($keyword);
	$_SESSION['user'] = $user_settings['id'];
	$_SESSION['groups'] = $user_settings['groups'];
	$_SESSION['threads_per_page'] = $user_settings['threads_per_page'];
	$_SESSION['posts_per_thread'] = $user_settings['posts_per_thread'];
	$_SESSION['lines_per_post'] = $user_settings['lines_per_post'];
	$_SESSION['stylesheet'] = $user_settings['stylesheet'];
	/* Язык мог измениться на язык пользователя. */
	if($_SESSION['language'] != $user_settings['language'])
		require "modules/lang/{$_SESSION['language']}/errors.php";
	$_SESSION['language'] = $user_settings['language'];
	$_SESSION['rempass'] = $user_settings['rempass'];
}
/**
 * Проверяет корректность номера страницы в постраничной разбивке просмотра
 * доски.
 *
 * Аргументы:
 * $page - номер страницы.
 *
 * Возвращает безопасный для использования номер страницы.
 */
function check_page($page)
{
	$length = strlen($page);
	$max_int_length = strlen('' . PHP_INT_MAX);
	if($length <= $max_int_length && $length >= 1)
	{
		$page = RawUrlEncode($page);
		$length = strlen($page);
		if($length > $max_int_length || (ctype_digit($page) === false) || $length < 1)
			throw new FormatException(FormatException::$messages['PAGE']);
	}
	else
		throw new FormatException(FormatException::$messages['PAGE']);
	return $page;
}
/**
 * Проверяет, загружено ли расширение php.
 * @param string
 * <p>Имя расширения</p>
 * @return bool
 * <p>Возвращает true, если расширение загружено и false в противном случае.</p>
 */
function check_module($name)
{
	return extension_loaded($name);
}
/**
 * Создаёт имена для загружаемого файла и уменьшенной копии (если это
 * изображение), с которыми они будут сохранены.
 * @param stored_ext string
 * <p>Расширение файла, с которым он будет сохранён.</p>
 * @return array
 * Возвращает имена файлов:<p>
 * 0 - новое имя загружаемого файла.<br>
 * 1 - имя уменьшенной копии (для изображений)<br>
 * 2 - новое имя файла без расширения.
 * </p>
 */
function create_filenames($stored_ext)
{
	list($usec, $sec) = explode(' ', microtime());
	// Три знака после запятой.
	$saved_filename = $sec . substr($usec, 2, 5);
	// Имена всех миниатюр заканчиваются на t.
	$saved_thumbname = $saved_filename . 't.' . $stored_ext;
	$raw_filename = $saved_filename;
	$saved_filename .= ".$stored_ext";
	return array($saved_filename, $saved_thumbname, $raw_filename);
}
/**
 * Создаёт трипкод.
 * @param name string <p>Имя отправителя, которое может содержать ключевое слово
 * для создания трипкода.</p>
 * @return string
 * Возвращает имя отправителя со сгенерированным трипкодом, если было задано
 * ключевое слово или просто имя отправителя, если ключевое слово задано небыло.
 */
function calculate_tripcode($name)
{
	@list($first, $code) = @preg_split("/[#!]/", $name);
	if(!isset($code) || strlen($code) == 0)
	{
		return $name;
	}
	$enc = mb_convert_encoding($code, 'Shift_JIS', Config::MB_ENCODING);
	$salt = substr($enc.'H..', 1, 2);
	$salt2 = preg_replace("/![\.-z]/", '.', $salt);
	$salt3 = strtr($salt2, ":;<=>?@[\]^_`", "ABCDEFGabcdef");
	$cr = crypt($code, $salt3);
	$trip = substr($cr, -10);
	return "$first!$trip";
}
/**
 * Перемещает загруженный файл на постоянное место хранения.
 * @param source string <p>Путь к загруженному файлу.</p>
 * @param dest string <p>Путь, куда должен быть сохранён загруженный файл.</p>
 */
function move_uploded_file($source, $dest)
{
	if(!@rename($source, $dest))
		throw new UploadException(UploadException::$messages['UPLOAD_SAVE']);
}
/**
 * Вычисляет md5 хеш файла.
 * @param path string <p>Путь к файлу.</p>
 * @return string
 * Возвращает строку, содержащую md5 хеш.
 */
function calculate_file_hash($path)
{
	$hash = null;
	if(($hash = hash_file('md5', $path)) === false)
		throw new UploadException(UploadException::$messages['UPLOAD_HASH']);
	return $hash;
}
/**
 * Выводит информацию о загруженых ранее файлах.
 * @param smarty SmartyKotobaSetup <p>Шаблонизатор.</p>
 * @param board_name string <p>Имя доски.</p>
 * @param same_uploads array <p>Файлы.</p>
 */
function show_same_uploads($smarty, $board_name, $same_uploads)
{
	$smarty->assign('same_uploads', $same_uploads);
	$smarty->assign('board_name', $board_name);
	$smarty->display('same_uploads.tpl');
}
/**
 * Проверяет, не произошло ли ошибки при загрузке файла.
 */
function check_upload_error($error)
{
	switch($error)
	{
		case UPLOAD_ERR_INI_SIZE:
			throw new UploadException::$messages['UPLOAD_ERR_INI_SIZE'];
			break;
		case UPLOAD_ERR_FORM_SIZE:
			throw new UploadException::$messages['UPLOAD_ERR_FORM_SIZE'];
			break;
		case UPLOAD_ERR_PARTIAL:
			throw new UploadException::$messages['UPLOAD_ERR_PARTIAL'];
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new UploadException::$messages['UPLOAD_ERR_NO_FILE'];
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
 *
 * Аргументы:
 * $name - имя файла.
 *
 * Возвращает расширение файла.
 */
function get_extension($name)
{
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

/***************************
 * Работа с изображениями. *
 ***************************/

/**
 * Вычисляет размеры загружаемого изображения.
 * @param upload_type array
 * <p>Тип загружаемого файла.</p>
 * @param file string
 * <p>Загружаемый файл.</p>
 * @return array
 * Возвращает размеры:<p>
 * 'x' - ширина.<br>
 * 'y' - высота.</p>
 */
function image_get_dimensions($upload_type, $file)
{
	$result = array();
	//gd library formats
	if((check_module('gd') | check_module('gd2')) & Config::TRY_IMAGE_GD)
	{
		$dimensions = getimagesize($file);
		$result['x'] = $dimensions[0];
		$result['y'] = $dimensions[1];
		return $result;
	}
	//image magick library
	elseif(check_module('imagick') & Config::TRY_IMAGE_IM)
	{
		$image = new Imagick($file);
		if(!$image->setImageFormat($upload_type['extension']))
		{
			throw new CommonException(CommonException::$messages['IMAGEMAGICK_FORMAT']);
		}
		$result['x'] = $image->getImageWidth();
		$result['y'] = $image->getImageHeight();
		$image->clear();
		$image->destroy();
		return $result;
	}
	else
	{
		throw new CommonException(CommonException::$messages['NO_IMG_LIB']);
	}
}
/**
 * Создаёт уменьшенную копию изображения.
 * @param source string <p>Исходное изображение.</p>
 * @param dest string <p>Файл, куда должна быть помещена уменьшенная копия.</p>
 * @param source_dimensions array <p>Размеры исходного изображения.</p>
 * @param type array <p>Тип файла изображения.</p>
 * @param resize_x mixed<p>Ширина уменьшенной копии изображения.</p>
 * @param resize_y mixed<p>Высота уменьшенной копии изображения.</p>
 * @param force bool<p>Создавать уменьшенную копию, даже если изображение мало.</p>
 * @return array
 * Возвращает размеры созданной уменьшенной копии изображения:<p>
 * 'x' - ширина изображения.<br>
 * 'y' - высота изображения.</p>
 */
function create_thumbnail($source, $dest, $source_dimensions, $type,
	$resize_x, $resize_y, $force)
{
	$result = array();
	// small image doesn't need to be thumbnailed
	if(!$force && $source_dimensions['x'] < $resize_x
		&& $source_dimensions['y'] < $resize_y)
	{
		// big file but small image is some kind of trolling
		if(filesize($source) > Config::SMALLIMAGE_LIMIT_FILE_SIZE)
			throw new LimitException(LimitException::$messages['MAX_SMALL_IMG_SIZE']);
		$result['x'] = $source_dimensions['x'];
		$result['y'] = $source_dimensions['y'];
		link_file_new($source, $dest);
		return $result;
	}
	return $type['upload_handler_name']($source, $dest, $source_dimensions,
		$type, $resize_x, $resize_y);
}
?>