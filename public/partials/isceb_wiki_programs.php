<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/


$get_wiki_programs = get_posts(array(
    'post_type' => 'phase',
    'meta_query' => array(
        array(
            'key' => 'program', // name of custom field
            'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
            'compare' => 'LIKE'
        )
    )
));


if( $get_wiki_programs ): ?>
    <ul>
    <?php foreach( $get_wiki_programs as $get_wiki_program ): ?>
        <?php 

     
        ?>
        <li>
            <!-- <p > The title of the file </p> -->
            <a href=" <?php echo get_permalink($get_wiki_program->ID); ?>"> <?php echo $get_wiki_program->post_title; ?> </a>

            <!-- <a href="<?php echo get_permalink($get_wiki_program->ID ); ?>">
                <img src="<?php echo $file_content['url']; ?>" alt="<?php echo $file_content['alt']; ?>" width="30" />
                <?php echo get_the_title( $get_wiki_program->ID ); ?>
            </a> -->
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; 

?>
