<?php
 include('head.php');
 $src_word = pg_escape_string($_REQUEST["src_word"]);
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

   if ($src_word != '' && $src_lang != '' && $dst_lang != '') {
       $query = "select id from words where string = '{$src_word}'";
       $result = pg_query($query) or die('Query failed: ' . pg_last_error());
       $row = pg_fetch_assoc($result);
       pg_free_result($result);
       $src_word_id = $row['id'];

       if ($src_word_id == '') {
           echo "The word you searched for was not found in the database.";
       } else {
	   $query = "select\n" .
		    " words.string as word, value, weight, reverse_weight, count, reverse_count, total, reverse_total\n" .
		    "from\n" .
		    " word_translation_value,\n" .
		    " words\n" .
		    "where\n" .
		    "     src_word = '{$src_word_id}'\n" .
		    " and src_language = '{$src_lang}'\n" .
		    " and dst_language = '{$dst_lang}'\n" .
		    " and dst_word = words.id\n" .
		    "order by value desc\n" .
		    "limit 20";

	    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

	    echo "<table border=1>\n";
	    echo "<tr>\n";
                echo "<td></td>";
		for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		    echo "<th>" . pg_field_name($result, $gt) . "</th>";
		}
	    echo "</tr>\n";

	    for($lt = 0; $lt < pg_num_rows($result); $lt++) {
		$dst_word = pg_result($result, $lt, 0);

		echo "<tr>\n";
                echo "<td>\n";
                echo "<a href='index.php?src_word={$dst_word}&src_lang={$_REQUEST["dst_lang"]}&dst_lang={$_REQUEST["src_lang"]}'>reverse</a> ";
                echo "<a href='centences.php?src_word={$src_word}&dst_word={$dst_word}&src_lang={$_REQUEST["src_lang"]}&dst_lang={$_REQUEST["dst_lang"]}'>examples</a> ";
                echo "</td>\n";

		for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		    echo "<td>";
		    echo pg_result($result, $lt, $gt);
		    echo "</td>\n";
		}
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
