create view word_translations2 as
 select
  forward_src_words.word as src_word,
  forward_dst_words.word as dst_word,
  forward_trans.src_language,
  forward_trans.dst_language,
  count(backward_dst_words.word) as weight
 from
  msgstr_words as forward_src_words

  join translation_pairs as forward_trans on
       forward_src_words.msgstr = forward_trans.src_msgstr

  join msgstr_words as forward_dst_words on
       forward_trans.dst_msgstr = forward_dst_words.msgstr

  join msgstr_words as backward_src_words on
       forward_dst_words.word = backward_src_words.word

  join translation_pairs as backward_trans on
       backward_src_words.msgstr = backward_trans.src_msgstr 
   and forward_trans.dst_language = backward_trans.src_language 
   and forward_trans.src_language = backward_trans.dst_language

  join msgstr_words as backward_dst_words on
       backward_trans.dst_msgstr = backward_dst_words.msgstr
   and backward_dst_words.word = forward_src_words.word

 group by
  forward_src_words.word,
  forward_dst_words.word,
  forward_trans.src_language,
  forward_trans.dst_language;
