<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Wp_Tgbot
 * @subpackage Wp_Tgbot/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Tgbot
 * @subpackage Wp_Tgbot/public
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */
class Wp_Tgbot_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $functions ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = $functions;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-tgbot-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-tgbot-public.js', array( 'jquery' ), $this->version, false );

	}

	function send_tgbot(){
		global $wpdb;
		$return = array(
			'status' => 'success',
			'data'	=> array()
		);

		if(!empty($_GET)){
			if (!empty($_GET['api_key']) && $_GET['api_key'] == get_option( TGBOT_APIKEY )) {
				if(!empty($_GET['id_koneksi'])){
					$request_body = file_get_contents('php://input');
					$pesan = json_decode($request_body);
					if(!empty($pesan)){
						$data = $this->functions->get_option_complex('_crb_tgbot_api');
						$options = array();
						foreach($data as $v){
							if($v['tgbot_id'] == $_GET['id_koneksi']){
								$message = $v['tg_message'];
								// gitlab
								if(empty($pesan->repository->html_url)){
									$message = str_replace('[username]', '<b>User:</b> '.$pesan->commits[0]->author->name, $message);
									$message = str_replace('[commit]', '<b>Pesan commit:</b> '.$pesan->commits[0]->message, $message);
									$message = str_replace('[link_commit]', '<b>Link commit:</b> '.$pesan->commits[0]->url, $message);
									$message = str_replace('[link_github]', '<b>Repository:</b> '.$pesan->repository->homepage, $message);
									$message = str_replace('[time]', '<b>Waktu commit:</b> '.$pesan->commits[0]->timestamp, $message);
									$message = str_replace('[modified]', "<b>Modified:</b> \n".implode("\n", $pesan->commits[0]->modified), $message);
								// github
								}else{
									$message = str_replace('[username]', '<b>User:</b> '.$pesan->head_commit->committer->name, $message);
									$message = str_replace('[commit]', '<b>Pesan commit:</b> '.$pesan->head_commit->message, $message);
									$message = str_replace('[link_commit]', '<b>Link commit:</b> '.$pesan->head_commit->url, $message);
									$message = str_replace('[link_github]', '<b>Repository:</b> '.$pesan->repository->html_url, $message);
									$message = str_replace('[time]', '<b>Waktu commit:</b> '.$pesan->head_commit->timestamp, $message);
									$message = str_replace('[modified]', "<b>Modified:</b> \n".implode("\n", $pesan->head_commit->modified), $message);
								}
								$message = $this->convert_links_for_parsing($message, $v['tg_mode']);
								$options = array(
									'token' => get_option('_crb_tgbot_token'),
									'tg_id' => $v['tg_id'],
									'topic_id' => $v['tg_chat_id'],
									'parse_mode' => $v['tg_mode'],
									'message' => $message
								);
							}
						}
						if(!empty($options)){
							// print_r($options); die();
							if(empty($options['tg_id'])){
								$return = array(
									'status' => 'error',
									'message'	=> 'Chanel ID / akun ID tidak boleh kosong!'
								);
							}else if(empty($options['token'])){
								$return = array(
									'status' => 'error',
									'message'	=> 'Bot token tidak boleh kosong!'
								);
							}else{
								$return['res'] = $this->functions->send_tg($options);
							}
						}else{
							$return = array(
								'status' => 'error',
								'message'	=> 'ID koneksi tidak ditemukan!'
							);
						}
					}else{
						$return = array(
							'status' => 'error',
							'message'	=> 'Pesan tidak boleh kosong!'
						);
					}
				}else{
					$return = array(
						'status' => 'error',
						'message'	=> 'ID koneksi tidak boleh kosong!'
					);
				}
			}else{
				$return = array(
					'status' => 'error',
					'message'	=> 'Api Key tidak sesuai!'
				);
			}
		}else{
			$return = array(
				'status' => 'error',
				'message'	=> 'Format tidak sesuai!'
			);
		}
		die(json_encode($return));
	}

	function convert_links_for_parsing( $text, $parse_mode='HTML' ) {

		if ( 'Markdown' !== $parse_mode ) {
			$text = preg_replace( '/\[([^\]]+?)\]\(([^\)]+?)\)/ui', '<a href="$2">$1</a>', $text );

			if ( 'HTML' !== $parse_mode ) {
				$text = wp_strip_all_tags( $text, false );
			}
		}
		return $text;
	}

}
