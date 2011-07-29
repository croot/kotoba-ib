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

if (!isset($KOTOBA_LOCALE_MESSAGES)) {
    $KOTOBA_LOCALE_MESSAGES = array();
}
$_ = &$KOTOBA_LOCALE_MESSAGES;

$_['ACL.']['rus'] = 'Список контроля доступа.';
$_['Admin.']['rus'] = 'Администратор.';
$_['Annotation too long.']['rus'] = 'Аннотация слишком длинная.';
$_['Arrays and Objects what not implements __toString() method, cannot be cast to string. See description to strval() function.']['rus'] = 'Массивы и Объекты, в которых не реализован метод __toString() не могут быть преобразованы к строке. См. описание функции strval()';
$_['Ban reason has wrong format.']['rus'] = 'Причина блокировки имеет не верный формат.';
$_['Bans.']['rus'] = 'Баны.';
$_['Begining of IP-address range has wrong format.']['rus'] = 'Начало диапазона IP-адресов имеет не верный формат.';
$_['Board id=%d not found.']['rus'] = 'Доска id=%d не найдена.';
$_['Board name has wrong format. Board name must be string length at 1 to 16 symbols. Symbols can be latin letters and digits.']['rus'] = 'Имя доски имеет неверный формат. Имя доски должно быть строкой, длиной от 1 до 16, состоящей только из латинских букв или цифр.';
$_['Board name=%s not found.']['rus'] = 'Доска name=%s не найдена.';
$_['Board title too long.']['rus'] = 'Заголовок доски слишком длинный.';
$_['Board, Thread or Post is unique. Set one of it.']['rus'] = 'Идентификаторы доски, нити и поста уникальны. Задайте что-то одно.';
$_['Boards.']['rus'] = 'Доски.';
$_['Bump limit must be digit greater than zero.']['rus'] = 'Специфичный для доски бамплимит должен быть целым числом больше нуля.';
$_['Cannot convert image to PNG format.']['rus'] = 'Не удалось преобразовать изображение в формат PNG.';
$_['Cant move file %s to %s.']['rus'] = 'Не удалось переместить файл %s в %s.';
$_['Cant write file to disk.']['rus'] = 'Не удалось записать файл на диск.';
$_['Captcha.']['rus'] = 'Капча.';
$_['Category name wrong format.']['rus'] = 'Имя категории имеет не верный формат.';
$_['Categories.']['rus'] = 'Категории.';
$_['Change permission cannot be set without view. Moderate permission cannot be set without all others.']['rus'] = 'Разрешение редактирования не может быть установлено, если нет разрешения просмотра. Разрешение модерирования не может быть установлено, если не установлены другие разрешения.';
$_['Copy file.']['rus'] = 'Копирование файла.';
$_['Count of lines per post must be in range %d-%d.']['rus'] = 'Число строк в сообщении на странице просмотра доски должно быть в пределах %d-%d.';
$_['Count of posts per thread must be in range %d-%d.']['rus'] = 'Число сообщений в нити на странице просмотра доски должно быть в пределах %d-%d.';
$_['Count of threads per page must be in range %d-%d.']['rus'] = 'Число нитей на странице просмотра доски должно быть в пределах %d-%d.';
$_['Database error.']['rus'] = 'Ошибка в базе данных.';
$_['End of IP-address range has wrong format.']['rus'] = 'Конец диапазона IP-адресов имеет не верный формат.';
$_['Error in database: %s.']['rus'] = 'При запросе к базе данных произошла ошибка: %s.';
$_['Extension has wrong format.']['rus'] = 'Расширение загружаемого файла имеет не верный формат.';
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
$_['Group name wrong format.']['rus'] = 'Имя группы имеет не верный формат.';
$_['Groups.']['rus'] = 'Группы.';
$_['Guest.']['rus'] = 'Гость.';
$_['Guests cannot do that.']['rus'] = 'Гости не могут делать этого.';
$_['ISO_639-2 code wrong format.']['rus'] = 'ISO_639-2 код языка имеет неверный формат.';
$_['Id of new group was not received.']['rus'] = 'Id новой группы небыл получен.';
$_['Image convertion.']['rus'] = 'Преобразование изображения.';
$_['Image dimensions too small.']['rus'] = 'Размеры изображения слишком малы.';
$_['Image libraries.']['rus'] = 'Графические библиотеки.';
$_['Image libraries disabled or doesn\'t work.']['rus'] = 'Графические библиотеки не установлены или не работают.';
$_['Image too small.']['rus'] = 'Размер изображения слишком мал.';
$_['Imagemagic doesn\'t support %s file type.']['rus'] = 'Imagemagic не поддерживает тип файла %s.';
$_['Imagemagic library.']['rus'] = 'Библиотека Imagemagic.';
$_['Invlid unicode characters deteced.']['rus'] = 'В тексте обнаружены не юникод символы.';
$_['Keyword length must be 2 up to 32 symbols. Valid symbols is: latin letters, digits, underscore and dash.']['rus'] = 'Длина ключего слова должна быть от 2 до 32 символов, допустимые значения: латинские буквы, цифры, нижнее подчеркивание и дефис.';
$_['Language id=%d not exist.']['rus'] = 'Языка с id=%d не существует.';
$_['Languages.']['rus'] = 'Языки.';
$_['Link creation.']['rus'] = 'Создание ссылки.';
$_['Link too long.']['rus'] = 'Слишком длинная сслыка на файл, имя загружаемого файла или код видео.';
$_['Locale.']['rus'] = 'Локаль.';
$_['Logging.']['rus'] = 'Логирование.';
$_['Macrochan.']['rus'] = 'Макрочан.';
$_['Macrochan tag name wrong format or not exist.']['rus'] = 'Тег макрочана имеет не верный формат или не существует.';
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
$_['Object cannot be cast to intger. See description to intval() function.']['rus'] = 'Объект не может быть преобразован к целому числу. См. описание фукнции intval()';
$_['One of search words is more than 60 characters.']['rus'] = 'Одно из слов имеет длинну более 60 символов.';
$_['Page number=%d not exist.']['rus'] = 'Страницы number=%d не существует.';
$_['Pages.']['rus'] = 'Страницы.';
$_['Password wrong format. Password must be at 1 to 12 symbols length. Valid symbold is digits and latin letters.']['rus'] = 'Пароль для удаления сообщения имеет не верный формат. Пароль должен быть длиной от 1 до 12 символов, включительно, состоять из цифр 0-9 или латинских букв a-z A-Z.';
$_['Popdown handler name wrong format.']['rus'] = 'Имя обработчика удаления нитей имеет не верный формат.';
$_['Popdown handlers.']['rus'] = 'Обработчики автоматического удаления нитей.';
$_['Post id=%d not found or user id=%d have no permission.']['rus'] = 'Сообщение id=%d не найдено или пользователь id=%d не имеет прав для его просмотра.';
$_['Posts.']['rus'] = 'Сообщения.';
$_['Redirection wrong format.']['rus'] = 'Перенаправление при постинге имеет не верный формат.';
$_['Request method.']['rus'] = 'Метод запроса.';
$_['Request method not defined or unexpected.']['rus'] = 'Неожиданный или неопределённый метод запроса.';
$_['Remote address is not an IP address.']['rus'] = 'Адрес клиента не является IP адресом.';
$_['Search.']['rus'] = 'Поиск.';
$_['Search keyword not set or too short.']['rus'] = 'Не задано достаточно текста для поиска.';
$_['Session.']['rus'] = 'Сессия.';
$_['Setup locale failed.']['rus'] = 'Не удалось установить локаль.';
$_['So small image cannot have so many data.']['rus'] = 'Слишком большой размер в байтах для такого маленького изображения.';
$_['Spam.']['rus'] = 'Спам.';
$_['Spamfilter.']['rus'] = 'Спамфильтр.';
$_['Stored extension has wrong format.']['rus'] = 'Сохраняемое расширение загружаемого файла имеет не верный формат.';
$_['Stylesheet id=%d not exist.']['rus'] = 'Стиля id=%d не существует.';
$_['Stylesheet name wrong format.']['rus'] = 'Имя файла стиля имеет не верный формат.';
$_['Stylesheets.']['rus'] = 'Стили.';
$_['Subject too long.']['rus'] = 'Тема сообщения слишком длинная.';
$_['Temporary directory not found.']['rus'] = 'Временная директория не найдена.';
$_['Text too long.']['rus'] = 'Текст сообщения слишком длинный.';
$_['Thread id=%d not found.']['rus'] = 'Нить id=%d не найдена.';
$_['Thread number=%d not found.']['rus'] = 'Нить number=%d не найдена.';
$_['Thread id=%d was archived.']['rus'] = 'Нить id=%d была заархивирована.';
$_['Thread id=%d was closed.']['rus'] = 'Нить id=%d была закрыта.';
$_['Threads.']['rus'] = 'Нити.';
$_['Thumbnail name for nonimage files has wrong format.']['rus'] = 'Имя картинки для файла, не являющегося изображением имеет не верный формат.';
$_['Unicode.']['rus'] = 'Юникод.';
$_['Unknown upload type.']['rus'] = 'Неизвестный тип вложения.';
$_['Upload handler function name has a wrong format.']['rus'] = 'Имя фукнции обработчика загружаемых файлов имеет не верный формат.';
$_['Upload handlers.']['rus'] = 'Обработчики загружаемых файлов.';
$_['Upload limit MAX_FILE_SIZE from html form exceeded.']['rus'] = 'Превышено ограничение на размер загружаемого файла MAX_FILE_SIZE из html формы.';
$_['Upload limit upload_max_filesize from php.ini exceeded.']['rus'] = 'Превышено ограничение на размер загружаемого файла upload_max_filesize из php.ini.';
$_['Upload policy from same files wrong format. It must be string at 1 to 32 latin letters.']['rus'] = 'Политика загрузки одинаковых файлов имеет не верный формат. Политика загрузки одинаковых файлов должна быть строкой, длиной от 1 до 32 символов, состоящей только из латинских букв.';
$_['Upload types.']['rus'] = 'Типы загружаемых файлов.';
$_['Uploads.']['rus'] = 'Загруженные файлы.';
$_['User id=%d has no group.']['rus'] = 'Пользователь id=%d не входит ни в одну группу.';
$_['User keyword=%s not exists.']['rus'] = 'Пользователя keyword=%s не существует.';
$_['Users.']['rus'] = 'Пользователи.';
$_['Word too long.']['rus'] = 'Слово для фильтрации слишком длинное.';
$_['Wordfilter.']['rus'] = 'Фильтр слов.';
$_['Wrong spamfilter pattern.']['rus'] = 'Шаблон спамфильтра имеет неверный формат.';
$_['You are not admin.']['rus'] = 'Вы не являетесь администратором.';
$_['You are not moderator.']['rus'] = 'Вы не являетесь модератором.';
$_['You enter wrong verification code %s.']['rus'] = 'Вы ввели неверный код подтверждения %s.';
$_['You id=%d have no permission to do it on board id=%d.']['rus'] = 'У вас id=%d нет прав для запрашиваемого действия с доской id=%d.';
$_['You id=%d have no permission to do it on thread id=%d.']['rus'] = 'У вас id=%d нет прав для запрашиваемого действия с нитью id=%d.';

unset($_);
?>
