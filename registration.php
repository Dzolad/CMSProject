<?php include "includes/db.php"; ?>
<?php include "includes/header.php"; ?>
<?php require __DIR__ . '/vendor/autoload.php'; ?>
<?php
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$options = array(
	'cluster' => 'eu',
	'useTLS' => true
);
$pusher = new Pusher\Pusher(getenv('APP_KEY'), getenv('APP_SECRET'), getenv('APP_ID'), $options);


if($_SERVER['REQUEST_METHOD'] == "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $error = [
        'username'=>'',
        'email'   =>'',
        'password'=>''
    ];
    
    if(strlen($username) < 4) {
        $error['username'] = "Username needs to be 4 characters or longer.";
    }
    if($username == '') {
        $error['username'] = "Username must not be empty.";
    }
    if(username_exists($username)) {
        $error['username'] = "Username already in use, please choose another one.";
    }
    
    if($email == '') {
        $error['email'] = "Email must not be empty.";
    }
    if(email_exists($email)) {
        $error['email'] = "Email already in use, please choose another one.";
    }
    
    if(strlen($password) < 4) {
        $error['password'] = "Password needs to be 4 characters or longer.";
    }
    if($password == '') {
        $error['password'] = "Password must not be empty.";
    }
    
    foreach($error as $key => $value) {
        if(empty($value)) {
            unset($error[$key]);
        }
    }
    
    if(empty($error)) {
        register_user($username, $email, $password);
		
		$data['message'] = $username;
		$pusher->trigger('notifications', 'new_user', $data);

        login_user($username, $password);
    }
}

 ?>

    <!-- Navigation -->
    
    <?php  include "includes/navigation.php"; ?>
    
    <!-- Page Content -->
    <div class="container">
    
		<section id="login">
			<div class="container">
				<div class="row">
					<div class="col-xs-6 col-xs-offset-3">
						<div class="form-wrap">
						<h1>Register</h1>
							<form role="form" action="registration.php" method="post" id="login-form" autocomplete="off">
							   <h6 class="text-center"></h6>
								<div class="form-group">
									<label for="username" class="sr-only">username</label>
									<p><?php echo isset($error['username']) ? $error['username'] : "" ?></p>
									<input type="text" name="username" id="username" class="form-control" placeholder="Enter Desired Username" autocomplete="on" value="<?php echo isset($username) ? $username : '' ?>">
								</div>
								 <div class="form-group">
									<label for="email" class="sr-only">Email</label>
									<p><?php echo isset($error['email']) ? $error['email'] : "" ?></p>
									<input type="email" name="email" id="email" class="form-control" placeholder="somebody@example.com" autocomplete="on" value="<?php echo isset($email) ? $email : '' ?>">
								</div>
								 <div class="form-group">
									<label for="password" class="sr-only">Password</label>
									<p><?php echo isset($error['password']) ? $error['password'] : "" ?></p>
									<input type="password" name="password" id="key" class="form-control" placeholder="Password">
								</div>
						
								<input type="submit" name="register" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Register">
							</form>
						 
						</div>
					</div> <!-- /.col-xs-12 -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</section>
        <hr>

<?php include "includes/footer.php";?>
