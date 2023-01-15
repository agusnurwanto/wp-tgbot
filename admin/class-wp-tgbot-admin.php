<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Wp_Tgbot
 * @subpackage Wp_Tgbot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Tgbot
 * @subpackage Wp_Tgbot/admin
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Wp_Tgbot_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $functions ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = $functions;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Tgbot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Tgbot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-tgbot-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Tgbot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Tgbot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-tgbot-admin.js', array( 'jquery' ), $this->version, false );

	}

	function crb_attach_tgbot_options(){
		$basic_options_container = Container::make( 'theme_options', __( 'TGBOT Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( array(
				Field::make( 'html', 'crb_tgbot_halaman_terkait' )
		        	->set_html( '
					<h5>Catatan</h5>
	            	<ol>
	            		<li>Video tutorial bisa dicek di ...</li>
	            		<li>Buat Bot dengan mengirimkan teks <b>/newbot</b> ke akun <b>@BotFather</b>.</li>
						<li>Setelah selesai, akun <b>@BotFather</b> akan memberikan anda Token Bot.</li>
						<li>Copy token dan paste pada kolom Bot Token.</li>
						<li>Endpoint API untuk GITHUB: <b>'.site_url().'/wp-admin/admin-ajax.php?action=send_tgbot&api_key='.get_option('_crb_apikey_tgbot').'&id_koneksi=[ID nama koneksi]</b></li>
	            	</ol>
		        	' ),
	            Field::make( 'text', 'crb_apikey_tgbot', 'API KEY' )
	            	->set_default_value($this->functions->generateRandomString())
	            	->set_help_text('Wajib diisi. API KEY digunakan untuk integrasi data.')
            		->set_required( true ),
	            Field::make( 'text', 'crb_tgbot_token', 'Token Bot' )
	            	->set_help_text('Wajib diisi. Token Bot Telegram yang sudah dibuat.')
            		->set_required( true ),
	            Field::make( 'complex', 'crb_tgbot_api', __( 'Telegram API' ) )
				    ->add_fields( array(
				        Field::make( 'text', 'tgbot_id', __( 'ID nama koneksi' ) )
            				->set_default_value('koneksi1')
            				->set_help_text('ID koneksi ini dipakai untuk keperluan API'),
				        Field::make( 'radio', 'tg_mode', __( 'Parse mode' ) )
			            	->add_options( array(
						        'HTML' => __( 'HTML' ),
						        'Markdown' => __( 'Markdown' )
						    ) )
            				->set_default_value('1'),
				        Field::make( 'text', 'tg_id', __( 'Chanel ID / Akun ID' ) ),
				        Field::make( 'textarea', 'tg_message', __( 'Format Pesan' ) )
	            			->set_default_value("[link_github]\n[commit]\n[username]\n[time]\n[link_commit]\n[modified]")
				        	->set_help_text('
				        		<ul>
				        			<li>[username] untuk menampilkan username github.</li>
				        			<li>[commit] pesan commit git.</li>
				        			<li>[link_commit] url commit repository.</li>
				        			<li>[link_github] url repository github.</li>
				        			<li>[time] waktu commit.</li>
				        			<li>[modified] file yang diubah.</li>
				        			<li>Telegram Bot API currently supports only &lt;b&gt;, &lt;i&gt;, &lt;a&gt;,&lt;code&gt; and &lt;pre&gt; tags, for HTML parse mode</li>
				        		</ul>')
				    ) )
	        ) );
	}
}
