<?php
 include('head.php');
 $translation = pg_escape_string($_REQUEST["translation"]);
 $src_lang = pg_escape_string($_REQUEST["src_lang"]);
 $dst_lang = pg_escape_string($_REQUEST["dst_lang"]);
?>
  <?php

   $query = "select\n" .
	    " mofile, string\n" .
	    "from\n" .
	    " language_msgstrs\n" .
	    "where\n" .
	    "     id ='{$translation}'";
   $result = pg_query($query) or die('Query failed: ' . pg_last_error());
   $mofile = pg_result($result, $lt, 0);
   $string = pg_result($result, $lt, 1);

   $query = "select\n" .
	    " path, name, version\n" .
	    "from\n" .
	    " mofiles_info\n" .
	    "where\n" .
	    "     id ='{$mofile}'";
   $result = pg_query($query) or die('Query failed: ' . pg_last_error());
   $mofile_path = pg_result($result, $lt, 0);
   $package_name = pg_result($result, $lt, 1);
   $package_version = pg_result($result, $lt, 1);

   $query = "select\n" .
	    " name, value\n" .
	    "from\n" .
	    " mofile_metadatas_info\n" .
	    "where\n" .
	    "     mofile ='{$mofile}'";
   $result = pg_query($query) or die('Query failed: ' . pg_last_error());

   echo "<table border=1>\n";
   echo "<tr><th>Name</th><th>Value</th></tr>\n";

   echo "<tr><td>Text</td><td>"; echo wordlink($string, $src_lang, $dst_lang); echo "</td></tr>";
   echo "<tr><td>Mofile</td><td>{$mofile_path}</td></tr>";
   echo "<tr><td>Package name</td><td>{$package_name}</td></tr>";
   echo "<tr><td>Package version</td><td>{$package_version}</td></tr>";

   for($lt = 0; $lt < pg_num_rows($result); $lt++) {
       echo "<tr>\n";
       echo "<td>";
       echo pg_result($result, $lt, 0);
       echo "</td>\n";
       echo "<td>";
       echo pg_result($result, $lt, 1);
       echo "</td>\n";
       echo "</tr>\n";
   }
   echo "</table>\n";

   pg_free_result($result);

  ?>

<?php
 include('foot.php');
?>
