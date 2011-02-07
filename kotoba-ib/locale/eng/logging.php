<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Logging functions in english.
 * @package englocale
 */

Logging::$f['ARCHIVE_USE'] = function () {
    Logging::write_msg('Использовано архивирование нитей.');
};

Logging::$f['DELETE_DANGLING_ATTACHMENTS_USE'] = function () {
    Logging::write_msg('Использовано удаление висячих файлов.');
};

Logging::$f['DELETE_MARKED_POSTS_USE'] = function () {
    Logging::write_msg('Использовано удаление отмеченных на удаление сообщений.');
};

Logging::$f['EDIT_ACL_USE'] = function () {
    Logging::write_msg('Использовано редактирование списка контроля доступа.');
};

Logging::$f['EDIT_BANS_USE'] = function () {
    Logging::write_msg('Использовано редактирование банов.');
};

Logging::$f['EDIT_BOARD_UPLOAD_TYPES_USE'] = function () {
    Logging::write_msg('Использовано редактирование связей загружаемых типов файлов с досками.');
};

Logging::$f['EDIT_BOARDS_USE'] = function () {
    Logging::write_msg('Edit boards used.');
};

Logging::$f['EDIT_CATEGORIES_USE'] = function () {
    Logging::write_msg('Использовано редактирование категорий досок.');
};

Logging::$f['EDIT_GROUPS_USE'] = function () {
    Logging::write_msg('Использовано редактирование групп пользователей.');
};

Logging::$f['EDIT_LANGUAGES_USE'] = function () {
    Logging::write_msg('Использовано редактирование языков.');
};

Logging::$f['EDIT_POPDOWN_HANDLERS_USE'] = function () {
    Logging::write_msg('Использовано редактирование обработчиков удаления нитей.');
};

Logging::$f['EDIT_SPAMFILTER_USE'] = function () {
    Logging::write_msg('Использовано редактирование спамфильтра.');
};

Logging::$f['EDIT_STYLESHEETS_USE'] = function () {
    Logging::write_msg('Использовано редактирование стилей оформления.');
};

Logging::$f['EDIT_THREADS_USE'] = function () {
    Logging::write_msg('Использовано редактирование настроек нитей.');
};

Logging::$f['EDIT_UPLOAD_HANDLERS_USE'] = function () {
    Logging::write_msg('Использовано редактирование обработчиков загружаемых файлов.');
};

Logging::$f['EDIT_UPLOAD_TYPES_USE'] = function () {
    Logging::write_msg('Использовано редактирование типов загружаемых файлов.');
};

Logging::$f['EDIT_USER_GROUPS_USE'] = function () {
    Logging::write_msg('Использовано редактирование принадлежности пользователей группам.');
};

Logging::$f['EDIT_WORDFILTER_USE'] = function () {
    Logging::write_msg('Использовано редактирование фильтрации слов.');
};

Logging::$f['LOG_VIEW_USE'] = function () {
    Logging::write_msg('Использован просмотр лога.');
};

Logging::$f['MANAGE_USE'] = function () {
    Logging::write_msg('Manage script used.');
};

Logging::$f['MASS_BAN_ADD'] = function ($range_beg, $range_end, $reason, $until) {
    if ($range_beg == $range_end) {
        Logging::write_msg("Доступ с адреса $range_beg заблокирован по причине '$reason' до $until.");
    } else {
        Logging::write_msg("Доступ с адресов [$range_beg, $range_end] заблокирован по причине '$reason' до $until.");
    }
};
Logging::$f['MASS_BAN_USE'] = function () {
    Logging::write_msg('Использован бан по списку.');
};

Logging::$f['MODERATE_USE'] = function () {
    Logging::write_msg('Использован основной скрипт модератора.');
};

Logging::$f['MOVE_THREAD_USE'] = function () {
    Logging::write_msg('Использован перенос нити.');
};

Logging::$f['REPORTS_USE'] = function () {
    Logging::write_msg('Использован скрипт работы с жалобами.');
};

Logging::$f['UPDATE_MACROCHAN_USE'] = function () {
    Logging::write_msg('Использован скрипт обновления данных с макрочана.');
};
?>
