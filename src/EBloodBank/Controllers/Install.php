<?php
/**
 * Install page controller class file
 *
 * @package    EBloodBank
 * @subpackage Controllers
 * @since      1.0
 */
namespace EBloodBank\Controllers;

use DateTime;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use EBloodBank as EBB;
use EBloodBank\Notices;
use EBloodBank\Options;
use EBloodBank\Models\User;

/**
 * Install page controller class
 *
 * @since 1.0
 */
class Install extends Controller
{
    /**
     * @return void
     * @since 1.0
     */
    public function __invoke()
    {
        $connection = $this->getContainer()->get('db_connection');
        if (EBB\getInstallationStatus($connection) !== EBB\DATABASE_INSTALLED || Options::getOption('installing')) {
            $this->doStepAction();
            $view = $this->viewFactory->forgeView('install', [
                'step' => $this->getStep(),
                'status' => 'installing',
            ]);
        } else {
            $view = $this->viewFactory->forgeView('install', [
                'status' => 'installed',
            ]);
        }
        $view();
    }

    /**
     * @return int
     * @since 1.0
     */
    protected function getStep()
    {
        return max((int) filter_input(INPUT_GET, 'step'), 1);
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doStepAction()
    {
        if ('install' === filter_input(INPUT_POST, 'action')) {
            switch ($this->getStep()) {
                case 1:
                    $this->doStep1Action();
                    break;
                case 2:
                    $this->doStep2Action();
                    break;
            }
        }
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doStep1Action()
    {
        $connection = $this->getContainer()->get('db_connection');
        if (EBB\isDatabaseSelected($connection)) {
            EBB\tryDatabaseConnection($connection);
            if (EBB\isDatabaseConnected($connection)) {
                EBB\redirect(
                    EBB\addQueryArgs(
                        EBB\getInstallerURL(),
                        ['step' => 2]
                    )
                );
            }
        }
    }

    /**
     * @return void
     * @since 1.3
     */
    protected function createTheSchemasTables()
    {
        $stmts = [];
        $connection = $this->getContainer()->get('db_connection');

        /*** Users Table **************************************************/

        $stmts[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(255) NOT NULL,
  `user_email` VARCHAR(100) NOT NULL,
  `user_pass` VARCHAR(128) NOT NULL,
  `user_role` VARCHAR(45) NOT NULL,
  `user_created_at` DATETIME NOT NULL,
  `user_status` VARCHAR(45) NOT NULL,
  `user_meta` JSON NULL,
  PRIMARY KEY (`user_id`))
ENGINE = InnoDB
SQL;

        $stmts[] = <<<'SQL'
CREATE UNIQUE INDEX `user_email_UNIQUE` ON `user` (`user_email` ASC)
SQL;

        /*** Cities Table *************************************************/

        $stmts[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS `city` (
  `city_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_name` VARCHAR(255) NOT NULL,
  `city_created_at` DATETIME NOT NULL,
  `city_created_by` INT UNSIGNED NULL,
  PRIMARY KEY (`city_id`),
  CONSTRAINT `city_created_by`
    FOREIGN KEY (`city_created_by`)
    REFERENCES `user` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
SQL;

        $stmts[] = <<<'SQL'
CREATE INDEX `city_created_by_idx` ON `city` (`city_created_by` ASC)
SQL;

        $stmts[] = <<<'SQL'
CREATE UNIQUE INDEX `city_name_UNIQUE` ON `city` (`city_name` ASC)
SQL;

        /*** Districts Table **********************************************/

        $stmts[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS `district` (
  `district_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `district_name` VARCHAR(255) NOT NULL,
  `district_city_id` INT UNSIGNED NOT NULL,
  `district_created_at` DATETIME NOT NULL,
  `district_created_by` INT UNSIGNED NULL,
  PRIMARY KEY (`district_id`),
  CONSTRAINT `district_city_id`
    FOREIGN KEY (`district_city_id`)
    REFERENCES `city` (`city_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `district_created_by`
    FOREIGN KEY (`district_created_by`)
    REFERENCES `user` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
SQL;

        $stmts[] = <<<'SQL'
CREATE INDEX `district_city_id_idx` ON `district` (`district_city_id` ASC)
SQL;

        $stmts[] = <<<'SQL'
CREATE INDEX `district_created_by_idx` ON `district` (`district_created_by` ASC)
SQL;

        $stmts[] = <<<'SQL'
CREATE UNIQUE INDEX `district_name_UNIQUE` ON `district` (`district_name` ASC, `district_city_id` ASC)
SQL;

        /*** Donors Table *************************************************/

        $stmts[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS `donor` (
  `donor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `donor_name` VARCHAR(255) NOT NULL,
  `donor_gender` VARCHAR(45) NOT NULL,
  `donor_birthdate` DATE NOT NULL,
  `donor_blood_group` VARCHAR(45) NOT NULL,
  `donor_district_id` INT UNSIGNED NOT NULL,
  `donor_created_at` DATETIME NOT NULL,
  `donor_created_by` INT UNSIGNED NULL,
  `donor_status` VARCHAR(45) NOT NULL,
  `donor_meta` JSON NULL,
  PRIMARY KEY (`donor_id`),
  CONSTRAINT `donor_district_id`
    FOREIGN KEY (`donor_district_id`)
    REFERENCES `district` (`district_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `donor_created_by`
    FOREIGN KEY (`donor_created_by`)
    REFERENCES `user` (`user_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
SQL;

        $stmts[] = <<<'SQL'
CREATE INDEX `donor_district_id_idx` ON `donor` (`donor_district_id` ASC)
SQL;

        $stmts[] = <<<'SQL'
CREATE INDEX `donor_created_by_idx` ON `donor` (`donor_created_by` ASC)
SQL;

        /*** Variables Table **********************************************/

        $stmts[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS `variable` (
  `variable_name` VARCHAR(45) NOT NULL,
  `variable_value` LONGTEXT NULL,
  PRIMARY KEY (`variable_name`))
ENGINE = InnoDB
SQL;

        foreach ($stmts as $stmt) {
            $connection->exec($stmt);
        }
    }

    /**
     * @return void
     * @since 1.3
     */
    protected function addTheDefaultOptions()
    {
        /* Reading Options */
        Options::addOption('entities_per_page', 10);
        Options::addOption('site_publication', 'on');

        /* Users Options */
        Options::addOption('self_registration', 'on');
        Options::addOption('new_user_status', 'pending');
        Options::addOption('new_user_role', 'contributor');

        /* Donors Options */
        Options::addOption('default_donor_phone_visibility', 'staff');
        Options::addOption('default_donor_email_visibility', 'members');

        /* General Options */
        Options::addOption('site_url', EBB\getHomeURL());
        Options::addOption('site_theme', EBB_DEFAULT_THEME, true);
        Options::addOption('site_name', filter_input(INPUT_POST, 'site_name'), true);
        Options::addOption('site_email', filter_input(INPUT_POST, 'site_email'), true);
    }

    /**
     * @return void
     * @since 1.3
     */
    protected function createTheAdministrator()
    {
        $user = new User();

        // Set the user name.
        $user->set('name', filter_input(INPUT_POST, 'user_name'));

        // Set the user name.
        $user->set('email', filter_input(INPUT_POST, 'user_email'));

        $userPass1 = filter_input(INPUT_POST, 'user_pass_1', FILTER_UNSAFE_RAW);
        $userPass2 = filter_input(INPUT_POST, 'user_pass_2', FILTER_UNSAFE_RAW);

        if (empty($userPass1)) {
            throw new InvalidArgumentException(__('Please enter your password.'));
        }

        if (empty($userPass2)) {
            throw new InvalidArgumentException(__('Please confirm your password.'));
        }

        if ($userPass1 !== $userPass2) {
            throw new InvalidArgumentException(__('Please enter the same password.'));
        }

        // Set the user password.
        $user->set('pass', password_hash($userPass1, PASSWORD_BCRYPT), false);

        // Set the user role.
        $user->set('role', 'administrator');
        $user->set('created_at', new DateTime('now', new DateTimeZone('UTC')));
        $user->set('status', 'activated');

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return void
     * @since 1.0
     */
    protected function doStep2Action()
    {
        try {
            $connection = $this->getContainer()->get('db_connection');

            if (EBB\getInstallationStatus($connection) === EBB\DATABASE_TABLE_NOT_EXIST) {
                $this->createTheSchemasTables();
            }

            if (EBB\getInstallationStatus($connection, true) === EBB\DATABASE_INSTALLED) {
                Options::addOption('installing', true);
                $this->addTheDefaultOptions();
                $this->createTheAdministrator();
                Options::deleteOption('installing');
                EBB\redirect(EBB\getLoginURL());
            }
        } catch (InvalidArgumentException $ex) {
            Notices::addNotice('installing_failed', $ex->getMessage());
        } catch (Exception $ex) {
            Notices::addNotice('installing_failed', __('An unexpected error occurred while installing eBloodBank.'));
        }
    }
}
