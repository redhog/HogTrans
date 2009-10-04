<?php
 include('head.php');
 $src_word = pg_escape_string($_REQUEST["src_word"]);
 $dst_word = pg_escape_string($_REQUEST["dst_word"]);
 $src_lang = pg_escape_string($_REQUEST["src_lang"]);
 $dst_lang = pg_escape_string($_REQUEST["dst_lang"]);
?>

  <form>
   <div>
    Source word: <input type="text" name="src_word" value="<?=$_REQUEST["src_word"];?>" />
    <a href="<?php echo "words.php?word={$src_word}&lang={$src_lang}"; ?>">...</a>
   </div>
   <div>Source language:
    <select name="src_lang">
     <?php
      $query = "select id, symbol, name from languages order by symbol, name";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      
      while ($row = pg_fetch_assoc($result)) {
       $selected = '';
       if ($src_lang == $row['id'])
        $selected = "selected='selected'";
       echo "<option {$selected} value='{$row['id']}'>{$row['symbol']} - {$row['name']}</option>";
      }
     ?>
    </select>
   <div>
    Destination word: <input type="text" name="dst_word" value="<?=$_REQUEST["dst_word"];?>" />
    <a href="<?php echo "words.php?word={$dst_word}&lang={$dst_lang}"; ?>">...</a>
   </div>
   <div>Destination language:
    <select name="dst_lang">
     <?php
      $query = "select id, symbol, name from languages order by symbol, name";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      
      while ($row = pg_fetch_assoc($result)) {
       $selected = '';
       if ($dst_lang == $row['id'])
        $selected = "selected='selected'";
       echo "<option {$selected} value='{$row['id']}'>{$row['symbol']} - {$row['name']}</option>";
      }
     ?>
    </select>
   </div>
   <div><button type="submit" name="submit">Submit</button></div>
  </form>

  <?php

   if ($src_word != '' && $dst_word != '' && $src_lang != '' && dst_lang != '') {
       $query = "select id from words where string = '{$src_word}'";
       $result = pg_query($query) or die('Query failed: ' . pg_last_error());
       $row = pg_fetch_assoc($result);
       pg_free_result($result);
       $src_word_id = $row['id'];

       $query = "select id from words where string = '{$dst_word}'";
       $result = pg_query($query) or die('Query failed: ' . pg_last_error());
       $row = pg_fetch_assoc($result);
       pg_free_result($result);
       $dst_word_id = $row['id'];

       if ($src_word_id == '' or $dst_word_id == '') {
           echo "The word(s) you searched for was not found in the database.";
       } else {
	   $query = "select distinct\n" .
		    " src, dst, src_msgstr_str, dst_msgstr_str\n" .
		    "from\n" .
		    " translation_strs\n" .
		    "where\n" .
		    "     src_word = '{$src_word_id}'\n" .
		    " and dst_word = '{$dst_word_id}'\n" .
		    " and src_language = '{$src_lang}'\n" .
		    " and dst_language = '{$dst_lang}'";

	    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

	    echo "<table border=1>\n";
	    echo "<th></th>";
	    echo "<th>Source language</th>";
	    echo "<th>Destination language</th>";
	    echo "</tr>\n";

	    for($lt = 0; $lt < pg_num_rows($result); $lt++) {
                $src=pg_result($result, $lt, 0);
                $dst=pg_result($result, $lt, 1);
                $src_msgstr=pg_result($result, $lt, 2);
                $dst_msgstr=pg_result($result, $lt, 3);

		echo "<tr>\n";
		echo "<td>";
		echo "<a href='centence.php?translation={$src}&src_lang={$src_lang}&dst_lang={$dst_lang}'>src</a> <a href='centence.php?translation={$dst}&src_lang={$dst_lang}&dst_lang={$src_lang}'>dst</a>";
		echo "</td>\n";
		echo "<td>";
		echo wordlink($src_msgstr, $src_lang, $dst_lang);
		echo "</td>";
		echo "<td>";
		echo wordlink($dst_msgstr, $dst_lang, $src_lang);
		echo "</td>\n";
		echo "</tr>\n";
	    }
	    echo "</table>\n";

            pg_free_result($result);

        }
   } else if ($src_word != '' && $src_lang != '') {
       $query = "select id from words where string = '{$src_word}'";
       $result = pg_query($query) or die('Query failed: ' . pg_last_error());
       $row = pg_fetch_assoc($result);
       pg_free_result($result);
       $src_word_id = $row['id'];

       if ($src_word_id == '') {
           echo "The word(s) you searched for was not found in the database.";
       } else {
	   $query = "select\n" .
		    " id, string\n" .
		    "from\n" .
		    " language_msgstrs_word\n" .
		    "where\n" .
		    "     word = '{$src_word_id}'\n" .
		    " and language = '{$src_lang}'";

	    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

	    echo "<table border=1>\n";
	    echo "<tr><th></th><th>String</th></tr>\n";

	    for($lt = 0; $lt < pg_num_rows($result); $lt++) {
                $src = pg_result($result, $lt, 0);
                $string = pg_result($result, $lt, 1);
		echo "<tr>\n";
		echo "<td>";
		echo "<a href='centence.php?translation={$src}&src_lang={$src_lang}&dst_lang={$dst_lang}'>src</a>";
		echo "</td>\n";
  	        echo "<td>";
		echo wordlink($string, $src_lang, $dst_lang);
		echo "</td>\n";
		echo "</tr>\n";
	    }
	    echo "</table>\n";

            pg_free_result($result);

        }
   }
  ?>

<?php
 include('foot.php');
?>
