<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exception messages in english.
 * @package englocale
 */

/* ********
 * Common *
 **********/

CommonException::$messages['CONVERT_PNG'] = 'Не удалось преобразовать изображение в формат png.';
CommonException::$messages['COPY_FAILED'] = 'Copy file failed.';
CommonException::$messages['GD_WRONG_FILETYPE'] = 'GD не поддерживает этот тип файла.';
CommonException::$messages['GROUPS_ADD'] = 'New group was not added.';
CommonException::$messages['IMAGEMAGICK_FORMAT'] = 'Imagemagick doesn\'t support this file format.';
CommonException::$messages['LOG_FILE'] = 'Не удалось открыть или создать файл лога.';
CommonException::$messages['LINK_FAILED'] = 'Hard link creation failed.';
CommonException::$messages['NO_IMG_LIB'] = 'Image libraries disabled or doesn\'t work.';
CommonException::$messages['REPLACE_FOR_WORD'] = 'Введите замену для слова.';
CommonException::$messages['SESSION_START'] = 'Session start failed.';
CommonException::$messages['SETLOCALE'] = 'Locale setup failed.';
CommonException::$messages['SPAM_DETECTED'] = 'Spam.';
CommonException::$messages['TEXT_UNICODE'] = 'Invlid unicode characters deteced.';
CommonException::$messages['THREAD_ARCHIVED'] = 'Thread was archived.';
CommonException::$messages['THREAD_CLOSED'] = 'Thread closed.';
CommonException::$messages['TOO_LONG'] = 'Одно из слов имеет длину более 100 символов.';
CommonException::$messages['WORD_FOR_REPLACE'] = 'Введите слово для замены.';

/* *********************
 * No data exceptions. *
 ***********************/

NodataException::$messages['EMPTY_MESSAGE'] = 'No attachment and message is empty.';
NodataException::$messages['GROUPS_NOT_EXIST'] = 'No one group exists.';
// Параметры: идентификатор языка.
NodataException::$messages['LANGUAGE_NOT_EXIST'] = 'Language id=%s not exist.';
NodataException::$messages['LANGUAGES_NOT_EXIST'] = 'No languages.';
NodataException::$messages['POST_NOT_FOUND'] = 'Post not found or you have no permission to it.';
NodataException::$messages['REQUEST_METHOD'] = 'Request method not defined or unexpected.';
NodataException::$messages['SEARCH_KEYWORD'] = 'Search keyword not set or too short.';
// Параметры: идентификатор стиля оформления.
NodataException::$messages['STYLESHEET_NOT_EXIST'] = 'Stylesheeit id=%s not exist.';
NodataException::$messages['STYLESHEETS_NOT_EXIST'] = 'No stylesheets.';
NodataException::$messages['THREAD_NOT_FOUND'] = 'Thread not found.';
NodataException::$messages['THREADS_EDIT'] = 'No thread to edit.';
NodataException::$messages['USER_WITHOUT_GROUP'] = 'User has no group.';
NodataException::$messages['USERS_NOT_EXIST'] = 'No one user exists.';

/* *************************
 * Data format exceptions. *
 ***************************/

FormatException::$messages['BOARD_NAME'] = 'Board name wrong format. Board name must be string length at 1 to 16 symbols. Symbols can be latin letters and digits.';
FormatException::$messages['BOARD_BUMP_LIMIT'] = 'Bump limit must be digit greater than zero.';
FormatException::$messages['BOARD_SAME_UPLOAD'] = 'Upload policy from same files wrong format. It must be string at 1 to 32 latin letters.';

FormatException::$messages['BANS_RANGE_BEG'] = 'Begining of IP-address range has wrong format.';
FormatException::$messages['BANS_RANGE_END'] = 'End of IP-address range has wrong format.';
FormatException::$messages['BANS_REASON'] = 'Ban reason has wrong format.';
FormatException::$messages['BANS_UNTILL'] = 'Время истечения блокировки имеет не верный формат.';

FormatException::$messages['CATEGORY_ID'] = 'Идентификатор категории имеет не верный формат.';
FormatException::$messages['CATEGORY_NAME'] = 'Category name wrong format.';

FormatException::$messages['GROUP_ID'] = 'Идентификатор группы имеет не верный формат.';
FormatException::$messages['GROUP_NAME'] = 'Group name wrong format.';

FormatException::$messages['KOTOBA_INTVAL'] = 'Object cannot be cast to intger. See description to intval() function.';
FormatException::$messages['KOTOBA_STRVAL'] = 'Arrays and Objects what not implements __toString() method, cannot be cast to string. See description to strval() function.';

FormatException::$messages['LANGUAGE_ID'] = 'Идентификатор языка имеет не верный формат.';
FormatException::$messages['LANGUAGE_CODE'] = 'ISO_639-2 code wrong format.';

FormatException::$messages['MACROCHAN_TAG_NAME'] = 'Macrochan tag name wrong format or not exist.';

FormatException::$messages['PAGE'] = 'Номер страницы имеет не верный формат.';

FormatException::$messages['POPDOWN_HANDLER_ID'] = 'Идентификатор обработчика автоматического удаления нитей имеет не верный формат.';
FormatException::$messages['POPDOWN_HANDLER_NAME'] = 'Popdown handler name wrong format.';

FormatException::$messages['POST_ID'] = 'Идентификатор сообщения имеет не верный формат.';
FormatException::$messages['POST_NUMBER'] = 'Номер сообщения имеет не верный формат.';
FormatException::$messages['POST_PASSWORD'] = 'Password wrong format. Password must be at 1 to 12 symbols length. Valid symbold is digits and latin letters.';

FormatException::$messages['SPAMFILTER_PATTERN'] = 'Wrong spamfilter pattern.';

FormatException::$messages['STYLESHEET_ID'] = 'Stylesheet id wrong format.';
FormatException::$messages['STYLESHEET_NAME'] = 'Stylesheet name wrong format.';

FormatException::$messages['THREAD_BUMP_LIMIT'] = 'Специфичный для нити бамплимит имеет не верный формат.';
FormatException::$messages['THREAD_ID'] = 'Идентификатор нити имеет не верный формат.';
FormatException::$messages['THREAD_NUMBER'] = 'Номер оригинального сообщения имеет не верный формат.';

FormatException::$messages['UPLOAD_HANDLER_ID'] = 'Идентификатор обработчика загружаемых файлов имеет не верный формат.';
FormatException::$messages['UPLOAD_HANDLER_NAME'] = 'Upload handler function name has a wrong format.';

FormatException::$messages['UPLOAD_TYPE_EXTENSION'] = 'Extension has wrong format.';
FormatException::$messages['UPLOAD_TYPE_ID'] = 'Идентификатор типа загружаемых файлов имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_STORE_EXTENSION'] = 'Stored extension has wrong format.';

FormatException::$messages['USER_GOTO'] = 'Redirection wrong format.';
FormatException::$messages['USER_ID'] = 'Идентификатор пользователя имеет не верный формат.';
FormatException::$messages['USER_KEYWORD'] = 'Keyword length must be 2 up to 32 symbols. Valid symbols is: latin letters, digits, underscore and dash.';
// Параметры: минимальное число строк, максимальное число строк.
FormatException::$messages['USER_LINES_PER_POST'] = 'Count of lines per post must be in range %s-%s.';
// Параметры: минимальное число сообщений, максимальное число сообщений.
FormatException::$messages['USER_POSTS_PER_THREAD'] = 'Count of posts per thread must be in range %s-%s.';
// Параметры: минимальное число нитей, максимальное число нитей.
FormatException::$messages['USER_THREADS_PER_PAGE'] = 'Count of threads per page must be in range %s-%s.';

FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE'] = 'Thumbnail name for nonimage files has wrong format.';

/* **************************************************************************
 * Registration, authorization, identification and access violation errors. *
 ****************************************************************************/

PermissionException::$messages['BOARD_NOT_ALLOWED'] = 'You have no rights to do it.';
PermissionException::$messages['GUEST'] = 'Guests cannot hide threads.';
PermissionException::$messages['NOT_ADMIN'] = 'You are not admin.';
PermissionException::$messages['NOT_MOD'] = 'You are not moderator.';
PermissionException::$messages['THREAD_NOT_ALLOWED'] = 'You have no rights to do it.';
PermissionException::$messages['USER_NOT_EXIST'] = 'User not exist.';

/***************************************
 * Ошибки обмена данными с хранилищем. *
 ***************************************/

DataExchangeException::$messages['SAVE_USER_SETTINGS'] = 'Не удалось сохранить настройки пользователя.';

/* ****************
 * Upload errors. *
 ******************/

UploadException::$messages['UPLOAD_ERR_INI_SIZE'] = 'Загруженный файл превышает размер, заданный директивой upload_max_filesize в php.ini.';
UploadException::$messages['UPLOAD_ERR_FORM_SIZE'] = 'Загруженный файл превышает размер, заданный директивой MAX_FILE_SIZE, определённой в HTML форме.';
UploadException::$messages['UPLOAD_ERR_PARTIAL'] = 'Файл был загружен лишь частично.';
UploadException::$messages['UPLOAD_ERR_NO_FILE'] = 'Файл не был загружен.';
UploadException::$messages['UPLOAD_ERR_NO_TMP_DIR'] = 'Временная папка не найдена.';
UploadException::$messages['UPLOAD_ERR_CANT_WRITE'] = 'Не удалось записать файл на диск.';
UploadException::$messages['UPLOAD_ERR_EXTENSION'] = 'Загрузка файла прервана расширением.';
UploadException::$messages['UPLOAD_FILETYPE_NOT_SUPPORTED'] = 'File type not allowed to upload here.';
UploadException::$messages['UPLOAD_HASH'] = 'File hash calcutalion failed.';
UploadException::$messages['UPLOAD_SAVE'] = 'Cannot save file.';
UploadException::$messages['UNKNOWN'] = 'Неизвестное вложение.';

/* ***************
 * Limit errors. *
 *****************/

LimitException::$messages['MAX_BOARD_TITLE'] = 'Board title too long.';
LimitException::$messages['MAX_NAME_LENGTH'] = 'Name length too long.';
LimitException::$messages['MAX_SUBJECT_LENGTH'] = 'Subject too long.';
LimitException::$messages['MAX_TEXT_LENGTH'] = 'Text too long.';

LimitException::$messages['MAX_ANNOTATION'] = 'Annotation too long.';
LimitException::$messages['MAX_FILE_LINK'] = 'Link too long.';
LimitException::$messages['MAX_PAGE'] = 'Page not exists.';
LimitException::$messages['MAX_SMALL_IMG_SIZE'] = 'So small image cannot have so many data.';
LimitException::$messages['MIN_IMG_DIMENTIONS'] = 'Image dimensions too small.';
LimitException::$messages['MIN_IMG_SIZE'] = 'Image too small.';

LimitException::$messages['WORD_TOO_LONG'] = 'Word too long.'
?>