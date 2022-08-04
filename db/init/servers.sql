create table servers
(
    id                        int auto_increment
        primary key,
    name                      text not null,
    external_host             text not null,
    internal_imap_host        text not null,
    internal_imap_port        int  not null,
    internal_smtp_host        text not null,
    internal_smtp_port        int  not null,
    imap_test_extra_variables text null,
    constraint servers_external_host_uindex
        unique (external_host) using hash,
    constraint servers_name_uindex
        unique (name) using hash
);

