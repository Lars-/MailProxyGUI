create table users
(
    id         integer
        constraint users_pk
            primary key autoincrement,
    username   text    not null,
    server     integer not null,
    last_login integer not null
);