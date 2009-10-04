drop table translations cascade;
drop table msgstr_words cascade;
drop table words cascade;
drop table msgstrs cascade;
drop table msgids cascade;
drop table mofile_metadatas cascade;
drop table mofiles cascade;
drop table packages cascade;
drop table metadatas cascade;
drop table languages cascade;

create table languages (
 id serial primary key,
 symbol varchar,
 name varchar);

create table metadatas (
 id serial primary key,
 name varchar);

create table packages (
 id serial primary key,
 name varchar,
 version varchar);

create table mofiles (
 id serial primary key,
 package integer references packages(id),
 path varchar);

create table mofile_metadatas (
 id serial primary key,
 mofile integer references mofiles(id),
 metadata integer references metadatas(id),
 value varchar);

create table msgids (
 id serial primary key,
 string varchar);

create table msgstrs (
 id serial primary key,
 string varchar);

create table words (
 id serial primary key,
 string varchar);

create table msgstr_words (
 id serial primary key,
 msgstr integer references msgstrs(id),
 word integer references words(id));

create table translations (
 id serial primary key,
 mofile integer references mofiles(id),
 language integer references languages(id),
 msgid integer references msgids(id),
 msgstr integer references msgstrs(id));
