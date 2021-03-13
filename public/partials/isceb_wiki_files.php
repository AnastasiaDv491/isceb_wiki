<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/

if (is_user_logged_in()) {
    $get_wiki_files = get_posts(array(
        'post_type' => 'wiki-file',
        'post-status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array (
                'key' => 'approved',
                'value' => 'Yes',
                'compare' => '=',
            ),
            array(
                'key' => 'course', // name of custom field
                'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                'compare' => 'LIKE'
            )
        )
    ));
    
    
    if( $get_wiki_files ): ?>
        <ul>
        <?php foreach( $get_wiki_files as $get_wiki_file ): ?>
            <?php 
    
            $file_category = get_field('wiki_file_category', $get_wiki_file->ID);
            $file_content = get_field('file_attachment', $get_wiki_file->ID);
            
            ?>
            <li>
                <!-- <p > The title of the file </p> -->
                <a href=" <?php echo $file_content['url'] ?>"> <?php echo $file_content['title']; ?></a>
                <p> <?php echo($file_category); ?> </p>
                <!-- <a href="<?php echo get_permalink($get_wiki_file->ID); ?>">
                    <img src="<?php echo $file_content['url']; ?>" alt="<?php echo $file_content['alt']; ?>" width="30" />
                    <?php echo get_the_title( $get_wiki_file->ID ); ?>
                </a> -->
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif;?>
<?php
}
else { 
    echo 'Please, log in';

    //user isn't logged in, create a login template and call from here
    get_template_part ( 'content', 'login' ); //create your login form at content-login.php file
    //or you can use the wp built in function to load the default form
    wp_login_form();
}
?>
