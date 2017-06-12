<?php

/**
 * Created by PhpStorm.
 * User: mac
 * Date: 12/06/2017
 * Time: 10:33
 */
//implements \Iterator,\ArrayAccess,\Serializable

class JsString implements \ArrayAccess, \Iterator
{

    private $internalString;
    private $internalCursor = 0;

    /**
     * JsString constructor.
     * @param mixed $data
     * @throws InvalidArgumentException
     */
    function __construct($data = '')
    {
        if(!extension_loaded('mbstring'))
        {
            throw new RuntimeException("mbstring module not enabled");
        }

        $this->internalString = $this->checkAndGetVarString($data);
    }


    public function __get($name)
    {
        if($name === 'length')
        {
            return mb_strlen($this->toString());
        }

        return null;
    }

    /**
     * @return string
     */
    public  function __toString ()
    {
        return $this->internalString;
    }

    /**
     * @return JsString
     */
    public function __clone()
    {
        return new JsString($this);
    }

    /**
     * @return JsString
     */
    public function copy()
    {
        return new JsString($this->__toString());
    }

    /**
     * @param mixed
     * @return JsString
     * @throws InvalidArgumentException
     */
    public function add($data)
    {
        $this->internalString .= $this->checkAndGetVarString($data);

        return $this;
    }

    /**
     * @return int
     */
    private function length()
    {
        return mb_strlen($this->toString());
    }

    /**
     * @param $offset
     * @return string
     */
    private function get($offset)
    {
        $this->checkOffset($offset);
        return mb_substr($this->toString(), $offset, 1);
    }

    /**
     * @param $offset
     * @param mixed
     */
    private function set($offset, $value)
    {
        $this->checkOffset($offset);
        $string = $this->checkAndGetVarString($value);
        //TODO
    }

    private function checkOffset($offset)
    {

    }

    /**
     * @return string
     * @param $data
     * @throws InvalidArgumentException
     */
    private function checkAndGetVarString($data)
    {
        if(!is_string($data))
        {
            if(is_object($data))
            {
                $rc = new ReflectionClass($data);
                if(!$rc->hasMethod('__toString'))
                {
                    goto error;
                }
                else{
                    return call_user_func([$data, '__toString']);
                }
            }
            else{
                error:
                    throw new InvalidArgumentException("Expected string|null ".gettype($data)." given");
            }

        }
        else{
            return $data;
        }
    }

    /**
     * @return string
     */
    public  function toString ()
    {
        return $this->internalString;
    }

    /********************** \Iterator,\ArrayAccess *****************/


    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $this->length() > $offset;
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        //
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this[$this->internalCursor];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->internalCursor++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->internalCursor;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->offsetExists($this->internalCursor);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->internalCursor = 0;
    }
}