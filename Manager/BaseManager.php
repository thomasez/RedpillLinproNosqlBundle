<?php

/**
 *
 * @author    Thomas Lundquist <thomasez@redpill-linpro.com>
 * @copyright 2011 Thomas Lundquist
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 */


namespace RedpillLinpro\NosqlBundle\Manager;

abstract class BaseManager
{
  /* 
   * Remember to put these in the Manages extending this one.
   * Right now they are all the same but I define different names here.
   * Or rather, they have to be defined in the object extending this one.
   */
  // protected static $_collection  = 'Base';
  // protected static $_model       = 'Model\Base';

  protected $access_service;

  public function __construct($access_service, $options = array())
  {
        $this->access_service = $access_service;
/*
        if (array_key_exists('model', $options)) {
            $this->model = $options['model'];
        }
*/
  }

  /*
   * Finders
   */
  public function findAll($params = array())
  {

    $objects = array();
    foreach ($this->access_service->findAll(static::$_collection, $params) as $o)
    {
      $object = new static::$_model($o);
      $object->setId($o['id']);
      $objects[] = $object;
    }

    return $objects;

  }

  public function findOneById($id)
  {
    // This was kinda annoying and I'm sure I just doing it wrong.
    $m = static::$_model;
    $data = $this->access_service->findOneById(
        static::$_collection, $m::getIdKey(), $id);

    if (!$data)
    {
      return null;
    }

    $object = new static::$_model($data);

    return $object;
  }

  public function findOneByKeyVal($key, $val)
  {
    $objects = array();
    $data = $this->access_service->findOneByKeyVal(
                    static::$_collection, $key, $val);

    if (!$data)
    {
      return null;
    }

    $object = new static::$_model($data);
    return $object;

  }

  public function findByKeyVal($key, $val)
  {
    $objects = array();

    foreach ($this->access_service->findByKeyVal(
        static::$_collection, $key, $val) as $data)
    {
      $object = new static::$_model($data);
      $objects[] = $object;
    }

    return $objects;
  }

  public function findOneByKeyValAndSet($criteria = array())
  {
    $objects = array();
    $data = $this->access_service->findOneByKeyValAndSet(
                    static::$_collection, $criteria);

    if (!$data)
    {
      return null;
    }

    $object = new static::$_model($data);
    return $object;

  }

  public function findByKeyValAndSet($criteria = array())
  {
    $objects = array();

    foreach ($this->access_service->findByKeyValAndSet(
        static::$_collection, $criteria) as $data)
    {
      $object = new static::$_model($data);
      $objects[] = $object;
    }

    return $objects;
  }

  public function save($object)
  {

    // Save can do both insert and update with MongoDB.
    $new_data = $this->access_service->save($object, static::$_collection);

    if (isset($new_data['id']))
    {
      $object->setId($new_data['id']);
    }

    return $object;

  }

  public function delete($object)
  {

    if (is_object($object) && $id = $object->getId()) {
        // This could be discussed as being superfluous or not, since later
        // here I'll just accept the object as being an id and just delete it.. 
        // So why bother checking this then?
        if ($object->getClassName() != static::$_collection
            && get_class($object) != static::$_model) {
            throw new \InvalidArgumentException('This is not an object I can delete. It may be a missing Id or wrong class.');
        }
    } elseif (!is_object($object)) {
       $id = $object; 
    }

    if (!$id) {
        throw new \InvalidArgumentException('This is not an object I can delete. It may be a missing Id or wrong class.');
    }

    $status = $this->access_service->remove($id, static::$_collection);
    return $status;
  }

}
