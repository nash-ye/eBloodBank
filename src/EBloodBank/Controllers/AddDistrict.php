<?php
/**
 * Add district page controller class file
 *
 * @package    EBloodBank
 * @subpackage Controllers
 * @since      1.0
 */
namespace EBloodBank\Controllers;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use EBloodBank as EBB;
use EBloodBank\Notices;
use EBloodBank\Models\District;
use Psr\Container\ContainerInterface;

/**
 * Add district page controller class
 *
 * @since 1.0
 */
class AddDistrict extends Controller
{
    /**
     * @var \EBloodBank\Models\District
     * @since 1.0
     */
    protected $district;

    /**
     * @return void
     * @since 1.0
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->district = new District();
    }

    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        if ($this->hasAuthenticatedUser() && $this->getAcl()->isUserAllowed($this->getAuthenticatedUser(), 'District', 'add')) {
            $this->doActions();
            $this->addNotices();
            $district = $this->getQueriedDistrict();
            $view = $this->viewFactory->forgeView('add-district', [
                'district' => $district,
            ]);
        } else {
            $view = $this->viewFactory->forgeView('error-403');
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
            case 'submit_district':
                $this->doSubmitAction();
                break;
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function addNotices()
    {
        if (filter_has_var(INPUT_GET, 'flag-added')) {
            Notices::addNotice('added', __('District added.'), 'success');
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doSubmitAction()
    {
        try {
            if (! $this->hasAuthenticatedUser() || ! $this->getAcl()->isUserAllowed($this->getAuthenticatedUser(), 'District', 'add')) {
                return;
            }

            $sessionToken = $this->getSession()->getCsrfToken();
            $actionToken = filter_input(INPUT_POST, 'token');

            if (! $actionToken || ! $sessionToken->isValid($actionToken)) {
                return;
            }

            $district = $this->getQueriedDistrict();
            $cityRepository = $this->getEntityManager()->getRepository('Entities:City');

            // Set the district name.
            $district->set('name', filter_input(INPUT_POST, 'district_name'), true);

            // Set the district city ID.
            $district->set('city', $cityRepository->find(filter_input(INPUT_POST, 'district_city_id')));

            // Set the creation date.
            $district->set('created_at', new DateTime('now', new DateTimeZone('UTC')));

            // Set the originator user.
            $district->set('created_by', $this->getAuthenticatedUser());

            $this->getEntityManager()->persist($district);
            $this->getEntityManager()->flush();

            $added = $district->isExists();

            EBB\redirect(
                EBB\addQueryArgs(
                    EBB\getAddDistrictURL(),
                    ['flag-added' => $added]
                )
            );
        } catch (InvalidArgumentException $ex) {
            Notices::addNotice('invalid_district_argument', $ex->getMessage());
        }
    }

    /**
     * @return \EBloodBank\Models\District
     * @since 1.0
     */
    protected function getQueriedDistrict()
    {
        return $this->district;
    }
}
