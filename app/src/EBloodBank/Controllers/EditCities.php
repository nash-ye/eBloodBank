<?php
/**
 * Edit Cities Controller
 *
 * @package EBloodBank
 * @subpackage Controllers
 * @since 1.0
 */
namespace EBloodBank\Controllers;

use EBloodBank as EBB;
use EBloodBank\Notices;
use EBloodBank\Views\View;

/**
 * @since 1.0
 */
class EditCities extends ViewCities
{
    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        if (EBB\isCurrentUserCan('edit_cities')) {
            $this->doActions();
            $this->addNotices();
            $view = View::forge('edit-cities', array(
                'cities' => $this->getQueriedCities(),
                'pagination.total' => $this->getPagesTotal(),
                'pagination.current' => $this->getCurrentPage(),
            ));
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
        switch (filter_input(INPUT_GET, 'action')) {
            case 'delete':
                $this->doDeleteAction();
                break;
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function addNotices()
    {
        if (filter_has_var(INPUT_GET, 'flag-deleted')) {
            $deleted = (int) filter_input(INPUT_GET, 'flag-deleted');
            Notices::addNotice('deleted', sprintf(n__('%d city permanently deleted.', '%d cities permanently deleted.', $deleted), $deleted), 'success');
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doDeleteAction()
    {
        if (EBB\isCurrentUserCan('delete_city')) {

            $cityID = filter_input(INPUT_GET, 'id');

            if (! EBB\isValidID($cityID)) {
                return;
            }

            $em = main()->getEntityManager();
            $city = $em->getReference('Entities:City', $cityID);

            $donorRepository = $em->getRepository('Entities:Donor');
            $donorsCount = $donorRepository->countBy(array('city' => $city));

            if ($donorsCount > 0) {
                return;
            }

            $em->remove($city);
            $em->flush();

            EBB\redirect(
                EBB\addQueryArgs(
                    EBB\getEditCitiesURL(),
                    array('flag-deleted' => 1)
                )
            );

        }
    }
}
