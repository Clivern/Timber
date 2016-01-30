Overview
=========
Timber is a Full-Featured Freelancer Platform. It comes with many features to extend both Appearance and Functionality. Some of these features are

 * Easy to Install and Use.
 * Both Child and Full Themes Support.
 * Custom Plugins Support.
 * Powerful Dashboard For Both Admins, Staff and Clients.
 * Memebers, Projects, Invoices, Estimates, Expenses, Quotations, Subscriptions, Items,...and much more.
 * Paypal and Stripe Payments Gateways supported.
 * Extreme Security.
 * Many Settings To Build Your Desired App.

*Latest Version: 1.0*

Installation
------------

Installing timber performed through five steps. Let's explore them one by one.

### Step One

After downloading timber from codecanyon, Uncompress `timber.zip` archive. You will find 5 folders.

* `Source Code` : Timber Application code that you will upload.
* `Documentation` : Help Files That you currently read.
* `Client File` : If Your server face issues with `Automatic Installer`, You will need to create `Client File` Manually.
* `Translation` : Language files if you like to translate timber [See Translation Section for details](#translation).
* `Themes Boilerplate` : A development theme if you desired to create themes for timber and have the skills needed [See Developer Section for details](#developers).

Now Open your `FTP Client` like `FileZilla` or `SSH Client` and Upload the content of `Source Code` folder to your `Remote Host` base directory or sub-directory.

### Step Two

In this step, timber creates your `client.php` file which contains base application configurations.

Just visit `http://site_address/`. and timber will show a form to enter the following data:

* `MySQL Host Name`: MySQL host name.
* `MySQL Database Name`: MySQL database name.
* `MySQL Database Username`: MySQL database username.
* `MySQL Database Password`: MySQL database password.
* `Home URL`: Your App home URL (eg.`http://timber.com/index.php`).

Now Click `Next` Button to Submit Form.

If You faced any `Issues` during this step, This may be due to `File Permissions` or Server Prevent `File Edits`. so Let's configure `Timber` Manually:

* Open `client.php` file that exists in `Client File` folder in any text editor.
* Insert All data that you already gathered `Site URL`,`Database Name`...etc. You will find help text inside `client.php`file to guide you.
* Then Upload `client.php` to your `Remote Host Base/timber/` directory with replacing.
* Please Ensure that Your `client.php` inside the `timber` directory beside `client-default.php` and other files. Which means you placed the file in the right place.
* Then visit `http://site_address/` again.

`Please Note That:` Timber won't move to step three Until it makes sure you entered the correct database configurations. So If you stuck in step two, This means you entered wrong database configurations.

### Step Three

If you configured timber correctly, Timber will show another form to enter the following data:

* `App Name`: Insert Application Name (eg.`Timber`).
* `App Email`: Insert Application Email.

Then Click `Next`. You will be redirected automatically to `Step Four`.

### Step Four

Timber will show another form to enter the following data:

* `Admin Username`: Administrator Account Username.
* `Admin Email`: Administrator Account Email (You can use the same email of `App Email`).
* `Admin Password`: Administrator Account Password.
* `Verify Password Again`: Insert Administrator Account Password Again.

Then Click `Finish`. You will be redirected automatically to `Login Page`. Now Timber Installed. Explore it and Return at any time and read [`Moderation`](#moderation) Section. It will help you.

Moderation
==========

Once you installed `Timber`, I think it will be a simple task to moderate and get your way with `Timber`

So through this section, I will introduce specific features that `Timber` can offer and may be a little tough to understand and Also I will add further info at each update according to frequently asked questions.


Settings
--------

#### What is `Maintenance Mode`:
* When you activate `Maintenance Mode`, Application will allow admins only to `Login` and both clients and staff can't login.

System Alerts
-------------

Timber ships with smart alerting system to alarm you of any tasks you must perform.

### Timber Need to be Updated. Read Documentation .. etc
This alert shown to admins if new version of timber is released, Explore [Upgrade](#upgrade) Section for more info about updating steps.

### Timber installation page should be blocked .. etc
`Don't worry!` Blocking Installation page for further security and performed automatically after installation but it seems your server
Prevent `client.php` editing during installation. Any way do the following steps.

* Download `client.php` file exist in `/timber` folder.
* Open the file in any text editor.
* Add the following lines to the end of this file.

```
/**
 * Whether timber installed
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_INSTALLED', true);
```

* Upload the file again with replacing.

Developers
==========

Timber uses [Twig Templating Engine](http://twig.sensiolabs.org/) So Please Read the following tutorial To be familiar with `Twig` syntax.

* [Twig for Template Designers.](http://twig.sensiolabs.org/doc/templates.html)

`Please Note:` Don't change any twig block in template files (`.tpl.twig` and `.sec.twig`) Until you know exactly the result or you are a professional. `Twig` blocks appears like the following:

```
{% include 'header.sec.twig' with {'tpl': 'expenses-edit', 'model' : 'expenses-edit'} %}
```

```
{% if expense_rec_type != 8 %}
```

```
{{ expense_terms.description|e }}
```

Child Themes
------------

Timber allows you to override all of the templates (`.tpl.twig` and `.sec.twig`) of the `default` theme.

You can override `default` theme templates by doing the following:

* Copy the template file you wish to override from `themes/default` folder.
* Paste this file in `themes/child` folder.
* Modify the pasted template file to your liking.
* Activate `Child` theme from `Themes` Page in timber dashboard.


Full Themes
-----------

### Themes Anatomy

Timber themes consists of template files (`.tpl.twig` and `.sec.twig`). The following rules show you how timber selects template file to render:

* For example when visitor visits `http://site_url/login`, One of timber controllers process the request,
* And finally timber checks for the `Current Active Theme` to render the required template file which in this case is `public/active theme/login.tpl.twig`.
* If the `Current Active Theme` is the `default` theme, timber renders `public/default/login.tpl.twig`.
* If the `Current Active Theme` is `x` theme, Timber checks if `public/x/login.tpl.twig` exists to render otherwise it renders `public/default/login.tpl.twig`.
* This means that `Child Themes` and `Full Themes` are similar except that `Full Themes` will override all template files But `Child Theme` used to override one or two template files according to client needs.

Now Let's Explore Building Themes Process.

### Building Theme

Actually I can't discuss everything I just trying to discuss the bolts and nuts of building new theme and the rest will be on you
`Awesome Developer`. Here's the steps:

* You must first install `Timber` in `Local` or `Remote Host`. You should follow [installation steps](#overview-installation) discussed before.
* Then copy `Themes Boilerplate` folder and rename it with a nice name (lowercase with no white space) for example `mytheme`.
* Open `info.php` inside `mytheme` folder to define theme data as follows:

```
return array(
	'author' => 'Your Brand Name',
	'author_url' => 'http://your_site.com',
	'theme_version' => '1.0',
	'theme_name' => 'My Theme',
	'theme_description' => 'Just a New Nice Theme',
	'theme_slug' => 'mytheme',
	'skins' => array(
		'skin1' => 'Skin 1',
		'skin2' => 'Skin 2',
		'skin3' => 'Skin 3',
		'skin4' => 'Skin 4',
		/* and so on */
	),
);
```

* Now You defined your theme which supports four additional skins.
* Open `mytheme/assets/skins/` folder and begin to build other themes `skin1`, `skin2`, `skin3`, `skin4`.
* Open `mytheme/assets/css/styles.css` and perform changes to base styles.
* Open any of the templates files in `mytheme/` and Perform any changes related to styling like adding new `CSS Classes` or Adding `Inline CSS`.
* Once You need to debug theme, Just activate your theme from dashboard (`Themes` Page) and Your template files will override `default` theme template files.
* You can also Debug skins by Switching between them from `Themes` Page.
* During building your theme, You will need to load CSS, JS and Images files from your theme assets Folder so Use the following functions in You templates.

```
//get Relative Path URL from themes folder
{{ themes_url('/relative/path') }}

//get Relative Path URL from default theme folder
{{ default_theme_url('/relative/path') }}

//get Relative Path URL from default theme assets folder
{{ default_theme_assets_url('/relative/path') }}

//get Relative Path URL from your theme folder
{{ theme_url('/relative/path') }}

//get Relative Path URL from your theme assets folder
{{ theme_assets_url('/relative/path') }}
```

* Timber `default` theme is the base theme so Read its code to fully understand everything. Also you are free to use it to build your theme.


Payments Gateways
=================
To Activate `Paypal` and/or `Stripe`, You will need to get needed credentials.

Paypal
------
To get needed credentials, Follow these steps:

* Log in to your PayPal account.
* Click the My Account tab.
* Click the Profile tab. If you haven't already done so, you need to verify your account before requesting API credentials.
* Click Request API credentials under Account information.
* Click Set up PayPal API credentials and permissions under Option 1.
* Click Request API Credentials.
* Click Request API signature.
* Click Agree and Submit.
* Copy and paste the `API username`, `Password`, and `Signature` into timber settings page.

![Capture](assets/img/pay1.png)

![Capture](assets/img/pay2.png)

![Capture](assets/img/pay3.png)

Stripe
------
To get needed credentials, Follow these steps:

* Log in to your Stripe account.
* You can switch between live and test mode.
* Click the Your Account tab.
* Then Click Account Settings.
* Click API keys.
* Copy `Live Secret Key` and paste in timber settings page.

![Capture](assets/img/str1.png)

![Capture](assets/img/str2.png)

![Capture](assets/img/str3.png)


Social Login
============

To Activate `Social Login`, You will need to create `App` at each provider and get needed credentials.

Facebook
--------
To Get Facebook `App ID` and `App Secret`. Do the following steps:

* Go to [Facebook Developers Page.](https://developers.facebook.com/apps)

![Capture](assets/img/fb1.png)


* Click on `Create a New App` and add App Name and Namespace.
* Then Click `Create App ID`.

![Capture](assets/img/fb2.png)


* You will be redirected to page containing `App ID` and `App Secret`.

![Capture](assets/img/fb3.png)

* Please Ensure that You Performed All Previous Step Correctly. Any ignored step or wrong step will cause issues to `Facebook Login.`

Twitter
-------
To Get Twitter `App Key` and `App Secret`. Do the following steps:

* Go to [Twitter Apps Page.](https://apps.twitter.com)

![Capture](assets/img/tw1.png)


* Click on `Create New App` and then fill app data.

![Capture](assets/img/tw2.png)

* Then Click `Create Your Twitter Application`

* You will be redirected to App page. Visit `Keys and Access Tokens` Tab to get `App Key` and `App Secret`

![Capture](assets/img/tw3.png)


* Note that `App Key` is the `Consumer Key` or `API Key` And `App Secret` is the `Consumer Secret` or `API Secret`.

* Please Ensure that You Performed All Previous Step Correctly. Any ignored step or wrong step will cause issues to `Twitter Login.`


Google
------
To Get Google `App Key` and `App Secret`. Do the following steps:

* Go to [Google Developers Console.](https://console.developers.google.com/).

![Capture](assets/img/go1.png)


* Click on `Create Project`, enter a name and a project ID, or accept the defaults, and click Create.

![Capture](assets/img/go2.png)


* Go to `APIs & auth` - `Credentials` sub section and click on `Create new Client ID`. a Form will be shown to insert App Info then after submit you will get `Client ID` and `Client secret`.

![Capture](assets/img/go3.png)

![Capture](assets/img/go4.png)

![Capture](assets/img/go5.png)


* Go to `APIs & auth` - `APIs` sub section and search for `Google+ API` and Enable.

![Capture](assets/img/go6.png)


* Go to `APIs & auth` - `APIs` sub section and search for `Contacts API` and Enable.

![Capture](assets/img/go7.png)


* `Note that` You must create `Google+` account attached to this google account.

* Please Ensure that You Performed All Previous Step Correctly. Any ignored step or wrong step will cause issues to `Google Login.`

FAQs
====

### How To Activate Pretty URLs?
* Open `timber/client.php` file and set `TIMBER_MOD_REWRITE` to `true`. It must be look like the following:
```
/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_MOD_REWRITE', true);
```

* Also make sure that `.htaccess` file exist in app root and contain the following:
```
# BEGIN Timber
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END Timber
```

### How To Deactivate Pretty URLs?
* Open `timber/client.php` file and set `TIMBER_MOD_REWRITE` to `false`. It must be look like the following:
```
/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_MOD_REWRITE', false);
```

* Also delete `.htaccess` file exist in app root.

### How to fix timber folders and files permissions?
On computer filesystems, different files and directories have permissions that specify who and what can read, write, modify and access them. This is important because Timber may need access to write to files in your `cache`, `storage`, `plugins`, `backups`, `themes` and `logs` directory to enable certain functions also to edit `timber/client.php`, `timber/client-sample.php` and `timber/client-default.php` files.

```
sudo chmod -R 755 /var/www/html/timber/cache;
sudo chmod -R 755 /var/www/html/timber/storage;
sudo chmod -R 755 /var/www/html/timber/plugins;
sudo chmod -R 755 /var/www/html/timber/backups;
sudo chmod -R 755 /var/www/html/timber/themes;
sudo chmod -R 755 /var/www/html/timber/logs;

sudo chown -R www-data:www-data /var/www/html/timber/cache;
sudo chown -R www-data:www-data /var/www/html/timber/storage;
sudo chown -R www-data:www-data /var/www/html/timber/plugins;
sudo chown -R www-data:www-data /var/www/html/timber/backups;
sudo chown -R www-data:www-data /var/www/html/timber/themes;
sudo chown -R www-data:www-data /var/www/html/timber/logs;

sudo chown -R www-data:www-data /var/www/html/timber/timber/client.php;
sudo chown -R www-data:www-data /var/www/html/timber/timber/client-sample.php;
sudo chown -R www-data:www-data /var/www/html/timber/timber/client-default.php;
```

### How to enable or disable debug mode?
* Open `timber/client.php` file and set `TIMBER_DEBUG_MODE` to `true` or `false`. So it should be look like the following:

```
/**
 * Debug mode active
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_DEBUG_MODE', true);
```

```
/**
 * Debug mode inactive
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_DEBUG_MODE', false);
```

Translation
===========

If You interested in Translating Timber. You can translate Files exist in `Translation` folder and send Translated Files to `support@clivern.com`. You can use [Poedit](http://poedit.net), It really Helps.

How To Translate
----------------
The basic translation screen consists of a list of strings for translation and two separate windows for the original string and the translated string.

The process is easy:

* Click on a string from the pool.
* Add the translation in the translation modal (there could be developer notes in the modal on the right, so don’t forget to look for those).
* Save your translations.
* Translated strings are shown next to the original in the pool of strings and you can keep track of your progress in the bottom of the translation screen.
* When you finish send the file to `support@clivern.com` and we will do the rest.

Requirements
============

* PHP 5.3+.
* MySQL 5.1.7+.
* Apache `mod_rewrite` Module.
* PHP `PDO` Extension.
* PHP `pdo_mysql` Extension.
* PHP `mysql` Extension.
* PHP `mysqli` Extension.
* PHP `mbstring` Extension.
* PHP `dom` Extension.
* PHP `curl` Extension.
* PHP `mcrypt` Extension (Optional For Extended Security).
* PHP `gd` Extension (Optional For Captcha).
* PHP `zlib` Extension (Optional For Compressed Backups).
* Cronjob (Optional For Scheduled Tasks).

Upgrade
=======

Timber will show notification to all administrators if new updates released.

### How to upgrade:
* If update notification shown in your dashboard, Download latest version from codecanyon.
* Login to timber dashboard, Go to `Settings` Page then `Backups` Tab and Perform Database Backup.
* Don't forget to backup any changes you performed to timber source code.
* Go to your FTP client or SSH client and upload all file and folders in `Source Code` directory to remote host with replacing.

### Changelog

*Latest Version: 1.0*

```
Version 1.0
-----------
> Initial Release.
```

Credits
=======

I've used the following images, icons or other files as listed.

* jQuery: MIT License.
* Semantic UI: MIT License.
* Fontello: MIT License
* Modernizr: MIT License
* Pace jQuery Plugin: MIT License
* Toastr jQuery Plugin: MIT License
* Font Awesome: MIT License.
* Dropzone.js: MIT License.
* DataTables: MIT License.
* DateTimePicker: MIT License.
* Moment.js: MIT License.
* FullCalendar: MIT License.
* AlertifyJS: MIT License.
* WysiBB: MIT License.
* navgoco: MIT License.
* decimal.js: MIT License.
* Slim PHP Framework (c) 2012 Josh Lockhart.
* Hybridauth PHP Library (c) 2009-2014, HybridAuth authors.
* Idiorm PHP Library (c) 2010, Jamie Matthews.
* Twig PHP Library (c) 2009-2014 by the Twig Team.
* Zend Translate PHP Library (c) 2005-2015 Zend Technologies.
* codeguy-upload (c) Josh Lockhart
* Omnipay (c) 2012-2013 Adrian Macneil
* PHPMailer (c) 2016
* tcpdf (c) 2005-2015 tcpdf.org.
* HTMLawd PHP Library (c) Santosh Patnaik.
* Flatdoc © 2013, 2014, Rico Sta. Cruz.

Support
=======

Thank you so much for purchasing timber. I'd be glad to [help you if you have any questions relating to timber](http://clivern.com/support). No guarantees, but I'll do my best to assist.

Item support includes:

* Availability of the author to answer questions.
* Answering technical questions about item’s features.
* Assistance with reported bugs and issues.
* Help with included 3rd party assets.

However, item support does not include:

* Customization services.
* Installation services.

> "Please, consider taking a few seconds to rate timber, it will help us understand your
> satisfaction and give us motivation to do even better."
> --Clivern