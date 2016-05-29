<script type="text/javascript">var __namespace = '<?php echo $namespace; ?>';</script>
<div class="wrap">

    <h2><?php echo $page_title; ?></h2>
        
    <?php if( isset( $_GET['message'] ) ): ?>
        <div id="message" class="updated below-h2"><p>Options successfully updated!</p></div>
    <?php endif; ?>

    <form action="" method="post" id="<?php echo $namespace; ?>-form">
        <?php wp_nonce_field( $namespace . "-update-options" ); ?>
        
        <table class="form-table">
            
            <tr valign="top">
                <th scope="row">Section title:</th>
                <td><input type="text" name="data[author_recommended_posts_title]" value="<?php echo $this->get_option( 'author_recommended_posts_title' ); ?>" size="50"/></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Show section title?</th>
                <td>
                    <label>Yes <input type="radio" name="data[author_recommended_posts_show_title]" value="1"<?php echo( $this->get_option( 'author_recommended_posts_show_title' ) )? ' checked="checked"' : '';?> /></label>
                    <label>No <input type="radio" name="data[author_recommended_posts_show_title]" value="0"<?php echo( !$this->get_option( 'author_recommended_posts_show_title' ) )? ' checked="checked"' : '';?> /></label>
                </td>
            </tr>
            
            <?php if( current_theme_supports( 'post-thumbnails' ) ) : ?>
            <tr valign="top">
                <th scope="row">Show featured image?</th>
                <td>
                    <label>Yes <input type="radio" name="data[author_recommended_posts_show_featured_image]" value="1"<?php echo( $this->get_option( 'author_recommended_posts_show_featured_image' ) )? ' checked="checked"' : '';?> /></label>
                    <label>No <input type="radio" name="data[author_recommended_posts_show_featured_image]" value="0"<?php echo( !$this->get_option( 'author_recommended_posts_show_featured_image' ) )? ' checked="checked"' : '';?> /></label>
                </td>
            </tr>
            <?php endif; ?>
            
            <tr valign="top">
                <th scope="row">Choose layout:</th>
                <td>
                    <label>Horizontal <input type="radio" name="data[author_recommended_posts_format_is_horizontal]" value="1"<?php echo( $this->get_option( 'author_recommended_posts_format_is_horizontal' ) )? ' checked="checked"' : '';?> /></label>
                    <label>Vertical <input type="radio" name="data[author_recommended_posts_format_is_horizontal]" value="0"<?php echo( !$this->get_option( 'author_recommended_posts_format_is_horizontal' ) )? ' checked="checked"' : '';?> /></label>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Allow these to be chosen as related content:</th>
                <td>
                    <?php foreach( $author_recommended_posts_post_types as $author_recommended_posts_post_type ) : ?>
                        <label><input type="checkbox" name="data[author_recommended_posts_post_types][]" value="<?php echo $author_recommended_posts_post_type['slug']; ?>"<?php echo( in_array( $author_recommended_posts_post_type['slug'], $this->get_option( 'author_recommended_posts_post_types' ) ) )? ' checked="checked"' : '' ?>/> <?php echo $author_recommended_posts_post_type['name']; ?></label></br>
                    <?php endforeach; ?>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Automatically output after the content of these:</th>
                <td>
                    <?php foreach( $author_recommended_posts_post_types as $author_recommended_posts_post_type ) : ?>
                        <label><input type="checkbox" name="data[author_recommended_posts_auto_output][]" value="<?php echo $author_recommended_posts_post_type['slug']; ?>"<?php echo( in_array( $author_recommended_posts_post_type['slug'], $this->get_option( 'author_recommended_posts_auto_output' ) ) )? ' checked="checked"' : '' ?>/> <?php echo $author_recommended_posts_post_type['name']; ?></label></br>
                    <?php endforeach; ?>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">You can also output into your theme template by using the following example:</th>
                <td>
                    <p><strong>Specific post:</strong><br/>
                    <code>&lt;?php echo do_shortcode( '[AuthorRecommendedPosts post_id="XXX"]' ); ?&gt;</code>
                    <br/>Where <strong>XXX</strong> is the ID of the post or page you want to pull in the results from.</p>
                    <p><strong>Use within the loop</strong><br/>
                    <code>&lt;?php echo do_shortcode( '[AuthorRecommendedPosts]' ); ?&gt;</code><br/>
                    Will automatically get the $post_id if it is contained within the loop.
                    </p>
                </td>
            </tr>
            
        </table>  
          
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php _e( "Save Changes", $namespace ) ?>" />
        </p>
    </form>
    
</div>