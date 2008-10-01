
<html>
 <head>
  <title>FOSSTrans</title>
 </head>
 <body>

  <?php
   $dbconn = pg_connect("host=localhost dbname=fosstrans user=redhog password=saltgurka")
	      or die('Could not connect: ' . pg_last_error());
   $src_word = pg_escape_string($_REQUEST["src_word"]);
   $src_lang = pg_escape_string($_REQUEST["src_lang"]);
   $dst_lang = pg_escape_string($_REQUEST["dst_lang"]);
  ?>

  <form>
   <h1>FOSSTrans</h1>
   <div>Source word: <input type="text" name="src_word" value="<?=$_REQUEST["src_word"];?>" /></div>
   <div>Source language:
    <select name="src_lang">
     <?php
      $query = "select id, symbol, name from languages";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());
      
      while ($row = pg_fetch_assoc($result)) {
       $selected = '';
       if ($src_lang == $row['id'])
        $selected = "selected='selected'";
       echo "<option {$selected} value='{$row['id']}'>{$row['symbol']} - {$row['name']}</option>";
      }
     ?>
    </select>
   <div>Destination language:
    <select name="dst_lang">
     <?php
      $query = "select id, symbol, name from languages";
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

   if ($src_word != '' and $src_lang != '' && dst_lang != '') {
       $query = "select id from words where string = '{$src_word}'";
       $result = pg_query($query) or die('Query failed: ' . pg_last_error());
       $row = pg_fetch_assoc($result);
       $src_word_id = $row['id'];

       $query = "select\n" .
		" dst_word_str, count, weight, value\n" .
		"from\n" .
		" word_translation_value\n" .
		"where\n" .
		"     src_word = '{$src_word_id}'\n" .
		" and src_language = '{$src_lang}'\n" .
		" and dst_language = '{$dst_lang}'\n" .
		"order by value desc\n" .
		"limit 20";

        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

	echo "<table border=1>\n";
	echo "<tr>\n";
	    for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		echo "<td>" . pg_field_name($result, $gt) . "</td>";
	    }
	echo "</tr>\n";

	for($lt = 0; $lt < pg_num_rows($result); $lt++) {
	    echo "<tr>\n";
	    for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		echo "<td>" . pg_result($result, $lt, $gt) . "</td>\n";
	    }
	    echo "</tr>\n";
	}
	echo "</table>\n";

       pg_free_result($result);

       echo "<pre>";
       echo htmlspecialchars($query);
       echo "</pre>";
   }
  ?>


  <?php
   pg_close($dbconn);
  ?>

 </body>
</html>
