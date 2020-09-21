<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Some Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<p>Access denied</p>';
    exit;
} else if (
		!(
			   isset($_SERVER['PHP_AUTH_USER'])
			&& isset($_SERVER['PHP_AUTH_PW'])
			&& $_SERVER['PHP_AUTH_USER'] == 'astro_admin'
			&& hash('md5',$_SERVER['PHP_AUTH_PW']) == '9fc4780379b227f6b308d33c6b812c0a'
		)
	) {
	// incorrect username or password
    echo '<p>Access Denied</p>';
    exit;
}
// successful authentication
?>
