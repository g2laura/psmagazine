<?php 
/**
 * Plugin Name: ISSUU Magazine Display
 * Description: This plugin will display up to 30 of your magazines that are hosted on ISSUU. 
 * Version: 1.0.5
 * Author: sightFACTORY Ltd.
 * Author URI: http://insights.sightfactory.com/wordpress-plugins/issuu-magazine-display/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
define( 'WP_DEBUG', false );
$options = get_option( 'issuu_option_name' );
$issuu_api_key = $options['issuu_api_key'];
$issuu_api_secret = $options['issuu_api_secret'];	

$issuu_api_limit = isset($options['issuu_api_limit']) ? $options['issuu_api_limit'] : 30;	

$issuu_api_sort_order = isset($options['issuu_api_sort_order']) && strlen($options['issuu_api_sort_order']) > 0 ? $options['issuu_api_sort_order'] : NULL;	
$issuu_api_sort_order_string = isset($issuu_api_sort_order) ? '&documentSortBy='.$issuu_api_sort_order: NULL;

$issuu_api_sort_order_sig = preg_replace("/[^A-Za-z0-9 ]/", '', $issuu_api_sort_order_string);

//define('MAGAZINE_RSS_URL', 'http://search.issuu.com/ttcsi/docs/recent.rss');
//define('MAGAZINE_JSON_URL', 'http://api.issuu.com/1_0?action=issuu.documents.list&apiKey=r1m67jxtitixbi0atchj53kf68havibt&access=public&format=json&pageSize=30&signature=ef9569461e487552fd28958144e57b95');
define('MAGAZINE_JSON_URL', 'http://api.issuu.com/1_0?action=issuu.documents.list&apiKey='.$issuu_api_key.'&access=public'.$issuu_api_sort_order_string.'&format=json&pageSize='.$issuu_api_limit.'&resultOrder=desc&signature='.generate_signature());
//echo MAGAZINE_JSON_URL;
//echo $issuu_api_sort_order_sig;
//exit;
/*Important: Any options changed above, have to be reflected below in the signature generation function. 
Options need to be listed in alphabetical order in both the url  above and in the generate_signature function below */

//This function taps into the rssfeed with all of the recently uploaded magazines
function show_magazines( $atts ) {
    //Array of default values'
    $defaults = array('display' => 'all', 'feed' => 'json');
    $a = shortcode_atts($defaults,$atts);
	
    if($a['feed'] == 'rss'){
        return download_rss($a['display']);
    }
    
    else if($a['feed'] == 'json'){
        return download_json($a['display']);
    }
    
    else {
        return download_rss($a['display']);
    }
    
}
add_shortcode('display_magazines', 'show_magazines');



//This function downloads the JSON magazne data and displays the list of magazines hosted on ISSU
function download_json($display){   
	
    //Downloads JSON data and decodes it
    $docs = $json_data = json_decode(file_get_contents(MAGAZINE_JSON_URL));	
	
    /*$docs = array_filter($json_data->rsp->_content->result->_content, function($var){
                        $include =  (stripos($var->document->issuu_api_secret,"") != false);
                        return $include;
    });*/
	$docs = $json_data->rsp->_content->result->_content;
	
    //Filters out unwanted magazines with odd issuu_api_secrets
    /*$docs = array_filter($docs,function($var){
        $blacklist = array('/ttcsi_quarterly_issue11_tmp_part1bfc66c86','TTCSI QUARTERLY FOR WEBSITE','/ttcsi_quarterly_122795a888','TTCSI Quarterly - Jan-Mar 2014 - No.17');
        
        foreach($blacklist as $unwanted_issuu_api_secret){
            if($var->document->issuu_api_secret == $unwanted_issuu_api_secret){
                return false;
            }
        }
        
        return true; //Returns true if issuu_api_secret isn't in blacklist
    });*/
    
    //Sorts the documents by their ID
    /*usort($docs, function($a, $b){
        
        //Obtaiins the first part of the IDs for both docs and then compares them
        $id1 = substr($a->document->documentId,0,12);
        $id2 = substr($b->document->documentId,0,12);
        
        return strcmp($id1, $id2) == 1 ? -1 : (strcmp($id1, $id2) < 0 ? 1 : 0);
    });*/
    
    if($display == 'all'){
	
	
        return json_display_all_magazines($docs);
    }
 
    else if($display == 'current'){
        return json_display_current_issue($docs[0]);
    }
    
    else {
        return json_display_all_magazines($docs);
    }
}

/**This function just displays the latest released magazine
 */
function json_display_current_issue ($docs){
 
    //String to store layout html
    $html .= '<div align="center">';
    $html .= '<div id="magazine_display">';
    
     $html .= '<div class="magazine_item">'; 
    $html .= '<a href="http://issuu.com/ttcsi/docs/' . $doc->document->name. '" target="_blank">';

    //Obtains the image url from the media namespace
    $html .= '<img src="http://image.issuu.com/' . $doc->document->documentId . '/jpg/page_1_thumb_large.jpg"/><br/>';
    $html .= '<div align="center" class="magazine_info">';

    $truncated_issuu_api_secret = substr($doc->issuu_api_secret,0,50);

    $html .= '<span class="magazine_issuu_api_secret">' . $truncated_issuu_api_secret . (strlen($doc->issuu_api_secret) > 50 ? '...' : '') . '</span>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';
    
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**This function displays all of the magazines in the json data
 */
 
function generate_signature()
{
	/*Fetch options saved in database*/
	$options = get_option( 'issuu_option_name' );
$issuu_api_key = $options['issuu_api_key'];
$issuu_api_secret = $options['issuu_api_secret'];	
$issuu_api_limit = isset($options['issuu_api_limit']) ? $options['issuu_api_limit'] : 30;	
$issuu_api_sort_order = isset($options['issuu_api_sort_order']) && strlen($options['issuu_api_sort_order']) > 0 ? $options['issuu_api_sort_order'] : NULL;	
$issuu_api_sort_order_string = isset($issuu_api_sort_order) ? '&documentSortBy='.$issuu_api_sort_order: NULL;

$issuu_api_sort_order_sig = preg_replace("/[^A-Za-z0-9 ]/", '', $issuu_api_sort_order_string);

	$signature_template = $issuu_api_secret.'accesspublicactionissuu.documents.listapiKey'.$issuu_api_key.$issuu_api_sort_order_sig.'formatjsonpageSize'.$issuu_api_limit.'resultOrderdesc';
	//echo '<br/>'.$signature_template;
	//exit;
	$signature = md5($signature_template);
	return $signature;
	
}
 
function json_display_all_magazines($docs){
    /*  print '<pre>';
	print_r ($docs);
	exit;*/
	$options = get_option( 'issuu_option_name' );
	$thumbnail_size  = $options['issuu_api_thumbnail'];	
	$display_title_on = 	$options[ 'issuu_api_display_title' ];
	$display_publish_date_on = 	$options[ 'issuu_api_display_publish_date' ];
	
    //String to store layout html
    $html .= '<div align="center">';
    $html .= '<div id="magazine_display">';

    foreach( $docs as $doc ){
     
        $html .= '<div class="magazine_item hover" style="width:'.$thumbnail_size.'%">'; 
        
		$html .= '<a href="http://issuu.com/'.$doc->document->username.'/docs/' . $doc->document->name. '" target="_blank">'; //corrected wrong link
		/*Are we displaying titles?*/
		
        //Obtains the image url from the media namespace
        $html .= '<img src="http://image.issuu.com/' . $doc->document->documentId . '/jpg/page_1_thumb_large.jpg"/>';
		if($display_title_on) {
			
			$html .= '<div class="issuu_doc_title">'.$doc->document->title.'</div>';		
			
		}
		if($display_publish_date_on) {
			
			$publishDate = strtotime($doc->document->publishDate);
			$publishDate = date('jS F, Y ',$publishDate);
			$html .= '<div class="issuu_doc_publish_date">'.$publishDate.'</div>';
		}
		
        $html .= '<div align="center" class="magazine_info">';

        $truncated_issuu_api_secret = substr($doc->document->issuu_api_secret,0,50);

        $html .= '<span class="magazine_issuu_api_secret">' . $truncated_issuu_api_secret . (strlen($doc->document->issuu_api_secret) > 50 ? '...' : '') . '</span>';
        $html .= '</div>';
        $html .= '</a>';
        $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**This function displays the latest magazine in the feed
 */
function display_current_issue($rss){
    
    //Obtains the first item (Which would have to latest pub date
    $item = $rss->channel->item;
    
    //String to store layout html
    $html .= '<div align="center">';
    $html .= '<div id="magazine_display">';
    
    $html .= '<div class="magazine_item">'; 
    $html .= '<a href="' . $item->link . '" target="_blank">';

    //Obtains the image url from the media namespace
    $xpath = $item->xpath('media:content');
    $html .= '<img src="' . $xpath[0]->attributes()->url . '"/><br/>';
    $html .= '<div align="center" class="magazine_info">';

    $truncated_issuu_api_secret = substr($item->issuu_api_secret,0,50);

    $html .= '<span class="magazine_issuu_api_secret">' . $truncated_issuu_api_secret . (strlen($item->link) > 50 ? '...' : '') . '</span>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';
    
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/*This function displays all of the magazines in  the feed*/
function display_all_magazines($rss) {
    
    //String to store layout html
    $html .= '<div align="center">';
    $html .= '<div id="magazine_display">';

    //Loop to traverse each item and display their properties
    foreach($rss->channel->item as $item) {

    $html .= '<div class="magazine_item">'; 
    $html .= '<a href="' . $item->link . '" target="_blank" class="imd">';

    //Obtains the image url from the media namespace
    $xpath = $item->xpath('media:content');
    $html .= '<img src="' . $xpath[0]->attributes()->url . '"/><br/>';
    $html .= '<div align="center" class="magazine_info">';

    $truncated_issuu_api_secret = substr($item->issuu_api_secret,0,50);

    $html .= '<span class="magazine_issuu_api_secret">' . $truncated_issuu_api_secret . (strlen($item->link) > 50 ? '...' : '') . '</span>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

//Loads the stylesheet used by this plugin to style the layout of magazines
function load_stylesheet() {
    wp_enqueue_style('magazine_layout_css',
                     plugins_url('css/imd_style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'load_stylesheet');


class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'ISSUU Magazine Display', 
            'manage_options', 
            'issuu-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'issuu_option_name' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>ISSUU Magazine Display</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
			//	print '<pre>';
			//	print_r($this->options);
                submit_button(); 
				settings_fields( 'my_option_group' );   
                do_settings_sections( 'issuu-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'issuu_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'API Information', // issuu_api_secret
            array( $this, 'print_section_info' ), // Callback
            'issuu-setting-admin' // Page
        );  

        add_settings_field(
            'issuu_api_key', // ID
            'ISSUU API Key', // issuu_api_secret 
            array( $this, 'issuu_api_key_callback' ), // Callback
            'issuu-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'issuu_api_secret', 
            'ISSUU API Secret', 
            array( $this, 'issuu_api_secret_callback' ), 
            'issuu-setting-admin', 
            'setting_section_id'
        );      
		
		add_settings_field(
            'issuu_thumbnail', 
            'Cover Size (%)', 
            array( $this, 'issuu_api_thumbnail_callback' ), 
            'issuu-setting-admin', 
            'setting_section_id'
        );  
		
		add_settings_field(
            'issuu_limit', 
            'Number of magazines to return (Maximum 30 results)', 
            array( $this, 'issuu_api_limit_callback' ), 
            'issuu-setting-admin', 
            'setting_section_id'
        );  

		add_settings_field(
			'issuu_sort_order',
			'Sort results by:',
			array( $this, 'issuu_api_sort_order_callback' ), 
	       'issuu-setting-admin', 
		    'setting_section_id'
		);	
		
		add_settings_field(
			'issuu_display_title',
			'Display title:',
			array( $this, 'issuu_api_display_title_callback' ), 
	       'issuu-setting-admin', 
		    'setting_section_id'
		);
		
		add_settings_field(
			'issuu_display_publish_date',
			'Display publish date:',
			array( $this, 'issuu_api_display_publish_date_callback' ), 
	       'issuu-setting-admin', 
		    'setting_section_id'
		);		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['issuu_api_key'] ) )
            $new_input['issuu_api_key'] = sanitize_text_field( $input['issuu_api_key'] );

        if( isset( $input['issuu_api_secret'] ) )
            $new_input['issuu_api_secret'] = sanitize_text_field( $input['issuu_api_secret'] );
			
		if( isset( $input['issuu_api_thumbnail'] ) )
            $new_input['issuu_api_thumbnail'] = intval( $input['issuu_api_thumbnail'] );	
		
		if( isset( $input['issuu_api_limit'] ) )
            $new_input['issuu_api_limit'] = intval( $input['issuu_api_limit'] );	
		
		 if( isset( $input['issuu_api_sort_order'] ) )
            $new_input['issuu_api_sort_order'] = sanitize_text_field( $input['issuu_api_sort_order'] );
		
		 if( isset( $input['issuu_api_display_title'] ) )
            $new_input['issuu_api_display_title'] = sanitize_text_field( $input['issuu_api_display_title'] );
		
		 if( isset( $input['issuu_api_display_publish_date'] ) )
            $new_input['issuu_api_display_publish_date'] = sanitize_text_field( $input['issuu_api_display_publish_date'] );
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print "Enter your settings below then add the following shortcode to a page to display your magazines: <br/> <br/> <strong>[display_magazines]</strong>  <br/> <br/>If you don't have an API key  
		for your account <a target='_blank' href='https://issuu.com/home/settings/apikey'>click here</a> and generate one by clicking on <strong>'CREATE API KEY'</strong>.";
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function issuu_api_key_callback()
    {
        printf(
            '<input type="text" id="issuu_api_key" name="issuu_option_name[issuu_api_key]" value="%s" />',
            isset( $this->options['issuu_api_key'] ) ? esc_attr( $this->options['issuu_api_key']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function issuu_api_secret_callback()
    {
        printf(
            '<input type="text" id="issuu_api_secret" name="issuu_option_name[issuu_api_secret]" value="%s" />',
            isset( $this->options['issuu_api_secret'] ) ? esc_attr( $this->options['issuu_api_secret']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function issuu_api_thumbnail_callback()
    {
        printf(
            '<input type="text" id="issuu_api_thumbnail" name="issuu_option_name[issuu_api_thumbnail]" value="%s" />',
            isset( $this->options['issuu_api_thumbnail'] ) ? esc_attr( $this->options['issuu_api_thumbnail']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function issuu_api_limit_callback()
    {
        printf(
            '<input type="text" id="issuu_api_limit" name="issuu_option_name[issuu_api_limit]" value="%s" />',
            isset( $this->options['issuu_api_limit'] ) ? esc_attr( $this->options['issuu_api_limit']) : ''
        );
    }
	
	public function issuu_api_sort_order_callback()
    {
		$sel_option= strip_tags($this->options['issuu_api_sort_order']);
		
		$html = '<select id="issuu_api_sort_order" name="issuu_option_name[issuu_api_sort_order]">';
        $html .= '<option value="">No preference</option>';
        if($sel_option == 'publishDate') {
			$html .= '<option selected="selected" value="publishDate">Publish date</option>';
		}
		else {
			$html .= '<option value="publishDate">Publish date</option>';

		}
		
		if($sel_option == 'title')  {
			$html .= '<option selected="selected" value="title">Title</option>';
		}
		else {
			$html .= '<option value="title">Title</option>';
		}
		$html .= '</select>';
     	print $html;
		
		
		
    }
	
		public function issuu_api_display_title_callback()
    {
		$sel_option= strip_tags($this->options['issuu_api_display_title']);
		
		$html = '<select id="issuu_api_display_title" name="issuu_option_name[issuu_api_display_title]">';
        $html .= '<option value="">Hide title</option>';
        if($sel_option == 'showTitle') {
			$html .= '<option selected="selected" value="showTitle">Show title</option>';
		}
		else {
			$html .= '<option value="showTitle">Show title</option>';

		}
		
		$html .= '</select>';
     	print $html;		
		
		
    }
	
		public function issuu_api_display_publish_date_callback()
    {
		$sel_option= strip_tags($this->options['issuu_api_display_publish_date']);
		
		$html = '<select id="issuu_api_display_publish_date" name="issuu_option_name[issuu_api_display_publish_date]">';
        $html .= '<option value="">Hide publish date</option>';
        if($sel_option == 'showPublishDate') {
			$html .= '<option selected="selected" value="showPublishDate">Show publish date</option>';
		}
		else {
			$html .= '<option value="showPublishDate">Show publish date</option>';

		}
		
		$html .= '</select>';
     	print $html;
		
		
		
    }
	
}
	
	// Add settings link on plugin page
	function imd_plugin_settings_link($links) { 
		$settings_link = '<a href="options-general.php?page=issuu-setting-admin">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}
 
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'imd_plugin_settings_link' );
	
if( is_admin() )
    $my_settings_page = new MySettingsPage();
