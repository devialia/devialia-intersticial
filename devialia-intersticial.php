<?php
/**
 * Plugin Name:     Devialia Intersticial
 * Plugin URI:      http://devialia.com/
 * Description:     Plugin que permite mostrar un intersticial. Contiene una página de configuración donde poder
 * indicar el HTML a mostrar, modificar los estilos e indicar la propiedad z-index.
 * Author:          Devialia
 * Author URI:      http://devialia.com/
 * Text Domain:     devialia-intersticial
 * Domain Path:     /languages
 * Version:         1.0.1
 *
 * @package         Devialia_Intersticial
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class DevialiaIntersticial {
	function __construct() {
		// Genera la página de opciones
		add_action('admin_menu', array($this, 'menu_devialia_intersticial'));
		// Añade los styles y scripts necesarios
		add_action('wp_enqueue_scripts', array($this, 'style_and_scripts_devialia_intersticial'));
		// Imprime el intersticial
		if (get_option('activate_intersticial')) :
			add_action('wp_footer', array($this, 'print_devialia_intersticial'));
		endif;
		// Registra las opciones del formulario de configuración
		add_action('admin_init', array($this, 'register_options_page_devialia_intersticial'));
	}

	/**
	* Se incluyen los styles y scripts necesarios del plugin
	*/
	function style_and_scripts_devialia_intersticial() {
		$version = get_plugin_data(__FILE__)['Version'];

		if (!wp_style_is('font-awesome', $list='enqueued') && !wp_style_is('fontawesome', $list='enqueued')) {
			wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		}

		wp_enqueue_style( 'devialia-intersticial', plugins_url( '/assets/devialia-intersticial.css', __FILE__ ), array(), $version );
		wp_enqueue_script( 'cookies', plugins_url( '/assets/js/cookie.js', __FILE__ ) , array('jquery'), $version, true );
		wp_enqueue_script( 'devialia-intersticial', plugins_url( '/assets/js/devialia-intersticial.js', __FILE__ ) , array('jquery', 'cookies'), $version, true );
	}

	/**
	* Devuelve el HTML del intersticial
	*/
	function html_devialia_intersticial() {
		if (!intval(get_option('z-index_intersticial'))) {
			$zindex = 2000;
		} else {
			$zindex = get_option('z-index_intersticial');
		}

		$html = '<style>
			.devialia_intersticial_wrapper {z-index: '.$zindex.'}
			'.get_option('css_intersticial').'
			</style>';
		$html .= '
		<div class="devialia_intersticial_wrapper" id="devialia_intersticial">
			<div id="devialia_intersticial_background"></div>
			<div class="devialia_intersticial_container">
				<div class="devialia_intersticial_container__header">
					<i class="fa fa-times" id="devialia_intersticial_container__header-close"></i>
				</div>
				<div class="devialia_intersticial_container__content">
					'.get_option('html_intersticial').'
				</div>
			</div>
		</div>
		';

		return $html;
	}

	/**
	* Imprime el intersticial
	*/
	function print_devialia_intersticial() {
		echo $this->html_devialia_intersticial();
	}

	/**
	* Creo la página de opciones del Store Locator
	**/
	function menu_devialia_intersticial() {
		add_menu_page(
			__('Intersticial Configuration'),
			__('Intersticial Configuration'),
			'administrator',
			'options-devialia-intersticial',
			array($this, 'print_options_page_devialia_intersticial')
		);
	}

	/**
	* Configuración del formulario
	*/
	function print_options_page_devialia_intersticial(){
		?>
		<style>
			#options-page-devialia-intersticial-form .group-field {
				margin-bottom: 20px;
			}

			#options-page-devialia-intersticial-form label {
				font-weight: bold;
				padding-bottom: 10px;
				display: block;
			}

			#options-page-devialia-intersticial-form textarea {
				width: 100%;
				height: 400px;
			}
		</style>
		<div class="wrap">
			<h2><?=__('Intersticial Configuration')?></h2>
			<form id="options-page-devialia-intersticial-form" method="post" action="options.php">
				<?php
				settings_fields('options-page-devialia-intersticial-group');
				do_settings_sections('options-page-devialia-intersticial-group');
				?>
				<div class="group-field">
					<label for="activate_intersticial">Activar el intersticial?</label>
					<input type="checkbox" name="activate_intersticial" id="activate_intersticial" value="1" <?php checked(1, get_option('activate_intersticial'), true); ?>>
				</div>
				<div class="group-field">
					<label for="z-index_intersticial">Z-INDEX del intersticial</label>
					<input type="number" name="z-index_intersticial" id="z-index_intersticial" value="<?=get_option('z-index_intersticial');?>">
				</div>
				<div class="group-field">
					<label for="html_intersticial">HTML del intersticial</label>
					<?php wp_editor( get_option('html_intersticial'), "html_intersticial", array() ); ?>
				</div>
				<div class="group-field">
					<label for="css_intersticial">CSS del intersticial</label>
					<p><?=__('Se recomienda encabezar cada regla css con #devialia_intersticial para no afectar a otros elementos.')?></p>
					<textarea name="css_intersticial" id="css_intersticial"><?=get_option('css_intersticial');?></textarea>
				</div>
				<div class="group-field">
					<?php submit_button(); ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	* Registro de los campos para guardarlos, hacer un register_setting por campo
	*/
	function register_options_page_devialia_intersticial() {
		// checkbox de activación del intersticial
		register_setting(
			'options-page-devialia-intersticial-group', 
			'activate_intersticial'
		);
		// html_intersticial field
		register_setting(
			'options-page-devialia-intersticial-group', 
			'html_intersticial'
		);
		// css_intersticial field
		register_setting(
			'options-page-devialia-intersticial-group', 
			'css_intersticial'
		);
		// z-index_intersticial field
		register_setting(
			'options-page-devialia-intersticial-group', 
			'z-index_intersticial'
		);
	}
}

$devialia_intersticial = new DevialiaIntersticial();