<?php include("delete_modal.php"); ?>
<?php
if(isset($_POST['check_box_array'])) {
    foreach($_POST['check_box_array'] as $check_box_value) {
        
        $bulk_options = $_POST['bulk_options'];
        
        switch($bulk_options) {
            case 'Published':
                $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$check_box_value}";
                $update_to_published_status = mysqli_query($connection, $query);
            break;
            case 'Draft':
                $query = "UPDATE posts SET post_status = '{$bulk_options}' WHERE post_id = {$check_box_value}";
                $update_to_draft_status = mysqli_query($connection, $query);
            break;
            case 'delete':
                $query = "DELETE FROM posts WHERE post_id = {$check_box_value}";
                $delete_post = mysqli_query($connection, $query);
            break;  
            case 'Clone':
                
            $query = "SELECT * FROM posts WHERE post_id = {$check_box_value}";
            $select_posts_query = mysqli_query($connection, $query);
                
            while ($row = mysqli_fetch_array($select_posts_query)) {
                $post_author      = $row['post_author'];
                $post_title       = $row['post_title'];
                $post_category_id = $row['post_category_id'];
                $post_status      = $row['post_status'];
                $post_image       = $row['post_image'];
                $post_tags        = $row['post_tags'];
                $post_content     = $row['post_content'];
                $post_date        = $row['post_date'];
            }
            $query  = "INSERT INTO posts(post_category_id, post_title, post_author, post_status, post_image, post_tags, post_content, post_date) ";
            $query .= "VALUES({$post_category_id}, '{$post_title}', '{$post_author}', '{$post_status}', '{$post_image}', '{$post_tags}', '{$post_content}', now())";
            $copy_query = mysqli_query($connection, $query);
            confirm_query($copy_query);
            break;
        }
    }  
}

?>
<form action="" method="post">
<table class="table table-bordered table-hover">
    <div class="row">
        <div id="bulk_option_container" class="col-xs-4 form-group">
            <select class="form-control" name="bulk_options" id="">
                <option value="">Select Options</option>
                <option value="Published">Publish</option>
                <option value="Draft">Draft</option>
                <option value="delete">Delete</option>
                <option value="Clone">Clone</option>
            </select>   
        </div>
        <div class="col-xs-4 form-group">
            <input type="submit" name="submit" value="Apply" class="btn btn-success">
            <a href="posts.php?source=add_post" class="btn btn-primary">Add New</a>
        </div>
    </div>
  
     <thead>
        <tr>
            <th><input id="select_all_boxes" type="checkbox"></th>
            <th>Id</th>
            <th>Author</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Image</th>
            <th>Tags</th>
            <th>Comments</th>
            <th>Date</th>
            <th>View Count</th>
            <th>View Post</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>                      
                           
<tbody>                                  

<?php                       
    if(isset($_GET['delete'])) {
        $the_post_id = escape($_GET['delete']);
        $query = "DELETE FROM posts WHERE post_id = {$the_post_id} ";
        $delete_query = mysqli_query($connection, $query);
        header("Location: posts.php");
    }
    if(isset($_GET['reset'])) {
        $the_post_id = escape($_GET['reset']);
        $query = "UPDATE posts SET post_view_count = 0 WHERE post_id = {$the_post_id} ";
        $reset_view_query = mysqli_query($connection, $query);
        header("Location: posts.php");
    }
    
	$user = return_current_user();
    $query  = "SELECT posts.post_id, posts.post_author, posts.post_title, posts.post_category_id, posts.post_status, posts.post_image, ";
    $query .= "posts.post_tags, posts.post_comment_count, posts.post_date, posts.post_view_count, categories.cat_id, categories.cat_title ";
    $query .= "FROM posts WHERE post_user = '{$user}' ";
    $query .= "LEFT JOIN categories ON posts.post_category_id = categories.cat_id ORDER BY posts.post_id DESC";
        
    $select_posts = mysqli_query($connection, $query);                                                         
    while($row = mysqli_fetch_assoc($select_posts)) {
        
        $post_id            = $row['post_id'];
        $post_author        = $row['post_author'];
        $post_title         = $row['post_title'];
        $post_category_id   = $row['post_category_id'];
        $post_status        = $row['post_status'];
        $post_image         = $row['post_image'];
        $post_tags          = $row['post_tags'];
        $post_comment_count = $row['post_comment_count'];
        $post_date          = $row['post_date'];
        $post_view_count    = $row['post_view_count'];
        $category_id        = $row['cat_id'];
        $category_title     = $row['cat_title'];
        
        echo "<tr>";
        ?>
        <td><input id='select_all_boxes' type='checkbox' class='check_boxes' name='check_box_array[]' value='<?php echo $post_id; ?>'></td>
        <?php
        echo "<td>{$post_id}</td>";
        echo "<td>{$post_author}</td>";
        echo "<td>{$post_title}</td>";
        echo "<td>{$category_title}</td>";   
        echo "<td>{$post_status}</td>";
        echo "<td><img width='100' height='50' src='../images/$post_image' alt='image'></td>";
        echo "<td>{$post_tags}</td>";
        
        $query = "SELECT * FROM comments WHERE comment_post_id = {$post_id}";
        $send_comment_count_query = mysqli_query($connection, $query);
        
        $row = mysqli_fetch_array($send_comment_count_query);
        $comment_id = $row['comment_id'];
        
        $count_comments = mysqli_num_rows($send_comment_count_query);
        echo "<td><a href='post_comments.php?id={$post_id}'>{$count_comments}</a></td>";
        
        echo "<td>{$post_date}</td>";
        echo "<td><a href='posts.php?reset={$post_id}'>{$post_view_count}</a></td>";
        echo "<td><a class='btn btn-primary' href='../post.php?&p_id={$post_id}'>View Post</a></td>";
        echo "<td><a class='btn btn-info' href='posts.php?source=edit_post&p_id={$post_id}'>Edit</a></td>";
        echo "<td><a rel='{$post_id}' href='javascript:void(0)' class='btn btn-danger delete_link'>Delete</a></td>";
        echo "</tr>";
    } 
?> 
<script>
    $(document).ready(function () {
        $(".delete_link").on('click', function(){
            var id = $(this).attr("rel");
            var delete_url = "posts.php?delete="+ id +" ";
            $(".modal_delete_link").attr("href", delete_url);
            
            $("#myModal").modal('show');
        });
    });
</script>

</tbody>
</table>   
</form>      