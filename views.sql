drop view word_synonyms cascade;
drop view word_translations2 cascade;
drop view word_translations cascade;
drop view word_translation_count_total cascade;
drop view word_translation_total cascade;
drop view word_translation_count cascade;
drop view translation_words_str cascade;
drop view translation_words cascade;
drop view translation_pairs cascade;

create view translation_pairs as
 select
  src.id as src,
  dst.id as dst,
  src.mofile as src_mofile,
  dst.mofile as dst_mofile,
  src.language as src_language,
  dst.language as dst_language,
  src.msgid,
  src.msgstr as src_msgstr,
  dst.msgstr as dst_msgstr
 from
  translations as src
  join translations as dst on
   src.msgid = dst.msgid
   and src.language != dst.language;

create view translation_words as
 select
  trans.*,
  src_words.word as src_word,
  dst_words.word as dst_word
 from
  translation_pairs as trans
  join msgstr_words as src_words on
   trans.src_msgstr = src_words.msgstr
  join msgstr_words as dst_words on
   trans.dst_msgstr = dst_words.msgstr;

create view translation_words_str as
 select
  trans.*,
  src.string as src_word_str,
  dst.string as dst_word_str,
  src_language.symbol as src_language_symbol,
  src_language.name as src_language_name,
  dst_language.symbol as dst_language_symbol,
  dst_language.name as dst_language_name
 from
  translation_words as trans
  join words as src on
    trans.src_word = src.id
  join words as dst on
    trans.dst_word = dst.id
  join languages as src_language on
   trans.src_language = src_language.id
  join languages as dst_language on
   trans.dst_language = dst_language.id;

create view word_translation_count as
 select
  trans.src_word,
  trans.src_word_str,
  trans.dst_word,
  trans.dst_word_str,
  trans.src_language,
  trans.dst_language,
  trans.src_language_symbol,
  trans.src_language_name,
  trans.dst_language_symbol,
  trans.dst_language_name,
  cast(count(trans.src_word) as float) as "count"
 from
  translation_words_str as trans
 group by
  trans.src_word,
  trans.src_word_str,
  trans.dst_word,
  trans.dst_word_str,
  trans.src_language,
  trans.dst_language,
  trans.src_language_symbol,
  trans.src_language_name,
  trans.dst_language_symbol,
  trans.dst_language_name
 order by trans.src_language asc, dst_language asc, "count" desc;

create view word_translation_total as
 select
  trans.src_word,
  trans.src_language,
  trans.dst_language,
  cast(count(trans.src_word) as float) as "total"
 from
  translation_words as trans
 group by
  trans.src_word,
  trans.src_language,
  trans.dst_language;

create view word_translation_count_total as
 select
  trans.src_word,
  trans.src_word_str,
  trans.dst_word,
  trans.dst_word_str,
  trans.src_language,
  trans.dst_language,
  trans.src_language_symbol,
  trans.src_language_name,
  trans.dst_language_symbol,
  trans.dst_language_name,
  trans.count,
  (select
    total.total
   from word_translation_total as total
   where
        trans.src_word = total.src_word
    and trans.src_language = total.src_language
    and trans.dst_language = total.dst_language) as total
 from
  word_translation_count as trans;

create view word_translations as
 select
  trans.*,
  trans.count / trans.total as weight
 from
  word_translation_count_total as trans;

create view word_translations2 as
 select
  (  trans1.weight
   * (select
       trans2.weight
      from
       word_translations as trans2
      where
           trans1.src_word = trans2.dst_word
       and trans1.dst_word = trans2.src_word
       and trans1.src_language_symbol = trans2.dst_language_symbol
       and trans1.dst_language_symbol = trans2.src_language_symbol
       and trans2.weight > 1)) as value,
  trans1.*
 from
  word_translations as trans1
 where
  trans1.weight > 1;



 
create view word_synonyms as
 select
  forward.src_word,
  backward.dst_word,
  forward.src_language,
  forward.dst_language,
  forward.weight * backward.weight as weight
 from
  word_translations as forward
  join word_translations as backward on
   forward.dst_word = backward.src_word
   and forward.dst_language = backward.src_language
   and forward.src_language = backward.dst_language;
