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

/*
 * Сообщения об ошибках на русском языке.
 */

/**********
 * Разное *
 **********/

CommonException::$messages['SESSION_START'] = 'Не удалось начать сессию.';
CommonException::$messages['SETLOCALE'] = 'Неудача при установке локали.';
CommonException::$messages['LOG_FILE'] = 'Не удалось открыть или создать файл лога.';
CommonException::$messages['IMAGEMAGICK_FORMAT'] = 'Imagemagick не поддерживает этот формат файла.';
CommonException::$messages['GD_WRONG_FILETYPE'] = 'GD не поддерживает этот тип файла.';
CommonException::$messages['NO_IMG_LIB'] = 'Библиотеки работы с изображением отсутствуют или отключены (см. config.default).';
CommonException::$messages['CONVERT_PNG'] = 'Не удалось преобразовать изображение в формат png.';
CommonException::$messages['LINK_FAILED'] = 'Не удалось создать жесткую ссылку.';
CommonException::$messages['COPY_FAILED'] = 'Не удалось скопировать файл.';
CommonException::$messages['THREAD_ARCHIVED'] = 'Нить заархивирована.';
CommonException::$messages['ACL_RULE_EXCESS'] = 'Получена избыточная информация. Доска, нить и сообщение определяются однозначно своими идентификаторами. Читайте в res/notes.txt раздел, посвященный правилам.';
CommonException::$messages['ACL_RULE_CONFLICT'] = 'Конфликт разрешений для правила. Читайте в res/notes.txt раздел, посвященный правилам.';
CommonException::$messages['CAPTCHA'] = 'Код подтвержения не верен.';

/*****************************
 * Ошибки отсутствия данных. *
 *****************************/

NodataException::$messages['KEYWORD_NOT_SPECIFED'] = 'Ключевое слово не задано.';
// Параметры: хеш ключевого слова.
NodataException::$messages['USER_WITHOUT_GROUP'] = 'Пользователь с хешем ключевого слова %s не входит ни в одну группу.';
NodataException::$messages['THREADS_PER_PAGE_NOT_SPECIFED'] = 'Количество нитей на странице просмотра доски не задано.';
NodataException::$messages['POSTS_PER_THREAD_NOT_SPECIFED'] = 'Количество сообщений в нити на странице просмотра доски не задано.';
NodataException::$messages['LINES_PER_POST_NOT_SPECIFED'] = 'Количество строк в сообщении на странице просмотра доски не задано.';
NodataException::$messages['STYLESHEETS_NOT_EXIST'] = 'Не задан ни один стиль оформления.';
// Параметры: идентификатор стиля оформления.
NodataException::$messages['STYLESHEET_NOT_EXIST'] = 'Стиля оформления с идентификатором %s не существует.';
NodataException::$messages['STYLESHEET_NAME_NOT_SPECIFED'] = 'Не задано имя стиля оформления.';
NodataException::$messages['STYLESHEET_ID_NOT_SPECIFED'] = 'Не задан идентификатор стиля оформления.';
NodataException::$messages['LANGUAGES_NOT_EXIST'] = 'Не задан ни один язык.';
// Параметры: идентификатор языка.
NodataException::$messages['LANGUAGE_NOT_EXIST'] = 'Языка с идентификатором %s не существует.';
NodataException::$messages['LANGUAGE_NAME_NOT_SPECIFED'] = 'Не задано имя языка.';
NodataException::$messages['LANGUAGE_ID_NOT_SPECIFED'] = 'Не задан идентификатор языка.';
NodataException::$messages['GROUPS_NOT_EXIST'] = 'Не создана ни одна группа пользователей.';
NodataException::$messages['GROUP_NAME_NOT_SPECIFED'] = 'Не задано имя группы.';
NodataException::$messages['USER_ID_NOT_SPECIFED'] = 'Не задан идентификатор пользователя.';
NodataException::$messages['USERS_NOT_EXIST'] = 'Не создан ни один пользователь.';
NodataException::$messages['GROUP_ID_NOT_SPECIFED'] = 'Не задан идентификатор группы.';
NodataException::$messages['ACL_NOT_EXIST'] = 'В списке контроля доступа нет ни одной записи.';
NodataException::$messages['BOARD_ID_NOT_SPECIFED'] = 'Не задан идентификатор доски.';
NodataException::$messages['POST_ID_NOT_SPECIFED'] = 'Не задан идентификатор сообщения.';
NodataException::$messages['CATEGORY_NAME_NOT_SPECIFED'] = 'Не задано имя категории.';
NodataException::$messages['UPLOAD_HANDLER_NAME_NOT_SPECIFED'] = 'Не задано имя обработчика загружаемых файлов.';
NodataException::$messages['POPDOWN_HANDLER_NAME_NOT_SPECIFED'] = 'Не задано имя обработчика удаления нитей.';
NodataException::$messages['UPLOAD_HANDLER_ID_NOT_SPECIFED'] = 'Не задан идентификатор обработчика загружаемых файлов.';
NodataException::$messages['UPLOAD_TYPE_EXTENSION_NOT_SPECIFED'] = 'Не задано расширение загружаемого файла.';
NodataException::$messages['UPLOAD_TYPE_STORE_EXTENSION_NOT_SPECIFED'] = 'Не задано сохраняемое расширение загружаемого файла.';
NodataException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE_NOT_SPECIFED'] = 'Не задано имя картинки для файла, не являющегося изображением.';
NodataException::$messages['UPLOAD_TYPE_ID_NOT_SPECIFED'] = 'Не задан идентификатор типа загружаемых файлов.';
NodataException::$messages['BOARD_TITLE_NOT_SPECIFED'] = 'Не задан заголовок доски.';
NodataException::$messages['BOARD_BUMP_LIMIT_NOT_SPECIFED'] = 'Не задан бампилимит.';
NodataException::$messages['THREADS_EDIT'] = 'Нет нитей для редактирования настроек';
// Параметры: имя доски.
NodataException::$messages['BOARD_NOT_FOUND'] = 'Доски с именем %s не существует.';
NodataException::$messages['POST_NUMBER_NOT_FOUND'] = 'Сообщения с заданным номером не существует.';
// Параметры: номер нити, идентификатор доски.
NodataException::$messages['THREAD_NOT_FOUND'] = 'Нити с номером %s не существует на доске с идентификатором %s.';
NodataException::$messages['EMPTY_MESSAGE'] = 'Файл не был загружен и пустой текст сообщения.';

/**************************
 * Ошибки формата данных. *
 **************************/

FormatException::$messages['KEYWORD'] = 'Длина ключего слова должна быть от 16 до 32 символов, допустимые значения: латинские буквы, цифры, нижнее подчеркивание и дефис.';
// Параметры: минимальное число нитей, максимальное число нитей.
FormatException::$messages['THREADS_PER_PAGE'] = 'Число нитей на странице просмотра доски должно быть в пределах %s-%s.';
// Параметры: минимальное число сообщений, максимальное число сообщений.
FormatException::$messages['POSTS_PER_THREAD'] = 'Число сообщений в нити на странице просмотра доски должно быть в пределах %s-%s.';
// Параметры: минимальное число строк, максимальное число строк.
FormatException::$messages['LINES_PER_POST'] = 'Число строк в сообщении на странице просмотра доски должно быть в пределах %s-%s.';
FormatException::$messages['STYLESHEET_NAME'] = 'Имя стиля оформления имеет не верный формат.';
FormatException::$messages['STYLESHEET_ID'] = 'Идентификатор стиля оформления имеет не верный формат.';
FormatException::$messages['LANGUAGE_NAME'] = 'Имя языка имеет не верный формат.';
FormatException::$messages['LANGUAGE_ID'] = 'Идентификатор языка имеет не верный формат.';
FormatException::$messages['GROUP_NAME'] = 'Имя группы имеет не верный формат.';
FormatException::$messages['USER_ID'] = 'Идентификатор пользователя имеет не верный формат.';
FormatException::$messages['GROUP_ID'] = 'Идентификатор группы имеет не верный формат.';
FormatException::$messages['BOARD_ID'] = 'Идентификатор доски имеет не верный формат.';
FormatException::$messages['THREAD_ID'] = 'Идентификатор нити имеет не верный формат.';
FormatException::$messages['POST_ID'] = 'Идентификатор сообщения имеет не верный формат.';
FormatException::$messages['CATEGORY_NAME'] = 'Имя категории имеет не верный формат.';
FormatException::$messages['UPLOAD_HANDLER_NAME'] = 'Имя обработчика загружаемых файлов имеет не верный формат.';
FormatException::$messages['POPDOWN_HANDLER_NAME'] = 'Имя обработчика удаления нитей имеет не верный формат.';
FormatException::$messages['UPLOAD_HANDLER_ID'] = 'Идентификатор обработчика загружаемых файлов имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_EXTENSION'] = 'Расширение загружаемого файла имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_STORE_EXTENSION'] = 'Сохраняемое расширение загружаемого файла имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_THUMBNAIL_IMAGE'] = 'Имя картинки для файла, не являющегося изображением имеет не верный формат.';
FormatException::$messages['UPLOAD_TYPE_ID'] = 'Идентификатор типа загружаемых файлов имеет не верный формат.';
FormatException::$messages['CATEGORY_ID'] = 'Идентификатор категории имеет не верный формат.';
FormatException::$messages['POPDOWN_HANDLER_ID'] = 'Идентификатор обработчика удаления нитей имеет не верный формат.';
FormatException::$messages['BOARD_SAME_UPLOAD'] = 'Название политики загрузки одинаковых файлов имеет не верный формат.';
FormatException::$messages['BOARD_TITLE'] = 'Заголовок доски имеет не верный формат.';
FormatException::$messages['BOARD_BUMP_LIMIT'] = 'Бампилимит имеет не верный формат.';
FormatException::$messages['BOARD_NAME'] = 'Имя доски имеет не верный формат.';
FormatException::$messages['BANS_RANGE_BEG'] = 'Начало диапазона IP адресов имеет не верный формат.';
FormatException::$messages['BANS_RANGE_END'] = 'Конец диапазона IP адресов имеет не верный формат.';
FormatException::$messages['BANS_REASON'] = 'Причина бана имеет не верный формат.';
FormatException::$messages['BANS_UNTILL'] = 'Время истечения бана имеет не верный формат.';
FormatException::$messages['THREAD_BUMP_LIMIT'] = 'Бампилимит имеет не верный формат.';
FormatException::$messages['PAGE'] = 'Номер страницы имеет не верный формат.';
FormatException::$messages['POST_NUMBER'] = 'Номер сообщения имеет не верный формат.';
FormatException::$messages['THREAD_NUMBER'] = 'Номер сообщения имеет не верный формат.';
FormatException::$messages['POST_PASSWORD'] = 'Пароль для удаления сообщения имеет не верный формат.';

/**********************************************************************
 * Ошибки при регистрации, авторизация, идентификация и прав доступа. *
 **********************************************************************/

// Параметры: хеш ключевого слова.
PremissionException::$messages['USER_NOT_EXIST'] = 'Пользователя с хешем ключевого слова %s не существует.';
PremissionException::$messages['NOT_ADMIN'] = 'Вы не являетесь администратром.';
PremissionException::$messages['NOT_MOD'] = 'Вы не являетесь модератором.';
PremissionException::$messages['THREAD_NOT_ALLOWED'] = 'Нет прав для запрашиваемого действия с нитью.';
PremissionException::$messages['BOARD_NOT_ALLOWED'] = 'Нет прав для запрашиваемого действия с доской.';
PremissionException::$messages['GUEST'] = 'Гости не могут скрывать нити.';

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

/**************************
 * Нарушение ограничений. *
 **************************/

LimitException::$messages['MIN_IMG_SIZE'] = 'Размер изображения слишком мал.';
LimitException::$messages['MAX_TEXT_LENGTH'] = 'Текст сообщения слишком длинный.';
LimitException::$messages['MAX_SUBJECT_LENGTH'] = 'Тема сообщения слишком длинная.';
LimitException::$messages['MAX_NAME_LENGTH'] = 'Имя отправителя слишком длинное.';
LimitException::$messages['MIN_IMG_DIMENTIONS'] = 'Размеры изображения слишком малы.';
LimitException::$messages['MAX_SMALL_IMG_SIZE'] = 'Слишком большой размер в байтах для такого маленького изображения.';
LimitException::$messages['MAX_PAGE'] = 'Номер страницы слишком большой. Такой страницы не существует.';
LimitException::$messages['MAX_ANNOTATION'] = 'Аннотация слишком длинная.';
?>