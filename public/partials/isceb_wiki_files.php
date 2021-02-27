<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/

$get_wiki_files = get_posts(array(
    'post_type' => 'wiki-file',
    'meta_query' => array(
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
<?php endif; 

?>