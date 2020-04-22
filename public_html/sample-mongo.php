<html>
<body>
MongoDB Sample program
<?php
	// display debug messages
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// https://gist.github.com/banker/795791
  // connect
  $m = new Mongo();

  // select a database
  $db = $m->training;

  $coll = $db->messages;

  for ($i = 0; $i < 10; $i = $i + 1) {
    $coll->insert( array( "text" => "Hello World", "n" => $i ) );
  }

  $cursor = $coll->find();

  foreach ($cursor as $obj) {
    print $obj['_id'] . "\n";
    print print_r( $obj ) . "\n";
  }

  print "\nCount " . $coll->count() . "\n";

  $coll->remove();

  print "\nCount " . print_r( $db->command( array( "count" => "messages" ) )) . "\n";

?>
</body>
</html>
