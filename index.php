<?php  
/*
Plugin Name: WP H5Plus Update
Description: h5+ App通过Wordpress检查更新
Version: 1.0.0
Author: SanceRain LLC@finderz&@江程训
License: MIT
*/
/* 注册激活插件时要调用的函数 */ 
register_activation_hook( __FILE__, 'updatepage_install');   

/* 注册停用插件时要调用的函数 */ 
register_deactivation_hook( __FILE__, 'updatepage_remove' );  
function updatepage_install() {  
    /* 在数据库的 wp_options 表中添加记录，第二个参数为默认值 */ 
    add_option("updatepage_appid", "default_id", '', 'yes');
	add_option("updatepage_version", "default_ver", '', 'yes');
	add_option("updatepage_status", "default_status", '', 'yes');
	add_option("updatepage_title", "default_title", '', 'yes');
	add_option("updatepage_note", "default_note", '', 'yes');
	add_option("updatepage_appurl", "default_downloadurl", '', 'yes');
	add_option("updatepage_level","default_level",'','yes');
}
function updatepage_remove() {  
    /* 删除 wp_options 表中的对应记录 */ 
    delete_option('updatepage_appid');  
	delete_option('updatepage_version');
	delete_option('updatepage_status');
	delete_option('updatepage_title');
	delete_option('updatepage_note');
	delete_option('updatepage_appurl');
	delete_option('updatepage_level');
}
if( is_admin() ) {
    /*  利用 admin_menu 钩子，添加菜单,仅管理员可以看见的菜单页面 */
    add_action('admin_menu', 'updatepage_menu');
}
function updatepage_menu() {
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
    add_options_page('updatepage', 'APP更新参数修改', 'administrator','update_app', 'updatepage_html_page');
}
function updatepage_html_page() {
wp_nonce_field('update-options');
    $updatepage_appid = get_option('updatepage_appid');
	$updatepage_version = get_option('updatepage_version');
	$updatepage_status = get_option('updatepage_status');
	$updatepage_title = get_option('updatepage_title');
	$updatepage_note = get_option('updatepage_note');
	$updatepage_appurl = get_option('updatepage_appurl');
	$updatepage_level = get_option('updatepage_level');
print <<<EOT
    <div>  
        <h2>edit update page(json)</h2>  
        <form method="post" action="">  
            <p> 
                <form action'' method='post'>
				<h3>应用ID（appid）</h3><br>
                <input name=updatepage_appid id=updatepage_appid value="{$updatepage_appid}"><br>
				<h3>应用版本号（version）</h3><br>
                <input name=updatepage_version id=updatepage_version value="{$updatepage_version}"><br>
				<h3>内部版本号（status）</h3><br>
                <input name=updatepage_status id=updatepage_status value="{$updatepage_status}"><br>
				<h3>更新标题（title）</h3><br>
                <input name=updatepage_title id=updatepage_title value="{$updatepage_title}"><br>
				<h3>更新日志（可使用php换行符换行）（note）</h3><br>
                <input name=updatepage_note id=updatepage_note value="{$updatepage_note}"><br>
				<h3>更新文件下载链接（带完整的http/s）（appurl）</h3><br>
                <input name=updatepage_appurl id=updatepage_appurl value="{$updatepage_appurl}"><br>
				<h3>此版本更新是否重要（重要1/不重要0）（appurl）</h3><br>
				<input name=updatepage_level id=updatepage_level value="{$updatepage_level}"><br>
                <input type="hidden" name="action" value="update" /><br>   
                <input type="submit" value="Save" class="button-primary" /></form>
EOT;
	if(!empty($_POST['updatepage_appid'])) {
		update_option('updatepage_appid',$_POST['updatepage_appid']);
	update_option('updatepage_version',$_POST['updatepage_version']);
	update_option('updatepage_status',$_POST['updatepage_status']);
	update_option('updatepage_title',$_POST['updatepage_title']);
	update_option('updatepage_note',$_POST['updatepage_note']);
	update_option('updatepage_appurl',$_POST['updatepage_appurl']);
	update_option('updatepage_level',$_POST['updatepage_level']);
    echo 对应设置已修改，修改效果请刷新页面查看;
	}  
else{
echo 检测到post值为空，打开此页面未进行修改会出现此提示，可以忽视;
}
    }
class PageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new PageTemplater();
		} 

		return self::$instance;

	} 

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);


		// Add your templates to this array.
		$this->templates = array(
			'pages/update.php' => '安卓更新接口模板',
			
		);
			
	} 

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

} 
add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );
add_action( 'wp_head', 'beijing_ie_hack' );

			


?>