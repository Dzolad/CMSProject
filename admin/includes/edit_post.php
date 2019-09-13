<?php
if(isset($_GET['p_id'])){
    $the_post_id = escape($_GET['p_id']);
}

$query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
$select_posts_by_id = mysqli_query($connection, $query);

while($row = mysqli_fetch_assoc($select_posts_by_id)) {
        
    $post_id            = $row['post_id'];
    $post_author        = $row['post_author'];
    $post_title         = $row['post_title'];
    $post_category_id   = $row['post_category_id'];
    $post_status        = $row['post_status'];
    $post_image         = $row['post_image'];
    $post_tags          = $row['post_tags'];
    $post_comment_count = $row['post_comment_count'];
    $post_date          = $row['post_date'];
    $post_content       = $row['post_content'];
}

if(isset($_POST['update_post'])) {
    
    $post_author      = escape($_POST['users']);
    $post_title       = escape($_POST['post_title']);
    $post_category_id = escape($_POST['post_category']);
    $post_status      = escape($_POST['post_status']);
    $post_image       = $_FILES['post_image']['name'];
    $post_image_temp  = $_FILES['post_image']['tmp_name'];
    $post_content     = escape($_POST['post_content']);
    $post_tags        = escape($_POST['post_tags']);
    
    move_uploaded_file($post_image_temp, '../images/$post_image');
    
    if(empty($post_image)) {
        $query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
        $select_image = mysqli_query($connection, $query);
        
        while($row = mysqli_fetch_array($select_image)) {
            $post_image = $row['post_image'];
        }
    }
    
    $query  = "UPDATE posts SET ";
    $query .= "post_title = '{$post_title}', ";
    $query .= "post_category_id = '{$post_category_id}', ";
    $query .= "post_date = now(), ";
    $query .= "post_author = '{$post_author}', ";
    $query .= "post_status = '{$post_status}', ";
    $query .= "post_tags = '{$post_tags}', ";
    $query .= "post_content = '{$post_content}', ";
    $query .= "post_image = '{$post_image}' ";
    $query .= "WHERE post_id = {$the_post_id} ";
    
    $update_query = mysqli_query($connection, $query);
    confirm_query($update_query);
    
    echo "<p class='bg-success'>Post Updated: <a href='../post.php?p_id={$post_id}'>View Post</a> or <a href='posts.php'>Edit More Posts</a></p>";
}
?>   
   
<form action="" method="post" enctype="multipart/form-data">
    
    <div class="form-group">
        <label  for="post_title">Post Title</label>
        <input value="<?php echo $post_title; ?>" type="text" class="form-control" name="post_title">
    </div>
    
    <div class="form-group">
        <label for="post_category">Post Category</label>
        <div>
            <select name="post_category" id="">
            <?php
            $query = "SELECT * FROM categories";
            $select_categories = mysqli_query($connection, $query);           
                
            confirm_query($select_categories);    
                
            while($row = mysqli_fetch_assoc($select_categories)) {
                $cat_id    = $row['cat_id'];
                $cat_title = $row['cat_title'];
                
                if($post_category_id == $cat_id) {
                    echo "<option selected value='{$cat_id}'>{$cat_title}</option>";
                } else {
                    echo "<option value='{$cat_id}'>{$cat_title}</option>";
                }
            }
            ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="users">Post Author</label>
        <div>
            <select name="users"  id="">
            <?php echo "<option value='{$post_author}'>{$post_author}</option>"; ?>
            <?php
            $query = "SELECT * FROM users";
            $select_users = mysqli_query($connection, $query);           
                
            confirm_query($select_users);    
                
            while($row = mysqli_fetch_assoc($select_users)) {
                $user_id  = $row['user_id'];
                $username = $row['username'];
                if($post_author == $user_id) {
                        echo "<option selected value='{$user_id}'>{$username}</option>";
                    } else {
                        echo "<option value='{$user_id}'>{$username}</option>";
                    }
            }
            ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">  
        <label for="post_status">Post Status</label>
        <div>
            <select name="post_status" id="">
                <option value='<?php echo $post_status ?>'><?php echo $post_status; ?></option>
                <?php
                    if($post_status == 'Published') {
                        echo "<option value='Draft'>Draft</option>";
                    } else {
                        echo "<option value='Published'>Published</option>";
                    }
                ?>
            </select>
        </div>
    </div>
    
    <div class="form_group">
        <img width="100" height="50" src="../images/<?php echo $post_image; ?>" alt="">
        <input type="file" name="post_image">
    </div>
    
    <div class="form-group">
        <label for="post_tags">Post Tags</label>
        <input value="<?php echo $post_tags; ?>" type="text" class="form-control" name="post_tags">
    </div>
    
    <div class="form-group">
        <label for="post_content">Post Content</label>
        <textarea class="form-control" name="post_content" id="" cols="30" rows="10"><?php echo str_replace('\r\n', '</br>', $post_content); ?></textarea>
    </div>
    
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="update_post" value="Update Post">
    </div>
</form>