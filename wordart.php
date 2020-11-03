<?php
/**
* Plugin Name: Wordart
* Plugin URI: http://wpplugin.es
* Description: Este plugln genera csv (wordart).
* Version: 1.0.0
* Author: Juan Ceballos
* Author URI: http://ester-ribas.com
* Requires at least: 4.0
* Tested up to: 4.3
*
*Text Domain: wpplugin-ejemplo
* Domain path: /languages/
*/

include_once dirname(__FILE__)  . '/krumo/class.krumo.php';
krumo::$skin = 'orange';

if (!function_exists('add_action')) die();
	$wordart_path    = plugin_basename(__FILE__); 

/* Btn de configuracion sobre el plugin */
function wordart_plugin_action_links($links, $file) {
	global $wordart_path;
	
	if ($file == $wordart_path) {
		
		$wordart_links = '<a href="'. get_admin_url() .'options-general.php?page='. $wordart_path .'">'. esc_html__('Settings', 'wordart') .'</a>';
		
		array_unshift($links, $wordart_links);
		
	}
	return $links;
}
add_filter ('plugin_action_links', 'wordart_plugin_action_links', 10, 2);



/* Js to Plugin */

function wordart_enqueue_script() {   
    wp_enqueue_script( 'wordart_script', plugin_dir_url( __FILE__ ) . 'js/wordart.js', array('jquery'), '1.0' );
}
add_action('wp_enqueue_scripts', 'wordart_enqueue_script');



/* Controlador lanzar el formulario */

function wordart_add_options_page() {
	global $wordart_plugin;
	add_options_page($wordart_plugin, esc_html__('Contact Form', 'wordart'), 'manage_options', __FILE__, 'wordart_render_form');
	
}
add_action ('admin_menu', 'wordart_add_options_page');

/* Query Db */
function get_data_contact_form_db(){
	global $wpdb;
	$response = $wpdb->get_results("SELECT * FROM wp_db7_forms WHERE form_post_id=43682");
	return $response;
}

/* Function count Lastname */
function CountRepeatLastname($array)
{
	$contar=array();

 	foreach($array as $value)
	{
		if(isset($contar[$value])){
			$contar[$value]+=1;
		}else{
			$contar[$value]=1;
		}
	}
	return $contar;
}


/* Function wordart_bad_words */

function wordart_bad_words($lastnames){

	$validate = '';
	$bad_words = array('GONORREA','PERRA','HP','PIROBOS','MALPARIDO','MALPARIDOS','COCAÍNA','COCAINE','PRESIDENTE',
					   'PRESIDENT','PERRAS','ZORRA','ZORRAS','NARCO','NARCOS','CABRON','CABRONES','PROSTITUTAS','BITCH','BITCHS','MARIHUANA','PUTA','PUTAS','ZUNGA','ZUNGAS','HIJUEPUTA','HIJUEPUTAS','HUEVON','HUEVONES','LAMPARA','LAMPARAS','SAPO','SAPOS','VAGABUNDA','VAGABUNDAS','ÑERO','ÑERAS','ÑEROS','IMBECIL','IMBECILES','LOCA','LOCAS','MARICON','MARICONES','PENDEJO','PENDEJOS',
					   		'PENDEJAS','PICHURRIA','PICHURRIAS','PUTOS','PREPAGO','PREPAGOS','CANNABIS','MK','MKAS','CACORRO','CACORROS','CACHORRA','CAREMONDA','CAREMONDAS','CARECHIMBAS','CARECHIMBA','FUFA','FUFAS','FUFURUFAS','GARBINBA','GARBINBAS','GUARICHA',
					   		'GUARICHAS','GUISA','GUISAS','HUEVA','LOBA','LOBAS','MENSO','MENSOS','BOBO','BOBOS','TARADO','TARADOS','SEXO','XXX',
					   		'PETARDO','PETARDOS','BOMBA','BOMBAS','CARTEL','CARTELES','DROGA','DROGAS','MAFIA','MAFIAS','MAFIOSOS','HUEVONADAS','GAY','GAYS','AMANERADO','AMANERADOS','ESTADO','GOBIERNO','ESTADOS','GOBIERNOS','MIERDA','MIERDAS','SHIT','PERRO','PERROS','MADRE','MADRES','IDIOTA','IDIOTAS','INEPTO','INEPTOS','TOMBO','TOMBOS','SIDA','VH','VIH','MAMAR','CHUPON','CHUPONES','MONDA','CARE','PENE','PENES','PITO','PITOS','VERGA','VERGAS','VAGINA','VAGINAS','CHOCHA','CHOCHAL','ANTRO','ANTROS','CHUPA','CHUPAS','PAJA','PAJONES','PAJASO','LADRON','LADRONES','PORRO','PORROS','NARCOESTADO','WEON','WEONES');

	if (in_array($lastnames, $bad_words)) {
	    $validate = 'error';
	}

	return $validate;
}


/* Render Form */
function wordart_render_form() {
	
	$data = get_data_contact_form_db();
	$lastname = array();
	$countdata = array();
	
	foreach ($data as $key => $value) {
		$data = unserialize($value->form_value);
		$lastnames = explode(" ", $data['your-lastname']);
		
		$check = wordart_bad_words($lastnames[false]);
	
		if ($check != 'error') {
			$lastname[] = $lastnames[false];
		}
	}

	
	if (is_array($lastname)) {
		$countdata = CountRepeatLastname($lastname);
	}
	
	$wordart = '';
	$color   = 'ffffff';
	$angle   = 'default';
	$font 	 = 'default';

	foreach ($countdata as $key => $value) {
		$wordart .= ''.$key.';'.$value.';'.$color.';'.$angle.';'.$font.';'."\n";
	}
?>
	<script type="text/javascript" src="../wp-content/plugins/wordart/js/wordart.js"></script>
	<div id="mm-plugin-options" class="wrap">
			<center>
				<strong> Este archivo se genera en tiempo real, sobre los registros existentes </strong>
				<div> <strong>Por favor copiar todo el CSV Generado y pegar en </strong> 
					<a href="https://wordart.com/edit/xuzuy1ybb7kb">wordart</a>
				</div>
				<textarea style="width: 500px; height: 500px;" id="textarea"><?php print($wordart); ?></textarea>
				<br>
				<button onclick="copy()">Copy</button>
			</center>
	</div>
<?php }