<?php
/**
 * Edit district page controller class file
 *
 * @package    eBloodBank
 * @subpackage Controllers
 * @since      1.0
 */
namespace EBloodBank\Controllers;

use InvalidArgumentException;
use EBloodBank as EBB;
use EBloodBank\Notices;
use EBloodBank\Views\View;
use Aura\Di\ContainerInterface;

/**
 * Edit district page controller class
 *
 * @since 1.0
 */
class EditDistrict extends Controller
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
    public function __construct(ContainerInterface $container, $id)
    {
        parent::__construct($container);
        if (EBB\isValidID($id)) {
            $districtRepository = $container->get('entity_manager')->getRepository('Entities:District');
            $this->district = $districtRepository->find($id);
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        $currentUser = EBB\getCurrentUser();

        if (! $currentUser || ! $currentUser->canEditDistricts()) {
            View::display('error-403');
            return;
        }

        if (! $this->isQueriedDistrictExists()) {
            View::display('error-404');
            return;
        }

        $district = $this->getQueriedDistrict();

        if (! $currentUser->canEditDistrict($district)) {
            View::display('error-403');
            return;
        }

        $this->doActions();
        $this->addNotices();
        View::display('edit-district', [
            'district' => $district,
        ]);
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
        if (filter_has_var(INPUT_GET, 'flag-edited')) {
            Notices::addNotice('edited', __('District edited.'), 'success');
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doSubmitAction()
    {
        try {
            $session = $this->getContainer()->get('session');
            $sessionToken = $session->getCsrfToken();
            $actionToken = filter_input(INPUT_POST, 'token');

            if (! $actionToken || ! $sessionToken->isValid($actionToken)) {
                return;
            }

            $currentUser = EBB\getCurrentUser();
            $district = $this->getQueriedDistrict();

            if (! $currentUser || ! $currentUser->canEditDistrict($district)) {
                return;
            }

            $em = $this->getContainer()->get('entity_manager');
            $cityRepository = $em->getRepository('Entities:City');

            // Set the district name.
            $district->set('name', filter_input(INPUT_POST, 'district_name'), true);

            // Set the district city ID.
            $district->set('city', $cityRepository->find(filter_input(INPUT_POST, 'district_city_id')));

            $em->flush($district);

            EBB\redirect(
                EBB\addQueryArgs(
                    EBB\getEditDistrictURL($district->get('id')),
                    ['flag-edited' => true]
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

    /**
     * @return bool
     * @since 1.2
     */
    protected function isQueriedDistrictExists()
    {
        $district = $this->getQueriedDistrict();
        return ($district && $district->isExists());
    }
}
