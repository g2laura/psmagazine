<div class="author-recommended-posts<?php echo ( $format_horizontal )? ' horizontal' : ' vertical'; ?>">
    
    <?php if( $html_title && $show_title ) { ?>
        <h3><?php echo $html_title; ?></h3>
    <?php } ?>
    
    <ol>
    <?php foreach( $recommended_ids as $recommended_id ) :
        if( in_array( get_post_type( $recommended_id ), $author_recommended_posts_post_types ) ){
            $recommended_post_thumbnail = false;
            if( $show_featured_image )
                $recommended_post_thumbnail_id = get_post_thumbnail_id( $recommended_id );
                $recommended_post_thumbnail_src = wp_get_attachment_image_src( $recommended_post_thumbnail_id, 'medium', true ); ?>
            
            <li<?php echo($recommended_post_thumbnail_id)? ' class="has-thumbnail"' : '';?>>
                <div>
                    <?php do_action( "{$namespace}_before_related", $recommended_id ); ?>
                    
                    <?php if( $recommended_post_thumbnail_id ) { ?>
                        <a href="<?php echo get_permalink( $recommended_id ); ?>" class="related-thumbnail" style="background-image:url('<?php echo $recommended_post_thumbnail_src[0]; ?>');">&nbsp;</a>
                    <?php } ?>
                    <a href="<?php echo get_permalink( $recommended_id ); ?>" class="related-title"><?php echo get_the_title( $recommended_id ); ?></a>
                    
                    <?php do_action( "{$namespace}_after_related", $recommended_id ); ?>
                </div>
            </li>
            
        <?php }
    endforeach; ?>
    </ol>
</div>