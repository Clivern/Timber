<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Timber
 */

namespace Timber\Configs;

/**
 * Inject App Libraries and Models
 *
 * @since 1.0
 */
class Container {

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
	 * Inject all dependencies to timber app
	 *
	 * @since 1.0
	 * @access public
	 * @return object
	 */
	public function bindAll()
	{
		# Define Routes
        $this->timber->applyHook('timber.container', $this->timber);

		$this->injectModels();
		$this->injectLibraries();
		return $this;
	}

	/**
	 * Inject all models to the core
	 *
	 * @since 1.0
	 * @access private
	 */
	private function injectModels()
	{
		# Inject custom model
		$this->timber->container->singleton('custom_model', function () {
			return \Timber\Models\Custom::instance();
		});

		# Inject discussion model
		$this->timber->container->singleton('discussion_model', function () {
			return \Timber\Models\Discussion::instance();
		});

		# Inject file model
		$this->timber->container->singleton('file_model', function () {
			return \Timber\Models\File::instance();
		});

		# Inject invoice model
		$this->timber->container->singleton('invoice_model', function () {
			return \Timber\Models\Invoice::instance();
		});

		# Inject item model
		$this->timber->container->singleton('item_model', function () {
			return \Timber\Models\Item::instance();
		});

		# Inject message model
		$this->timber->container->singleton('message_model', function () {
			return \Timber\Models\Message::instance();
		});

		# Inject meta model
		$this->timber->container->singleton('meta_model', function () {
			return \Timber\Models\Meta::instance();
		});

		# Inject milestone model
		$this->timber->container->singleton('milestone_model', function () {
			return \Timber\Models\Milestone::instance();
		});

		# Inject option model
		$this->timber->container->singleton('option_model', function () {
			return \Timber\Models\Option::instance();
		});

		# Inject project model
		$this->timber->container->singleton('project_model', function () {
			return \Timber\Models\Project::instance();
		});

		# Inject project meta model
		$this->timber->container->singleton('project_meta_model', function () {
			return \Timber\Models\ProjectMeta::instance();
		});

		# Inject quotation model
		$this->timber->container->singleton('quotation_model', function () {
			return \Timber\Models\Quotation::instance();
		});

		# Inject subscription model
		$this->timber->container->singleton('subscription_model', function () {
			return \Timber\Models\Subscription::instance();
		});

		# Inject task model
		$this->timber->container->singleton('task_model', function () {
			return \Timber\Models\Task::instance();
		});

		# Inject ticket meta model
		$this->timber->container->singleton('ticket_model', function () {
			return \Timber\Models\Ticket::instance();
		});

		# Inject user model
		$this->timber->container->singleton('user_model', function () {
			return \Timber\Models\User::instance();
		});

		# Inject user meta model
		$this->timber->container->singleton('user_meta_model', function () {
			return \Timber\Models\UserMeta::instance();
		});
	}

	/**
	 * Inject libraries to the core
	 *
	 * @since 1.0
	 * @access private
	 */
	private function injectLibraries()
	{

		# Inject access library
		$this->timber->container->singleton('access', function () {
			return \Timber\Libraries\Access::instance();
		});

		# Inject backup library
		$this->timber->container->singleton('backup', function () {
			return \Timber\Libraries\Backup::instance();
		});

		# Inject bench library
		$this->timber->container->singleton('bench', function () {
			return \Timber\Libraries\Bench::instance();
		});

		# Inject cachier library
		$this->timber->container->singleton('cachier', function () {
			return  \Timber\Libraries\Cachier::instance();
		});

		# Inject cookie library
		$this->timber->container->singleton('cookie', function () {
			return  \Timber\Libraries\Cookie::instance();
		});

		# Inject debug library
		$this->timber->container->singleton('debug', function () {
			return \Timber\Libraries\Debug::instance();
		});

		# Inject encrypter library
		$this->timber->container->singleton('encrypter', function () {
			return \Timber\Libraries\Encrypter::instance();
		});

		# Inject faker library
		$this->timber->container->singleton('faker', function () {
			return \Timber\Libraries\Faker::instance();
		});

		# Inject filter library
		$this->timber->container->singleton('filter', function () {
			return \Timber\Libraries\Filter::instance();
		});

		# Inject gravatar library
		$this->timber->container->singleton('gravatar', function () {
			return  \Timber\Libraries\Gravatar::instance();
		});

		# Inject hasher library
		$this->timber->container->singleton('hasher', function () {
			return \Timber\Libraries\Hasher::instance();
		});

		# Inject helpers library
		$this->timber->container->singleton('helpers', function () {
			return \Timber\Libraries\Helpers::instance();
		});

		# Inject pdf writer library
		$this->timber->container->singleton('biller', function () {
			return \Timber\Libraries\Biller::instance();
		});

		# Inject logger library
		$this->timber->container->singleton('logger', function () {
			return \Timber\Libraries\Logger::instance();
		});

		# Inject mailer library
		$this->timber->container->singleton('mailer', function () {
			return \Timber\Libraries\Mailer::instance();
		});

		# Inject notify library
		$this->timber->container->singleton('notify', function () {
			return \Timber\Libraries\Notify::instance();
		});

		# Inject plugins library
		$this->timber->container->singleton('plugins', function () {
			return \Timber\Libraries\Plugins::instance();
		});

		# Inject remote library
		$this->timber->container->singleton('remote', function () {
			return \Timber\Libraries\Remote::instance();
		});

		# Inject security library
		$this->timber->container->singleton('security', function () {
			return \Timber\Libraries\Security::instance();
		});

		# Inject storage library
		$this->timber->container->singleton('storage', function () {
			return \Timber\Libraries\Storage::instance();
		});

		# Inject time library
		$this->timber->container->singleton('time', function () {
			return \Timber\Libraries\Time::instance();
		});

		# Inject translator library
		$this->timber->container->singleton('translator', function () {
			return \Timber\Libraries\Translator::instance();
		});

		# Inject twig library
		$this->timber->container->singleton('twig', function () {
			return \Timber\Libraries\Twig::instance();
		});

		# Inject twigext library
		$this->timber->container->singleton('twigext', function () {
			return \Timber\Libraries\TwigExt::instance();
		});

		# Inject upgrade library
		$this->timber->container->singleton('upgrade', function () {
			return \Timber\Libraries\Upgrade::instance();
		});

		# Inject validator library
		$this->timber->container->singleton('validator', function () {
			return \Timber\Libraries\Validator::instance();
		});

		# Inject demo library
		$this->timber->container->singleton('demo', function () {
			return \Timber\Libraries\Demo::instance();
		});

	}
}