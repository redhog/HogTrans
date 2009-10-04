<?php

function wordlink($string, $src_lang, $dst_lang) {
 return preg_replace("/([^][ !\"#$%&'()*+,-.\/0123456789:;<=>?@\\^_`{|}~]*)/", "<a href='index.php?src_word=$1&src_lang={$src_lang}&dst_lang={$dst_lang}'>$1</a>", $string);
}


