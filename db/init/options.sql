create table options
(
    id    int auto_increment
        primary key,
    `key` text     not null,
    value longtext null,
    constraint options_key_uindex
        unique (`key`) using hash
);

