<?php
if(isset($_POST['create_post'])) {
    $post_title       = escape($_POST['post_title']);
    $post_author      = escape($_POST['users']);
    $post_category_id = escape($_POST['post_category']);
    $post_status      = escape($_POST['post_status']);
    $post_image       = $_FILES['post_image']['name'];
    $post_image_temp  = $_FILES['post_image']['tmp_name'];
    $post_tags        = escape($_POST['post_tags']);
    $post_content     = escape($_POST['post_content']);
    $post_date        = date('d-m-y');

    move_uploaded_file($post_image_temp, "../images/$post_image");

    $query  = "INSERT INTO posts(post_category_id, post_title, post_author, post_date, post_image, post_content, post_tags, post_status) "; 
    $query .= "VALUES({$post_category_id}, '{$post_title}', '{$post_author}', now(), '{$post_image}', '{$post_content}', '{$post_tags}', '{$post_status}') "; 

    $create_post_query = mysqli_query($connection, $query);
    confirm_query($create_post_query);

    $the_post_id = mysqli_insert_id($connection);

    echo "<p class='bg-success'>Post Created: <a href='../post.php?p_id={$the_post_id}'>View Post</a>";
}
?>
   
<form action="" method="post" enctype="multipart/form-data">
    
    <div class="form-group">
        <label for="post_title">Post Title</label>
        <input type="text" class="form-control" name="post_title">
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
                echo "<option value='$cat_id'>{$cat_title}</option>";
            }
            ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="users">Post Author</label>
        <div>
            <select name="users" id="">
            <?php
            $query = "SELECT * FROM users";
            $select_users = mysqli_query($connection, $query);            
            confirm_query($select_users);    
                
            while($row = mysqli_fetch_assoc($select_users)) {
                $user_id  = $row['user_id'];
                $username = $row['username'];
                echo "<option value='{$username}'>{$username}</option>";
            }
            ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <div>
            <label for="post_status">Post Status</label>
        </div>
        <div>
            <select name="post_status" id="">
                <?php
                echo "<option value='Draft'>Draft</option>";
                echo "<option value='Published'>Published</option>";
                ?>
            </select>
        </div>
    </div>  
    
    <div class="form-group">
        <label for="post_image">Post Image</label>
        <input type="file" name="post_image">
    </div>
    
    <div class="form-group">
        <label for="post_tags">Post Tags</label>
        <input type="text" class="form-control" name="post_tags">
    </div>
    
    <div class="form-group">
        <label for="post_content">Post Content</label>
        <textarea class="form-control" name="post_content" id="" cols="30" rows="10"></textarea>
    </div>
    
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="create_post" value="Publish Post">
    </div>
</form>