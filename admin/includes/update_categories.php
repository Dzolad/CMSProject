<!--  This page is called from categories.php when a edit button is pressed.  -->

<form action="" method="post">
    <div class="form-group">
        <label for="cat_title">Edit Category</label>        
        
        <?php 
        // Extracts category id in order to use id in the UPDATE QUERY if statement below.
        // Additionally, the category title is extracted for it to be entered as a value in the input box.
        // Deleted $cat_id = $row['cat_id'] because it was unnecessary.
        if(isset($_GET['edit'])){
            $cat_id = escape($_GET['edit']);
            
            $query = "SELECT * FROM categories WHERE cat_id = $cat_id ";
            $select_categories = mysqli_query($connection, $query);                                                             
            while($row = mysqli_fetch_assoc($select_categories)) {
                $cat_title = $row['cat_title']; 
        ?>
        
        <!-- Input box with categoty title if statement -->        
        <input value="<?php if(isset($cat_title)){echo $cat_title;} ?>" type="text" class="form-control" name="cat_title">    
                                   
        <?php }} ?>
                            
        <?php
        /* 
            Update category query.
            Functionality:
            - Escaped post value.
            - Prepared statement implemented.
            Next: Refactor into single function.
        */
        if(isset($_POST['update_category'])) {
            $the_cat_title = escape($_POST['cat_title']);
            $prep_statement = mysqli_prepare($connection, "UPDATE categories SET cat_title = ? WHERE cat_id = ? ");
            mysqli_stmt_bind_param($prep_statement, "si", $the_cat_title, $cat_id);
            mysqli_stmt_execute($prep_statement);
            confirm_query($prep_statement);
            mysqli_stmt_close($prep_statement);
            redirect("categories.php");
        }         
        ?>
                               
    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="update_category" value="Update Category">
    </div>
</form>