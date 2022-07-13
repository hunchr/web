drop database if exists sm;
create database sm;
use sm;

----- Tables -----
create table subs ( 
    id mediumint unsigned not null auto_increment,
    type tinyint unsigned not null default 0,
    cat tinyint unsigned not null default 0,
    display_name varchar(30) not null,
    bio varchar(500) default null,
    created_at timestamp(0) not null default current_timestamp,
    likes mediumint unsigned not null default 0,
    posts mediumint unsigned not null default 0,
    primary key (id)
);

create table users (
    uid mediumint unsigned not null,
    name varchar(20) not null unique,
    email varchar(100) not null,
    pw char(97) not null,
    lang enum("en", "de") not null,
    display set("light","show-nsfw") not null default "",
    special set("premium","verified","bot","admin") not null default "",
    foreign key (uid) references subs (id)
);

create table communities (
    cid mediumint unsigned not null,
    name varchar(20) not null unique,
    foreign key (cid) references subs (id)
);

create table posts (
    id mediumint unsigned not null auto_increment,
    uid mediumint unsigned not null,
    pid mediumint unsigned,
    type tinyint unsigned not null,
    cat set("op","mod","spoiler","nudity","gore","promoted","edited") not null,
    content varchar(10000) not null,
    created_at timestamp(0) not null default current_timestamp,
    likes mediumint unsigned not null default 0,
    replies mediumint unsigned not null default 0,
    primary key (id),
    foreign key (uid) references subs (id)
);

create table liked_subs (
    uid mediumint unsigned not null,
    sid mediumint unsigned not null,
    foreign key (uid) references subs (id),
    foreign key (sid) references subs (id)
);

create table saved_posts (
    uid mediumint unsigned not null,
    pid mediumint unsigned not null,
    foreign key (uid) references subs (id),
    foreign key (pid) references posts (id)
);

create table liked_posts (
    uid mediumint unsigned not null,
    pid mediumint unsigned not null,
    foreign key (uid) references subs (id),
    foreign key (pid) references posts (id)
);

create table auth (
    uid mediumint unsigned not null,
    token char(97) not null,
    created_at timestamp(0) not null default current_timestamp,
    foreign key (uid) references subs (id)
);

/*
----- Notes -----
subject type:
0 = user
1 = community
2 = list

subject cat:
0 = public
1 = unlisted
2 = private
3 = archived
4 = banned

post type:
0 = reply
1 = text
2 = image
3 = video
4 = audio
*/
