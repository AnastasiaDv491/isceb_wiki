<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/

if (is_user_logged_in()) {

    
    $wiki_file_terms = get_terms( 'wiki_file_category' );

    foreach ($wiki_file_terms as $wiki_file_term) {
       
        $get_wiki_files = get_posts(array(
            'post_type' => 'wiki-file',
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                'taxonomy' => 'wiki_file_category',
                'field'=> 'name',
                'terms' => $wiki_file_term->name,
                'operator' =>'AND'

            )),
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
            <h2> <?php echo($wiki_file_term->name); ?> </h2>
            <ul>
            <?php foreach( $get_wiki_files as $get_wiki_file ): ?>
                <?php 
        
                
                $file_content = get_field('file_attachment', $get_wiki_file->ID);
                
                ?>
                <li>
                    <a href=" <?php echo $file_content['url'] ?>"> <?php echo $file_content['title']; ?></a>
                    <p> <?php echo($file_category); ?> </p>
                    
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif;?>
        <?php
        $get_wiki_files = array();

    }


}
// else { 
//     echo 'Please, log in';

//     //user isn't logged in, create a login template and call from here
//     get_template_part ( 'content', 'login' ); //create your login form at content-login.php file
//     //or you can use the wp built in function to load the default form
//     wp_login_form();
// }
?>