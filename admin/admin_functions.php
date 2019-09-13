<?php
// MISCELANEOUS FUNCTIONS  

    function redirect($location) {
        header("Location:" . $location);
		exit;
    }

	function if_it_is_method($method = null) {
		if($_SERVER['REQUEST_METHOD'] == strtoupper($method)) {
			return true;
		}
		return false;
	}
	
	function is_logged_in() {
		if(isset($_SESSION['user_role'])) {
			return true;
		}
		return false;
	}
	
	function logged_in_user_id() {
		if(is_logged_in()) {
			$query = "SELECT * FROM users WHERE username=" . "'" . $_SESSION['username'] . "'";
			$result = create_and_confirm_query($query);
			$user = mysqli_fetch_array($result);
			return mysqli_num_rows($result) >= 1 ? $user['user_id'] : false;
		}
		return false;
	}
	
	function post_liked($post_id = '') {
		$query = "SELECT * FROM likes WHERE user_id=" . logged_in_user_id() . " AND post_id='{$post_id}'";
		$result = create_and_confirm_query($query);
		return mysqli_num_rows($result) >= 1 ? true : false;
	}
	
	function image_placeholder($image = '') {
		if(!$image) {
			return '/cms/images/image_1.jpg';
		} else {
			return $image;
		}
	}
	
	function check_if_user_is_logged_in_and_redirect($redirect_location = null) {
		if(is_logged_in()) {
			redirect($redirect_location);
		}
	}

    function escape($string) {
        global $connection;
        return mysqli_real_escape_string($connection, trim($string));
    }

    function confirm_query($result) {    
        global $connection;       
        if(!$result) {
            die("QUERY FAILED ." . mysqli_error($connection));
        }
    }

    function check_if_there_are_entries($query) {
        global $connection;
        if(mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function record_count($table) {
        global $connection;
        $query  = "SELECT * FROM " . $table;
        $result = create_and_confirm_query($query);
		return mysqli_num_rows($result);
    }

    function create_and_confirm_query($query_sql) {
		global $connection;
		$created_query = mysqli_query($connection, $query_sql);
		confirm_query($created_query);
		return $created_query;
	}

    function get_something($something) {
		global $connection;
		return $the_thing = escape($_GET[$something]);
	}

// CATEGORY RELATED FUNCTIONS
 
    function insert_categories() {
        global $connection;
        if(isset($_POST['submit'])) {
            $cat_title = escape($_POST['cat_title']);
            if($cat_title == "" || empty($cat_title)) {
                echo "This field should not be empty";
            } else {
                $prep_statement = mysqli_prepare($connection, "INSERT INTO categories(cat_title) VALUE(?) ");
                mysqli_stmt_bind_param($prep_statement, "s", $cat_title);
                mysqli_stmt_execute($prep_statement);
                confirm_query($prep_statement);
            }
            mysqli_stmt_close($prep_statement);
            redirect("categories.php");
        }
    }

    function find_all_categories() {
        global $connection;
        $query = "SELECT * FROM categories";
        $select_categories = mysqli_query($connection, $query);                                                             
        while($row = mysqli_fetch_assoc($select_categories)) {
            $cat_id    = $row['cat_id'];
            $cat_title = $row['cat_title'];

            echo "<tr>";    
            echo "<td>{$cat_id}</td>";    
            echo "<td>{$cat_title}</td>";
            echo "<td><a href='categories.php?delete={$cat_id}'>Delete</a></td>";
            echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";
            echo "</tr>";    
        }
    }

    function delete_categories() {
        global $connection;
        if(isset($_GET['delete'])) {
            $the_cat_id = escape($_GET['delete']);
            $query = "DELETE FROM categories WHERE cat_id = {$the_cat_id} ";
            $delete_query = mysqli_query($connection, $query);
            redirect("categories.php");
        }
    }
    
    function update_categories() {
        // create it later while refactoring categories.php and update_categories.php
    }

// POST RELATED FUNCTIONS

    function update_post_count() {
        global $connection;
        $the_post_id = get_something('p_id');
        $query = "UPDATE posts SET post_view_count = post_view_count + 1 WHERE post_id = {$the_post_id} ";       
        create_and_confirm_query($query);
    }

    function select_posts_for_user_role_limited($number_of_posts, $posts_per_page) {
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
            $query = "SELECT * FROM posts ORDER BY post_id DESC LIMIT {$number_of_posts}, {$posts_per_page} ";
        } else {
            $query = "SELECT * FROM posts WHERE post_status = 'Published' ORDER BY post_id DESC LIMIT {$number_of_posts}, {$posts_per_page}";    
        }
        return create_and_confirm_query($query);
    }

    function select_all_posts() {
        global $connection;
        $the_post_id = get_something('p_id');
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
            $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} "; 
        } else {
            $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} AND post_status = 'Published' "; 
        }    
        return create_and_confirm_query($query);
    }

    function select_all_posts_by_tag($search) {
        global $connection;
        $query = "SELECT * FROM posts WHERE post_tags LIKE '%$search%' ";
        return create_and_confirm_query($query);
    }

    function check_status($table, $column, $status) {
        global $connection;
        $query  = "SELECT * FROM $table WHERE $column = '$status'";
        $result = create_and_confirm_query($query);
        return mysqli_num_rows($result);  
    }

    function check_for_admin_then_select_posts_query() {
        global $connection;
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
            $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} "; 
        } else {
            $query = "SELECT * FROM posts WHERE post_id = {$the_post_id} AND post_status = 'Published' "; 
        }    
        create_and_confirm_query($query);
    }
	
	function get_post_likes($post_id = '') {
		global $connection;
		$result = "SELECT * FROM likes WHERE post_id={$post_id}";
		echo mysqli_num_rows(create_and_confirm_query($result));
	}

// USER RELATED FUNCTIONS

    function is_admin($username = '') {
		global $connection;
		$query = "SELECT user_role FROM users WHERE username = '$username'";
		$row = mysqli_fetch_array(create_and_confirm_query($query));
		if($row['user_role'] == 'admin') {
			return true;
		} else {
			return false;
		} 
	}

    function username_exists($username) {
		global $connection;
		$query = "SELECT username FROM users WHERE username = '$username'";
		$result = create_and_confirm_query($query);
		return check_if_there_are_entries($result);
	}

    function email_exists($email) {
		global $connection;
		$query = "SELECT user_email FROM users WHERE user_email = '$email'";
		$result = create_and_confirm_query($query);
		return check_if_there_are_entries($result);
	}

    function register_user($username, $email, $password) {
		global $connection;
		$username = escape($_POST['username']);
		$email    = escape($_POST['email']);
		$password = escape($_POST['password']);
		
		if(username_exists($username)) {
			// What goes here?
			// Echo username already exists.

		} else if(!empty($username) && !empty($email) && !empty($password)) {
			$password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
			
			insert_user_into_db($username, $email, $password);
		}
	}

    function insert_user_into_db($username, $email, $password) {
		global $connection;
		$query  = "INSERT INTO users (username, user_email, user_password, user_role)";
		$query .= "VALUES ('{$username}', '{$email}', '{$password}', 'subscriber')";
		create_and_confirm_query($query);
	}

    function login_user($username, $password) {
		global $connection;
		
		$username = escape($username);
		$password = escape($password);

		$selected_user = select_user_query($username);
		
		while($row = mysqli_fetch_assoc($selected_user)) {
			$db_user_id        = $row['user_id'];
			$db_username       = $row['username'];
			$db_user_password  = $row['user_password'];
			$db_user_firstname = $row['user_firstname'];
			$db_user_lastname  = $row['user_lastname'];
			$db_user_role      = $row['user_role'];
		
			if(password_verify($password, $db_user_password)) {
				$_SESSION['username']  = $db_username;
				$_SESSION['firstname'] = $db_user_firstname;
				$_SESSION['lastname']  = $db_user_lastname;
				$_SESSION['user_role'] = $db_user_role;
				redirect("/cms/admin");
			} else {
				return false;
			}
		}
		return true;
	}

    function select_user_query($username) {
		global $connection;
		$query = "SELECT * FROM users WHERE username = '{$username}' ";
		return create_and_confirm_query($query);
	}

    function users_online() {
		if(isset($_GET['onlineusers'])) {
			global $connection;
			if(!$connection) {
				session_start();
				include("../includes/db.php");
			
				$session = session_id();
				$time = time();
				$time_out_in_seconds = 05;
				$time_out = $time - $time_out_in_seconds;

				$query = "SELECT * FROM users_online WHERE session = '$session'";
				$send_query = mysqli_query($connection, $query);
				$count = mysqli_num_rows($send_query);

				if($count == NULL) {
					mysqli_query($connection, "INSERT INTO users_online(session, time) VALUES('$session', $time)");
				} else {
					mysqli_query($connection, "UPDATE users_online SET time = $time WHERE session = '$session'");
				}

				$users_online_query = mysqli_query($connection, "SELECT * FROM users_online WHERE time > '$time_out'");
				$user_count = mysqli_num_rows($users_online_query);
				echo $user_count;

			}
		} // Get Request isset
	}
	users_online();

// COMMENT RELATED FUNCTIONS

    function select_all_comments_query() {
        global $connection;
        $the_post_id = get_something('p_id');
        $query  = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
        $query .= "AND comment_status = 'approved' ";
        $query .= "ORDER BY comment_id DESC ";
        return create_and_confirm_query($query);
    }           

    function insert_comment($comment_author, $comment_email, $comment_content) {
        global $connection;
        $the_post_id = get_something('p_id');
        $query  = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date) ";
        $query .= "VALUES ($the_post_id, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unnaproved', now() )";
        create_and_confirm_query($query);
    }

// PAGER FUNCTIONS

    function find_post_count_for_user_role() {
        global $connection;
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
            $query = "SELECT * FROM posts ";
        } else {
            $query = "SELECT * FROM posts WHERE post_status = 'Published' ";
        }
        return mysqli_num_rows(create_and_confirm_query($query));
    }

    function pager($count, $page) {
        for($i = 1; $i <= $count; $i++) {
            if($i == $page) {
                echo "<li><a class='active_link' href='index.php?page={$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='index.php?page={$i}'>{$i}</a></li>";
            }
        }
    }

    function get_page() {
        if(isset($_GET['page'])) {
            return $page = $_GET['page'];
        } else {
            return $page = "";
        } 
    }

    function number_of_posts_first_page($page, $posts_per_page) {
        if($page == "" || $page == 1) {
            return $page_1 = 0;
        } else {
            return $page_1 = ($page * $posts_per_page) - $posts_per_page;
        }
    }

// DEVELOPMENT ZONE  

?>