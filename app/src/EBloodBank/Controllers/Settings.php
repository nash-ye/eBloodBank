<?php
/**
 * Settings Page Controller
 *
 * @package EBloodBank
 * @subpackage Controllers
 * @since 1.0
 */
namespace EBloodBank\Controllers;

use InvalidArgumentException;
use EBloodBank as EBB;
use EBloodBank\Options;
use EBloodBank\Notices;
use EBloodBank\Views\View;

/**
 * @since 1.0
 */
class Settings extends Controller
{
    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        if (EBB\isCurrentUserCan('edit_settings')) {
            $this->doActions();
            $this->addNotices();
            $view = View::forge('settings');
        } else {
            $view = View::forge('error-403');
        }
        $view();
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doActions()
    {
        switch (filter_input(INPUT_POST, 'action')) {
            case 'save_settings':
                $this->doSaveAction();
                break;
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function addNotices()
    {
        if (filter_has_var(INPUT_GET, 'flag-saved')) {
            Notices::addNotice('saved', __('Settings saved.'), 'success');
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doSaveAction()
    {
        try {

            /* General Options */
            Options::submitOption('site_url', filter_input(INPUT_POST, 'site_url'), true);
            Options::submitOption('site_name', filter_input(INPUT_POST, 'site_name'), true);
            Options::submitOption('site_slogan', filter_input(INPUT_POST, 'site_slogan'), true);
            Options::submitOption('site_locale', filter_input(INPUT_POST, 'site_locale'), true);
            Options::submitOption('site_email', filter_input(INPUT_POST, 'site_email'), true);

            /* Accounts Options */
            Options::submitOption('self_registration', filter_input(INPUT_POST, 'self_registration'), true);
            Options::submitOption('default_user_role', filter_input(INPUT_POST, 'default_user_role'), true);
            Options::submitOption('default_user_status', filter_input(INPUT_POST, 'default_user_status'), true);

            /* Reading Options */
            Options::submitOption('entities_per_page', filter_input(INPUT_POST, 'entities_per_page'), true);

            EBB\redirect(
                 EBB\addQueryArgs(
                    EBB\getSettingsURL(),
                    array('flag-saved' => true)
                 )
             );

        } catch (InvalidArgumentException $ex) {
            Notices::addNotice('invalid_option', $ex->getMessage());
        }
    }
}
