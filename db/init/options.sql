create table options
(
    id    int auto_increment
        primary key,
    `key` text     not null,
    value longtext null,
    constraint options_key_uindex
        unique (`key`) using hash
);

INSERT INTO options (id, `key`, value)
VALUES (1, 'dns_keys', 'a:1:{i:0;s:4:"mail";}');
