<!-- post.php is the file that displays individual posts within the index.php page. -->
<!-- Refactoring necessary -->

<!-- Main Includes: Database and Header -->
<?php include "includes/db.php" ?>
<?php include "includes/header.php"; ?>   
    
    <!-- Navigation -->
<?php include "includes/navigation.php"; ?>
    
<?php
	if(isset($_POST['liked'])) {
		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		
		$query = "SELECT * FROM posts WHERE post_id='{$post_id}'";
		$search_post_query = create_and_confirm_query($query);
		$post_result = mysqli_fetch_array($search_post_query);
		$likes = $post_result['likes'];

		$query = "UPDATE posts SET likes='{$likes}'+1 WHERE post_id='{$post_id}'";
		create_and_confirm_query($query);
		
		$query = "INSERT INTO likes(user_id, post_id) VALUES($user_id, $post_id)";
		create_and_confirm_query($query);
	}

	if(isset($_POST['unliked'])) {
		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		
		$query = "SELECT * FROM posts WHERE post_id='{$post_id}'";
		$search_post_query = create_and_confirm_query($query);
		$post_result = mysqli_fetch_array($search_post_query);
		$likes = $post_result['likes'];

		$query = "UPDATE posts SET likes='{$likes}'-1 WHERE post_id='{$post_id}'";
		create_and_confirm_query($query);
		
		$query = "DELETE FROM likes WHERE post_id='{$post_id}' AND user_id='{$user_id}'";
		create_and_confirm_query($query);
	}
?>
	
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">
                <?php
                
                if(isset($_GET['p_id'])) {
					$the_post_id = $_GET['p_id'];
					
					$update_statement = mysqli_prepare($connection, "UPDATE posts SET post_view_count = post_view_count + 1 WHERE post_id = ?");
					
					mysqli_stmt_bind_param($update_statement, "i", $the_post_id);
					mysqli_stmt_execute($update_statement);
					
					if(!$update_statement) {
						die("Query failed.");
					}
					
					if(isset($_SESSION['username']) && is_admin($_SESSION['username'])) {
						$stmt1 = mysqli_prepare($connection, "SELECT post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_id = ?");
					} else {
						$status = "published";
						$stmt2 = mysqli_prepare($connection, "SELECT post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_id = ? AND post_status = ?");
					}
					
					if(isset($stmt1)) {
						mysqli_stmt_bind_param($stmt1, "i", $the_post_id);
						mysqli_stmt_execute($stmt1);
						mysqli_stmt_bind_result($stmt1, $post_title, $post_author, $post_date, $post_image, $post_content);
						$stmt = $stmt1;
					} else {
						mysqli_stmt_bind_param($stmt2, "is", $the_post_id, $status);
						mysqli_stmt_execute($stmt2);
						mysqli_stmt_bind_result($stmt2, $post_title, $post_author, $post_date, $post_image, $post_content);
						$stmt = $stmt2;
					}
				
					while(mysqli_stmt_fetch($stmt)) {
?>

					<!-- First Blog Post -->
					<h1>
						<a href="<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
					</h1>
					<p class="lead">
						by <a href="/cms/index"><?php echo $post_author ?></a>
					</p>
					<p><span class="glyphicon glyphicon-time"></span><?php echo " " . $post_date ?></p>
					<hr>
					<img class="img-responsive" src="/cms/images/<?php echo image_placeholder($post_image); ?>" alt="">
					<hr>
					<p><?php echo $post_content ?></p>
					<hr>

<!-- Free the MYSQLI Statement for the Like Button to work. -->
<?php mysqli_stmt_free_result($stmt); ?>

				<?php if(is_logged_in()) { ?>

					<div class="row">
						<p class="pull-right"><a 
						   class="<?php echo post_liked($the_post_id) ? ' unlike' : ' like'; ?>"
						   href=""><span 
						   class=<?php echo post_liked($the_post_id) ? "'glyphicon glyphicon-thumbs-down'" : "'glyphicon glyphicon-thumbs-up'" ?>
						   data-toggle="tooltip"
						   data-placement="top"
						   title="<?php echo post_liked($the_post_id) ? ' I liked this before' : ' Want to like it?'; ?>"
						   ></span><?php echo post_liked($the_post_id) ? ' Unlike' : ' Like'?></a></p>
					</div>
					<div class="clearfix"></div>

                <?php  } else { ?>
					<div class="row">
						<p class="pull-right login-to-post">You need to <a href="/cms/login.php">Login</a> to like.<p>
					</div>
				<?php } ?>
				
					<div class="row">
						<p class="pull-right">Like: <?php get_post_likes($the_post_id); ?></p>
					</div>

                    <!-- Create New Comments -->
                    <?php
                    if(isset($_POST['create_comment'])) {
                        $comment_author  = escape($_POST['comment_author']);
                        $comment_email   = escape($_POST['comment_email']);
                        $comment_content = escape($_POST['comment_content']);

                        if(!empty($comment_author) && !empty($comment_email) && !empty($comment_content)) {
                            insert_comment($comment_author, $comment_email, $comment_content); 
                        } else {
                            echo "<script>alert('Fields Cannot Be Empty')</script>";
                        }                
                    }    
                    ?>                     

                    <!-- Comments Form -->
                    <div class="well">
                        <h4>Leave a Comment:</h4>
                        <form action="" method="post" role="form">

                            <div class="form-group">
                                <label for="Author">Author</label>
                                <input type="text" class="form-control" name="comment_author">
                            </div>

                            <div class="form-group">
                               <label for="Email">Email</label>
                                <input type="email" class="form-control" name="comment_email">
                            </div>

                            <div class="form-group">
                                <label for="Comment">Your Comment</label>
                                <textarea class="form-control" rows="3" name="comment_content"></textarea>
                            </div>
                            <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
                        </form>
                    </div>

                    <hr>
                  
                  
                    <!-- Posted Comments -->
                    <?php   
                    $comment_query = select_all_comments_query();
                    while ($row = mysqli_fetch_assoc($comment_query)) {
                        $comment_date       = $row['comment_date'];  
                        $comment_content    = $row['comment_content'];             
                        $comment_author     = $row['comment_author'];  
                    ?>

                    <!-- Comment -->
                    <div class="media">           
                        <a class="pull-left" href="#">
                            <img class="media-object" src="http://placehold.it/64x64" alt="">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo $comment_author; ?>
                                <small><?php echo $comment_date; ?></small>
                            </h4>
                            <?php echo $comment_content; ?>
                        </div>
                    </div>   

                    <?php }}} else {
                        redirect("index.php");
                    } ?>              

                </div>

    <!-- Blog Sidebar Widgets Column -->
    <?php include "includes/side_bar.php"; ?>

        </div>

<!-- /.row -->
<?php include "includes/footer.php"; ?>
<script>
	$(document).ready(function() {
		var post_id = <?php echo $the_post_id; ?>;
		var user_id = <?php echo logged_in_user_id(); ?>;
		
		// Like post
		$('.like').click(function() {
			$.ajax({
				url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
				type: 'post',
				data: {
					'liked': 1,
					'post_id': post_id,
					'user_id': user_id
				}
			});
		});
		
		//Unlike post
		$('.unlike').click(function() {
			$.ajax({
				url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
				type: 'post',
				data: {
					'unliked': 1,
					'post_id': post_id,
					'user_id': user_id
				}
			});
		});
	});
</script>