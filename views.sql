drop view word_synonyms cascade;
drop view word_translation_value cascade;
drop view word_translation_reverse_weight cascade;
drop view word_translation_weight cascade;
drop view word_translation_count_total cascade;
drop view word_translation_total cascade;
drop view word_translation_count cascade;
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
  msgstr_words as src_words
  join translation_pairs as trans on
   src_words.msgstr = trans.src_msgstr
  join msgstr_words as dst_words on
   trans.dst_msgstr = dst_words.msgstr;

create view word_translation_count as
 select
  trans.src_word,
  trans.dst_word,
  trans.src_language,
  trans.dst_language,
  cast(count(trans.src_word) as float) as "count"
 from
  translation_words as trans
 group by
  trans.src_word,
  trans.dst_word,
  trans.src_language,
  trans.dst_language
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
  trans.dst_word,
  trans.src_language,
  trans.dst_language,
  trans.count,
  total.total
 from
  word_translation_count as trans
  join word_translation_total as total on
        trans.src_word = total.src_word
    and trans.src_language = total.src_language
    and trans.dst_language = total.dst_language;

create view word_translation_weight as
 select
  trans.*,
  trans.count / trans.total as weight
 from
  word_translation_count_total as trans;

create view word_translation_reverse_weight as
 select
  trans1.*,
  trans2.weight as reverse_weight
 from
  word_translation_weight as trans1
  join word_translation_weight as trans2 on
        trans1.weight > 0
    and trans1.src_word = trans2.dst_word
    and trans1.dst_word = trans2.src_word
    and trans1.src_language = trans2.dst_language
    and trans1.dst_language = trans2.src_language
    and trans2.weight > 0;

create view word_translation_value as
 select
  trans.*,
  trans.weight * trans.reverse_weight as value
 from
  word_translation_reverse_weight as trans;
 
create view word_synonyms as
 select
  forward.src_word,
  backward.dst_word,
  forward.src_language,
  forward.dst_language,
  forward.weight * backward.weight as weight
 from
  word_translation_weight as forward
  join word_translation_weight as backward on
   forward.dst_word = backward.src_word
   and forward.dst_language = backward.src_language
   and forward.src_language = backward.dst_language;
