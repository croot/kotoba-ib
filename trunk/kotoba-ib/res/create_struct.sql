CREATE DATABASE kotoba DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE kotoba;

-- ------------------
-- Создание таблиц --
-- ------------------

-- Описание экземпляров сущности: Блокировки (мн.ч.), Блокировка (ед.ч.).
create table bans                   -- Блокировки.
(
    id int not null auto_increment, -- Идентификатор.
    range_beg bigint not null,      -- Начало диапазона IP-адресов.
    range_end bigint not null,      -- Конец диапазона IP-адресов.
    reason text default null,       -- Причина блокировки.
    untill datetime not null,       -- Время истечения блокировки.
    primary key (id),
    unique key (range_beg, range_end)
)
engine=InnoDB;

create table categories             -- Категории.
(
    id int not null auto_increment, -- Идентификатор.
    name varchar(50) not null,      -- Имя.
    primary key (id)
)
engine=InnoDB;

-- Заметки:
-- Вложенные файлы - загруженные пользователями и сохранённые на сервере файлы,
-- не являющиеся изображениями. thumbnail - имя файла изображения, который
-- ассоциирован с конкретным типом вложенного файла и служит для него
-- уменьшенной копией.
create table files                      -- Вложенные файлы.
(
    id int not null auto_increment,     -- Идентификатор.
    hash varchar(32) default null,      -- Хеш.
    name varchar(256) not null,         -- Имя.
    size int not null,                  -- Размер в байтах.
    thumbnail varchar(256) not null,    -- Уменьшенная копия.
    thumbnail_w int not null,           -- Ширина уменьшенной копии.
    thumbnail_h int not null,           -- Высота уменьшенной копии.
    primary key (id)
)
engine=InnoDB;

create table groups                 -- Группы.
(
    id int not null auto_increment, -- Идентификатор.
    name varchar(50) not null,      -- Имя.
    primary key (id),
    unique key (name)
)
engine=InnoDB;

-- Заметки:
-- Вложенные изображения - файлы изображений, загруженные пользователями и
-- сохранённые на сервере. name и thumbnail - имя файла исходного изображения и
-- файла уменьшенной копии, соответственно. hash - хеш исходного файла.
create table images                     -- Вложенные изображения.
(
    id int not null auto_increment,     -- Идентификатор.
    hash varchar(32) default null,      -- Хеш.
    name varchar(256) not null,         -- Имя.
    widht int not null,                 -- Ширина.
    height int not null,                -- Высота.
    size int not null,                  -- Размер в байтах.
    thumbnail varchar(256) not null,    -- Уменьшенная копия.
    thumbnail_w int not null,           -- Ширина уменьшенной копии.
    thumbnail_h int not null,           -- Высота уменьшенной копии.
    spoiler bit default 0,              -- Флаг спойлера.
    primary key (id)
)
engine=InnoDB;

create table languages              -- Языки.
(
    id int not null auto_increment, -- Идентификатор.
    code char(3) not null,          -- Код ISO_639-2.
    primary key (id)
)
engine=InnoDB;

create table links                      -- Вложенные ссылки на изображения.
(
    id int not null auto_increment,     -- Идентификатор.
    url varchar(2048) not null,         -- URL.
    widht int not null,                 -- Ширина.
    height int not null,                -- Высота.
    size int not null,                  -- Размер в байтах.
    thumbnail varchar(2048) not null,   -- URL уменьшенной копии.
    thumbnail_w int not null,           -- Ширина уменьшенной копии.
    thumbnail_h int not null,           -- Высота уменьшенной копии.
    primary key (id)
)
engine=InnoDB;

create table popdown_handlers       -- Обработчики автоматического удаления нитей.
(
    id int not null auto_increment, -- Идентификатор.
    name varchar(50) not null,      -- Имя функции.
    primary key (id)
)
engine=InnoDB;

create table stylesheets            -- Стили.
(
    id int not null auto_increment,    -- Идентификатор.
    name varchar(50) not null,        -- Имя файла.
    primary key (id)
)
engine=InnoDB;

create table upload_handlers        -- Обработчики загружаемых файлов.
(
    id int not null auto_increment,    -- Идентификатор.
    name varchar(50) not null,        -- Имя фукнции.
    primary key (id)
)
engine=InnoDB;

create table videos                 -- Вложенные видео.
(
    id int not null auto_increment, -- Идентификатор.
    code varchar(256) not null,     -- HTML-код.
    widht int not null,             -- Ширина.
    height int not null,            -- Высота.
    primary key (id)
)
engine=InnoDB;

create table boards                         -- Доски.
(
    id int not null auto_increment,         -- Идентификатор.
    name varchar(16) not null,              -- Имя.
    title varchar(50) default null,         -- Заголовок.
    annotation text default null,           -- Аннотация.
    bump_limit int not null,                -- Специфичный для доски бамплимит.
    force_anonymous bit not null,           -- Флаг отображения имени отправителя.
    default_name varchar(128) default null, -- Имя отправителя по умолчанию.
    -- Этот флаг не может быть null, так как для него нет родительского
    -- значения, которое можно было бы унаследовать.
    with_attachments bit not null,          -- Флаг вложений.

    -- Следующие флаги могут принимать 3 значения:
    -- null - унаследовано из config.php.
    -- 1 - включено.
    -- 0 - отключено.
    enable_macro bit default null,          -- Включение интеграции с макрочаном.
    enable_youtube bit default null,        -- Включение вложения видео с ютуба.
    enable_captcha bit default null,        -- Включение капчи.
    enable_translation bit default null,    -- Включение перевода текста сообщения.
    enable_geoip bit default null,          -- Включение отображения страны автора сообщения.
    enable_shi bit default null,            -- Включение рисования.
    enable_postid bit default null,         -- Включение идентификатора сообщения.

    same_upload varchar(32) not null,       -- Политика загрузки одинаковых файлов.
    popdown_handler int not null,           -- Идентификатор обработчика автоматического удаления нитей.
    category int not null,                  -- Идентификатор категории.
    primary key (id),
    unique key (name),
    constraint foreign key (category) references categories (id) on delete restrict on update restrict,
    constraint foreign key (popdown_handler) references popdown_handlers (id) on delete restrict on update restrict
)
engine=InnoDB;

create table users                      -- Пользователи.
(
    id int not null auto_increment,     -- Идентификатор.
    keyword varchar(32) default null,   -- Хеш ключевого слова.
    posts_per_thread int default null,  -- Число сообщений в нити на странице просмотра доски.
    threads_per_page int default null,  -- Число нитей на странице просмотра доски.
    lines_per_post int default null,    -- Количество строк в предпросмотре сообщения.
    language int not null,              -- Идентификатор языка.
    stylesheet int not null,            -- Идентификатор стиля.
    password varchar(12) default null,  -- Пароль для удаления сообщений.
    `goto` varchar(32) default null,    -- Перенаправление.
    primary key (id),
    unique key (keyword),
    constraint foreign key (language) references languages (id) on delete restrict on update restrict,
    constraint foreign key (stylesheet) references stylesheets (id) on delete restrict on update restrict
)
engine=InnoDB;

create table user_groups    -- Связь пользователей с группами.
(
    user int not null,      -- Идентификатор пользователя.
    `group` int not null,   -- Идентификатор группы.
    constraint foreign key (`group`) references groups (id) on delete cascade on update restrict,
    constraint foreign key (user) references users (id),
    unique key (user, `group`)
)
engine=InnoDB;

-- Заметки:
-- Имя файла уменьшенной копии типа загружаемых файлов является именем файла
-- изображения. См. заметки к таблице files, описание для поля thumbnail.
create table upload_types                       -- Типы загружаемых файлов.
(
    id int not null auto_increment,             -- Идентификатор.
    extension varchar(10) not null,             -- Расширение.
    store_extension varchar(10) default null,   -- Сохраняемое расширение.
    is_image bit not null,                      -- Флаг изображения.
    upload_handler int not null,                -- Идентификатор обработчика загружаемых файлов.
    thumbnail_image varchar(256) default null,  -- Имя файла уменьшенной копии.
    primary key (id),
    constraint foreign key (upload_handler) references upload_handlers (id) on delete restrict on update restrict,
    unique key (extension)
)
engine=InnoDB;

-- Описание экземпляров сущности: Связи досок с типами загружаемых файлов (мн.ч.)
-- Связь доски с типом загружаемых файлов (ед.ч.).
create table board_upload_types -- Связь досок с типами загружаемых файлов.
(
    board int not null,         -- Идентификатор доски.
    upload_type int not null,   -- Идентификатор типа загружаемых файлов.
    constraint foreign key (board) references boards (id) on delete restrict on update restrict,
    constraint foreign key (upload_type) references upload_types (id) on delete restrict on update restrict,
    unique (board, upload_type)
)
engine=InnoDB;

create table threads                    -- Нити.
(
    id int not null auto_increment,     -- Идентификатор.
    board int not null,                 -- Идентификатор доски.
    original_post int default null,     -- Номер оригинального сообщения.
    bump_limit int default null,        -- Специфичный для нити бамплимит.
    deleted bit not null,               -- Пометка на удаление.
    archived bit not null,              -- Флаг архивирования.
    -- Этот флаг не может быть null, так как для него нет родительского
    -- значения, которое можно было бы унаследовать.
    sage bit not null,                  -- Флаг поднятия нити.
    sticky bit not null default 0,      -- Флаг закрепления.
    -- Если этот флаг null, то берётся родительский with_attachments доски.
    with_attachments bit default null,  -- Флаг вложений.
    primary key (id),
    constraint foreign key (board) references boards (id) on delete restrict on update restrict
)
engine=InnoDB;

create table hidden_threads -- Скрытые нити.
(
    user int,               -- Пользователь.
    thread int,             -- Нить.
    unique key (user, thread),
    constraint foreign key (user) references users (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict
)
engine=InnoDB;

-- Заметки:
-- Если установлен флаг удаления, то сообщение считается "помеченным на
-- удаление".
create table posts                      -- Сообщения.
(
    id int not null auto_increment,     -- Идентификатор.
    board int not null,                 -- Идентификатор доски.
    thread int not null,                -- Идентификатор нити.
    number int not null,                -- Номер.
    user int not null,                  -- Идентификатор пользователя.
    password varchar(12) default null,  -- Пароль.
    name varchar(128) default null,     -- Имя отправителя.
    tripcode varchar(128) default null, -- Трипкод.
    ip bigint default null,             -- IP-адрес отправителя.
    subject varchar(128) default null,  -- Тема.
    date_time datetime default null,    -- Время сохранения.
    `text` text default null,           -- Текст.
    -- Если этот флаг null, то берётся родительский sage от нити.
    sage bit default null,              -- Флаг поднятия нити.
    deleted bit not null,               -- Флаг удаления.
    primary key (id),
    constraint foreign key (board) references boards (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
    constraint foreign key (user) references users (id) on delete restrict on update restrict
)
engine=InnoDB;

-- Описание экземпляров сущности: Правила (мн.ч.), Правило (ед.ч.).
create table acl                -- Список контроля доступа.
(
    `group` int default null,   -- Идентификатор группы.
    board int default null,     -- Идентификатор доски.
    thread int default null,    -- Идентификатор нити.
    post int default null,      -- Идентификатор сообщения.
    `view` bit not null,        -- Право на просмотр.
    `change` bit not null,      -- Право на изменение.
    moderate bit not null,      -- Право на модерирование.
    unique key (`group`, board, thread, post),
    constraint foreign key (`group`) references groups (id)  on delete cascade on update restrict,
    constraint foreign key (board) references boards (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
    constraint foreign key (post) references posts (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_files    -- Связь сообщений и вложенных файлов.
(
    post int not null,      -- Идентификатор сообщения.
    file int not null,      -- Идентификатор вложенного файла.
    deleted bit not null,   -- Флаг удаления.
    unique key (post, file),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (file) references files (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_images   -- Связь сообщений и вложенных изображений.
(
    post int not null,      -- Идентификатор сообщения.
    image int not null,     -- Идентификатор вложенного изображения.
    deleted bit not null,   -- Флаг удаления.
    unique key (post, image),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (image) references images (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_links    -- Связь сообщений и вложенных ссылок на изображения.
(
    post int not null,      -- Идентификатор сообщения.
    link int not null,      -- Идентификатор вложенной ссылки на изображение.
    deleted bit not null,   -- Флаг удаления.
    unique key (post, link),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (link) references links (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_videos   -- Связь сообщений и вложенного видео.
(
    post int not null,      -- Идентификатор сообщения.
    video int not null,     -- Идентификатор вложенного видео.
    deleted bit not null,   -- Флаг удаления.
    unique key (post, video),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (video) references videos (id) on delete restrict on update restrict
)
engine=InnoDB;

create table words                      -- Таблица фильтра слов.
(
    id int not null auto_increment,     -- Идентификатор замены.
    board_id int not null,              -- Идентификатор доски.
    word varchar(100) not null,         -- Слово для замены.
    `replace` varchar(100) not null,    -- Замена.
    unique key (id)
)
engine=InnoDB;

create table macrochan_tags             -- Теги макрочана.
(
    id int not null auto_increment,     -- Идентификатор.
    name varchar(256) not null,         -- Имя.
    unique key (id)
)
engine=InnoDB;

create table macrochan_images           -- Изображения макрочана.
(
    id int not null auto_increment,     -- Идентификатор.
    name varchar(256) not null,         -- Имя.
    width int not null,                 -- Ширина.
    height int not null,                -- Высота.
    size int not null,                  -- Размер в байтах.
    thumbnail varchar(256) not null,    -- Уменьшенная копия.
    thumbnail_w int not null,           -- Ширина уменьшенной копии.
    thumbnail_h int not null,           -- Высота уменьшенной копии.
    unique key (id)
)
engine=InnoDB;

create table macrochan_tags_images  -- Связь тегов и изображений макрочана.
(
    tag int not null,               -- Идентификатор тега макрочана.
    image int not null,             -- Идентификатор изображения макрочана.
    unique key (tag, image),
    constraint foreign key (tag) references macrochan_tags (id) on delete restrict on update restrict,
    constraint foreign key (image) references macrochan_images (id) on delete restrict on update restrict
)
engine=InnoDB;

create table db_version     -- Версия базы данных.
(
    version int default 0   -- Текущий номер версии базы данных.
)
engine=InnoDB;

create table hard_ban               -- Блокировки в фаерволе.
(
    range_beg varchar(15) not null, -- Начало диапазона IP-адресов.
    range_end varchar(15) not null  -- Конец диапазона IP-адресов.
)
engine=InnoDB;

create table reports    -- Жалобы.
(
    post int not null,  -- Идентификатор сообщения.
    constraint foreign key (post) references posts (id) on delete restrict on update restrict
)
engine=InnoDB;

-- Заметки:
-- Спамфильтр осуществляет фильтрацию текста сообщений от спама. Спам ищется
-- в тексте сообщений по Шаблонам.
create table spamfilter             -- Спамфильтр.
(
    id int not null auto_increment, -- Идентификатор.
    pattern varchar(256) not null,  -- Шаблон.
    primary key (id)
)
engine=InnoDB;

CREATE TABLE favorites          -- Избранные нити.
(
    user int not null,          -- Идентификатор пользователя.
    thread int not null,        -- Идентификатор нити.
    last_readed int not null,   -- Номер последнего прочитанного сообщения в нити.
    unique key (user, thread),
    constraint foreign key (user) references users (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict
)
engine=InnoDB;

-- --------------------------------------
-- Заполнение базы начальными данными. --
-- --------------------------------------

insert into languages (`id`, `code`) values (1, 'rus');
insert into languages (`code`) values ('eng');
insert into categories (`name`) values ('default');
-- insert into categories (`name`) values ('rule 34');
insert into popdown_handlers (`name`) values ('popdown_default_handler');
insert into stylesheets (`id`, `name`) values (1, 'kotoba.css');
insert into groups (`id`, `name`) values (1, 'Guests');
insert into groups (`id`, `name`) values (2, 'Users');
insert into groups (`id`, `name`) values (3, 'Moderators');
insert into groups (`id`, `name`) values (4, 'Administrators');
insert into upload_handlers (`name`) values ('thumb_default_handler');
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('b', 30, 'no', 1, 1);
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('azu',  30, 'once', 1, 1);
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('azu34',  30, 'yes', 1, 2);

-- Замечание: Задаваемые здесь язык и стиль не имеют значения для гостя, язык и
-- стиль для гостя настраиваются в config.php и никогда не берутся из базы
-- данных.
insert into users (`id`, `language`, `stylesheet`) values (1, 1, 1);

insert into user_groups (`user`, `group`) values (1, 1);
/*
 * При добавлении новых групп используются права как для гостя. Если вы вносите
 * изменения в права для гостя по умолчанию, то возможно вы захотите изменить и
 * права по умолчанию для вновь создаваемых групп. Для этого отредактируйте код
 * хранимой процедуры sp_group_add.
 */
insert into acl (`group`, `view`, `change`, `moderate`) values (1, 1, 1, 0);
insert into acl (`group`, `view`, `change`, `moderate`) values (2, 1, 1, 0);
insert into acl (`group`, `view`, `change`, `moderate`) values (3, 1, 1, 1);
insert into acl (`group`, `view`, `change`, `moderate`) values (4, 1, 1, 1);

-- Текущая версия БД.
insert into db_version (version) values (6);