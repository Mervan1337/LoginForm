<h1><a href="index.php">Return to Start</a></h1>
<?php
require_once 'core/init.php';
$user = new User();

if($user->hasPermission('admin')) {
    echo '<form method="post" action="adminconsole.php">';
    echo "<table><tr><th>Registered users:</th></tr>";
    $users = new User();
    foreach ($users->getUsers() as $user) {
        if(isset($_POST['delete' . $user->username])) {
            $users->deleteUser($user->id);
        } else {

            $start_date = new DateTime($user->last_login,new DateTimeZone('Europe/Stockholm'));
            $end_date     = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Europe/Stockholm'));
            $interval     = $start_date->diff($end_date);
            $hours       = $interval->format('%h'); 
            $minutes     = $interval->format('%i');
            $total         = ($hours * 60 + $minutes);
        echo "<tr>
                <td>Username = ".$user->username."</td>
                <td>Name = ".$user->name."</td>
                <td>Group = ".$user->group."</td>
                <td>Joined = ".$user->joined."</td>";
                if ($total < 5) {
                    ?><td><span id='bold'>Last logged in = </span><span id="online">Online</span></td>
                    <?php
                } else {
                    echo "<td><span id='bold'>Last logged in = </span>".$user->last_login."</td>";
                }
                ?>
                <td>
                    <label for="delete"><span id="delete">Delete User:</span></label>
                    <input type="checkbox" name="delete<?php echo $user->username ?>">

                </td>
                <?php
        echo "</tr>";

        
        }
    }
    echo "</table>";
    echo '<input type="submit" value="Delete">';
    echo '</form>';    
    echo '<strong>Register a new user:</strong><br><br>';
    if (Input::exists()) {
        if (Token::check(Input::get('token'))) {
    
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                'username' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 20,
                    'unique' => 'users'
                ),
                'password' => array(
                    'required' => true,
                    'min' => 6
                ),
                'password_again' => array(
                    'required' => true,
                    'matches' => 'password'
                ),
                'name' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 50
                )
            ));
    
            if($validation->passed()) {
                $user = new User();
    
                $salt = Hash::salt(32);
    
                try {
    
                    $user->create(array(
                        'username' => Input::get('username'),
                        'password' => Hash::make(Input::get('password'), $salt),
                        'salt' => $salt,
                        'name' => Input::get('name'),
                        'joined' => date('Y-m-d H:i:s'),
                        'group' => Input::get('group')
                    ));
    
                    Session::flash('home', 'A new user has been registered.');
                    Redirect::to('adminconsole.php');
    
                } catch(Exeption $e) {
                    die($e->getMessage());
                }
            } else {
                foreach ($validation->errors() as $error) {
                    echo $error, '<br>';
                }
            }
        }
        }
    
    

    ?>
    
    <form action="" method="post" class="form">
        <div class="field">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" autocomplete="off">
        </div>
    
        <div class="field">
            <label for="password">Choose a password</label>
            <input type="password" name="password" id="password">
        </div>
    
        <div class="field">
            <label for="password_again">Enter your password again</label>
            <input type="password" name="password_again" id="password_again">
        </div>
    
        <div class="password">
            <label for="name">Your name</label>
            <input type="text" name="name" value="<?php echo escape(Input::get('name')); ?>" id="name">
        </div>

        <div class="group">
            <label for="group">Group</label>
            <input type="number" max="2" min="1" name="group" id="group" value="1">
        </div>
    
        <input type="submit" value="Register">
        <input type="hidden" name="token" value="<?php echo Token::generate();?>">
    </form>

<?php
} 
else {
    Redirect::to('index.php');
}
?>