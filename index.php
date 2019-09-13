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
                $posts_per_page = 2;         
                $page = get_page();
                $number_of_shown_posts = number_of_posts_first_page($page, $posts_per_page);
                $post_count = find_post_count_for_user_role();

                if($post_count < 1) {
                    echo "<h1 class=text-center>No Posts Available</h1>";
                } else {

                    $count = ceil ($post_count / $posts_per_page);
                    $select_posts_to_show = select_posts_for_user_role_limited($number_of_shown_posts, $posts_per_page);
                    while($row = mysqli_fetch_assoc($select_posts_to_show)) {
                        $post_id      = $row['post_id'];
                        $post_title   = $row['post_title'];
                        $post_author  = $row['post_author'];
                        $post_date    = $row['post_date'];
                        $post_image   = $row['post_image'];
                        $post_content = substr($row['post_content'], 113, 150);
                        $post_status  = $row['post_status'];
                        ?>

				<!-- First Blog Post -->
				<h1>
					<a href="post/<?php echo $post_id; ?>"><?php echo $post_title; ?></a>
				</h1>
				<p class="lead">
					by <a href="/cms/author_post.php?author=<?php echo $post_author; ?>"><?php echo $post_author; ?></a>
				</p>
				<p><span class="glyphicon glyphicon-time"></span><?php echo " " . $post_date ?></p>
				<hr>
				<a href="post.php?p_id=<?php echo $post_id; ?>">
				<img class="img-responsive" src="images/<?php echo image_placeholder($post_image); ?>" alt="" title="Click image to go to this post">
				</a>
				<hr>
				<p><?php echo $post_content; ?></p>
				<hr>
				<a class="btn btn-primary" href="post.php?p_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
				<hr>

<?php 				}	 
				} ?>
            </div>

            <!-- Blog Sidebar Widgets Column -->
<?php include "includes/side_bar.php"; ?>

        </div>

    <!-- /.row -->
    <ul class="pager">
        <?php
        pager($count, $page);
        ?>
    </ul>
    
<?php include "includes/footer.php"; ?>