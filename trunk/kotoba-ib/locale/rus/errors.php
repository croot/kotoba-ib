<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Error messages in russian.
 * @package ruslocale
 */

/**
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'errors.php'; })) {
    throw new Exception('File <b>errors.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}

/***/
$ERRORS['DEFAULT']
    = new Error('Произошла стандартная ошибка.', 'Стандартная ошибка.');
$ERRORS['BOARD_NOT_FOUND']
    = new Error('Доска с именем %s не найдена.',
                'Доска не найдена.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $board_name, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $board_name));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['BOARD_NOT_FOUND_ID']
    = new Error('Доска с id %s не найдена.',
                'Доска не найдена.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $board_id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $board_id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['ACL_NO_RULES']
    = new Error('В списке контроля доступа нет ни одного правила.',
                'Нет правил.');
$ERRORS['KOTOBA_INTVAL']
    = new Error('Объект не может быть преобразован к целому числу. См. '
                . 'описание фукнции intval() в документации PHP.',
                'Преобразование к целому числу.');
$ERRORS['MAX_PAGE']
    = new Error('Номер страницы %s слишком большой. Такой страницы не '
                . 'существует.',
                'Номер страницы.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $page, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $page));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['ACL_RULE_EXCESS']
    = new Error('Доска, нить и сообщение определяются однозначно своими '
                . 'идентификаторами.',
                'Получена избыточная информация.');
$ERRORS['ACL_RULE_CONFLICT']
    = new Error('Разрешение редактирования не может быть установлено, если нет '
                . 'разрешения просмотра. Разрешение модерирования не может '
                . 'быть установлено, если не установлены другие разрешения.',
                'Ошибка разрешений в правиле.');
$ERRORS['CAPTCHA']
    = new Error('Введённый вами код подтверждения "%s" не совпадает с "%s".',
                'Код подтвержения не верен.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $ccode, $exp_ccode, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $ccode, $exp_ccode));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['REMOTE_ADDR']
    = new Error('Адрес клиента не является IP адресом.',
                'Неверный адрес клиента.');
$ERRORS['SPAM_DETECTED']
    = new Error('Ваше сообщение не прошло спам фильтр.', 'Обнаруже спам.');
$ERRORS['THREAD_ARCHIVED']
    = new Error('Ответ в заархивированную нить невозможен.',
                'Нить заархивирована.');
$ERRORS['THREAD_CLOSED']
    = new Error('Ответ в закрытую нить невозможен.',
                'Нить закрыта.');
?>
