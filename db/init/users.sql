create table users
(
    id            int auto_increment
        primary key,
    username      text     not null,
    domain        int      not null,
    last_verified datetime not null,
    constraint users_username_uindex
        unique (username) using hash
);

