delimiter |

drop table if exists macrochan_tags_images|
drop table if exists macrochan_images|
drop table if exists macrochan_tags|

create table macrochan_tags             -- Теги макрочана.
(
    id int not null auto_increment,     -- Идентификатор.
    name varchar(256) not null,         -- Имя.
    unique key (id)
)
engine=InnoDB|

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
engine=InnoDB|

create table macrochan_tags_images  -- Связь тегов и изображений макрочана.
(
    tag int not null,               -- Идентификатор тега макрочана.
    image int not null,             -- Идентификатор изображения макрочана.
    unique key (tag, image),
    constraint foreign key (tag) references macrochan_tags (id) on delete restrict on update restrict,
    constraint foreign key (image) references macrochan_images (id) on delete restrict on update restrict
)
engine=InnoDB|