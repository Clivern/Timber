<?php
/**
 * Home - Timber Landing Page
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Home
 */

global $timber;
$home_url = rtrim( $timber->config('request_url'), '/index.php' );
$assets_url = $home_url . TIMBER_PLUGINS_DIR . '/home/assets';
?>
<html>
	<head>
		<title><?php echo $timber->config('_site_title'); ?></title>

		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Montserrat:300,400,600,700">
		<!--
		<script src="<?php echo $assets_url ?>/scripts.js"></script>
		<script src="<?php echo $assets_url ?>/styles.js"></script>
		-->
		<style type="text/css">
	    	html{font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif !important;}
	    	body{font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif !important;}
			.main-container{ background-color: #000; padding: 200px; color: white; font-size: 50px; font-weight: bold; margin: 22px; height:40%; }
		</style>

	</head>
	<body>

		<div class="main-container">
			<a href="<?php echo $home_url . "/login"; ?>">
				<img width="200px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANEAAADRCAYAAABSOlfvAAAD0klEQVR42u3dy23jMBRA0exTRsqYLoyU4TZSRsqYNgx4LW9Eyy3MVoO3C/JBZJE2RfMcwA2Qvnnxj3x6AgAAcp3P53erACvNH1gNyIxot9v9syKwMiDTCAoEJCQoFJGQIDOg4B07+EG8eTAvZLVg5RQSEhQKSEjwQbzGWRvROI6vVhBTKJMVREAFWEm6lFJ6mwuyophCmY7H47NVRUCmEfzudDr9mW/ICmMKZZqm6WCVEZBpBF/FC//5jqw4ppCQoG5AIaW0t/r4N840whSqzy4gICEhovqGYXixIwjINEJAQoLmI3KaKgIyjXhk1xx7JSRodAoJCQEV5DRVNiPn2CvTCBqdQkJCQELiEZQ+9qqmOPvBjmIKmUYISEh04tbHXvlaEKaQaQT9BiQkbip+2DZ3xI5jCvlaEAIyjRCRkKD3gISEgApxyTIiMo0QkJBoTEvnJQgJU0hEiEhEcDOXy+VvzSe8M7gx1UwNEBGICEQEIhIRIhIRiAhEBCISESISEYgIRAQiEhEiEhGICEQEIhIRPZim6bCFn3M7U5tmpwCmY/Nqn2/A91yybAphGgkIIfGLeCHtKbp9Llk2hTCNBISQ+CTu0PGUFBKmUJd8ICwgTKO2xYd3noJCwhRCSAKiDJcsiwjTSEAISUAICREhIgEhpK2Kb/56WgkJU4grxa+UPfsFhGlU11ZOzkFIphBCEhC9SyntVXEF5yVgGplCCElACKlJzktgiWEYXtRiCmEaiYi6nO3tj4G/1LDlkKw+IhIRiAhEBCICEYkIEYkIRAQiAhGJCBGJCEQEIgIRiQgRiQhEBCICAS0Ut2jYhcY5fBGHnGSIAyRsE0Jq+F8ZaDok24KQRISIBARNhmQbEJKI6FzVS5YtP6ZRhvhk3NIjJFMI6oRkqXlUd7lkeZqmg6XGNDKFoE5IlhYh+YkDLFb8kmVLimkkIKgTUrzlZxnpWbwjbQpBrWlk6SAjJOclQGZIlgu+io96BAT3mEaWCTJCsjyQEZJlgeXizTcRQclpZDkgIyRf7YEC/9bF+98e6x9x7NKWNtae3O/hLhiXfIGIRISIRAQiAhGBiESEiEQEIgIRgYisPiISEYgIRAQiAhGJCBGJCEQEIgIRrRRXgcTv/uOmt9yH3aTLiEw3RCQiRCQiEJGIEJGIEJGIEJGIQEQiQkQiQkQiQkQigqullN5EBKaRiBCRiBCRiOhZXEMoIjCNRISIRISQRISIRATdh2QHEZKIeATjOL6KCDK1+tmRnWNTUkp7EUFnr5HsFkISEWISETQdk53BO3kiAgAAANiu/6gdPDH2Xi1UAAAAAElFTkSuQmCC" />
			</a>
		</div>

	</body>
</html>