/**
 * Admin Control Panel JavaScripts
 * 
 * @version 1.0.0
 * @since 1.0.0
 */

(function($){
	
	var authorRecommendedPosts = {
	    post_entry_rows: function(){
	        var self = this;
	        if( self.recommended_post_items_container.length ){
    	        self.recommended_post_items_container.sortable({
                    handle: '.ui-handle',
                    axis: 'y'  
                }).on('click', 'a.remove-recommended-post', function(event){
                    event.preventDefault();
                    var row = $(this).closest('div');
                    var id = row.attr('data-post_id');
                    
                    self.recommended_post_results_container.find('li.selected').find('a[data-post_id="'+ id +'"]').closest('li').removeClass('selected');
                    
                    row.remove();
                });
	        }
	    },	    
	    selected_result: function(){
	        var self = this;
	        
	        self.recommended_post_items_container.find('input').each(function(){            
                var id = $(this).val();
                var recommended_post_item = self.recommended_post_results_ul.find('a[data-post_id="' + id + '"]');
                
                if( recommended_post_item.length ) {
                    
                    recommended_post_item.closest('li').addClass('selected');
                }
            });
	    },
	    add_post: function(){
	        var self = this;
	        self.recommended_post_results_ul.on('click', 'a', function(event){
	            event.preventDefault();
	            
	            if( !$(this).closest('li').hasClass('selected') ){
    	            var recommended_post_item_title = $(this).find('.recommended-posts-title').text();
    	            var recommended_post_item_id = $(this).attr('data-post_id');
    	            
    	            var recommended_post_item = '<div class="author-recommended-post-row" data-post_id="'+ recommended_post_item_id +'">';
    	            recommended_post_item += '<span class="ui-handle"></span>';
    	            recommended_post_item += '<span class="recommended-post-title">'+ recommended_post_item_title +'</span>';
    	            recommended_post_item += '<input type="hidden" name="author-recommended-posts[]" value="'+ recommended_post_item_id +'" />';
    	            recommended_post_item += '<a href="#remove" class="button remove-recommended-post">&#215;</a>';
    	            recommended_post_item += '</div>';
    	            
    	            self.recommended_post_items_container.append( recommended_post_item );
    	            self.selected_result();
	            }
	        });
	    },
	    update_results: function( searchVal ){
	        var self = this;
	        
	        // get results
            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'html',
                data: { 
                    'action' : 'author_recommended_posts_search', 
                    's' : searchVal
                },
                success: function( html ){
                    self.recommended_post_results_ul.empty().append( html );
                    self.selected_result();                    
                }
            });
	    },
	    init: function(){
	        var self = this;
	        self.recommended_post_results_container = $('#recommended-posts-results');
	        self.recommended_post_results_ul = self.recommended_post_results_container.find('ul');
	        self.recommended_post_items_container = $('#recommended-posts-items-container');
	        $('#author-recommended-posts-search').on('keyup', function(){
                var val = $(this).val();
                
                // ajax
                clearTimeout( self.author_recommended_posts_timeout );
                self.author_recommended_posts_timeout = setTimeout(function(){
                    self.update_results( val );
                }, 250);
                
                return false;
                
            }).on('focus', function(){
                $(this).siblings('label').hide();
            }).on('blur', function(){
                if( $(this).val() == '' ){
                    $(this).siblings('label').show();
                }
            });
            
            self.add_post();
            self.selected_result();
            self.post_entry_rows();
	    }
	};
	
	$(function(){
	    authorRecommendedPosts.init();
	});
	
})(jQuery);