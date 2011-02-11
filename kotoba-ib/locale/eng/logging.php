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
    Logging::write_msg('Edit thread used.');
};

Logging::$f['EDIT_UPLOAD_HANDLERS_USE'] = function () {
    Logging::write_msg('Edit upload handlers used.');
};

Logging::$f['EDIT_UPLOAD_TYPES_USE'] = function () {
    Logging::write_msg('Edit upload types used.');
};

Logging::$f['EDIT_USER_GROUPS_USE'] = function () {
    Logging::write_msg('Edit user groups used.');
};

Logging::$f['EDIT_WORDFILTER_USE'] = function () {
    Logging::write_msg('Edit word filter used.');
};

Logging::$f['HARD_BAN_USE'] = function () {
    Logging::write_msg('Ban in firewall used.');
};

Logging::$f['LOG_VIEW_USE'] = function () {
    Logging::write_msg('Log view used.');
};

Logging::$f['MANAGE_USE'] = function () {
    Logging::write_msg('Manage script used.');
};

Logging::$f['MASS_BAN_ADD'] = function ($range_beg, $range_end, $reason, $until) {
    if ($range_beg == $range_end) {
        Logging::write_msg("Access from address $range_beg blocked due to '$reason' until $until.");
    } else {
        Logging::write_msg("Access from addresses [$range_beg, $range_end] blocked due to '$reason' until $until.");
    }
};
Logging::$f['MASS_BAN_USE'] = function () {
    Logging::write_msg('Mass ban used.');
};

Logging::$f['MODERATE_USE'] = function () {
    Logging::write_msg('Использован основной скрипт модератора.');
};

Logging::$f['MOVE_THREAD_USE'] = function () {
    Logging::write_msg('Move thread script used.');
};

Logging::$f['REPORTS_USE'] = function () {
    Logging::write_msg('Reports handing script used.');
};

Logging::$f['UPDATE_MACROCHAN_USE'] = function () {
    Logging::write_msg('Использован скрипт обновления данных с макрочана.');
};
?>
