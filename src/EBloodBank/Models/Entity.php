<?php
/**
 * Abstract entity class file
 *
 * @package    EBloodBank
 * @subpackage Models
 * @since      1.0
 */
namespace EBloodBank\Models;

use EBloodBank as EBB;

/**
 * Abstract entity class
 *
 * @since 1.0
 *
 * @MappedSuperclass
 */
abstract class Entity
{
    /**
     * @return mixed
     * @since 1.0
     */
    public function get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
    }

    /**
     * @return bool
     * @since 1.0
     */
    abstract public function isExists();

    /**
     * @return void
     * @since 1.0
     */
    public function display($key, $format = 'html')
    {
        switch ($format) {

            case 'attr':
                echo EBB\escAttr($this->get($key));
                break;

            case 'html':
                echo EBB\escHTML($this->get($key));
                break;

            default:
            case 'plain':
                echo $this->get($key);
                break;

        }
    }

    /**
     * @return mixed
     * @since 1.0
     */
    public static function sanitize($key, $value)
    {
        return $value;
    }

    /**
     * @return bool
     * @since 1.0
     */
    public static function validate($key, $value)
    {
        return true;
    }

    /**
     * @return void
     * @since 1.0
     */
    public function set($key, $value, $sanitize = false, $validate = true)
    {
        if (property_exists($this, $key)) {
            if ($sanitize) {
                $value = static::sanitize($key, $value);
            }
            if (! $validate || static::validate($key, $value)) {
                $this->$key = $value;
            }
        }
    }
}
