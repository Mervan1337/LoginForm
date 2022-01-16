<?php
require_once 'core/init.php';

if (!$username = Input::get('user')) {
	Redirect::to('index.php');
} else {
	$user = new User();
	if(empty($user->data()->name)) {
		Redirect::to(404);
	} else {
		$data = $user->data();
	}
	?>
	<h1><a href="index.php">Return to start</a></h1>
	<h1>User: <?php echo escape($data->username); ?></h1>
	<p>Full name: <?php echo escape($data->name); ?></p>

	<?php
}