<?php

/*
*  Query posts for a relationship value.
*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
*/

$get_wiki_programs = get_posts(array(
    'post_type' => 'program'
));

if ($get_wiki_programs) {
    foreach ($get_wiki_programs as $get_wiki_program) {
        $get_wiki_phases = get_posts(array(
            'post_type' => 'phase',
            'meta_query' => array(
                array(
                    'key' => 'program', // name of custom field
                    'value' => '"' .$get_wiki_program->ID . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                    'compare' => 'LIKE'
                )
            )
        ));

        var_dump($get_wiki_phases
    );

        if( $get_wiki_phases ): ?>
            <p><?php echo($get_wiki_program->post_title); ?></p>
            <ul>
            <?php foreach( $get_wiki_phases as $get_wiki_phase ): ?>
                               
                <li>
                    <!-- <p > The title of the file </p> -->
                    <a href=" <?php echo get_permalink($get_wiki_phase->ID); ?>"> <?php echo $get_wiki_phase->post_title; ?> </a>

                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif;
        
    }
}


