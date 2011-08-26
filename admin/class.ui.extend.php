<?php
/**
 * 
 *
 *  Extend Control Interface
 *
 *
 *  @package PageLines Admin
 *  @subpackage OptionsUI
 *  @since 2.0.b9
 *
 */


class PageLinesExtendUI {
	

	/**
	 * Construct
	 */
	function __construct() {
		
		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		
		$this->defaultpane = array(
				'name' 		=> 'Unnamed', 
				'version'	=> 'No version', 
				'desc'		=> 'No description.', 
				'auth_url'	=> 'http://www.pagelines.com',
				'auth'		=> '',
				'image'		=> '',
				'buttons'	=> '',
				'importance'=> '',
				'key'		=> '',
				'type'		=> '',
				'count'		=> '',
				'status'	=> '',
				'actions'	=> array() 
		);
		
		/**
		 * Hooked Actions
		 */
		add_action('admin_head', array(&$this, 'extension_js'));
		
	}

	/**
	 * Draw a list of extended items
	 */
	function extension_list( $list = array(), $mode = 'list'){
		
		$ext = '';
		
		
		
		if($mode == 'graphic'){
			
			foreach( $list as $eid => $e )
				$ext .= $this->graphic_pane( $e );

			$output = sprintf('<ul class="graphic_panes fix">%s</ul>', $ext);
			
		} else {
			
			foreach( $list as $eid => $e )
				$ext .= $this->pane_template( $e );
			
			$output = sprintf('<ul class="the_sections plpanes">%s</ul>', $ext);
			
		}
		
			return $output;
		
		
	}
	
	function graphic_pane( $e ){
	
		$e = wp_parse_args( $e, $this->defaultpane);
		
		$image = ( $e['actions']['install']['condition'] || $e['actions']['purchase']['condition']) ? sprintf( 'http://api.pagelines.com/themes/img/%s.png', $e['key'] ) : get_theme_root_uri() .'/'. $e['key'] . '/screenshot.png';
		
		$image = sprintf( '<img class="" src="%s" alt="Screenshot" />', $image );
		
		$title = sprintf('<h2>%s</h2>', $e['name']);
		
		$text = sprintf('<p>%s</p>', $e['desc']);
		
		$link =  $this->get_extend_buttons( $e );
		
		$out = sprintf('<div class="graphic_pane media fix"><div class="theme-screen img">%s</div><div class="theme-desc bd">%s%s%s</div></div>', $image, $title, $text, $link);
	
		return $out;
		
	}
	
	function pane_template( $e ){

			$s = wp_parse_args( $e, $this->defaultpane);

			// Left for reference
			//$screenshot = ( $s['image'] ) ? sprintf('<div class="extend-screenshot"><a class="screenshot-%s" href="http://api.pagelines.com/%s/img/%s.png" rel="http://api.pagelines.com/%s/img/%s.png"><img src="http://api.pagelines.com/%s/img/thumb-%s.png"></a></div>' , str_replace( '.', '-', $s['key']), $s['type'], $s['key'], $s['type'], $s['key'], $s['type'], $s['key']) : '';

			$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $s['name'], $this->get_extend_buttons( $e ));

			$auth = sprintf('<div class="pane-dets"><strong>%s</strong> | by <a href="%s">%s</a></div>', 'v' . $s['version'], $s['auth_url'], $s['auth']);

			$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);

			$response = sprintf('<li id="response%s" class="install_response"><div class="rp"></div></li>', $s['key']);

			return sprintf('<li class="plpane pane-plugin"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s </div></div></li>%s', $title, $body, $response);
		
	}
	
	function get_extend_buttons( $e ){
		
		/* 
			'Mode' 	= the extension handling mode
			'Key' 	= the key of the element in the array, for the response
			'Type' 	= what is being extended
			'File' 	= the url for the extension/install/update
			'duringText' = the text while the extension is happening
		*/
		
		$buttons = '';
		foreach( $e['actions'] as $type => $a ){
			
			if($a['condition'])
				$buttons .= $this->extend_button( $e['key'], $a );
			
		}
		
		return $buttons;
		
	}
	
	function extend_button( $key, $a ){
		
		$d = array(
			'mode'	=> '',
			'case'	=> '', 
			'file'	=> '', 
			'text'	=> 'Extend',
			'dtext'	=> '',
			'key'	=> $key, 
			'type'	=> '',
			'path'	=> ''
		);
		
		$a = wp_parse_args($a, $d);
		
		$js_call = sprintf( $this->exprint, $a['case'], $a['key'], $a['type'], $a['file'], $a['path'], $a['dtext']);
		
		$button = sprintf('<span class="extend_button %s" %s>%s</span>', $a['mode'], $js_call, $a['text']);
		
		return $button;
	}
	
	
	function install_button( $e ){
		
		
		$install_js_call = sprintf( $this->exprint, 'section_install', $key, 'sections', $key, 'Installing');

		$button = OptEngine::superlink('Install Section', 'black', '', '', $install_js_call);
		
	}
	
	/**
	 * Draw a list of extended items
	 */
	function get_extend_plugin( $status = '', $tab = '' ){
		
		$key = 'ext'.$tab;
		
		$name = 'pagelines-sections';
		
		if($status == 'notactive'){
			$file = '/' . trailingslashit( $name ) . $name . '.php'; 
			$btext = 'Activate Sections';
			$text = sprintf('Sections plugin installed, now activate it!');
			$install_js_call = sprintf( $this->exprint, 'plugin_activate', $key, 'plugins', $file, '', 'Activating');
			
		} elseif($status == 'notinstalled'){
			$btext = 'Install It Now!';
			$text = sprintf('You need to install and activate PageLines Sections Plugin');
			$install_js_call = sprintf( $this->exprint, 'plugin_install', $key, 'plugins', 'pagelines-sections', '/pagelines-sections/pagelines-sections.php', 'Installing');
		}
			
		$eresponse = 'response'.$key;
		
		// The button
		$install_button = OptEngine::superlink($btext, 'blue', 'install_now iblock', '', $install_js_call);
		
		// The banner
		return sprintf('<div class="install-control fix"><span id="%s" class="banner-text">%s</span><br/><br/>%s</div>', $eresponse, $text, $install_button);
	}
	
	/**
	 * Draw a list of extended items
	 */
	function extension_banner( $text ){
		
		// The banner
		return sprintf('<div class="install-control fix"><span class="banner-text">%s</span></div>', $text);
	}
	
	function upload_form( $type ){
		
			$file = $type;
			
		ob_start();
		 ?>
		<div class="pagelines_upload_form">
			<h4><?php _e('Install a section in .zip format') ?></h4>
			<p class="install-help"><?php _e('If you have a section in a .zip format, you may install it by uploading it here.') ?></p>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'pagelines_extend_upload', 'upload_check' ) ?>
				<label class="screen-reader-text" for="<?php echo $file;?>"><?php _e('Section zip file'); ?></label>
				<input type="file" id="<?php echo $file;?>" name="<?php echo $file;?>" />
				<input type="hidden" name="type" value="<?php echo $file;?>" />
				<input type="submit" class="button" value="<?php esc_attr_e('Install Now') ?>" />
			</form>
		</div>
	<?php 
	
		return ob_get_clean();
	}
	
	function search_extend( $type ){
		
		return $this->extension_banner( 'Search functionality is currently disabled. Check back soon!' );
	}
	
	/**
	 * 
	 * Add Javascript to header (hook in contructor)
	 * 
	 */
	function extension_js(){ ?>

<script type="text/javascript">/*<![CDATA[*/

		function extendIt( mode, key, type, file, path, duringText ){

				/* 
					'Mode' 	= the type of extension
					'Key' 	= the key of the element in the array, for the response
					'Type' 	= ?
					'File' 	= the url for the extension/install/update
					'duringText' = the text while the extension is happening
				*/

				var data = {
					action: 'pagelines_ajax_extend_it_callback',
					extend_mode: mode,
					extend_type: type,
					extend_file: file,
					extend_path: path
				};

				var responseElement = jQuery('#dialog');
				var duringTextLength = duringText.length + 3;
				var dotInterval = 400;
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						responseElement.html( duringText ).dialog({ 
							minWidth: 500, 
							minHeight: 100,
							modal: true, 
							dialogClass: 'ajax_dialog', 
							open: function(event, ui) { 
								jQuery(".ui-dialog-titlebar-close").hide(); 
							} 
						});
						
						//responseElement.html( duringText ).slideDown();

						// add some dots while saving.
						interval = window.setInterval(function(){

							var text = responseElement.text();

							if ( text.length < duringTextLength ){	
								responseElement.text( text + '.' ); 
							} else { 
								responseElement.text( duringText ); 
							} 

						}, dotInterval);

					},
				  	success: function( response ){
						window.clearInterval( interval ); // clear dots...
						responseElement.dialog().html(response);
							//responseElement.effect("highlight", {color: "#CCCCCC"}, 2000).html(response).delay(6500).slideUp();
					}
				});

		}
		/*]]>*/</script>

<?php }
	
	
}

/**
 *
 *  Returns Extension Array Config
 *
 */
function extension_array(  ){

	global $extension_control;

	$d = array(
		'Sections' => array(
			'icon'		=> PL_ADMIN_ICONS.'/dragdrop.png',
			'htabs' 	=> array(
				'all_sections'	=> array(
					'title'		=> 'Installed PageLines Sections',
					'callback'	=> $extension_control->extension_sections()
					),
				'added'	=> array(
					'title'		=> 'Sections Added',
					'callback'	=> $extension_control->extension_sections( 'user' )
					),
				'core'	=> array(
					'title'		=> 'Sections From PageLines Core',
					'callback'	=> $extension_control->extension_sections( 'internal' )
					),
				'add_sections'	=> array(
					'type'		=> 'subtabs',
					'title'		=> 'Extend Sections',
					'class'		=> 'left ht-special',
					'featured'	=> array(
						'title'		=> 'Featured on PageLines.com',
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'featured' )
						),
					'top_premium'	=> array(
						'title'		=> 'Premium PageLines Sections',
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'premium' )
						),
					'top_free'	=> array(
						'title'		=> 'Free PageLines Sections',
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'free' )
						),
					'search'		=> array(
						'title'		=> 'Search Sections',
						'callback'	=> $extension_control->ui->search_extend( 'section' )
					),
					'upload'		=> array(
						'title'		=> 'Upload Sections',
						'callback'	=> $extension_control->ui->upload_form( 'section' )
					),
					
				)
			)

		),
		'Themes' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-themes.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> 'Installed PageLines Themes',
					'callback'	=> $extension_control->extension_themes( 'installed' )
					),
				'add_themes'	=> array(
					'type'		=> 'subtabs',
					'title'		=> 'Extend Themes',
					'class'		=> 'left ht-special',
					'featured'	=> array(
						'title'		=> 'Featured PageLines Themes',
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_themes( 'premium' )
						),
					'popular'	=> array(
						'title'		=> 'Premium PageLines Themes',
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_themes( 'premium' )
						),
					'upload'		=> array(
						'title'		=> 'Upload A PageLines Theme',
						'callback'	=> $extension_control->ui->upload_form( 'theme' )
						),
					)
				)
		),
		'Plugins' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-plugins.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> 'Installed PageLines Plugins',
					'callback'	=> $extension_control->extension_plugins( 'installed' )
					),
				'add_plugins'	=> array(
					'type'		=> 'subtabs',
					'title'		=> 'Add PageLines Plugins',
					'class'		=> 'left ht-special',
					'top_premium'		=> array(
						'title'		=> 'Premium Plugins',
						'callback'	=> $extension_control->extension_plugins( 'premium' )
					),
					'top_free'		=> array(
						'title'		=> 'Free Plugins',
						'callback'	=> $extension_control->extension_plugins( 'free' )
					),
					'search'		=> array(
						'title'		=> 'Search Plugins',
						'callback'	=> $extension_control->ui->search_extend( 'plugin' )
					),
					'upload'		=> array(
						'title'		=> 'Upload Plugin',
						'callback'	=> $extension_control->ui->upload_form( 'plugin' )
					),
				)
			)

		),
		'Import-Export' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-inout.png',
			'import_set'	=> array(
				'default'	=> '',
				'type'		=> 'import_export',
				'layout'	=> 'full',
				'title'		=> 'Import/Export PageLines Settings',						
				'shortexp'	=> 'Use this form to upload PageLines settings from another install.',
			),
		),
		'Your_Account'	=> array(
			'icon'		=> PL_ADMIN_ICONS.'/rocket-fly.png',
			'credentials' => array(
				'version'	=> 'pro',
				'type'		=> 'updates_setup',
				'title'		=> 'Configure PageLines Account &amp; Auto Updates',
				'shortexp'	=> 'Get your latest updates automatically, direct from PageLines.',
				'layout'	=> 'full',
			),
		)

	);

	return apply_filters('extension_array', $d); 
}


