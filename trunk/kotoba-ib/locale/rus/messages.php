<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Messages in russian.
 * @package api
 */

/**
 * 
 */
require_once '../config.php';

if (!isset($KOTOBA_LOCALE_MESSAGES)) {
    $KOTOBA_LOCALE_MESSAGES = array();
}
$_ = &$KOTOBA_LOCALE_MESSAGES;

$_['ACL.']['rus'] = 'Список контроля доступа.';
$_['Admin.']['rus'] = 'Администратор.';
$_['Annotation too long.']['rus'] = 'Аннотация слишком длинная.';
$_['Board id=%d not found.']['rus'] = 'Доска id=%d не найдена.';
$_['Board name wrong format. Board name must be string length at 1 to 16 symbols. Symbols can be latin letters and digits.']['rus'] = 'Имя доски имеет не верный формат. Имя доски должно быть строкой, длиной от 1 до 16, состоящей только из латинских букв или цифр.';
$_['Board name=%s not found.']['rus'] = 'Доска name=%s не найдена.';
$_['Board title too long.']['rus'] = 'Заголовок доски слишком длинный.';
$_['Board, Thread or Post is unique. Set one of it.']['rus'] = 'Идентификаторы доски, нити и поста уникальны. Задайте что-то одно.';
$_['Boards.']['rus'] = 'Доски.';
$_['Cannot convert image to PNG format.']['rus'] = 'Не удалось преобразовать изображение в формат PNG.';
$_['Cant move file %s to %s.']['rus'] = 'Не удалось переместить файл %s в %s.';
$_['Cant write file to disk.']['rus'] = 'Не удалось записать файл на диск.';
$_['Captcha.']['rus'] = 'Капча.';
$_['Change permission cannot be set without view. Moderate permission cannot be set without all others.']['rus'] = 'Разрешение редактирования не может быть установлено, если нет разрешения просмотра. Разрешение модерирования не может быть установлено, если не установлены другие разрешения.';
$_['Copy file.']['rus'] = 'Копирование файла.';
$_['Failed to copy file %s to %s.']['rus'] = 'Не удалось скопировать файл %s в %s.';
$_['Failed to create hard link %s for file %s.']['rus'] = 'Не удалось создать жесткую ссылку %s на файл %s.';
$_['Failed to open or create log file %s.']['rus'] = 'Не удалось открыть или создать файл лога %s.';
$_['Failed to start session.']['rus'] = 'Не удалось начать сессию.';
$_['File %s hash calculation failed.']['rus'] = 'Не удалось вычислить хеш файла %s.';
$_['File is loaded partially.']['rus'] = 'Фаил был загружен лишь частично.';
$_['File type %s not supported for upload.']['rus'] = 'Тип файла %s не разрешен для загрузки.';
$_['File uploading interrupted by extension.']['rus'] = 'Загрузка файла прервана расширением.';
$_['GD doesn\'t support %s file type.']['rus'] = 'GD не поддерживает тип файла %s.';
$_['GD library.']['rus'] = 'Библиотека GD';
$_['Groups.']['rus'] = 'Группы.';
$_['Guest.']['rus'] = 'Гость.';
$_['Guests cannot do that.']['rus'] = 'Гости не могут делать этого.';
$_['Id of new group was not received.']['rus'] = 'Id новой группы небыл получен.';
$_['Image convertion.']['rus'] = 'Преобразование изображения.';
$_['Image dimensions too small.']['rus'] = 'Размеры изображения слишком малы.';
$_['Image libraries.']['rus'] = 'Графические библиотеки.';
$_['Image libraries disabled or doesn\'t work.']['rus'] = 'Графические библиотеки не установлены или не работают.';
$_['Image too small.']['rus'] = 'Размер изображения слишком мал.';
$_['Imagemagic doesn\'t support %s file type.']['rus'] = 'Imagemagic не поддерживает тип файла %s.';
$_['Imagemagic library.']['rus'] = 'Библиотека Imagemagic.';
$_['Invlid unicode characters deteced.']['rus'] = 'В тексте обнаружены не юникод символы.';
$_['Language id=%d not exist.']['rus'] = 'Языка с id=%d не существует.';
$_['Languages.']['rus'] = 'Языки.';
$_['Link creation.']['rus'] = 'Создание ссылки.';
$_['Link too long.']['rus'] = 'Слишком длинная сслыка на файл, имя загружаемого файла или код видео.';
$_['Locale.']['rus'] = 'Локаль.';
$_['Logging.']['rus'] = 'Логирование.';
$_['Message detected as spam.']['rus'] = 'Сообщение не прошло сам фильтр.';
$_['Moderator.']['rus'] = 'Модератор.';
$_['Name length too long.']['rus'] = 'Имя отправителя слишком длинное.';
$_['No attachment and text is empty.']['rus'] = 'Файл не был загружен и пустой текст сообщения.';
$_['No file uploaded.']['rus'] = 'Файл не был загружен.';
$_['No one group exists.']['rus'] = 'Не создана ни одна группа пользователей.';
$_['No one language exists.']['rus'] = 'Не создан ни один язык.';
$_['No one rule in ACL.']['rus'] = 'В списке контроля доступа нет ни одного правила.';
$_['No one stylesheet exists.']['rus'] = 'Не создан ни один стиль.';
$_['No one user exists.']['rus'] = 'Не создан ни один пользователь.';
$_['No threads to edit.']['rus'] = 'Нет нитей для редактирования настроек.';
$_['No words for search.']['rus'] = 'Нет слов для поиска.';
$_['One of search words is more than 60 characters.']['rus'] = 'Одно из слов имеет длинну более 60 символов.';
$_['Page number=%d not exist.']['rus'] = 'Страницы number=%d не существует.';
$_['Pages.']['rus'] = 'Страницы.';
$_['Post id=%d not found or user id=%d have no permission.']['rus'] = 'Сообщение id=%d не найдено или пользователь id=%d не имеет прав для его просмотра.';
$_['Posts.']['rus'] = 'Сообщения.';
$_['Request method.']['rus'] = 'Метод запроса.';
$_['Request method not defined or unexpected.']['rus'] = 'Неожиданный или неопределённый метод запроса.';
$_['Remote address is not an IP address.']['rus'] = 'Адрес клиента не является IP адресом.';
$_['Search.']['rus'] = 'Поиск.';
$_['Search keyword not set or too short.']['rus'] = 'Не задано достаточно текста для поиска.';
$_['Session.']['rus'] = 'Сессия.';
$_['Setup locale failed.']['rus'] = 'Не удалось установить локаль.';
$_['So small image cannot have so many data.']['rus'] = 'Слишком большой размер в байтах для такого маленького изображения.';
$_['Spam.']['rus'] = 'Спам.';
$_['Stylesheet id=%d not exist.']['rus'] = 'Стиля id=%d не существует.';
$_['Stylesheets.']['rus'] = 'Стили.';
$_['Subject too long.']['rus'] = 'Тема сообщения слишком длинная.';
$_['Temporary directory not found.']['rus'] = 'Временная директория не найдена.';
$_['Text too long.']['rus'] = 'Текст сообщения слишком длинный.';
$_['Thread id=%d not found.']['rus'] = 'Нить id=%d не найдена.';
$_['Thread number=%d not found.']['rus'] = 'Нить number=%d не найдена.';
$_['Thread id=%d was archived.']['rus'] = 'Нить id=%d была заархивирована.';
$_['Thread id=%d was closed.']['rus'] = 'Нить id=%d была закрыта.';
$_['Threads.']['rus'] = 'Нити.';
$_['Unicode.']['rus'] = 'Юникод.';
$_['Unknown upload type.']['rus'] = 'Неизвестный тип вложения.';
$_['Upload limit MAX_FILE_SIZE from html form exceeded.']['rus'] = 'Превышено ограничение на размер загружаемого файла MAX_FILE_SIZE из html формы.';
$_['Upload limit upload_max_filesize from php.ini exceeded.']['rus'] = 'Превышено ограничение на размер загружаемого файла upload_max_filesize из php.ini.';
$_['Uploads.']['rus'] = 'Загруженные файлы.';
$_['User id=%d has no group.']['rus'] = 'Пользователь id=%d не входит ни в одну группу.';
$_['User keyword=%s not exists.']['rus'] = 'Пользователя keyword=%s не существует.';
$_['Users.']['rus'] = 'Пользователи.';
$_['Word too long.']['rus'] = 'Слово для фильтрации слишком длинное.';
$_['Wordfilter.']['rus'] = 'Фильтр слов.';
$_['You are not admin.']['rus'] = 'Вы не являетесь администратором.';
$_['You are not moderator.']['rus'] = 'Вы не являетесь модератором.';
$_['You enter wrong verification code %s.']['rus'] = 'Вы ввели неверный код подтверждения %s.';
$_['You id=%d have no permission to do it on board id=%d.']['rus'] = 'У вас id=%d нет прав для запрашиваемого действия с доской id=%d.';
$_['You id=%d have no permission to do it on thread id=%d.']['rus'] = 'У вас id=%d нет прав для запрашиваемого действия с нитью id=%d.';

unset($_);
?>
