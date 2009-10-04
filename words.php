<?php
 include('head.php');
 $word = pg_escape_string($_REQUEST["word"]);
 $lang = pg_escape_string($_REQUEST["lang"]);
?>

  <form>
   <div>Word pattern: <input type="text" name="word" value="<?=$_REQUEST["word"];?>" /></div>
   <div>Language:
    <select name="lang">
     <?php
      $query = "select id, symbol, name from languages order by symbol, name";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      
      while ($row = pg_fetch_assoc($result)) {
       $selected = '';
       if ($lang == $row['id'])
        $selected = "selected='selected'";
       echo "<option {$selected} value='{$row['id']}'>{$row['symbol']} - {$row['name']}</option>";
      }
     ?>
    </select>
   </div>
   <div><button type="submit" name="submit">Submit</button></div>
  </form>

  <?php

   if ($word != '') {
       $query = "select\n" .
		" word, string\n" .
		"from\n" .
		" language_words_strs\n" .
		"where\n" .
		"     string like '%{$word}%'\n" .
		" and language = '{$lang}'\n" .
		"order by string desc\n" .
		"limit 20";

	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

	echo "<table border=1>\n";
	echo "<tr>\n";
	    echo "<td></td>";
	    echo "<td>Word</td>";
	echo "</tr>\n";

	for($lt = 0; $lt < pg_num_rows($result); $lt++) {
	    $word = pg_result($result, $lt, 0);
            $string = pg_result($result, $lt, 1);

	    echo "<tr>\n";
	    echo "<td>\n";
	    echo "<a href='index.php?src_word={$string}&src_lang={$lang}'>translate</a> ";
	    echo "<a href='centences.php?src_word={$string}&src_lang={$lang}'>examples</a> ";
	    echo "</td>\n";

	    echo "<td>";
	    echo $string;
	    echo "</td>\n";
	    echo "</tr>\n";
	}
	echo "</table>\n";

	pg_free_result($result);
   }
  ?>

<?php
 include('foot.php');
?>
