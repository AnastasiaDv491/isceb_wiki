<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/

$get_wiki_courses = get_posts(array(
    'post_type' => 'course',
    'meta_query' => array(
        array(
            'key' => 'phases', // name of custom field
            'value' => '"' . get_the_ID() . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
            'compare' => 'LIKE'
        )
    )
));

if( $get_wiki_courses ): ?>
    <ul>
    <?php foreach( $get_wiki_courses as $get_wiki_course ): ?>
        <?php 

        var_dump($get_wiki_course);       
        ?>
        <li>
            <!-- <p > The title of the file </p> -->
            <a href=" <?php echo get_permalink($get_wiki_course->ID); ?>"> <?php echo $get_wiki_course->post_title; ?> </a>

            <!-- <a href="<?php echo get_permalink($get_wiki_course->ID ); ?>">
                <img src="<?php echo $file_content['url']; ?>" alt="<?php echo $file_content['alt']; ?>" width="30" />
                <?php echo get_the_title( $get_wiki_course->ID ); ?>
            </a> -->
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; 

?>
