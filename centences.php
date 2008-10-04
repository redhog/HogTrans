<html>
 <head>
  <title>FOSSTrans</title>
  <link type="text/css" rel="stylesheet" href="fosstrans.css" />
 </head>
 <body>

  <?php
   $dbconn = pg_connect("host=localhost dbname=fosstrans user=redhog password=saltgurka")
	      or die('Could not connect: ' . pg_last_error());
   $src_word = pg_escape_string($_REQUEST["src_word"]);
   $dst_word = pg_escape_string($_REQUEST["dst_word"]);
   $src_lang = pg_escape_string($_REQUEST["src_lang"]);
   $dst_lang = pg_escape_string($_REQUEST["dst_lang"]);
  ?>

  <form>
   <h1>FOSSTrans</h1>
   <div>Source word: <input type="text" name="src_word" value="<?=$_REQUEST["src_word"];?>" /></div>
   <div>Destination word: <input type="text" name="dst_word" value="<?=$_REQUEST["dst_word"];?>" /></div>
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
	   $query = "select\n" .
		    " src_msgstr_str, dst_msgstr_str\n" .
		    "from\n" .
		    " translation_strs\n" .
		    "where\n" .
		    "     src_word = '{$src_word_id}'\n" .
		    " and dst_word = '{$dst_word_id}'\n" .
		    " and src_language = '{$src_lang}'\n" .
		    " and dst_language = '{$dst_lang}'";

	    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

	    echo "<table border=1>\n";
	    echo "<tr>\n";
		for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		    echo "<th>" . pg_field_name($result, $gt) . "</th>";
		}
	    echo "</tr>\n";

	    for($lt = 0; $lt < pg_num_rows($result); $lt++) {
		echo "<tr>\n";
		$dst_word = pg_result($result, $lt, 0);
		for($gt = 0; $gt < pg_num_fields($result); $gt++) {
		    echo "<td>";
		    echo "<a href='?src_word={$dst_word}&src_lang={$_REQUEST["dst_lang"]}&dst_lang={$_REQUEST["src_lang"]}'>";
		    echo pg_result($result, $lt, $gt);
		    echo "</a>";
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
   pg_close($dbconn);
  ?>

 </body>
</html>
