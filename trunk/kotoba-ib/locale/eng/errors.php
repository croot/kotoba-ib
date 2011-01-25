<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Error messages in english.
 * @package englocale
 */

/* ********
 * Common *
 **********/

CommonException::$messages['ACL_RULE_EXCESS'] = 'Получена избыточная информация. Доска, нить и сообщение определяются однозначно своими идентификаторами.';
CommonException::$messages['ACL_RULE_CONFLICT'] = 'Конфликт разрешений для правила.';
CommonException::$messages['CAPTCHA'] = 'Код подтверждения не верен.';
CommonException::$messages['CONVERT_PNG'] = 'Не удалось преобразовать изображение в формат png.';
CommonException::$messages['COPY_FAILED'] = 'Не удалось скопировать файл.';
CommonException::$messages['GD_WRONG_FILETYPE'] = 'GD не поддерживает этот тип файла.';
CommonException::$messages['GROUPS_ADD'] = 'Идентификатор новой группы не был получен.';
CommonException::$messages['IMAGEMAGICK_FORMAT'] = 'Imagemagick не поддерживает этот формат файла.';
CommonException::$messages['LOG_FILE'] = 'Не удалось открыть или создать файл лога.';
CommonException::$messages['LINK_FAILED'] = 'Не удалось создать жесткую ссылку.';
CommonException::$messages['NO_IMG_LIB'] = 'Библиотеки работы с изображением отсутствуют или отключены (см. config.default).';
CommonException::$messages['REMOTE_ADDR'] = 'Invalid remote adderess.';
CommonException::$messages['REPLACE_FOR_WORD'] = 'Введите замену для слова.';
CommonException::$messages['SESSION_START'] = 'Session start failed.';
CommonException::$messages['SETLOCALE'] = 'Locale setup failed.';
CommonException::$messages['SPAM_DETECTED'] = 'Спам.';
CommonException::$messages['TEXT_UNICODE'] = 'В тексте обнаружены не юникод символы.';
CommonException::$messages['THREAD_ARCHIVED'] = 'Нить заархивирована.';
CommonException::$messages['TOO_LONG'] = 'Одно из слов имеет длину более 100 символов.';
CommonException::$messages['WORD_FOR_REPLACE'] = 'Введите слово для замены.';

/* *********************
 * No data exceptions. *
 ***********************/

NodataException::$messages['ACL_NOT_EXIST'] = 'В списке контроля доступа нет ни одного правила.';
NodataException::$messages['BOARD_NOT_FOUND'] = 'Board not found.';
NodataException::$messages['EMPTY_MESSAGE'] = 'Файл не был загружен и пустой текст сообщения.';
NodataException::$messages['GROUPS_NOT_EXIST'] = 'Не создана ни одна группа пользователей.';
// Параметры: идентификатор языка.
NodataException::$messages['LANGUAGE_NOT_EXIST'] = 'Language id=%s not exist.';
NodataException::$messages['LANGUAGES_NOT_EXIST'] = 'No languages.';
NodataException::$messages['POST_NOT_FOUND'] = 'Сообщение с идентификатором %s не найдено или не доступно для просмотра пользователю с идентификатором %s.';
NodataException::$messages['SEARCH_KEYWORD'] = 'Не задано достаточно текста для поиска.';
// Параметры: идентификатор стиля оформления.
NodataException::$messages['STYLESHEET_NOT_EXIST'] = 'Stylesheeit id=%s not exist.';
NodataException::$messages['STYLESHEETS_NOT_EXIST'] = 'No stylesheets.';
NodataException::$messages['THREAD_NOT_FOUND'] = 'Нить не найдена.';
NodataException::$messages['THREADS_EDIT'] = 'Нет нитей для редактирования настроек';
NodataException::$messages['USER_WITHOUT_GROUP'] = 'User has no group.';
NodataException::$messages['USERS_NOT_EXIST'] = 'Не создан ни один пользователь.';

/* *************************
 * Data format exceptions. *
 ***************************/

FormatException::$messages['BOARD_NAME'] = 'Board name wrong format. Board name must be string length at 1 to 16 symbols. Symbols can be latin letters and digits.';
FormatException::$messages['BOARD_BUMP_LIMIT'] = 'Bump limit must be digit greater than zero.';
FormatException::$messages['BOARD_SAME_UPLOAD'] = 'Upload policy from same files wrong format. It must be string at 1 to 32 latin letters.';

FormatException::$messages['BANS_RANGE_BEG'] = 'Начало диапазона IP-адресов имеет не верный формат.';
FormatException::$messages['BANS_RANGE_END'] = 'Конец диапазона IP-адресов имеет не верный формат.';
FormatException::$messages['BANS_REASON'] = 'Причина блокировки имеет не верный формат.';
FormatException::$messages['BANS_UNTILL'] = 'Время истечения блокировки имеет не верный формат.';

FormatException::$messages['CATEGORY_ID'] = 'Идентификатор категории имеет не верный формат.';
FormatException::$messages['CATEGORY_NAME'] = 'Имя категории имеет не верный формат.';

FormatException::$messages['GROUP_ID'] = 'Идентификатор группы имеет не верный формат.';
FormatException::$messages['GROUP_NAME'] = 'Имя группы имеет не верный формат.';

FormatException::$messages['KOTOBA_INTVAL'] = 'Object cannot be cast to intger. See description to intval() function.';
FormatException::$messages['KOTOBA_STRVAL'] = 'Arrays and Objects what not implements __toString() method, cannot be cast to string. See description to strval() function.';

FormatException::$messages['LANGUAGE_ID'] = 'Идентификатор языка имеет не верный формат.';
FormatException::$messages['LANGUAGE_CODE'] = 'ISO_639-2 код языка имеет не верный формат.';

FormatException::$messages['MACROCHAN_TAG_NAME'] = 'Тег макрочана имеет не верный формат или не существует.';

FormatException::$messages['PAGE'] = 'Номер страницы имеет не верный формат.';

FormatException::$messages['POPDOWN_HANDLER_ID'] = 'Идентификатор обработчика автоматического удаления нитей имеет не верный формат.';
FormatException::$messages['POPDOWN_HANDLER_NAME'] = 'Имя обработчика удаления нитей имеет не верный формат.';

FormatException::$messages['POST_ID'] = 'Идентификатор сообщения имеет не верный формат.';
FormatException::$messages['POST_NUMBER'] = 'Номер сообщения имеет не верный формат.';
FormatException::$messages['POST_PASSWORD'] = 'Пароль для удаления сообщения имеет не верный формат. Пароль должен быть длиной от 1 до 12 символов, включительно, состоять из цифр 0-9 или латинских букв a-z A-Z.';

FormatException::$messages['SPAMFILTER_PATTERN'] = 'Шаблон спамфильтра имеет не верный формат.';

FormatException::$messages['STYLESHEET_ID'] = 'Stylesheet id wrong format.';
FormatException::$messages['STYLESHEET_NAME'] = 'Имя файла стиля имеет не верный формат.';

FormatException::$messages['THREAD_BUMP_LIMIT'] = 'Специфичный для нити бамплимит имеет не верный формат.';
FormatException::$messages['THREAD_ID'] = 'Идентификатор нити имеет не верный формат.';
FormatException::$messages['THREAD_NUMBER'] = 'Номер оригинального сообщения имеет не верный формат.';

FormatException::$messages['UPLOAD_HANDLER_ID'] = 'Идентификатор обработчика загружаемых файлов имеет не верный формат.';
FormatException::$messages['UPLOAD_HANDLER_NAME'] = 'Имя фукнции обработчика загружаемых файлов имеет не верный формат.';

FormatException::$messages['UPLOAD_TYPE_EXTENSION'] = 'Расширение загружаемого файла имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_ID'] = 'Идентификатор типа загружаемых файлов имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_STORE_EXTENSION'] = 'Сохраняемое расширение загружаемого файла имеет не верный формат.';

FormatException::$messages['USER_GOTO'] = 'Redirection wrong format.';
FormatException::$messages['USER_ID'] = 'Идентификатор пользователя имеет не верный формат.';
FormatException::$messages['USER_KEYWORD'] = 'Keyword length must be 2 up to 32 symbols. Valid symbols is: latin letters, digits, underscore and dash.';
// Параметры: минимальное число строк, максимальное число строк.
FormatException::$messages['USER_LINES_PER_POST'] = 'Count of lines per post must be in range %s-%s.';
// Параметры: минимальное число сообщений, максимальное число сообщений.
FormatException::$messages['USER_POSTS_PER_THREAD'] = 'Count of posts per thread must be in range %s-%s.';
// Параметры: минимальное число нитей, максимальное число нитей.
FormatException::$messages['USER_THREADS_PER_PAGE'] = 'Count of threads per page must be in range %s-%s.';

FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE'] = 'Имя картинки для файла, не являющегося изображением имеет не верный формат.';

/* **************************************************************************
 * Registration, authorization, identification and access violation errors. *
 ****************************************************************************/

PermissionException::$messages['BOARD_NOT_ALLOWED'] = 'Нет прав для запрашиваемого действия с доской.';
PermissionException::$messages['GUEST'] = 'Гости не могут скрывать нити.';
PermissionException::$messages['NOT_ADMIN'] = 'You are not admin.';
PermissionException::$messages['NOT_MOD'] = 'Вы не являетесь модератором.';
PermissionException::$messages['THREAD_NOT_ALLOWED'] = 'Нет прав для запрашиваемого действия с нитью.';
PermissionException::$messages['USER_NOT_EXIST'] = 'User not exist.';

/***************************************
 * Ошибки обмена данными с хранилищем. *
 ***************************************/

DataExchangeException::$messages['SAVE_USER_SETTINGS'] = 'Не удалось сохранить настройки пользователя.';

/**************************
 * Ошибки загрузки файла. *
 **************************/

UploadException::$messages['UPLOAD_ERR_INI_SIZE'] = 'Загруженный файл превышает размер, заданный директивой upload_max_filesize в php.ini.';
UploadException::$messages['UPLOAD_ERR_FORM_SIZE'] = 'Загруженный файл превышает размер, заданный директивой MAX_FILE_SIZE, определённой в HTML форме.';
UploadException::$messages['UPLOAD_ERR_PARTIAL'] = 'Файл был загружен лишь частично.';
UploadException::$messages['UPLOAD_ERR_NO_FILE'] = 'Файл не был загружен.';
UploadException::$messages['UPLOAD_ERR_NO_TMP_DIR'] = 'Временная папка не найдена.';
UploadException::$messages['UPLOAD_ERR_CANT_WRITE'] = 'Не удалось записать файл на диск.';
UploadException::$messages['UPLOAD_ERR_EXTENSION'] = 'Загрузка файла прервана расширением.';
UploadException::$messages['UPLOAD_SAVE'] = 'Файл не удалось сохранить.';
UploadException::$messages['UPLOAD_HASH'] = 'Не удалось вычислить хеш файла.';
UploadException::$messages['UPLOAD_FILETYPE_NOT_SUPPORTED'] = 'Тип файла не поодерживается.';

/* ***************
 * Limit errors. *
 *****************/

LimitException::$messages['MAX_BOARD_TITLE'] = 'Board title too long.';
LimitException::$messages['MAX_NAME_LENGTH'] = 'Name length too long.';
LimitException::$messages['MAX_SUBJECT_LENGTH'] = 'Тема сообщения слишком длинная.';
LimitException::$messages['MAX_TEXT_LENGTH'] = 'Текст сообщения слишком длинный.';

LimitException::$messages['MAX_ANNOTATION'] = 'Annotation too long.';
LimitException::$messages['MAX_FILE_LINK'] = 'Слишком длинная сслыка на файл, имя загружаемого файла или код видео.';
LimitException::$messages['MAX_PAGE'] = 'Page not exists.';
LimitException::$messages['MAX_SMALL_IMG_SIZE'] = 'Слишком большой размер в байтах для такого маленького изображения.';
LimitException::$messages['MIN_IMG_DIMENTIONS'] = 'Размеры изображения слишком малы.';
LimitException::$messages['MIN_IMG_SIZE'] = 'Размер изображения слишком мал.';
?>