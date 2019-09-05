<?php
/**
 * Delete donor page controller class file
 *
 * @package    EBloodBank
 * @subpackage Controllers
 * @since      1.0
 */
namespace EBloodBank\Controllers;

use EBloodBank as EBB;
use Psr\Container\ContainerInterface;

/**
 * Delete donor page controller class
 *
 * @since 1.0
 */
class DeleteDonor extends Controller
{
    /**
     * @var \EBloodBank\Models\Donor
     * @since 1.0
     */
    protected $donor;

    /**
     * @return void
     * @since 1.0
     */
    public function __construct(ContainerInterface $container, $id)
    {
        parent::__construct($container);
        if (EBB\isValidID($id)) {
            $donorRepository = $this->getEntityManager()->getRepository('Entities:Donor');
            $this->donor = $donorRepository->find($id);
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        if (! $this->hasAuthenticatedUser() || ! $this->getAcl()->isUserAllowed($this->getAuthenticatedUser(), 'Donor', 'delete')) {
            $this->viewFactory->displayView('error-403');
            return;
        }

        if (! $this->isQueriedDonorExists()) {
            $this->viewFactory->displayView('error-404');
            return;
        }

        $donor = $this->getQueriedDonor();

        if (! $this->getAcl()->canDeleteEntity($this->getAuthenticatedUser(), $donor)) {
            $this->viewFactory->displayView('error-403');
            return;
        }

        $this->doActions();
        $this->viewFactory->displayView('delete-donor', [
            'donor' => $donor,
        ]);
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doActions()
    {
        switch (filter_input(INPUT_POST, 'action')) {
            case 'delete_donor':
                $this->doDeleteAction();
                break;
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doDeleteAction()
    {
        $sessionToken = $this->getSession()->getCsrfToken();
        $actionToken = filter_input(INPUT_POST, 'token');

        if (! $actionToken || ! $sessionToken->isValid($actionToken)) {
            return;
        }

        $donor = $this->getQueriedDonor();

        if (! $this->hasAuthenticatedUser() || ! $this->getAcl()->canDeleteEntity($this->getAuthenticatedUser(), $donor)) {
            return;
        }

        $this->getEntityManager()->remove($donor);
        $this->getEntityManager()->flush();

        EBB\redirect(
            EBB\addQueryArgs(
                EBB\getEditDonorsURL(),
                ['flag-deleted' => 1]
            )
        );
    }

    /**
     * @return \EBloodBank\Models\Donor
     * @since 1.0
     */
    protected function getQueriedDonor()
    {
        return $this->donor;
    }

    /**
     * @return bool
     * @since 1.2
     */
    protected function isQueriedDonorExists()
    {
        $donor = $this->getQueriedDonor();
        return ($donor && $donor->isExists());
    }
}
