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

namespace Timber\Libraries;

/**
 * Configure twig to replace slim view
 *
 * @since 1.0
 * @link http://twig.sensiolabs.org/documentation
 */
class Twig extends \Slim\View {

	/**
	 * Default theme name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->default_theme
	 */
	private $default_theme = '/default';

	/**
	 * Enabled theme name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->enabled_theme
	 */
	private $enabled_theme = '';

	/**
	 * Template files extension
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->tpl_ext
	 */
	private $tpl_ext = '.tpl.twig';

	/**
	 * Instance of configured twig library
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->twig
	 */
	private $twig;

	/**
	 * Themes data in public dir
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->themes_data
	 */
	private $themes_data;

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->timber
	 */
	private $timber;

	/**
	 * Holds an instance of this class
	 *
	 * @since 1.0
	 * @access private
	 * @var object self::$instance
	 */
	private static $instance;

	/**
	 * Create instance of this class or return existing instance
	 *
	 * @since 1.0
	 * @access public
	 * @return object an instance of this class
	 */
	public static function instance()
	{
		if ( !isset(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set class dependencies
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 * @return object
	 */
	public function setDepen($timber)
	{
		$this->timber = $timber;
		return $this;
	}

	/**
	 * Configure twig to render templates
	 *
	 * @since 1.0
	 * @access public
	 * @link http://twig.sensiolabs.org/documentation
	 */
	public function config()
	{
		$this->themes_data = $this->timber->config('_themes_data');
		$this->themes_data = unserialize($this->themes_data);
		$caching_data = $this->timber->config('_site_caching');
		$caching_data = unserialize($caching_data);

		$now = time();
		$auto_reload = false;

		if ( ($caching_data['status'] == 'on') && (($now - $caching_data['last_run']) >= ( $caching_data['purge_each'] * 24 * 60 * 60 )) ) {
			//purge caching
			$this->dumpCache( TIMBER_ROOT . TIMBER_CACHE_DIR );
			$auto_reload = true;
			$caching_data['last_run'] = $now;
			$this->timber->option_model->updateOptionByKey( array('op_key' => '_site_caching', 'op_value' => serialize($caching_data)) );
		}

		if( $caching_data['status'] != 'on' ){
			$this->dumpCache( TIMBER_ROOT . TIMBER_CACHE_DIR );
		}

		$caching = ($caching_data['status'] == 'on') ? TIMBER_ROOT . TIMBER_CACHE_DIR : false;
		$this->enabled_theme = ( $this->timber->config('_site_theme') != trim($this->default_theme, '/') ) ? '/' . $this->timber->config('_site_theme') : $this->default_theme;

		$themes = TIMBER_ROOT . TIMBER_THEMES_DIR . $this->default_theme;

		if( $this->default_theme != $this->enabled_theme ){
			$themes = array(TIMBER_ROOT . TIMBER_THEMES_DIR . $this->enabled_theme, TIMBER_ROOT . TIMBER_THEMES_DIR . $this->default_theme);
		}

		$loader = new \Twig_Loader_Filesystem($themes);
		$this->twig = new \Twig_Environment($loader, array(
			'debug' => TIMBER_DEBUG_MODE,
			'charset' => 'utf-8',
			'strict_variables' => TIMBER_DEBUG_MODE,
			'autoescape' => true,
				'cache' => $caching,
				'auto_reload' => $auto_reload,
		));
		$this->twig->getExtension('core')->setDateFormat('d/m/Y', '%d days');
		$this->twig->getExtension('core')->setTimezone($this->timber->config('_site_timezone'));
		$this->twig->addExtension( $this->timber->twigext );
		if( TIMBER_DEBUG_MODE ){
			$this->twig->addExtension(new \Twig_Extension_Debug());
			$this->twig->addExtension(new \Twig_Extension_Optimizer());
		}
	}

	/**
	 * Render Twig Template
	 *
	 * @since 1.0
	 * @access public
     * @param string $template The path to the Twig template, relative to the Twig template directory.
	 * @param array $data
	 * @return string
	 */
	public function render($template, $data = array())
	{
		if( !(strpos($template, '/') > 0) ){
			$template = $template . $this->tpl_ext;
		}
		$parser = $this->twig->loadTemplate($template);
		$data = array_merge($this->all(), (array) $data);
		return $parser->render($data);
	}

	/**
	 * Get current themes in public dir
	 *
	 * all themes should have alphanumeric lowercase name (eg. default, default12, .....)
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getThemes()
	{
		$themes = array();

		@chmod( TIMBER_ROOT . TIMBER_THEMES_DIR , 0755);
		$dirs = @scandir( TIMBER_ROOT . TIMBER_THEMES_DIR );

		if( $dirs === false ){
			return array();
		}

		foreach ( $dirs as $dir ) {
			if ( ($dir === '.') || ($dir === '..') ){ continue; }
			$dir = strtolower($dir);
			if ( $dir != preg_replace('/[^a-z0-9]/i', '', $dir) ){ continue; }
			if ( (is_dir(TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $dir)) && (is_file(TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $dir . '/info.php')) ) {
				$themes[] = array(
					'slug' => $dir,
					'value' => ucfirst($dir),
				);
			}
		}
		return $themes;
	}

	/**
	 * Get current themes in public dir
	 *
	 * all themes should have alphanumeric lowercase name (eg. default, default12, .....)
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getThemesList()
	{
		$themes = array();
		@chmod( TIMBER_ROOT . TIMBER_THEMES_DIR , 0755);
		$dirs = @scandir( TIMBER_ROOT . TIMBER_THEMES_DIR );

		if( $dirs === false ){
			return array();
		}

		foreach ( $dirs as $dir ) {
			if ( $dir === '.' || $dir === '..'){ continue; }
			$dir = strtolower($dir);
			if ( $dir != preg_replace('/[^a-z0-9]/i', '', $dir) ){ continue; }
			if ( (is_dir(TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $dir)) && (is_file(TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $dir . '/info.php')) ) {
				$themes[] =  $dir;
			}
		}
		return $themes;
	}

	/**
	 * Sync Themes and Reflect changes to DB
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function syncThemes()
	{
		$themes = $this->getThemesList();
		$themes_data = array();

		if( !(count($themes) > 0) ){
			//issue to read public dir
			return false;
		}

		foreach ($themes as $key => $theme) {
			$themes_data[$theme] = include_once TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $theme . '/info.php';
		}

		if( !(count($this->themes_data) > 0) || (count($this->themes_data) != count($themes_data)) ){
			$this->themes_data = $themes_data;
			return (boolean) $this->timber->option_model->updateOptionByKey( array('op_key' => '_themes_data', 'op_value' => serialize($themes_data)) );
		}

		$changes_detector = false;
		foreach ($themes_data as $theme => $theme_info) {
			if( !(isset($this->themes_data[$theme])) || ($this->themes_data[$theme]['theme_version'] != $theme_info['theme_version']) ){
				$changes_detector = true;
			}
		}
		if( $changes_detector ){
			$this->themes_data = $themes_data;
			return (boolean) $this->timber->option_model->updateOptionByKey( array('op_key' => '_themes_data', 'op_value' => serialize($themes_data)) );
		}
	}

	/**
	 * Dump Cache
	 *
	 * @since 1.0
	 * @access public
	 * @param string $path
	 * @return boolean
	 */
	private function dumpCache($path)
	{
		if( !(is_dir($path)) ){
			return true;
		}
		$path = rtrim( $path, '/' ) . '/';
		@chmod($path, 0755);
		$dirs = @scandir( $path );
		if( !(is_array($dirs)) || !(count($dirs) > 2) ){
			return true;
		}
		$i = 1;
		foreach ( $dirs as $dir ) {
			if($i >= 3){ break; }
			if ( $dir === '.' || $dir === '..'){ continue; }
			if ( is_dir( $path . $dir ) ) {
				$this->dumpCache($path . $dir);
				@chmod( $path . $dir , 0755);
				@rmdir( $path . $dir );
			}elseif( is_file( $path . $dir ) ) {
				@chmod( $path . $dir, 0755);
				@unlink( $path . $dir );
			}
			$i += 1;
		}
		return true;
	}

    /**
     * Delete Theme
     *
     * @since 1.0
     * @access public
     * @param string $theme
     * @return boolean
     */
	public function deleteTheme($theme)
	{
		$path = TIMBER_ROOT . TIMBER_THEMES_DIR . '/' . $theme;
		$this->dumpTheme($path);
		return true;
	}

    /**
     * Dump Theme Files and Folders
     *
     * @since 1.0
     * @access public
     * @param string $path
     * @return boolean
     */
    private function dumpTheme($path)
    {
        if( !(is_dir($path)) ){
           return true;
        }

        $path = rtrim( $path, '/' ) . '/';
        @chmod($path, 0755);
        $dirs = @scandir( $path );
        if( !(is_array($dirs)) || !(count($dirs) > 0) ){
            return true;
        }
        foreach ( $dirs as $dir ) {
            if ( $dir === '.' || $dir === '..'){ continue; }
           	if ( is_dir( $path . $dir ) ) {
                $this->dumpTheme($path . $dir);
                @chmod( $path . $dir , 0755);
                @rmdir( $path . $dir );
            }elseif( is_file( $path . $dir ) ) {
                @chmod( $path . $dir, 0755);
                @unlink( $path . $dir );
            }
        }
        @chmod( $path , 0755);
        @rmdir( $path );
        return true;
    }

	/**
	 * Activate Theme
	 *
	 * @since 1.0
	 * @access public
	 * @param string $theme
	 * @param string $skin
	 * @param string $font
	 * @return boolean
	 */
    public function activateTheme($theme, $skin, $font)
    {
        $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_site_theme',
            'op_value' => $theme
        ));
        $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_site_skin',
            'op_value' => $skin
        ));
        $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_google_font',
            'op_value' => $font
        ));
        return true;
    }

	/**
	 * Update Theme
	 *
	 * @since 1.0
	 * @access public
	 * @param string $skin
	 * @param string $font
	 * @return boolean
	 */
    public function updateTheme($skin, $font)
    {
        $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_site_skin',
            'op_value' => $skin
        ));
        $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_google_font',
            'op_value' => $font
        ));
        return true;
    }

    /**
     * Validate theme
     *
     * @since 1.0
     * @access public
     * @param string $theme
     * @return boolean
     */
    public function validateTheme($theme)
    {
        $themes = $this->getThemesList();
        return (boolean)(in_array($theme, $themes));
    }

    /**
     * Validate Skin
     *
     * @since 1.0
     * @access public
     * @param string $skin
     * @return boolean
     */
    public function validateSkin($skin)
    {
        $themes_data = $this->getThemesData();
        $skins = array();
        foreach ($themes_data as $theme_key => $theme_info) {
            $skins = array_merge($skins, $theme_info['skins']);
        }
        return (boolean) (array_key_exists($skin, $skins));
    }

	/**
	 * Validate Google Font
	 *
	 * @since 1.0
	 * @access public
	 * @param string $font_name
	 * @return boolean
	 */
	public function validateFont($font_name)
	{
        $fonts = '[{"family":"ABeeZee"},{"family":"Abel"},{"family":"Abril Fatface"},{"family":"Aclonica"},{"family":"Acme"},{"family":"Actor"},{"family":"Adamina"},{"family":"Advent Pro"},{"family":"Aguafina Script"},{"family":"Akronim"},{"family":"Aladin"},{"family":"Aldrich"},{"family":"Alef"},{"family":"Alegreya"},{"family":"Alegreya SC"},{"family":"Alex Brush"},{"family":"Alfa Slab One"},{"family":"Alice"},{"family":"Alike"},{"family":"Alike Angular"},{"family":"Allan"},{"family":"Allerta"},{"family":"Allerta Stencil"},{"family":"Allura"},{"family":"Almendra"},{"family":"Almendra Display"},{"family":"Almendra SC"},{"family":"Amarante"},{"family":"Amaranth"},{"family":"Amatic SC"},{"family":"Amethysta"},{"family":"Anaheim"},{"family":"Andada"},{"family":"Andika"},{"family":"Angkor"},{"family":"Annie Use Your Telescope"},{"family":"Anonymous Pro"},{"family":"Antic"},{"family":"Antic Didone"},{"family":"Antic Slab"},{"family":"Anton"},{"family":"Arapey"},{"family":"Arbutus"},{"family":"Arbutus Slab"},{"family":"Architects Daughter"},{"family":"Archivo Black"},{"family":"Archivo Narrow"},{"family":"Arimo"},{"family":"Arizonia"},{"family":"Armata"},{"family":"Artifika"},{"family":"Arvo"},{"family":"Asap"},{"family":"Asset"},{"family":"Astloch"},{"family":"Asul"},{"family":"Atomic Age"},{"family":"Aubrey"},{"family":"Audiowide"},{"family":"Autour One"},{"family":"Average"},{"family":"Average Sans"},{"family":"Averia Gruesa Libre"},{"family":"Averia Libre"},{"family":"Averia Sans Libre"},{"family":"Averia Serif Libre"},{"family":"Bad Script"},{"family":"Balthazar"},{"family":"Bangers"},{"family":"Basic"},{"family":"Battambang"},{"family":"Baumans"},{"family":"Bayon"},{"family":"Belgrano"},{"family":"Belleza"},{"family":"BenchNine"},{"family":"Bentham"},{"family":"Berkshire Swash"},{"family":"Bevan"},{"family":"Bigelow Rules"},{"family":"Bigshot One"},{"family":"Bilbo"},{"family":"Bilbo Swash Caps"},{"family":"Bitter"},{"family":"Black Ops One"},{"family":"Bokor"},{"family":"Bonbon"},{"family":"Boogaloo"},{"family":"Timberlby One"},{"family":"Timberlby One SC"},{"family":"Brawler"},{"family":"Bree Serif"},{"family":"Bubblegum Sans"},{"family":"Bubbler One"},{"family":"Buda"},{"family":"Buenard"},{"family":"Butcherman"},{"family":"Butterfly Kids"},{"family":"Cabin"},{"family":"Cabin Condensed"},{"family":"Cabin Sketch"},{"family":"Caesar Dressing"},{"family":"Cagliostro"},{"family":"Calligraffitti"},{"family":"Cambo"},{"family":"Candal"},{"family":"Cantarell"},{"family":"Cantata One"},{"family":"Cantora One"},{"family":"Capriola"},{"family":"Cardo"},{"family":"Carme"},{"family":"Carrois Gothic"},{"family":"Carrois Gothic SC"},{"family":"Carter One"},{"family":"Caudex"},{"family":"Cedarville Cursive"},{"family":"Ceviche One"},{"family":"Changa One"},{"family":"Chango"},{"family":"Chau Philomene One"},{"family":"Chela One"},{"family":"Chelsea Market"},{"family":"Chenla"},{"family":"Cherry Cream Soda"},{"family":"Cherry Swash"},{"family":"Chewy"},{"family":"Chicle"},{"family":"Chivo"},{"family":"Cinzel"},{"family":"Cinzel Decorative"},{"family":"Clicker Script"},{"family":"Coda"},{"family":"Coda Caption"},{"family":"Codystar"},{"family":"Combo"},{"family":"Comfortaa"},{"family":"Coming Soon"},{"family":"Concert One"},{"family":"Condiment"},{"family":"Content"},{"family":"Contrail One"},{"family":"Convergence"},{"family":"Cookie"},{"family":"Copse"},{"family":"Corben"},{"family":"Courgette"},{"family":"Cousine"},{"family":"Coustard"},{"family":"Covered By Your Grace"},{"family":"Crafty Girls"},{"family":"Creepster"},{"family":"Crete Round"},{"family":"Crimson Text"},{"family":"Croissant One"},{"family":"Crushed"},{"family":"Cuprum"},{"family":"Cutive"},{"family":"Cutive Mono"},{"family":"Damion"},{"family":"Timbercing Script"},{"family":"Timbergrek"},{"family":"Dawning of a New Day"},{"family":"Days One"},{"family":"Delius"},{"family":"Delius Swash Caps"},{"family":"Delius Unicase"},{"family":"Della Respira"},{"family":"Denk One"},{"family":"Devonshire"},{"family":"Didact Gothic"},{"family":"Diplomata"},{"family":"Diplomata SC"},{"family":"Domine"},{"family":"Donegal One"},{"family":"Doppio One"},{"family":"Dorsa"},{"family":"Dosis"},{"family":"Dr Sugiyama"},{"family":"Droid Sans"},{"family":"Droid Sans Mono"},{"family":"Droid Serif"},{"family":"Duru Sans"},{"family":"Dynalight"},{"family":"EB Garamond"},{"family":"Eagle Lake"},{"family":"Eater"},{"family":"Economica"},{"family":"Electrolize"},{"family":"Elsie"},{"family":"Elsie Swash Caps"},{"family":"Emblema One"},{"family":"Emilys Candy"},{"family":"Engagement"},{"family":"Englebert"},{"family":"Enriqueta"},{"family":"Erica One"},{"family":"Esteban"},{"family":"Euphoria Script"},{"family":"Ewert"},{"family":"Exo"},{"family":"Expletus Sans"},{"family":"Fanwood Text"},{"family":"Fascinate"},{"family":"Fascinate Inline"},{"family":"Faster One"},{"family":"Fasthand"},{"family":"Fauna One"},{"family":"Federant"},{"family":"Federo"},{"family":"Felipa"},{"family":"Fenix"},{"family":"Finger Paint"},{"family":"Fjalla One"},{"family":"Fjord One"},{"family":"Flamenco"},{"family":"Flavors"},{"family":"Fondamento"},{"family":"Fontdiner Swanky"},{"family":"Forum"},{"family":"Francois One"},{"family":"Freckle Face"},{"family":"Fredericka the Great"},{"family":"Fredoka One"},{"family":"Freehand"},{"family":"Fresca"},{"family":"Frijole"},{"family":"Fruktur"},{"family":"Fugaz One"},{"family":"GFS Didot"},{"family":"GFS Neohellenic"},{"family":"Gabriela"},{"family":"Gafata"},{"family":"Galdeano"},{"family":"Galindo"},{"family":"Gentium Basic"},{"family":"Gentium Book Basic"},{"family":"Geo"},{"family":"Geostar"},{"family":"Geostar Fill"},{"family":"Germania One"},{"family":"Gilda Display"},{"family":"Give You Glory"},{"family":"Glass Antiqua"},{"family":"Glegoo"},{"family":"Gloria Hallelujah"},{"family":"Goblin One"},{"family":"Gochi Hand"},{"family":"Gorditas"},{"family":"Goudy Bookletter 1911"},{"family":"Graduate"},{"family":"Grand Hotel"},{"family":"Gravitas One"},{"family":"Great Vibes"},{"family":"Griffy"},{"family":"Gruppo"},{"family":"Gudea"},{"family":"Habibi"},{"family":"Hammersmith One"},{"family":"Hanalei"},{"family":"Hanalei Fill"},{"family":"Handlee"},{"family":"Hanuman"},{"family":"Happy Monkey"},{"family":"Headland One"},{"family":"Henny Penny"},{"family":"Herr Von Muellerhoff"},{"family":"Holtwood One SC"},{"family":"Homemade Apple"},{"family":"Homenaje"},{"family":"IM Fell DW Pica"},{"family":"IM Fell DW Pica SC"},{"family":"IM Fell Double Pica"},{"family":"IM Fell Double Pica SC"},{"family":"IM Fell English"},{"family":"IM Fell English SC"},{"family":"IM Fell French Canon"},{"family":"IM Fell French Canon SC"},{"family":"IM Fell Great Primer"},{"family":"IM Fell Great Primer SC"},{"family":"Iceberg"},{"family":"Iceland"},{"family":"Imprima"},{"family":"Inconsolata"},{"family":"Inder"},{"family":"Indie Flower"},{"family":"Inika"},{"family":"Irish Grover"},{"family":"Istok Web"},{"family":"Italiana"},{"family":"Italianno"},{"family":"Jacques Francois"},{"family":"Jacques Francois Shadow"},{"family":"Jim Nightshade"},{"family":"Jockey One"},{"family":"Jolly Lodger"},{"family":"Josefin Sans"},{"family":"Josefin Slab"},{"family":"Joti One"},{"family":"Judson"},{"family":"Julee"},{"family":"Julius Sans One"},{"family":"Junge"},{"family":"Jura"},{"family":"Just Another Hand"},{"family":"Just Me Again Down Here"},{"family":"Kameron"},{"family":"Karla"},{"family":"Kaushan Script"},{"family":"Kavoon"},{"family":"Keania One"},{"family":"Kelly Slab"},{"family":"Kenia"},{"family":"Khmer"},{"family":"Kite One"},{"family":"Knewave"},{"family":"Kotta One"},{"family":"Koulen"},{"family":"Kranky"},{"family":"Kreon"},{"family":"Kristi"},{"family":"Krona One"},{"family":"La Belle Aurore"},{"family":"Lancelot"},{"family":"Lato"},{"family":"League Script"},{"family":"Leckerli One"},{"family":"Ledger"},{"family":"Lekton"},{"family":"Lemon"},{"family":"Libre Baskerville"},{"family":"Life Savers"},{"family":"Lilita One"},{"family":"Lily Script One"},{"family":"Limelight"},{"family":"Linden Hill"},{"family":"Lobster"},{"family":"Lobster Two"},{"family":"Londrina Outline"},{"family":"Londrina Shadow"},{"family":"Londrina Sketch"},{"family":"Londrina Solid"},{"family":"Lora"},{"family":"Love Ya Like A Sister"},{"family":"Loved by the King"},{"family":"Lovers Quarrel"},{"family":"Luckiest Guy"},{"family":"Lusitana"},{"family":"Lustria"},{"family":"Macondo"},{"family":"Macondo Swash Caps"},{"family":"Magra"},{"family":"Maiden Orange"},{"family":"Mako"},{"family":"Marcellus"},{"family":"Marcellus SC"},{"family":"Marck Script"},{"family":"Margarine"},{"family":"Marko One"},{"family":"Marmelad"},{"family":"Marvel"},{"family":"Mate"},{"family":"Mate SC"},{"family":"Maven Pro"},{"family":"McLaren"},{"family":"Meddon"},{"family":"MedievalSharp"},{"family":"Medula One"},{"family":"Megrim"},{"family":"Meie Script"},{"family":"Merienda"},{"family":"Merienda One"},{"family":"Merriweather"},{"family":"Merriweather Sans"},{"family":"Metal"},{"family":"Metal Mania"},{"family":"Metamorphous"},{"family":"Metrophobic"},{"family":"Michroma"},{"family":"Milonga"},{"family":"Miltonian"},{"family":"Miltonian Tattoo"},{"family":"Miniver"},{"family":"Miss Fajardose"},{"family":"Modern Antiqua"},{"family":"Molengo"},{"family":"Molle"},{"family":"Monda"},{"family":"Monofett"},{"family":"Monoton"},{"family":"Monsieur La Doulaise"},{"family":"Montaga"},{"family":"Montez"},{"family":"Montserrat"},{"family":"Montserrat Alternates"},{"family":"Montserrat Subrayada"},{"family":"Moul"},{"family":"Moulpali"},{"family":"Mountains of Christmas"},{"family":"Mouse Memoirs"},{"family":"Mr Bedfort"},{"family":"Mr Dafoe"},{"family":"Mr De Haviland"},{"family":"Mrs Saint Delafield"},{"family":"Mrs Sheppards"},{"family":"Muli"},{"family":"Mystery Quest"},{"family":"Neucha"},{"family":"Neuton"},{"family":"New Rocker"},{"family":"News Cycle"},{"family":"Niconne"},{"family":"Nixie One"},{"family":"Nobile"},{"family":"Nokora"},{"family":"Norican"},{"family":"Nosifer"},{"family":"Nothing You Could Do"},{"family":"Noticia Text"},{"family":"Noto Sans"},{"family":"Noto Serif"},{"family":"Nova Cut"},{"family":"Nova Flat"},{"family":"Nova Mono"},{"family":"Nova Oval"},{"family":"Nova Round"},{"family":"Nova Script"},{"family":"Nova Slim"},{"family":"Nova Square"},{"family":"Numans"},{"family":"Nunito"},{"family":"Odor Mean Chey"},{"family":"Offside"},{"family":"Old Standard TT"},{"family":"Oldenburg"},{"family":"Oleo Script"},{"family":"Oleo Script Swash Caps"},{"family":"Open Sans"},{"family":"Open Sans Condensed"},{"family":"Oranienbaum"},{"family":"Orbitron"},{"family":"Oregano"},{"family":"Orienta"},{"family":"Original Surfer"},{"family":"Oswald"},{"family":"Over the Raintimber"},{"family":"Overlock"},{"family":"Overlock SC"},{"family":"Ovo"},{"family":"Oxygen"},{"family":"Oxygen Mono"},{"family":"PT Mono"},{"family":"PT Sans"},{"family":"PT Sans Caption"},{"family":"PT Sans Narrow"},{"family":"PT Serif"},{"family":"PT Serif Caption"},{"family":"Pacifico"},{"family":"Paprika"},{"family":"Parisienne"},{"family":"Passero One"},{"family":"Passion One"},{"family":"Pathway Gothic One"},{"family":"Patrick Hand"},{"family":"Patrick Hand SC"},{"family":"Patua One"},{"family":"Paytone One"},{"family":"Peralta"},{"family":"Permanent Marker"},{"family":"Petit Formal Script"},{"family":"Petrona"},{"family":"Philosopher"},{"family":"Piedra"},{"family":"Pinyon Script"},{"family":"Pirata One"},{"family":"Plaster"},{"family":"Play"},{"family":"Playball"},{"family":"Playfair Display"},{"family":"Playfair Display SC"},{"family":"Podkova"},{"family":"Poiret One"},{"family":"Poller One"},{"family":"Poly"},{"family":"Pompiere"},{"family":"Pontano Sans"},{"family":"Port Lligat Sans"},{"family":"Port Lligat Slab"},{"family":"Prata"},{"family":"Preahvihear"},{"family":"Press Start 2P"},{"family":"Princess Sofia"},{"family":"Prociono"},{"family":"Prosto One"},{"family":"Puritan"},{"family":"Purple Purse"},{"family":"Quando"},{"family":"Quantico"},{"family":"Quattrocento"},{"family":"Quattrocento Sans"},{"family":"Questrial"},{"family":"Quicksand"},{"family":"Quintessential"},{"family":"Qwigley"},{"family":"Racing Sans One"},{"family":"Radley"},{"family":"Raleway"},{"family":"Raleway Dots"},{"family":"Rambla"},{"family":"Rammetto One"},{"family":"Ranchers"},{"family":"Rancho"},{"family":"Rationale"},{"family":"Redressed"},{"family":"Reenie Beanie"},{"family":"Revalia"},{"family":"Ribeye"},{"family":"Ribeye Marrow"},{"family":"Righteous"},{"family":"Risque"},{"family":"Roboto"},{"family":"Roboto Condensed"},{"family":"Roboto Slab"},{"family":"Rochester"},{"family":"Rock Salt"},{"family":"Rokkitt"},{"family":"Romanesco"},{"family":"Ropa Sans"},{"family":"Rosario"},{"family":"Rosarivo"},{"family":"Rouge Script"},{"family":"Ruda"},{"family":"Rufina"},{"family":"Ruge Boogie"},{"family":"Ruluko"},{"family":"Rum Raisin"},{"family":"Ruslan Display"},{"family":"Russo One"},{"family":"Ruthie"},{"family":"Rye"},{"family":"Sacramento"},{"family":"Sail"},{"family":"Salsa"},{"family":"Sanchez"},{"family":"Sancreek"},{"family":"Sansita One"},{"family":"Sarina"},{"family":"Satisfy"},{"family":"Scada"},{"family":"Schoolbell"},{"family":"Seaweed Script"},{"family":"Sevillana"},{"family":"Seymour One"},{"family":"Shadows Into Light"},{"family":"Shadows Into Light Two"},{"family":"Shanti"},{"family":"Share"},{"family":"Share Tech"},{"family":"Share Tech Mono"},{"family":"Shojumaru"},{"family":"Short Stack"},{"family":"Siemreap"},{"family":"Sigmar One"},{"family":"Signika"},{"family":"Signika Negative"},{"family":"Simonetta"},{"family":"Sintony"},{"family":"Sirin Stencil"},{"family":"Six Caps"},{"family":"Skranji"},{"family":"Slackey"},{"family":"Smokum"},{"family":"Smythe"},{"family":"Sniglet"},{"family":"Snippet"},{"family":"Snowburst One"},{"family":"Sofadi One"},{"family":"Sofia"},{"family":"Sonsie One"},{"family":"Sorts Mill Goudy"},{"family":"Source Code Pro"},{"family":"Source Sans Pro"},{"family":"Special Elite"},{"family":"Spicy Rice"},{"family":"Spinnaker"},{"family":"Spirax"},{"family":"Squada One"},{"family":"Stalemate"},{"family":"Stalinist One"},{"family":"Stardos Stencil"},{"family":"Stint Ultra Condensed"},{"family":"Stint Ultra Expanded"},{"family":"Stoke"},{"family":"Strait"},{"family":"Sue Ellen Francisco"},{"family":"Sunshiney"},{"family":"Supermercado One"},{"family":"Suwannaphum"},{"family":"Swanky and Moo Moo"},{"family":"Syncopate"},{"family":"Tangerine"},{"family":"Taprom"},{"family":"Tauri"},{"family":"Telex"},{"family":"Tenor Sans"},{"family":"Text Me One"},{"family":"The Girl Next Door"},{"family":"Tienne"},{"family":"Tinos"},{"family":"Titan One"},{"family":"Titillium Web"},{"family":"Trade Winds"},{"family":"Trocchi"},{"family":"Trochut"},{"family":"Trykker"},{"family":"Tulpen One"},{"family":"Ubuntu"},{"family":"Ubuntu Condensed"},{"family":"Ubuntu Mono"},{"family":"Ultra"},{"family":"Uncial Antiqua"},{"family":"Underdog"},{"family":"Unica One"},{"family":"UnifrakturCook"},{"family":"UnifrakturMaguntia"},{"family":"Unkempt"},{"family":"Unlock"},{"family":"Unna"},{"family":"VT323"},{"family":"Vampiro One"},{"family":"Varela"},{"family":"Varela Round"},{"family":"Vast Shadow"},{"family":"Vibur"},{"family":"Vidaloka"},{"family":"Viga"},{"family":"Voces"},{"family":"Volkhov"},{"family":"Vollkorn"},{"family":"Voltaire"},{"family":"Waiting for the Sunrise"},{"family":"Wallpoet"},{"family":"Walter Turncoat"},{"family":"Warnes"},{"family":"Wellfleet"},{"family":"Wendy One"},{"family":"Wire One"},{"family":"Yanone Kaffeesatz"},{"family":"Yellowtail"},{"family":"Yeseva One"},{"family":"Yesteryear"},{"family":"Zeyada"}]';
        $fonts = json_decode($fonts, true);
        $fonts_list = array();
        foreach ($fonts as $key => $font) {
              $fonts_list[] = $font['family'];
        }
        return (boolean) (in_array($font_name, $fonts_list));
	}

	/**
	 * Get themes data after sync
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getThemesData()
	{
		return $this->themes_data;
	}

	/**
	 * Get default theme rel path
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function getDefaultTheme()
	{
		return $this->default_theme;
	}

	/**
	 * Get enabled theme rel path
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function getEnabledTheme()
	{
		return $this->enabled_theme;
	}
}