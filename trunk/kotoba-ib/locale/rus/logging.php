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
 * Сообщения на русском языке для записи в лог.
 * @package ruslocale
 */

/* *******************************************
 * Активность администраторов и модераторов. *
 *********************************************/

// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_GROUPS'] = 'Задействовано редактирование групп пользователей. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_USER_GROUPS'] = 'Задействовано редактирование принадлежности пользователей группам. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['THREADS_EDIT_FUNCTIONS'] = 'Задействован фукнционал для редактирования нитей. Пользователь %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_LANGUAGES'] = 'Задействовано редактирование языков. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_STYLESHEETS'] = 'Задействовано редактирование стилей оформления. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_CATEGORIES'] = 'Задействовано редактирование категорий досок. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_UPLOAD_HANDLERS'] = 'Задействовано редактирование обработчиков загружаемых файлов. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_POPDOWN_HANDLERS'] = 'Задействовано редактирование обработчиков удаления нитей. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_UPLOAD_TYPES'] = 'Задействовано редактирование типов загружаемых файлов. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARD_UPLOAD_TYPES'] = 'Задействовано редактирование связей загружаемых типов файлов с досками. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARDS'] = 'Задействовано редактирование досок. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_WORDFILTER'] = 'Задействовано редактирование фильтрации слов. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_BOARDS_ANNOTATION'] = 'Задействовано редактирование аннотаций досок. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_UPDATE_MACROCHAN'] = 'Использован скрипт обновления данных с макрочана. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_MANAGE'] = 'Задействован скрипт административных фукнций и фукнций модераторов. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['MOD_FUNCTIONS_MANAGE'] = 'Задействован скрипт административных фукнций и фукнций модераторов. Модератор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADM_FUNCTIONS_REPORTS'] = 'Задействован скрипт работы с жалобами. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_EDIT_SPAMFILTER'] = 'Задействовано редактирование спамфильтра. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес.
Logging::$messages['ADMIN_FUNCTIONS_MOVE_THREAD'] = 'Задействован перенос нити. Администратор %s IP %s';
// Параметры: id пользователя, ip адрес
Logging::$messages['ADMIN_FUNCTIONS_MASS_BAN'] = 'Задействован бан по списку. Администратор %s IP %s';

/* *********
 * Разное. *
 ***********/

// Параметры: id пользователя, ip адрес.
Logging::$messages['EDIT_THREADS'] = 'Задействовано редактирование настроек нитей. Пользователь %s IP %s';

/* ****
 * :D *
 ******/

Logging::$f['MASS_BAN_USE'] = function () {
    Logging::write_msg('Задействован бан по списку.');
};
Logging::$f['MASS_BAN_ADD'] = function ($range_beg,
                                        $range_end,
                                        $reason,
                                        $until) {

    if ($range_beg == $range_end) {
        Logging::write_msg("Доступ с адреса $range_beg заблокирован по причине '$reason' до $until.");
    } else {
        Logging::write_msg("Доступ с адресов [$range_beg, $range_end] заблокирован по причине '$reason' до $until.");
    }
};

Logging::$f['MANAGE'] = function () {
    Logging::write_msg('Задействован скрипт административных фукнций и фукнций модераторов.');
};

Logging::$f['LOG_VIEW'] = function () {
    Logging::write_msg('Задействован просмотр лога.');
};

Logging::$f['EDIT_BANS_USE'] = function () {
    Logging::write_msg('Задействовано редактирование банов.');
};

Logging::$f['MODERATE_USE'] = function () {
    Logging::write_msg('Использован основной скрипт модератора.');
};

Logging::$f['ARCHIVE_USE'] = function () {
    Logging::write_msg('Задействовано архивирование нитей.');
};

Logging::$f['DELETE_DANGLING_FILES'] = function () {
    Logging::write_msg('Задействовано удаление висячих файлов.');
};

Logging::$f['DELETE_MARKED_POSTS_USE'] = function () {
    Logging::write_msg('Задействовано удаление висячих файлов.');
};

Logging::$f['DELETE_MARKED_POSTS_USE'] = function () {
    Logging::write_msg('Задействовано удаление отмеченных на удаление сообщений.');
};

Logging::$f['EDIT_ACL_USE'] = function () {
    Logging::write_msg('Задействовано редактирование списка контроля доступа.');
};
?>
