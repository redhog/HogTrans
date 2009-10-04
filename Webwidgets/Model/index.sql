drop index translations_msgstr_index;
drop index translations_msgid_index;
drop index translations_language_index;
drop index translations_mofile_index;
drop index translations_id_index;

drop index msgstr_words_word_index;
drop index msgstr_words_msgstr_index;
drop index msgstr_words_id_index;

drop index words_string_index;
drop index words_id_index;

drop index msgstrs_string_index;
drop index msgstrs_id_index;

drop index msgids_string_index;
drop index msgids_id_index;

drop index mofile_metadatas_value_index;
drop index mofile_metadatas_metadata_index;
drop index mofile_metadatas_mofile_index;
drop index mofile_metadatas_id_index;

drop index mofiles_path_index;
drop index mofiles_package_index;
drop index mofiles_id_index;

-- version
drop index packages_name_index;
drop index packages_id_index;

drop index metadatas_name_index;
drop index metadatas_id_index;

drop index languages_name_index;
drop index languages_symbol_index;
drop index languages_id_index;

create index languages_id_index on languages(id);
create index languages_symbol_index on languages(symbol);
create index languages_name_index on languages(name);

create index metadatas_id_index on metadatas(id);
create index metadatas_name_index on metadatas(name);

create index packages_id_index on packages(id);
create index packages_name_index on packages(name);
-- version

create index mofiles_id_index on mofiles(id);
create index mofiles_package_index on mofiles(package);
create index mofiles_path_index on mofiles(path);

create index mofile_metadatas_id_index on mofile_metadatas(id);
create index mofile_metadatas_mofile_index on mofile_metadatas(mofile);
create index mofile_metadatas_metadata_index on mofile_metadatas(metadata);
create index mofile_metadatas_value_index on mofile_metadatas(value);

create index msgids_id_index on msgids(id);
create index msgids_string_index on msgids(string);

create index msgstrs_id_index on msgstrs(id);
create index msgstrs_string_index on msgstrs(string);

create index words_id_index on words(id);
create index words_string_index on words(string);

create index msgstr_words_id_index on msgstr_words(id);
create index msgstr_words_msgstr_index on msgstr_words(msgstr);
create index msgstr_words_word_index on msgstr_words(word);

create index translations_id_index on translations(id);
create index translations_mofile_index on translations(mofile);
create index translations_language_index on translations(language);
create index translations_msgid_index on translations(language, msgid);
create index translations_msgstr_index on translations(language, msgstr);
