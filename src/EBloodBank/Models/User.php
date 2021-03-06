<?php
/**
 * User entity class file
 *
 * @package    EBloodBank
 * @subpackage Models
 * @since      1.0
 */
namespace EBloodBank\Models;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use EBloodBank as EBB;
use EBloodBank\Traits\EntityMeta;

/**
 * User entity class
 *
 * @since 1.0
 *
 * @Entity(repositoryClass="EBloodBank\Models\UserRepository")
 * @Table(name="user")
 * @HasLifecycleCallbacks
 */
class User extends Entity
{
    use EntityMeta;

    /**
     * User ID
     * 
     * @var   int
     * @since 1.0
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="user_id")
     */
    protected $id = 0;

    /**
     * User name
     * 
     * @var   string
     * @since 1.0
     *
     * @Column(type="string", name="user_name")
     */
    protected $name;

    /**
     * User email
     * 
     * @var   string
     * @since 1.0
     *
     * @Column(type="string", name="user_email", unique=true)
     */
    protected $email;

    /**
     * User password
     * 
     * @var   string
     * @since 1.0
     *
     * @Column(type="string", name="user_pass")
     */
    protected $pass;

    /**
     * User role
     * 
     * @var   string
     * @since 1.0
     *
     * @Column(type="string", name="user_role")
     */
    protected $role;

    /**
     * User creation datetime
     * 
     * @var   \DateTime
     * @since 1.0
     *
     * @Column(type="datetime", name="user_created_at")
     */
    protected $created_at;

    /**
     * User status
     * 
     * @var   string
     * @since 1.0
     *
     * @Column(type="string", name="user_status")
     */
    protected $status;

    /**
     * User meta
     * 
     * @var   array
     * @since 1.0
     *
     * @Column(type="json", name="user_meta")
     */
    protected $meta = [];

    /**
     * @return bool
     * @since  1.0
     */
    public function isExists()
    {
        $id = (int) $this->get('id');
        return ! empty($id);
    }

    /**
     * @return bool
     * @since  1.0
     */
    public function isPending()
    {
        return 'pending' === $this->get('status');
    }

    /**
     * @return bool
     * @since  1.0
     */
    public function isActivated()
    {
        return 'activated' === $this->get('status');
    }

    /**
     * @return void
     * @since  1.6
     * 
     * @PrePersist
     */
    public function doActionOnPrePersist()
    {
        $this->set('created_at', new DateTime('now', new DateTimeZone('UTC')));
    }

    /**
     * @return mixed
     * @since  1.0
     * @static
     */
    public static function sanitize($key, $value)
    {
        switch ($key) {
            case 'id':
                $value = EBB\sanitizeInteger($value);
                break;
            case 'name':
                $value = EBB\sanitizeTitle($value);
                break;
            case 'email':
                $value = EBB\sanitizeEmail($value);
                break;
            case 'role':
            case 'status':
                $value = EBB\sanitizeSlug($value);
                break;
            case 'created_at':
                break;
        }
        return $value;
    }

    /**
     * @throws \InvalidArgumentException
     * @return bool
     * @since  1.0
     * @static
     */
    public static function validate($key, $value)
    {
        switch ($key) {
            case 'id':
                if (! EBB\isValidID($value)) {
                    throw new InvalidArgumentException(__('Invalid user ID.'));
                }
                break;
            case 'name':
                if (! is_string($value) || empty($value)) {
                    throw new InvalidArgumentException(__('Invalid user name.'));
                }
                break;
            case 'email':
                if (! EBB\isValidEmail($value)) {
                    throw new InvalidArgumentException(__('Invalid user e-mail.'));
                }
                break;
            case 'pass':
                if (! is_string($value) || empty($value)) {
                    throw new InvalidArgumentException(__('Invalid user password.'));
                }
                break;
            case 'role':
                if (! is_string($value)) {
                    throw new InvalidArgumentException(__('Invalid user role.'));
                }
                break;
            case 'status':
                if (! is_string($value) || empty($value)) {
                    throw new InvalidArgumentException(__('Invalid user status.'));
                }
                break;
        }

        return true;
    }
}
