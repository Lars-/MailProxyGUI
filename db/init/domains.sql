create table domains
(
    id     int auto_increment
        primary key,
    domain text not null,
    server int  not null,
    constraint domains_domain_uindex
        unique (domain) using hash
);

