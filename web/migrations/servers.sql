create table servers
(
    id                 integer
        constraint servers_pk
            primary key autoincrement,
    name               text    not null,
    external_host      text    not null,
    internal_imap_host text    not null,
    internal_imap_port integer not null,
    internal_smtp_host text    not null,
    internal_smtp_port integer not null
);

create unique index servers_external_host_uindex
    on servers (external_host);

create unique index servers_name_uindex
    on servers (name);