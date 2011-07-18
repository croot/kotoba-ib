CREATE DATABASE kotoba DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE kotoba;

-- ------------------
-- Создание таблиц --
-- ------------------

create table bans                   -- Bans.
(
    id int not null auto_increment, -- Id.
    range_beg bigint not null,      -- Begin of banned IP-address range.
    range_end bigint not null,      -- End of banned IP-address range.
    reason text default null,       -- Ban reason.
    untill datetime not null,       -- Expiration time.
    primary key (id),
    unique key (range_beg, range_end)
)
engine=InnoDB;

create table categories             -- Categories.
(
    id int not null auto_increment, -- Id.
    name varchar(50) not null,      -- Name.
    primary key (id)
)
engine=InnoDB;

-- Заметки:
-- Вложенные файлы - загруженные пользователями и сохранённые на сервере файлы,
-- не являющиеся изображениями. thumbnail - имя файла изображения, который
-- ассоциирован с конкретным типом вложенного файла и служит для него
-- уменьшенной копией.
create table files                      -- Files.
(
    id int not null auto_increment,     -- Id.
    hash varchar(32) default null,      -- Hash.
    name varchar(256) not null,         -- Name.
    size int not null,                  -- Size in bytes.
    thumbnail varchar(256) not null,    -- Thumbnail.
    thumbnail_w int not null,           -- Thumbnail width.
    thumbnail_h int not null,           -- Thumbnail height.
    primary key (id)
)
engine=InnoDB;

create table groups                 -- Groups.
(
    id int not null auto_increment, -- Id.
    name varchar(50) not null,      -- Name.
    primary key (id),
    unique key (name)
)
engine=InnoDB;

-- Заметки:
-- Вложенные изображения - файлы изображений, загруженные пользователями и
-- сохранённые на сервере. name и thumbnail - имя файла исходного изображения и
-- файла уменьшенной копии, соответственно. hash - хеш исходного файла.
create table images                     -- Images.
(
    id int not null auto_increment,     -- Id.
    hash varchar(32) default null,      -- Hash.
    name varchar(256) not null,         -- Name.
    widht int not null,                 -- Width.
    height int not null,                -- Height.
    size int not null,                  -- Size in bytes.
    thumbnail varchar(256) not null,    -- Thumbnail.
    thumbnail_w int not null,           -- Thumbnail width.
    thumbnail_h int not null,           -- Thumbnail height.
    spoiler bit default 0,              -- Spoiler flag.
    primary key (id)
)
engine=InnoDB;

create table languages              -- Languages.
(
    id int not null auto_increment, -- Id.
    code char(3) not null,          -- ISO_639-2 code.
    primary key (id)
)
engine=InnoDB;

create table links                      -- Links.
(
    id int not null auto_increment,     -- Id.
    url varchar(2048) not null,         -- URL.
    widht int not null,                 -- Width.
    height int not null,                -- Height.
    size int not null,                  -- Size in bytes.
    thumbnail varchar(2048) not null,   -- Thumbnail URL.
    thumbnail_w int not null,           -- Thumbnail width.
    thumbnail_h int not null,           -- Thumbnail height.
    primary key (id)
)
engine=InnoDB;

create table popdown_handlers       -- Popdown handlers.
(
    id int not null auto_increment, -- Id.
    name varchar(50) not null,      -- Function name.
    primary key (id)
)
engine=InnoDB;

create table stylesheets            -- Stylesheets.
(
    id int not null auto_increment, -- Id.
    name varchar(50) not null,      -- Stylesheet file name.
    primary key (id)
)
engine=InnoDB;

create table upload_handlers        -- Upload handlers.
(
    id int not null auto_increment, -- Id.
    name varchar(50) not null,      -- Function name.
    primary key (id)
)
engine=InnoDB;

create table videos                 -- Videos.
(
    id int not null auto_increment, -- Id.
    code varchar(256) not null,     -- Code.
    widht int not null,             -- Width.
    height int not null,            -- Height.
    primary key (id)
)
engine=InnoDB;

create table boards                         -- Boards.
(
    id int not null auto_increment,         -- Id.
    name varchar(16) not null,              -- Name.
    title varchar(50) default null,         -- Title.
    annotation text default null,           -- Annotation.
    bump_limit int not null,                -- Board specific bump limit.
    force_anonymous bit not null,           -- Hide name flag.
    default_name varchar(128) default null, -- Default name.
    -- Этот флаг не может быть null, так как для него нет родительского
    -- значения, которое можно было бы унаследовать.
    with_attachments bit not null,          -- Attachments flag.

    -- Следующие флаги могут принимать 3 значения:
    -- null - унаследовано из config.php.
    -- 1 - включено.
    -- 0 - отключено.
    enable_macro bit default null,              -- Macrochan integration flag.
    enable_youtube bit default null,            -- Youtube video posting flag.
    enable_captcha bit default null,            -- Captcha flag.
    enable_translation bit default null,        -- Translation flag.
    enable_geoip bit default null,              -- GeoIP flag.
    enable_shi bit default null,                -- Painting flag.
    enable_postid bit default null,             -- Post identification flag.

    same_upload varchar(32) not null,           -- Upload policy from same files.
    popdown_handler int not null,               -- Popdown handler id.
    category int not null,                      -- Category id.
    last_post_number int not null default 0,    -- Last post number.
    primary key (id),
    unique key (name),
    constraint foreign key (category) references categories (id) on delete restrict on update restrict,
    constraint foreign key (popdown_handler) references popdown_handlers (id) on delete restrict on update restrict
)
engine=InnoDB;

create table users                      -- Users.
(
    id int not null auto_increment,     -- Id.
    keyword varchar(32) default null,   -- Keyword hash.
    posts_per_thread int default null,  -- Count of posts per thread.
    threads_per_page int default null,  -- Count of threads per page.
    lines_per_post int default null,    -- Count of lines per post.
    language int not null,              -- Language id.
    stylesheet int not null,            -- Stylesheet id.
    password varchar(12) default null,  -- Password.
    `goto` varchar(32) default null,    -- Redirection.
    primary key (id),
    unique key (keyword),
    constraint foreign key (language) references languages (id) on delete restrict on update restrict,
    constraint foreign key (stylesheet) references stylesheets (id) on delete restrict on update restrict
)
engine=InnoDB;

create table user_groups    -- User groups relations.
(
    user int not null,      -- User id.
    `group` int not null,   -- Group id.
    constraint foreign key (`group`) references groups (id) on delete cascade on update restrict,
    constraint foreign key (user) references users (id),
    unique key (user, `group`)
)
engine=InnoDB;

create table upload_types                       -- Upload types.
(
    id int not null auto_increment,             -- Id.
    extension varchar(10) not null,             -- Extension.
    store_extension varchar(10) default null,   -- Stored extension.
    is_image bit not null,                      -- Image flag.
    upload_handler int not null,                -- Upload handler id.
    thumbnail_image varchar(256) default null,  -- Thumbnail.
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

create table threads                    -- Threads.
(
    id int not null auto_increment,     -- Id.
    board int not null,                 -- Board id.
    original_post int default null,     -- Original post number.
    bump_limit int default null,        -- Thread specific bump limit.
    deleted bit not null,               -- Mark to delete.
    archived bit not null,              -- Archived flag.
    -- Этот флаг не может быть null, так как для него нет родительского
    -- значения, которое можно было бы унаследовать.
    sage bit not null,                  -- Sage flag.
    sticky bit not null default 0,      -- Sticky flag.
    -- Если этот флаг null, то берётся родительский with_attachments доски.
    with_attachments bit default null,  -- Attachments flag.
    closed bit not null default 0,      -- Thread closed flag.
    last_post int not null default 0,   -- Last undeleted unsaged post number.
    primary key (id),
    constraint foreign key (board) references boards (id) on delete restrict on update restrict
)
engine=InnoDB;

create table hidden_threads -- Hidden threads.
(
    user int,               -- User id.
    thread int,             -- Thread id.
    unique key (user, thread),
    constraint foreign key (user) references users (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts                      -- Posts.
(
    id int not null auto_increment,     -- Id.
    board int not null,                 -- Board id.
    thread int not null,                -- Thread id.
    number int not null,                -- Number.
    user int not null,                  -- User id.
    password varchar(12) default null,  -- Password.
    name varchar(128) default null,     -- Name.
    tripcode varchar(128) default null, -- Tripcode.
    ip bigint default null,             -- IP-address.
    subject varchar(128) default null,  -- Subject.
    date_time datetime default null,    -- Date.
    `text` text default null,           -- Text.
    -- Если этот флаг null, то берётся родительский sage от нити.
    sage bit default null,              -- Sage flag.
    deleted bit not null,               -- Mark to delete.
    primary key (id),
    index (number),
    unique key (board, number),
    constraint foreign key (board) references boards (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
    constraint foreign key (user) references users (id) on delete restrict on update restrict
)
engine=InnoDB;

create table acl                -- Access Control List
(
    `group` int default null,   -- Group id.
    board int default null,     -- Board id.
    thread int default null,    -- Thread id.
    post int default null,      -- Post id.
    `view` bit not null,        -- View permission.
    `change` bit not null,      -- Change permission.
    moderate bit not null,      -- Moderate permission.
    unique key (`group`, board, thread, post),
    constraint foreign key (`group`) references groups (id)  on delete cascade on update restrict,
    constraint foreign key (board) references boards (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
    constraint foreign key (post) references posts (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_files    -- Posts files relations.
(
    post int not null,      -- Post id.
    file int not null,      -- File id.
    deleted bit not null,   -- Mark to delete.
    unique key (post, file),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (file) references files (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_images   -- Posts images relations.
(
    post int not null,      -- Post id.
    image int not null,     -- Image id.
    deleted bit not null,   -- Mark to delete.
    unique key (post, image),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (image) references images (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_links    -- Posts links relations.
(
    post int not null,      -- Post id.
    link int not null,      -- Link id.
    deleted bit not null,   -- Mark to delete.
    unique key (post, link),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (link) references links (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts_videos   -- Posts videos relations.
(
    post int not null,      -- Post id.
    video int not null,     -- Video id.
    deleted bit not null,   -- Mark to delete.
    unique key (post, video),
    constraint foreign key (post) references posts (id) on delete restrict on update restrict,
    constraint foreign key (video) references videos (id) on delete restrict on update restrict
)
engine=InnoDB;

create table words                      -- Wordfilter.
(
    id int not null auto_increment,     -- Id.
    board_id int not null,              -- Board id.
    word varchar(100) not null,         -- Word.
    `replace` varchar(100) not null,    -- Replacement.
    unique key (id)
)
engine=InnoDB;

create table macrochan_tags             -- Macrochan tags.
(
    id int not null auto_increment,     -- Id.
    name varchar(256) not null,         -- Name.
    unique key (id)
)
engine=InnoDB;

create table macrochan_images           -- Macrochan images.
(
    id int not null auto_increment,     -- Id.
    name varchar(256) not null,         -- Name.
    width int not null,                 -- Width.
    height int not null,                -- Height.
    size int not null,                  -- Size in bytes.
    thumbnail varchar(256) not null,    -- Thumbnail.
    thumbnail_w int not null,           -- Thumbnail width.
    thumbnail_h int not null,           -- Thumbnail height.
    unique key (id)
)
engine=InnoDB;

create table macrochan_tags_images  -- Macrochan tags images relations.
(
    tag int not null,               -- Macrochan tag id.
    image int not null,             -- Macrochan image id.
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

create table hard_ban               -- Bans in firewall.
(
    range_beg varchar(15) not null, -- Begin of banned IP-address range.
    range_end varchar(15) not null  -- End of banned IP-address range.
)
engine=InnoDB;

create table reports    -- Жалобы.
(
    post int not null,  -- Идентификатор сообщения.
    constraint foreign key (post) references posts (id) on delete restrict on update restrict
)
engine=InnoDB;

create table spamfilter             -- Spam filter.
(
    id int not null auto_increment, -- Id.
    pattern varchar(256) not null,  -- Pattern.
    primary key (id)
)
engine=InnoDB;

CREATE TABLE favorites          -- Favorites.
(
    user int not null,          -- User id.
    thread int not null,        -- Thread id.
    last_readed int not null,   -- Last readed post number.
    unique key (user, thread),
    constraint foreign key (user) references users (id) on delete restrict on update restrict,
    constraint foreign key (thread) references threads (id) on delete restrict on update restrict
)
engine=InnoDB;

-- ----------------
-- Initial data. --
-- ----------------

insert into languages (`id`, `code`) values (1, 'rus');
insert into languages (`code`) values ('eng');
insert into categories (`name`) values ('default');
insert into popdown_handlers (`id`, `name`) values (1, 'popdown_default_handler');
insert into stylesheets (`id`, `name`) values (1, 'kusaba.css');
insert into groups (`id`, `name`) values (1, 'Guests');
insert into groups (`id`, `name`) values (2, 'Users');
insert into groups (`id`, `name`) values (3, 'Moderators');
insert into groups (`id`, `name`) values (4, 'Administrators');
insert into upload_handlers (`name`) values ('thumb_default_handler');
insert into boards (id, name, bump_limit, force_anonymous, with_attachments, same_upload, popdown_handler, category)
            values (1, 'n', 30, 0, 1, 'once', 1, 1);
insert into boards (id, name, bump_limit, force_anonymous, with_attachments, same_upload, popdown_handler, category)
            values (2, 'misc',  30, 0, 1, 'once', 1, 1);

-- Замечание: Задаваемые здесь язык и стиль не имеют значения для гостя, язык и
-- стиль для гостя настраиваются в config.php и никогда не берутся из базы
-- данных.
insert into users (`id`, `language`, `stylesheet`) values (1, 1, 1);

insert into user_groups (`user`, `group`) values (1, 1);

insert into acl (`group`, `view`, `change`, moderate) values (1, 1, 1, 0);
insert into acl (`group`, `view`, `change`, moderate) values (2, 1, 1, 0);
insert into acl (`group`, `view`, `change`, moderate) values (3, 1, 1, 1);
insert into acl (`group`, `view`, `change`, moderate) values (4, 1, 1, 1);

-- Everybody can read news and see banners.
insert into acl (board, `view`, `change`, moderate) values (1, 1, 0, 0);
insert into acl (board, `view`, `change`, moderate) values (2, 1, 0, 0);

-- Admins can post news and banners.
insert into acl (`group`, board, `view`, `change`, moderate) values (4, 1, 1, 1, 1);
insert into acl (`group`, board, `view`, `change`, moderate) values (4, 2, 1, 1, 1);

-- Current database version.
insert into db_version (version) values (14);