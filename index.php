<html>
 <head>
  <title>FOSSTrans</title>
 </head>
 <body>
  <form>
   <h1>FOSSTrans</h1>
   <div>Source word: <input type="text" name="src_word" value="<?=$_REQUEST["src_word"];?>" /></div>
   <div>Source language: <input type="text" name="src_lang" value="<?=$_REQUEST["src_lang"];?>" /></div>
   <div>Destination language: <input type="text" name="dst_lang" value="<?=$_REQUEST["dst_lang"];?>" /></div>
   <div><button type="submit" name="submit">Submit</button></div>
  </form>

  <?php
   $src_word = pg_escape_string($_REQUEST["src_word"]);
   $src_lang = pg_escape_string($_REQUEST["src_lang"]);
   $dst_lang = pg_escape_string($_REQUEST["dst_lang"]);

   if ($src_word != '' and $src_lang != '' && dst_lang != '') {
       $dbconn = pg_connect("host=localhost dbname=fosstrans user=redhog password=saltgurka")
	  or die('Could not connect: ' . pg_last_error());

       $query = "select " .
		" dst_word_str, count, weight, value " .
		"from " .
		" word_translations2 " .
		"where " .
		"     src_word_str = 'message' " .
		" and src_language_symbol = 'fr' " .
		" and dst_language_symbol = 'de' " .
		"order by value desc " .
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

       pg_close($dbconn);
   }
  ?>

 </body>
</html>
