<?php 

// SEO-TOOLS

// check if sessione started yet
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){

	$password_err = "";

	if($_SERVER["REQUEST_METHOD"] == "POST"){

	    // Check if password is empty
	    if(empty(trim($_POST['password']))){
	        $password_err = 'Please enter your password.';
	    } else {

	    	require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';  // $psw

	        $password = trim($_POST['password']); 
	        $hashed_password = trim(password_hash($psw, PASSWORD_DEFAULT)); 
	        //var_dump($hashed_password);
	    }
	    
	    // Validate credentials
	    if( empty($password_err) ){         
	        if(password_verify($password, $hashed_password)) {

	            /* Password is correct, so start a new session and save the username to the session */
	            // session_start();
	            $_SESSION['username'] = 'me-stesso-medesimo';
	            $user = $_SESSION['username'];
	            $cookie_expiring = 2592000;  // 30 gg  // -1 delete cookie
	            setcookie('LOGIN', $user, time()+$cookie_expiring);
	            header("location: index.php");
	            
	        } else {

	            $password_err = 'The password you entered was not valid.';
	        }

	    } else {

	    	echo "Oops! Something went wrong. Please try again later.";
	    }
	}
} else {

	$username = trim($_SESSION['username']);

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['logout'] == 'logout'){
		//logout - Unset all of the session variables
		$_SESSION = array();
		// Destroy the session.
		session_destroy();
		// delete LOGIN cookie
		setcookie('LOGIN', '', time()-1);
		header("location: index.php");
	}

}


?>
<html>
	<head>
		<style>
			body { margin:0; padding:0; width: 100%; height: 100%; font-family: 'Courier New'; }
			.container { position: absolute; text-align: center; top: 50%; transform: translate(-50%, -50%); left: 50%; padding: 10px; border: 1px solid #CCCCCC; }
			.alert-msg { border: 1px solid red; color: red; padding: 5px 0; }
			.title { letter-spacing: 3px; text-shadow: 2px 2px 0px #CCC;}
			.sub-title { font-size: 70%; }
			.list ul{ text-align: left; padding-left: 10%; }
			.list li { margin: 10px auto; }
			.login {  }
			.form-control { float: left; max-width: 150px; }
			.help-block, .has-error { color: red; font-size: 80%; display: block;}
			.logged-in { color: green; margin-bottom: 10px;}
			.notation { font-size: 75%; }
		</style>
	</head>
	<body>
		<div class="container">
			<?php 

				if( isset($_GET['alertmsg']) && !isset($_SESSION['username']) ) { 
					$msg = $_GET['alertmsg'];
					echo '<div class="alert-msg">' . urldecode($msg) . '</div>';
			 	} 

			?>
			<h1 class="title">SEO TOOLS</h1>
			<div class="sub-title">Your IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></div>
			<?php 
				if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {  
			?>
			<div class="list">
				<ul style="text-align: center; list-style: none; padding-left:0;">
					<li>Keywords list</li>
					<li>Scrape keywords</li>
					<li>Scrape serp</li>
					<li>Links Monitor</li>
				</ul>
			</div>
			<div class="login">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
	                	<input type="password" name="password" class="form-control" placeholder="password..">
	                	<input type="submit" class="btn btn-primary" value="Login">
	                	<span class="help-block"><?php echo $password_err; ?></span>
            		</div>
        		</form>
			</div>
			<?php 
				} else {  
			?>
			<div class="list">
				<ul>
					<li><a href="keywords" target="_blank">Keywords List</a> <span class="notation">(API google search console)</span></li>
					<li><a href="scrape-serp" target="_blank">Scrape Serp</a><span class="notation"> (google serp scraping)</span></li>
					<li><a href="scrape-keywords" target="_blank">Scrape Keywords</a><span class="notation"> (google serp scraping (related search))</span></li>
					<li><a href="links-monitor" target="_blank">Links Monitor</a><span class="notation">(external pages scraping)</span></li>
					<li><a href="adminer.php" target="_blank">DB</a><span class="notation"> (Adminer)</span></li>
				</ul>
			</div>
			<div class="logged-in">You are Logged-in</div>
			<div class="logout">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group">
						<input type="hidden" name="logout" value="logout">
	                	<input type="submit" class="btn btn-primary" value="Logout">
            		</div>
        		</form>
			</div>
			<?php  
				}  
			?>
		</div>
	</body>
</html>
	
