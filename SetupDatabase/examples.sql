
select
 trans1.weight * trans2.weight as value,
 trans1.*
from
 word_translations as trans1
 left outer join word_translations as trans2 on
  trans1.src_word_str = 'message'
  and trans2.dst_word_str = 'message'
  and trans1.src_language_symbol = 'fr'
  and trans2.dst_language_symbol = 'fr'
  and trans1.dst_language_symbol = 'de'
  and trans2.src_language_symbol = 'de'
  and trans1.dst_word = trans2.src_word
  and trans1.weight > 1
  and trans2.weight > 1;
 