<?php
/**
 * User Model
 *
 * @package EBloodBank
 * @subpackage Models
 * @since 1.0
 */
namespace EBloodBank\Models;

use EBloodBank\Kernal\Roles;
use EBloodBank\Traits\EntityMeta;
use EBloodBank\Exceptions\InvaildArgument;

/**
 * @since 1.0
 *
 * @Entity(repositoryClass="EBloodBank\Models\UserRepository")
 * @Table(name="user")
 */
class User extends Entity
{
    use EntityMeta;

    /**
     * @var int
     * @since 1.0
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="user_id")
     */
    protected $id = 0;

    /**
     * @var string
     * @since 1.0
     *
     * @Column(type="string", name="user_logon")
     */
    protected $logon;

    /**
     * @var string
     * @since 1.0
     *
     * @Column(type="string", name="user_pass")
     */
    protected $pass;

    /**
     * @var string
     * @since 1.0
     *
     * @Column(type="string", name="user_rtime")
     */
    protected $rtime;

    /**
     * @var string
     * @since 1.0
     *
     * @Column(type="string", name="user_role")
     */
    protected $role = 'default';

    /**
     * @var string
     * @since 1.0
     *
     * @Column(type="string", name="user_status")
     */
    protected $status = 'pending';

    /**
     * @return Role
     * @since 1.0
     */
    public function getRole()
    {
        return Roles::getRole($this->get('role'));
    }

    /**
     * @return bool
     * @since 1.0
     */
    public function hasCaps(array $caps, $opt = 'AND')
    {
        $role = $this->getRole();

        if (empty($role)) {
            return false;
        }

        return $role->hasCaps($caps, $opt);
    }

    /**
     * @var bool
     * @since 1.0
     */
    public function isActivated()
    {
        return 'activated' === $this->get('status');
    }

    /**
     * @var bool
     * @since 1.0
     */
    public function isPending()
    {
        return 'pending' === $this->get('status');
    }

    /**
     * @return mixed
     * @since 1.0
     */
    public static function sanitize($key, $value)
    {
        switch ($key) {
            case 'id':
                $value = (int) $value;
                break;
            case 'logon':
            case 'role':
                $value = trim(filter_var($value, FILTER_SANITIZE_STRING));
                break;
        }
        return $value;
    }

    /**
     * @throws \EBloodBank\Exceptions\InvaildArgument
     * @return bool
     * @since 1.0
     */
    public static function validate($key, $value)
    {
        switch ($key) {
            case 'id':
                if (! isVaildID($value)) {
                    throw new InvaildArgument(__('Invaild user ID'), 'invaild_user_id');
                }
                break;
            case 'logon':
                if (! is_string($value) || empty($value)) {
                    throw new InvaildArgument(__('Invaild user name'), 'invaild_user_name');
                }
                break;
            case 'pass':
                if (! is_string($value) || empty($value)) {
                    throw new InvaildArgument(__('Invaild user password'), 'invaild_user_pass');
                }
                break;
            case 'role':
                if (! is_string($value) || ! Roles::isExists($value)) {
                    throw new InvaildArgument(__('Invaild user role'), 'invaild_user_role');
                }
                break;
        }
        return true;
    }
}
