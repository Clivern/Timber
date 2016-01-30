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
 * Define App Routes
 *
 * @since 1.0
 */
class Router {

    /**
     * Instance of Timber app
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
     * Define All Routes
     *
     * @since 1.0
     * @access public
     * @return object
     */
    public function defineRoutes()
    {
        $this->frontendRoutes();
        $this->backendendRoutes();
        $this->requestRoutes();
        return $this;
    }

    /**
     * Define Frontend Routes
     *
     * @since 1.0
     * @access private
     */
    private function frontendRoutes()
    {
        $this->timber->get('/sandbox/:plugin', function(){ \Timber\Controllers\Sandbox::instance()->setDepen($this->timber)->renderFilters(); }, function ($plugin = '') {
            \Timber\Controllers\Sandbox::instance()->setDepen($this->timber)->render($plugin);
        })->name('/sandbox');

        # Override Error Handler
        $this->timber->error(function (\Exception $e) {
            # Log Messages
            if( TIMBER_DEBUG_MODE ){
                var_dump($e);
                $this->timber->log->error($e);
            }
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        });

        # Override default not found route
        $this->timber->notFound( function () {
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        });

        # Home Page Route
        $this->timber->get('(/)', function(){ \Timber\Controllers\Home::instance()->setDepen($this->timber)->renderFilters(); }, function () {
            \Timber\Controllers\Home::instance()->setDepen($this->timber)->render();
        })->name('/');

        # Install Route
        $this->timber->get('/install', function(){ \Timber\Controllers\Install::instance()->setDepen($this->timber)->renderFilters(); }, function () {
            \Timber\Controllers\Install::instance()->setDepen($this->timber)->render();
        })->name('/install');

        # 404 Page Route
        $this->timber->get('/404', function(){ \Timber\Controllers\Error::instance()->setDepen($this->timber)->filters404(); }, function () {
            \Timber\Controllers\Error::instance()->setDepen($this->timber)->run404();
        })->name('/404');

        # Maintenance Page Route
        $this->timber->get('/maintenance', function(){ \Timber\Controllers\Error::instance()->setDepen($this->timber)->filtersMaintenance(); }, function () {
            \Timber\Controllers\Error::instance()->setDepen($this->timber)->runMaintenance();
        })->name('/maintenance');

        # 500 Page Route
        $this->timber->get('/500', function(){ \Timber\Controllers\Error::instance()->setDepen($this->timber)->filters500(); }, function () {
            \Timber\Controllers\Error::instance()->setDepen($this->timber)->run500();
        })->name('/500');

        # Login Page Route
        $this->timber->get('/login(/:error)', function(){ \Timber\Controllers\Login::instance()->setDepen($this->timber)->renderFilters(); }, function ($error = '') {
            \Timber\Controllers\Login::instance()->setDepen($this->timber)->render($error);
        })->name('/login');

        # Register Page Route
        $this->timber->get('/register/:hash', function(){ \Timber\Controllers\Register::instance()->setDepen($this->timber)->renderFilters(); }, function ($hash = '') {
            \Timber\Controllers\Register::instance()->setDepen($this->timber)->render($hash);
        })->name('/register');

        # Forgot Password Route
        $this->timber->get('/fpwd(/:hash)', function(){ \Timber\Controllers\Fpwd::instance()->setDepen($this->timber)->renderFilters(); }, function ($hash = '') {
            \Timber\Controllers\Fpwd::instance()->setDepen($this->timber)->render($hash);
        })->name('/fpwd');

        # Verify Page Route
        $this->timber->get('/verify/:email/:hash', function(){ \Timber\Controllers\Verify::instance()->setDepen($this->timber)->renderFilters(); }, function ($email = '', $hash = '') {
            \Timber\Controllers\Verify::instance()->setDepen($this->timber)->render($email, $hash);
        })->name('/verify');

        # Crons Page Route
        $this->timber->get('/crons/:key', function(){ \Timber\Controllers\Crons::instance()->setDepen($this->timber)->filters(); }, function ($key = '') {
            \Timber\Controllers\Crons::instance()->setDepen($this->timber)->render($key);
        })->name('/crons');
    }

    /**
     * Define Backend Routes
     *
     * @since 1.0
     * @access private
     */
    private function backendendRoutes()
    {

        $this->timber->group('/admin', function (){

            # Dashboard Page Router
            $this->timber->get('/dashboard', function(){ \Timber\Controllers\Dashboard::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                 \Timber\Controllers\Dashboard::instance()->setDepen($this->timber)->render();
            })->name('/admin/dashboard');

            # Settings Page Router
            $this->timber->get('/settings', function(){ \Timber\Controllers\Settings::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                \Timber\Controllers\Settings::instance()->setDepen($this->timber)->render();
            })->name('/admin/settings');

            # Profile Page Router
            $this->timber->get('/profile', function(){ \Timber\Controllers\Profile::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                \Timber\Controllers\Profile::instance()->setDepen($this->timber)->render();
            })->name('/admin/profile');


            # Messages Page Router
            $this->timber->group('/messages', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Messages::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Messages::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/messages');

                $this->timber->get('/add', function(){ \Timber\Controllers\Messages::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Messages::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/messages/add');

                $this->timber->get('/view/:message_id', function(){ \Timber\Controllers\Messages::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($message_id = '') {
                    \Timber\Controllers\Messages::instance()->setDepen($this->timber)->render('view', $message_id);
                })->name('/admin/messages/view');

            });

            # Calendar Page Router
            $this->timber->get('/calendar', function(){ \Timber\Controllers\Calendar::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                \Timber\Controllers\Calendar::instance()->setDepen($this->timber)->render();
            })->name('/admin/calendar');

            # Members Page Router
            $this->timber->group('/members', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Members::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Members::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/members');

                $this->timber->get('/add', function(){ \Timber\Controllers\Members::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Members::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/members/add');

                $this->timber->get('/edit/:member_id', function(){ \Timber\Controllers\Members::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($member_id = '') {
                    \Timber\Controllers\Members::instance()->setDepen($this->timber)->render('edit', $member_id);
                })->name('/admin/members/edit');

                $this->timber->get('/view/:member_id', function(){ \Timber\Controllers\Members::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($member_id = '') {
                    \Timber\Controllers\Members::instance()->setDepen($this->timber)->render('view', $member_id);
                })->name('/admin/members/view');

            });

            # Items Page Router
            $this->timber->group('/items', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Items::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Items::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/items');

                $this->timber->get('/add', function(){ \Timber\Controllers\Items::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Items::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/items/add');

                $this->timber->get('/edit/:item_id', function(){ \Timber\Controllers\Items::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($item_id = '') {
                    \Timber\Controllers\Items::instance()->setDepen($this->timber)->render('edit', $item_id);
                })->name('/admin/items/edit');

            });

            # Invoices Page Router
            $this->timber->group('/invoices', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/invoices');

                $this->timber->get('/add', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/invoices/add');

                $this->timber->get('/edit/:invoice_id', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($invoice_id = '') {
                    \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->render('edit', $invoice_id);
                })->name('/admin/invoices/edit');

                $this->timber->get('/view/:invoice_id', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($invoice_id = '') {
                    \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->render('view', $invoice_id);
                })->name('/admin/invoices/view');


                $this->timber->get('/checkout', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->renderFilters('checkout'); }, function () {
                    \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->render('checkout');
                })->name('/admin/invoices/checkout');

            });

            # Estimates Page Router
            $this->timber->group('/estimates', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/estimates');

                $this->timber->get('/add', function(){ \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/estimates/add');

                $this->timber->get('/edit/:estimate_id', function(){ \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($estimate_id = '') {
                    \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->render('edit', $estimate_id);
                })->name('/admin/estimates/edit');

                $this->timber->get('/view/:estimate_id', function(){ \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($estimate_id = '') {
                    \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->render('view', $estimate_id);
                })->name('/admin/estimates/view');

            });

            # Expenses Page Router
            $this->timber->group('/expenses', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/expenses');

                $this->timber->get('/add', function(){ \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/expenses/add');

                $this->timber->get('/edit/:expense_id', function(){ \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($expense_id = '') {
                    \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->render('edit', $expense_id);
                })->name('/admin/expenses/edit');

                $this->timber->get('/view/:expense_id', function(){ \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($expense_id = '') {
                    \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->render('view', $expense_id);
                })->name('/admin/expenses/view');

            });

            # Projects Page Router
            $this->timber->group('/projects', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Projects::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Projects::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/projects');

                $this->timber->get('/add', function(){ \Timber\Controllers\Projects::instance()->setDepen($this->timber)->renderFilters('add'); }, function ($project_id = '') {
                    \Timber\Controllers\Projects::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/projects/add');

                $this->timber->get('/edit/:project_id', function(){ \Timber\Controllers\Projects::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($project_id = '') {
                    \Timber\Controllers\Projects::instance()->setDepen($this->timber)->render('edit', $project_id);
                })->name('/admin/projects/edit');

                $this->timber->get('/view/:project_id', function(){ \Timber\Controllers\Projects::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($project_id = '') {
                    \Timber\Controllers\Projects::instance()->setDepen($this->timber)->render('view', $project_id);
                })->name('/admin/projects/view');

            });

            # Subscriptions Page Router
            $this->timber->group('/subscriptions', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/subscriptions');

                $this->timber->get('/add', function(){ \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/subscriptions/add');

                $this->timber->get('/edit/:subscription_id', function(){ \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->renderFilters('edit'); }, function ($subscription_id = '') {
                    \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->render('edit', $subscription_id);
                })->name('/admin/subscriptions/edit');

                $this->timber->get('/view/:subscription_id', function(){ \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($subscription_id = '') {
                    \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->render('view', $subscription_id);
                })->name('/admin/subscriptions/view');

            });

            # Quotations Page Router
            $this->timber->group('/quotations', function (){

                $this->timber->get('(/)', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->renderFilters('list'); }, function () {
                    \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->render('list');
                })->name('/admin/quotations');

                $this->timber->get('/add', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->renderFilters('add'); }, function () {
                    \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->render('add');
                })->name('/admin/quotations/add');

                $this->timber->get('/submit/:quotation_id', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->renderFilters('submit'); }, function ($quotation_id = '') {
                    \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->render('submit', $quotation_id);
                })->name('/admin/quotations/submit');

                $this->timber->get('/pubsubmit/:quotation_id/:email', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->renderFilters('submit', true); }, function ($quotation_id = '', $email ='') {
                    \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->render('submit', $quotation_id, $email, true);
                })->name('/admin/quotations/pubsubmit');

                $this->timber->get('/view/:quotation_id', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->renderFilters('view'); }, function ($quotation_id = '') {
                    \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->render('view', $quotation_id);
                })->name('/admin/quotations/view');

            });

            # Plugins Page Router
            $this->timber->get('/plugins', function(){ \Timber\Controllers\Plugins::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                \Timber\Controllers\Plugins::instance()->setDepen($this->timber)->render();
            })->name('/admin/plugins');

            # Themes Page Router
            $this->timber->get('/themes', function(){ \Timber\Controllers\Themes::instance()->setDepen($this->timber)->renderFilters(); }, function () {
                \Timber\Controllers\Themes::instance()->setDepen($this->timber)->render();
            })->name('/admin/themes');

            # Logout Page Router
            $this->timber->get('/logout/:nonce', function(){ \Timber\Controllers\Logout::instance()->setDepen($this->timber)->requestFilter(); }, function ($nonce = '') {
                \Timber\Controllers\Logout::instance()->setDepen($this->timber)->request($nonce);
            })->name('/admin/logout');

        });
    }

    /**
     * Define Request Routes
     *
     * @since 1.0
     * @access private
     */
    private function requestRoutes()
    {

        # Frontend Ajax requests routes
        $this->timber->group('/request', function (){

            $this->timber->group('/frontend', function (){

                # Frontend direct requests routes
                $this->timber->group('/direct', function (){

                    # Social Login Ajax Requests
                    $this->timber->get('/auth/:provider', function(){ \Timber\Controllers\Login::instance()->setDepen($this->timber)->socialAuthFilter(); }, function ($provider = '') {
                        \Timber\Controllers\Login::instance()->setDepen($this->timber)->socialAuth($provider);
                    })->name('/request/frontend/direct/auth');

                    # Helpers Ajax Requests
                    $this->timber->get('/helpers/:helper', function(){ \Timber\Controllers\Helpers::instance()->setDepen($this->timber)->filters(); } ,function ($helper = '') {
                        \Timber\Controllers\Helpers::instance()->setDepen($this->timber)->render($helper);
                    })->name('/request/frontend/direct/helpers');

                });

                # Frontend ajax requests routes
                $this->timber->group('/ajax', function (){

                    # Install Ajax Requests
                    $this->timber->post('/install', function(){ \Timber\Controllers\Install::instance()->setDepen($this->timber)->requestFilters(); } ,function () {
                        \Timber\Controllers\Install::instance()->setDepen($this->timber)->requests();
                    })->name('/request/frontend/ajax/install');

                    # Login Ajax Requests
                    $this->timber->post('/login', function(){ \Timber\Controllers\Login::instance()->setDepen($this->timber)->requestFilters(); } ,function () {
                        \Timber\Controllers\Login::instance()->setDepen($this->timber)->requests();
                    })->name('/request/frontend/ajax/login');

                    # Register Ajax Requests
                    $this->timber->post('/register', function(){ \Timber\Controllers\Register::instance()->setDepen($this->timber)->requestFilters(); } ,function () {
                        \Timber\Controllers\Register::instance()->setDepen($this->timber)->requests();
                    })->name('/request/frontend/ajax/register');

                    # FPWD Ajax Requests
                    $this->timber->post('/fpwd', function(){ \Timber\Controllers\Fpwd::instance()->setDepen($this->timber)->requestFilters(); } ,function () {
                        \Timber\Controllers\Fpwd::instance()->setDepen($this->timber)->requests();
                    })->name('/request/frontend/ajax/fpwd');

                });
            });

            # Backend requests router
            $this->timber->group('/backend', function (){

                $this->timber->group('/direct', function (){

                    $this->timber->get('/download/:iden/:file_id/:hash', function(){ \Timber\Controllers\Download::instance()->setDepen($this->timber)->requestFilters(); } ,function ($iden = '', $file_id = '', $hash = '') {
                        \Timber\Controllers\Download::instance()->setDepen($this->timber)->requests($iden, $file_id, $hash);
                    })->name('/request/backend/direct/download');

                    # Checkout pay requests
                    $this->timber->post('/pay/stripe', function(){ \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->renderFilters(); } ,function () {
                        \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->paymentRequest('stripe');
                    })->name('/request/backend/direct/pay');

                    $this->timber->get('/pay/paypal', function(){ \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->renderFilters(); } ,function () {
                        \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->paymentRequest('paypal');
                    })->name('/request/backend/direct/pay');

                    # Checkout success requests
                    $this->timber->get('/success_pay/:provider', function(){ \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->renderFilters(); } ,function ($provider = 'paypal') {
                        \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->successPayment($provider);
                    })->name('/request/backend/direct/success_pay');

                    # Checkout cancel requests
                    $this->timber->get('/cancel_pay/:provider', function(){ \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->renderFilters(); } ,function ($provider = 'paypal') {
                        \Timber\Controllers\Checkout::instance()->setDepen($this->timber)->cancelPayment($provider);
                    })->name('/request/backend/direct/cancel_pay');

                });

                # Ajax backend requests routes
                $this->timber->group('/ajax', function (){

                    # Profile Page Requests
                    $this->timber->post('/profile/:form', function(){ \Timber\Controllers\Profile::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Profile::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/profile');

                    # Verify Email Requests
                    $this->timber->post('/verify/:form', function(){ \Timber\Controllers\Verify::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Verify::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/verify');

                    # Settings Page Requests
                    $this->timber->post('/settings/:form', function(){ \Timber\Controllers\Settings::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Settings::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/settings');

                    # Messages Page Requests
                    $this->timber->post('/messages/:form', function(){ \Timber\Controllers\Messages::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Messages::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/messages');

                    # Invoices Page Requests
                    $this->timber->post('/invoices/:form', function(){ \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Invoices::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/invoices');

                    # Subscriptions Page Requests
                    $this->timber->post('/subscriptions/:form', function(){ \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Subscriptions::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/subscriptions');

                    # Quotations Page Requests
                    $this->timber->post('/quotations/:form', function(){ \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Quotations::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/quotations');

                    # Estimates Page Requests
                    $this->timber->post('/estimates/:form', function(){ \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Estimates::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/estimates');

                    # Expenses Page Requests
                    $this->timber->post('/expenses/:form', function(){ \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Expenses::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/expenses');

                    # Members Page Requests
                    $this->timber->post('/members/:form', function(){ \Timber\Controllers\Members::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Members::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/members');

                    # Projects Page Requests
                    $this->timber->post('/projects/:form', function(){ \Timber\Controllers\Projects::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Projects::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/projects');

                    # Items Page Requests
                    $this->timber->post('/items/:form', function(){ \Timber\Controllers\Items::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Items::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/items');

                    # Themes Page Requests
                    $this->timber->post('/themes/:form', function(){ \Timber\Controllers\Themes::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Themes::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/themes');

                    # Plugin Page Requests
                    $this->timber->post('/plugins/:form', function(){ \Timber\Controllers\Plugins::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Plugins::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/plugins');

                    # Upload Requests
                    $this->timber->post('/upload/:form', function(){ \Timber\Controllers\Upload::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Upload::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/upload');

                    # Realtime Requests
                    $this->timber->post('/realtime/:form', function(){ \Timber\Controllers\Realtime::instance()->setDepen($this->timber)->requestFilters(); } ,function ($form = '') {
                        \Timber\Controllers\Realtime::instance()->setDepen($this->timber)->requests($form);
                    })->name('/request/backend/ajax/realtime');


                });
            });
        });
    }
}