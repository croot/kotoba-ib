<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exception messages in english.
 * @package englocale
 */

/**
 * Derp. PHPDoc sucks.
 */
require_once '../config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

/* *************************
 * Data format exceptions. *
 ***************************/

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
?>
