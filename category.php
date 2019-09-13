<?php include "includes/db.php" ?>
<?php include "includes/header.php"; ?>    
    
    <!-- Navigation -->
<?php include "includes/navigation.php"; ?>
    
    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">
                <?php
                if(isset($_GET['category'])) {
                    $post_category_id = $_GET['category'];
                         
                    if(isset($_SESSION['username']) && is_admin($_SESSION['username'])){
                        
                        $prep_statement1 = mysqli_prepare($connection, "SELECT post_id, post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_category_id = ? ");
                        
                    } else {
                        $prep_statement2 = mysqli_prepare($connection, "SELECT post_id, post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_category_id = ? AND post_status = ? ");
                        $published = 'Published';
                    }
                    
                    if(isset($prep_statement1)) {
                        
                        mysqli_stmt_bind_param($prep_statement1, "i", $post_category_id);
                        mysqli_stmt_execute($prep_statement1);
                        mysqli_stmt_bind_result($prep_statement1, $post_id, $post_title, $post_author, $post_date, $post_image, $post_content);
                        mysqli_stmt_store_result($prep_statement1);
                        
                        $statement = $prep_statement1;
                    } else {
                        
                        mysqli_stmt_bind_param($prep_statement2, "is", $post_category_id, $published);
                        mysqli_stmt_execute($prep_statement2);
                        mysqli_stmt_bind_result($prep_statement2, $post_id, $post_title, $post_author, $post_date, $post_image, $post_content);
                        mysqli_stmt_store_result($prep_statement2);
                        
                        $statement = $prep_statement2;
                    }
                     
                    if(mysqli_stmt_num_rows($statement) === 0) {
                        echo "<h1 class='text-center'>No Posts Available</h1>";
                    }
                        
                    while(mysqli_stmt_fetch($statement)):
                        
                ?>
    
                <!-- First Blog Post -->
                <h1>
                    <a href="post.php?p_id=<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
                </h1>
                <p class="lead">
                    by <a href="/cms/author_post.php?author=<?php echo $post_author; ?>"><?php echo $post_author ?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span><?php echo " " . $post_date ?></p>
                <hr>
                <a href="post.php?p_id=<?php echo $post_id; ?>">
                <img class="img-responsive" src="/cms/images/<?php echo image_placeholder($post_image); ?>" alt="" title="Click image to go to this post">
                </a>
                <hr>
                <p><?php echo substr($post_content, 113, 150) ?></p>
				<hr>
                <a class="btn btn-primary" href="/cms/post.php?p_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                <hr>
    
<?php endwhile; mysqli_stmt_close($statement); } else {
                header("Location: ../index.php");     
            } 
    ?>
        
            </div>

    <!-- Blog Sidebar Widgets Column -->
<?php include "includes/side_bar.php"; ?>

        </div>

    <!-- /.row -->
<?php include "includes/footer.php"; ?>