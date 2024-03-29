<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.1
 * @package     Timber
 */

namespace Timber\Services;

/**
 * Themes Data Services
 *
 * @since 1.0
 */
class ThemesData extends \Timber\Services\Base {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
    public function __construct($timber) {
        parent::__construct($timber);
    }

	/**
	 * Bind Themes
	 *
	 * @since 1.0
	 * @access public
	 */
	public function themesData()
	{
		$data = array();

		$data['activate_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/themes/activate';
		$data['save_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/themes/save';
		$data['delete_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/themes/delete';
		$this->timber->twig->syncThemes();
		$data['themes_data'] = $this->timber->twig->getThemesData();
		$enabled_theme = $this->timber->twig->getEnabledTheme();
		$data['current_site_skin'] = $this->timber->config('_site_skin');
		$data['current_google_font'] = $this->timber->config('_google_font');
		foreach ($data['themes_data'] as $theme => $theme_data) {
			$data['themes_data'][$theme]['enabled'] = ( $theme == trim($enabled_theme, '/') ) ? true : false;
		}

		return $data;
	}

	/**
	 * Bind Google Fonts
	 *
	 * @since 1.0
	 * @access public
	 */
	public function fontsData()
	{
		$data = array();

		$fonts = '[{"family":"ABeeZee"},{"family":"Abel"},{"family":"Abril Fatface"},{"family":"Aclonica"},{"family":"Acme"},{"family":"Actor"},{"family":"Adamina"},{"family":"Advent Pro"},{"family":"Aguafina Script"},{"family":"Akronim"},{"family":"Aladin"},{"family":"Aldrich"},{"family":"Alef"},{"family":"Alegreya"},{"family":"Alegreya SC"},{"family":"Alex Brush"},{"family":"Alfa Slab One"},{"family":"Alice"},{"family":"Alike"},{"family":"Alike Angular"},{"family":"Allan"},{"family":"Allerta"},{"family":"Allerta Stencil"},{"family":"Allura"},{"family":"Almendra"},{"family":"Almendra Display"},{"family":"Almendra SC"},{"family":"Amarante"},{"family":"Amaranth"},{"family":"Amatic SC"},{"family":"Amethysta"},{"family":"Anaheim"},{"family":"Andada"},{"family":"Andika"},{"family":"Angkor"},{"family":"Annie Use Your Telescope"},{"family":"Anonymous Pro"},{"family":"Antic"},{"family":"Antic Didone"},{"family":"Antic Slab"},{"family":"Anton"},{"family":"Arapey"},{"family":"Arbutus"},{"family":"Arbutus Slab"},{"family":"Architects Daughter"},{"family":"Archivo Black"},{"family":"Archivo Narrow"},{"family":"Arimo"},{"family":"Arizonia"},{"family":"Armata"},{"family":"Artifika"},{"family":"Arvo"},{"family":"Asap"},{"family":"Asset"},{"family":"Astloch"},{"family":"Asul"},{"family":"Atomic Age"},{"family":"Aubrey"},{"family":"Audiowide"},{"family":"Autour One"},{"family":"Average"},{"family":"Average Sans"},{"family":"Averia Gruesa Libre"},{"family":"Averia Libre"},{"family":"Averia Sans Libre"},{"family":"Averia Serif Libre"},{"family":"Bad Script"},{"family":"Balthazar"},{"family":"Bangers"},{"family":"Basic"},{"family":"Battambang"},{"family":"Baumans"},{"family":"Bayon"},{"family":"Belgrano"},{"family":"Belleza"},{"family":"BenchNine"},{"family":"Bentham"},{"family":"Berkshire Swash"},{"family":"Bevan"},{"family":"Bigelow Rules"},{"family":"Bigshot One"},{"family":"Bilbo"},{"family":"Bilbo Swash Caps"},{"family":"Bitter"},{"family":"Black Ops One"},{"family":"Bokor"},{"family":"Bonbon"},{"family":"Boogaloo"},{"family":"Bowlby One"},{"family":"Bowlby One SC"},{"family":"Brawler"},{"family":"Bree Serif"},{"family":"Bubblegum Sans"},{"family":"Bubbler One"},{"family":"Buda"},{"family":"Buenard"},{"family":"Butcherman"},{"family":"Butterfly Kids"},{"family":"Cabin"},{"family":"Cabin Condensed"},{"family":"Cabin Sketch"},{"family":"Caesar Dressing"},{"family":"Cagliostro"},{"family":"Calligraffitti"},{"family":"Cambo"},{"family":"Candal"},{"family":"Cantarell"},{"family":"Cantata One"},{"family":"Cantora One"},{"family":"Capriola"},{"family":"Cardo"},{"family":"Carme"},{"family":"Carrois Gothic"},{"family":"Carrois Gothic SC"},{"family":"Carter One"},{"family":"Caudex"},{"family":"Cedarville Cursive"},{"family":"Ceviche One"},{"family":"Changa One"},{"family":"Chango"},{"family":"Chau Philomene One"},{"family":"Chela One"},{"family":"Chelsea Market"},{"family":"Chenla"},{"family":"Cherry Cream Soda"},{"family":"Cherry Swash"},{"family":"Chewy"},{"family":"Chicle"},{"family":"Chivo"},{"family":"Cinzel"},{"family":"Cinzel Decorative"},{"family":"Clicker Script"},{"family":"Coda"},{"family":"Coda Caption"},{"family":"Codystar"},{"family":"Combo"},{"family":"Comfortaa"},{"family":"Coming Soon"},{"family":"Concert One"},{"family":"Condiment"},{"family":"Content"},{"family":"Contrail One"},{"family":"Convergence"},{"family":"Cookie"},{"family":"Copse"},{"family":"Corben"},{"family":"Courgette"},{"family":"Cousine"},{"family":"Coustard"},{"family":"Covered By Your Grace"},{"family":"Crafty Girls"},{"family":"Creepster"},{"family":"Crete Round"},{"family":"Crimson Text"},{"family":"Croissant One"},{"family":"Crushed"},{"family":"Cuprum"},{"family":"Cutive"},{"family":"Cutive Mono"},{"family":"Damion"},{"family":"Dancing Script"},{"family":"Dangrek"},{"family":"Dawning of a New Day"},{"family":"Days One"},{"family":"Delius"},{"family":"Delius Swash Caps"},{"family":"Delius Unicase"},{"family":"Della Respira"},{"family":"Denk One"},{"family":"Devonshire"},{"family":"Didact Gothic"},{"family":"Diplomata"},{"family":"Diplomata SC"},{"family":"Domine"},{"family":"Donegal One"},{"family":"Doppio One"},{"family":"Dorsa"},{"family":"Dosis"},{"family":"Dr Sugiyama"},{"family":"Droid Sans"},{"family":"Droid Sans Mono"},{"family":"Droid Serif"},{"family":"Duru Sans"},{"family":"Dynalight"},{"family":"EB Garamond"},{"family":"Eagle Lake"},{"family":"Eater"},{"family":"Economica"},{"family":"Electrolize"},{"family":"Elsie"},{"family":"Elsie Swash Caps"},{"family":"Emblema One"},{"family":"Emilys Candy"},{"family":"Engagement"},{"family":"Englebert"},{"family":"Enriqueta"},{"family":"Erica One"},{"family":"Esteban"},{"family":"Euphoria Script"},{"family":"Ewert"},{"family":"Exo"},{"family":"Expletus Sans"},{"family":"Fanwood Text"},{"family":"Fascinate"},{"family":"Fascinate Inline"},{"family":"Faster One"},{"family":"Fasthand"},{"family":"Fauna One"},{"family":"Federant"},{"family":"Federo"},{"family":"Felipa"},{"family":"Fenix"},{"family":"Finger Paint"},{"family":"Fjalla One"},{"family":"Fjord One"},{"family":"Flamenco"},{"family":"Flavors"},{"family":"Fondamento"},{"family":"Fontdiner Swanky"},{"family":"Forum"},{"family":"Francois One"},{"family":"Freckle Face"},{"family":"Fredericka the Great"},{"family":"Fredoka One"},{"family":"Freehand"},{"family":"Fresca"},{"family":"Frijole"},{"family":"Fruktur"},{"family":"Fugaz One"},{"family":"GFS Didot"},{"family":"GFS Neohellenic"},{"family":"Gabriela"},{"family":"Gafata"},{"family":"Galdeano"},{"family":"Galindo"},{"family":"Gentium Basic"},{"family":"Gentium Book Basic"},{"family":"Geo"},{"family":"Geostar"},{"family":"Geostar Fill"},{"family":"Germania One"},{"family":"Gilda Display"},{"family":"Give You Glory"},{"family":"Glass Antiqua"},{"family":"Glegoo"},{"family":"Gloria Hallelujah"},{"family":"Goblin One"},{"family":"Gochi Hand"},{"family":"Gorditas"},{"family":"Goudy Bookletter 1911"},{"family":"Graduate"},{"family":"Grand Hotel"},{"family":"Gravitas One"},{"family":"Great Vibes"},{"family":"Griffy"},{"family":"Gruppo"},{"family":"Gudea"},{"family":"Habibi"},{"family":"Hammersmith One"},{"family":"Hanalei"},{"family":"Hanalei Fill"},{"family":"Handlee"},{"family":"Hanuman"},{"family":"Happy Monkey"},{"family":"Headland One"},{"family":"Henny Penny"},{"family":"Herr Von Muellerhoff"},{"family":"Holtwood One SC"},{"family":"Homemade Apple"},{"family":"Homenaje"},{"family":"IM Fell DW Pica"},{"family":"IM Fell DW Pica SC"},{"family":"IM Fell Double Pica"},{"family":"IM Fell Double Pica SC"},{"family":"IM Fell English"},{"family":"IM Fell English SC"},{"family":"IM Fell French Canon"},{"family":"IM Fell French Canon SC"},{"family":"IM Fell Great Primer"},{"family":"IM Fell Great Primer SC"},{"family":"Iceberg"},{"family":"Iceland"},{"family":"Imprima"},{"family":"Inconsolata"},{"family":"Inder"},{"family":"Indie Flower"},{"family":"Inika"},{"family":"Irish Grover"},{"family":"Istok Web"},{"family":"Italiana"},{"family":"Italianno"},{"family":"Jacques Francois"},{"family":"Jacques Francois Shadow"},{"family":"Jim Nightshade"},{"family":"Jockey One"},{"family":"Jolly Lodger"},{"family":"Josefin Sans"},{"family":"Josefin Slab"},{"family":"Joti One"},{"family":"Judson"},{"family":"Julee"},{"family":"Julius Sans One"},{"family":"Junge"},{"family":"Jura"},{"family":"Just Another Hand"},{"family":"Just Me Again Down Here"},{"family":"Kameron"},{"family":"Karla"},{"family":"Kaushan Script"},{"family":"Kavoon"},{"family":"Keania One"},{"family":"Kelly Slab"},{"family":"Kenia"},{"family":"Khmer"},{"family":"Kite One"},{"family":"Knewave"},{"family":"Kotta One"},{"family":"Koulen"},{"family":"Kranky"},{"family":"Kreon"},{"family":"Kristi"},{"family":"Krona One"},{"family":"La Belle Aurore"},{"family":"Lancelot"},{"family":"Lato"},{"family":"League Script"},{"family":"Leckerli One"},{"family":"Ledger"},{"family":"Lekton"},{"family":"Lemon"},{"family":"Libre Baskerville"},{"family":"Life Savers"},{"family":"Lilita One"},{"family":"Lily Script One"},{"family":"Limelight"},{"family":"Linden Hill"},{"family":"Lobster"},{"family":"Lobster Two"},{"family":"Londrina Outline"},{"family":"Londrina Shadow"},{"family":"Londrina Sketch"},{"family":"Londrina Solid"},{"family":"Lora"},{"family":"Love Ya Like A Sister"},{"family":"Loved by the King"},{"family":"Lovers Quarrel"},{"family":"Luckiest Guy"},{"family":"Lusitana"},{"family":"Lustria"},{"family":"Macondo"},{"family":"Macondo Swash Caps"},{"family":"Magra"},{"family":"Maiden Orange"},{"family":"Mako"},{"family":"Marcellus"},{"family":"Marcellus SC"},{"family":"Marck Script"},{"family":"Margarine"},{"family":"Marko One"},{"family":"Marmelad"},{"family":"Marvel"},{"family":"Mate"},{"family":"Mate SC"},{"family":"Maven Pro"},{"family":"McLaren"},{"family":"Meddon"},{"family":"MedievalSharp"},{"family":"Medula One"},{"family":"Megrim"},{"family":"Meie Script"},{"family":"Merienda"},{"family":"Merienda One"},{"family":"Merriweather"},{"family":"Merriweather Sans"},{"family":"Metal"},{"family":"Metal Mania"},{"family":"Metamorphous"},{"family":"Metrophobic"},{"family":"Michroma"},{"family":"Milonga"},{"family":"Miltonian"},{"family":"Miltonian Tattoo"},{"family":"Miniver"},{"family":"Miss Fajardose"},{"family":"Modern Antiqua"},{"family":"Molengo"},{"family":"Molle"},{"family":"Monda"},{"family":"Monofett"},{"family":"Monoton"},{"family":"Monsieur La Doulaise"},{"family":"Montaga"},{"family":"Montez"},{"family":"Montserrat"},{"family":"Montserrat Alternates"},{"family":"Montserrat Subrayada"},{"family":"Moul"},{"family":"Moulpali"},{"family":"Mountains of Christmas"},{"family":"Mouse Memoirs"},{"family":"Mr Bedfort"},{"family":"Mr Dafoe"},{"family":"Mr De Haviland"},{"family":"Mrs Saint Delafield"},{"family":"Mrs Sheppards"},{"family":"Muli"},{"family":"Mystery Quest"},{"family":"Neucha"},{"family":"Neuton"},{"family":"New Rocker"},{"family":"News Cycle"},{"family":"Niconne"},{"family":"Nixie One"},{"family":"Nobile"},{"family":"Nokora"},{"family":"Norican"},{"family":"Nosifer"},{"family":"Nothing You Could Do"},{"family":"Noticia Text"},{"family":"Noto Sans"},{"family":"Noto Serif"},{"family":"Nova Cut"},{"family":"Nova Flat"},{"family":"Nova Mono"},{"family":"Nova Oval"},{"family":"Nova Round"},{"family":"Nova Script"},{"family":"Nova Slim"},{"family":"Nova Square"},{"family":"Numans"},{"family":"Nunito"},{"family":"Odor Mean Chey"},{"family":"Offside"},{"family":"Old Standard TT"},{"family":"Oldenburg"},{"family":"Oleo Script"},{"family":"Oleo Script Swash Caps"},{"family":"Open Sans"},{"family":"Open Sans Condensed"},{"family":"Oranienbaum"},{"family":"Orbitron"},{"family":"Oregano"},{"family":"Orienta"},{"family":"Original Surfer"},{"family":"Oswald"},{"family":"Over the Rainbow"},{"family":"Overlock"},{"family":"Overlock SC"},{"family":"Ovo"},{"family":"Oxygen"},{"family":"Oxygen Mono"},{"family":"PT Mono"},{"family":"PT Sans"},{"family":"PT Sans Caption"},{"family":"PT Sans Narrow"},{"family":"PT Serif"},{"family":"PT Serif Caption"},{"family":"Pacifico"},{"family":"Paprika"},{"family":"Parisienne"},{"family":"Passero One"},{"family":"Passion One"},{"family":"Pathway Gothic One"},{"family":"Patrick Hand"},{"family":"Patrick Hand SC"},{"family":"Patua One"},{"family":"Paytone One"},{"family":"Peralta"},{"family":"Permanent Marker"},{"family":"Petit Formal Script"},{"family":"Petrona"},{"family":"Philosopher"},{"family":"Piedra"},{"family":"Pinyon Script"},{"family":"Pirata One"},{"family":"Plaster"},{"family":"Play"},{"family":"Playball"},{"family":"Playfair Display"},{"family":"Playfair Display SC"},{"family":"Podkova"},{"family":"Poiret One"},{"family":"Poller One"},{"family":"Poly"},{"family":"Pompiere"},{"family":"Pontano Sans"},{"family":"Port Lligat Sans"},{"family":"Port Lligat Slab"},{"family":"Prata"},{"family":"Preahvihear"},{"family":"Press Start 2P"},{"family":"Princess Sofia"},{"family":"Prociono"},{"family":"Prosto One"},{"family":"Puritan"},{"family":"Purple Purse"},{"family":"Quando"},{"family":"Quantico"},{"family":"Quattrocento"},{"family":"Quattrocento Sans"},{"family":"Questrial"},{"family":"Quicksand"},{"family":"Quintessential"},{"family":"Qwigley"},{"family":"Racing Sans One"},{"family":"Radley"},{"family":"Raleway"},{"family":"Raleway Dots"},{"family":"Rambla"},{"family":"Rammetto One"},{"family":"Ranchers"},{"family":"Rancho"},{"family":"Rationale"},{"family":"Redressed"},{"family":"Reenie Beanie"},{"family":"Revalia"},{"family":"Ribeye"},{"family":"Ribeye Marrow"},{"family":"Righteous"},{"family":"Risque"},{"family":"Roboto"},{"family":"Roboto Condensed"},{"family":"Roboto Slab"},{"family":"Rochester"},{"family":"Rock Salt"},{"family":"Rokkitt"},{"family":"Romanesco"},{"family":"Ropa Sans"},{"family":"Rosario"},{"family":"Rosarivo"},{"family":"Rouge Script"},{"family":"Ruda"},{"family":"Rufina"},{"family":"Ruge Boogie"},{"family":"Ruluko"},{"family":"Rum Raisin"},{"family":"Ruslan Display"},{"family":"Russo One"},{"family":"Ruthie"},{"family":"Rye"},{"family":"Sacramento"},{"family":"Sail"},{"family":"Salsa"},{"family":"Sanchez"},{"family":"Sancreek"},{"family":"Sansita One"},{"family":"Sarina"},{"family":"Satisfy"},{"family":"Scada"},{"family":"Schoolbell"},{"family":"Seaweed Script"},{"family":"Sevillana"},{"family":"Seymour One"},{"family":"Shadows Into Light"},{"family":"Shadows Into Light Two"},{"family":"Shanti"},{"family":"Share"},{"family":"Share Tech"},{"family":"Share Tech Mono"},{"family":"Shojumaru"},{"family":"Short Stack"},{"family":"Siemreap"},{"family":"Sigmar One"},{"family":"Signika"},{"family":"Signika Negative"},{"family":"Simonetta"},{"family":"Sintony"},{"family":"Sirin Stencil"},{"family":"Six Caps"},{"family":"Skranji"},{"family":"Slackey"},{"family":"Smokum"},{"family":"Smythe"},{"family":"Sniglet"},{"family":"Snippet"},{"family":"Snowburst One"},{"family":"Sofadi One"},{"family":"Sofia"},{"family":"Sonsie One"},{"family":"Sorts Mill Goudy"},{"family":"Source Code Pro"},{"family":"Source Sans Pro"},{"family":"Special Elite"},{"family":"Spicy Rice"},{"family":"Spinnaker"},{"family":"Spirax"},{"family":"Squada One"},{"family":"Stalemate"},{"family":"Stalinist One"},{"family":"Stardos Stencil"},{"family":"Stint Ultra Condensed"},{"family":"Stint Ultra Expanded"},{"family":"Stoke"},{"family":"Strait"},{"family":"Sue Ellen Francisco"},{"family":"Sunshiney"},{"family":"Supermercado One"},{"family":"Suwannaphum"},{"family":"Swanky and Moo Moo"},{"family":"Syncopate"},{"family":"Tangerine"},{"family":"Taprom"},{"family":"Tauri"},{"family":"Telex"},{"family":"Tenor Sans"},{"family":"Text Me One"},{"family":"The Girl Next Door"},{"family":"Tienne"},{"family":"Tinos"},{"family":"Titan One"},{"family":"Titillium Web"},{"family":"Trade Winds"},{"family":"Trocchi"},{"family":"Trochut"},{"family":"Trykker"},{"family":"Tulpen One"},{"family":"Ubuntu"},{"family":"Ubuntu Condensed"},{"family":"Ubuntu Mono"},{"family":"Ultra"},{"family":"Uncial Antiqua"},{"family":"Underdog"},{"family":"Unica One"},{"family":"UnifrakturCook"},{"family":"UnifrakturMaguntia"},{"family":"Unkempt"},{"family":"Unlock"},{"family":"Unna"},{"family":"VT323"},{"family":"Vampiro One"},{"family":"Varela"},{"family":"Varela Round"},{"family":"Vast Shadow"},{"family":"Vibur"},{"family":"Vidaloka"},{"family":"Viga"},{"family":"Voces"},{"family":"Volkhov"},{"family":"Vollkorn"},{"family":"Voltaire"},{"family":"Waiting for the Sunrise"},{"family":"Wallpoet"},{"family":"Walter Turncoat"},{"family":"Warnes"},{"family":"Wellfleet"},{"family":"Wendy One"},{"family":"Wire One"},{"family":"Yanone Kaffeesatz"},{"family":"Yellowtail"},{"family":"Yeseva One"},{"family":"Yesteryear"},{"family":"Zeyada"}]';
		$data['google_fonts'] = json_decode($fonts, true);

		return $data;
	}

	/**
	 * Get Current User Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function currentUserData()
	{
		$data = array();
		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById($user_id);

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->security->endSession();
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();
		foreach ($user as $key => $value) {
			$data['current_user_' . $key] = $value;
		}

		return $data;
	}
}