<div style="display:none;">
    <?php wp_nonce_field( "{$this->namespace}_post_ids_nonce", '_post_ids_nonce' ); ?>
</div>
<div id="recommended-posts-items">
    <div id="recommended-posts-items-container">
        <?php 
        if( $author_recommended_posts ) {
            foreach( $author_recommended_posts as $author_recommended_post ) : ?>
                <?php if( in_array( get_post_type( $author_recommended_post ), $author_recommended_posts_post_types ) ) { ?>
                    <div class="author-recommended-post-row" data-post_id="<?php echo $author_recommended_post; ?>">
                        <span class="ui-handle"></span>
                        <span class="recommended-post-title"><?php echo get_the_title( $author_recommended_post ); ?></span>
                        <input type="hidden" name="author-recommended-posts[]" value="<?php echo $author_recommended_post; ?>" />
                        <a href="#remove" class="button remove-recommended-post">&#215;</a>
                    </div>
                <?php } ?>
            <?php endforeach;
        } ?>
    </div>
    <div id="recommended-posts-search">
        <label for="author-recommended-posts-search">Search...</label>
        <input class="widefat" type="text" name="author-recommended-posts-search" id="author-recommended-posts-search" />
    </div>
    <div id="recommended-posts-results">
        <ul>
        <?php echo $author_recommended_posts_search_results; ?>
        </ul>
    </div>
    <div id="recommended-posts-settings">
        <p>Post type not showing up? <a href="<?php echo $author_recommended_posts_options_url; ?>">Change Settings</a></p>
    </div>
</div>
