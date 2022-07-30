<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

if( get_option("GRel_start") == 1 ) {
	
	//cURL Check
	function GRel_is_curl_installed() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else {
			return false;
		}
	}
	
	//Add Metabox
	add_action( 'add_meta_boxes', 'GRel_add_all_meta_box' );
	function GRel_add_all_meta_box()
	{
		add_meta_box( 'GRel_related_keyword', __('Generate Related Keywords', 'GRel-plugin'), 'GRel_addmeta', 'post', 'normal', 'high' );
		if (get_option('GRel_start_woo') == "1") {
			add_meta_box( 'GRel_related_keyword', __('Generate Related Keywords', 'GRel-plugin'), 'GRel_addmeta', 'product', 'normal', 'high' );
		}
		if (get_option('GRel_start_bbp') == "1") {
			add_meta_box( 'GRel_related_keyword', __('Generate Related Keywords', 'GRel-plugin'), 'GRel_addmeta', 'topic', 'normal', 'high' );
		}
	}
	
	//Add Metabox to Product or Post Page
	function GRel_addmeta( $post ) {
	
		$values = get_post_custom ( $post->ID );
		
		//get Limit

		if (!empty($values['GRel_limit'][0])) 
			$getLimit = $values['GRel_limit'][0];
		else if (get_option("GRel_limit") !== "")
			$getLimit = get_option("GRel_limit");
		else
			$getLimit = "10";
		
		//get Locale
		if (!empty($values['GRel_locale'][0])) 
			$getLocale = $values['GRel_locale'][0];
		else if (get_option("GRel_locale") !== "")
			$getLocale = get_option("GRel_locale");
		else
			$getLocale = get_locale();
		

		$keyword = isset( $values['GRel_KEYW'] ) ? $values['GRel_KEYW'][0] : '';
		$limit = $getLimit;
		$locale = $getLocale;
		
		wp_nonce_field( 'GRel_meta_box_nonce', 'meta_box_nonce' );
		?>
		<h4 style="font-size:14px"><?php echo __('Tag Keyword *', 'GRel-plugin'); ?></h4>
		<p>
			<input type="text" autocomplete="off" name="GRel_KEYW" id="GRel_KEYW" value="<?php echo $keyword; ?>">
		</p>		
		
		<h4 style="font-size:14px"><?php echo __('Tag Limit (optional*)', 'GRel-plugin'); ?></h4>
		<p>
			<input type="number" autocomplete="off" name="GRel_limit" id="GRel_limit" value="<?php echo $limit; ?>">
		</p>		
		
		<h4 style="font-size:14px"><?php echo __('Tag Locale Code (optional*)', 'GRel-plugin'); ?></h4>
		<p>
			<input type="text" autocomplete="off" name="GRel_locale" id="GRel_locale" value="<?php echo $locale; ?>">
		</p>
		
		<div onclick="getData(document.getElementById('GRel_KEYW').value, document.getElementById('GRel_limit').value, document.getElementById('GRel_locale').value);" class="button button-primary button-large"><?php echo __('Get the Tags', 'GRel-plugin'); ?></div>
		<div id="setSuccess"></div>
		<div class="clear"></div>

			<?php
		}

		add_action( 'save_post', 'GRel_meta_box_save' );
		function GRel_meta_box_save( $post_id ) {
			// Autosave
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

			// if our nonce isn't there, or we can't verify it, bail
			if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'GRel_meta_box_nonce' ) ) return;

			// User can "edit_post"
			if( !current_user_can( 'edit_post' ) ) return;

			// Update data
			if( isset( $_POST['GRel_limit'] ) || isset( $_POST['GRel_locale'] ) || isset( $_POST['GRel_KEYW'] ) ) {
				update_post_meta( $post_id, 'GRel_limit', $_POST['GRel_limit'] );
				update_post_meta( $post_id, 'GRel_locale', $_POST['GRel_locale'] );
				update_post_meta( $post_id, 'GRel_KEYW', $_POST['GRel_KEYW'] );
			}

		}


	// AJAX

	add_action( 'admin_footer', 'GRel_AJAX_javascript' );

	function GRel_AJAX_javascript() { 
		if (stripos($_SERVER['REQUEST_URI'], "?post_type=product") !== false ) {
			$echoType = "new-tag-product_tag";
		} else if (stripos($_SERVER['REQUEST_URI'], "?post_type=topic") !== false ) {
			$echoType = "new-tag-topic-tag";
		} else {
			$echoType = "new-tag-post_tag";
		}
		
	?><script type="text/javascript">
		function getData(myTerm, myLimit, myLocale){

			var data = {
				'action': 'GRel_ajax',
				'getAjaxWPTerm': myTerm,
				'getAjaxWPMax': myLimit,
				'getAjaxWPLocale': myLocale
			};

			jQuery.post(ajaxurl, data, function(response) { <?php 
			if(use_block_editor_for_post($post)) {?>
			document.getElementById("setSuccess").innerHTML = "<br><div style='margin-top:1rem'><textarea class='components-textarea-control__input' id='inspector-textarea-control-0' rows='4'>"+response+"</textarea></div><br><p class='showSuccess alert alert-success'>Success! Copy and paste into Tags.</p>";
			<?php } else { ?>
			document.getElementById("<?php echo $echoType; ?>").value = response;
			document.getElementById("setSuccess").innerHTML = "<p class='showSuccess alert alert-success'>Success! Please check the Tags Widget.</p>";
			<?php } ?>
				
				$(".showSuccess").delay(3600).fadeOut(600);
			});
		}
		</script><?php
	}


	add_action( 'wp_ajax_GRel_ajax', 'GRel_ajax' );

	function GRel_ajax() {
		global $wpdb; // this is how you get access to the database

		if(isset($_POST['getAjaxWPTerm'])) {
			$term = urlencode($_POST['getAjaxWPTerm']);
			$getLimit = $_POST['getAjaxWPMax'];
			$getLocale = $_POST['getAjaxWPLocale'];
			
			$getLocale = str_replace("_", "-", $getLocale);

			$get_useragent = $_SERVER['HTTP_USER_AGENT'];

			$url = 'https://api.bing.com/osjson.aspx?query='.$term.'&setmkt='.$getLocale;
			$ch  = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
			curl_setopt($ch, CURLOPT_USERAGENT, $get_useragent);
			curl_setopt($ch, CURLOPT_REFERER, "https://google.com");
			$result = curl_exec($ch);
			curl_close($ch);

			$resultInArray = json_decode($result, true);

			//
			$resultCount = count($resultInArray[1]);

			for ( $i = 1; $i < $resultCount; $i++ ) {
				echo $resultInArray[1][$i].', ';
				
				if ( $i == $getLimit) 
					break;
			}
		}
		
		wp_die();

	}
}