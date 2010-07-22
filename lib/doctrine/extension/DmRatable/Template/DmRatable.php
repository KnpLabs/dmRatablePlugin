<?php

/**
 * Add Rating capabilities to your models
 */
class Doctrine_Template_DmRatable extends Doctrine_Template
{
  protected $_options = array(
    'className'     => '%CLASS%Rate',
    'tableName'     => false,
    'generateFiles' => false,
    'table'         => false,
    'pluginTable'   => false,
    'children'      => array(),
    'options'       => array(),
    'field'         => 'rate',
    'max_rate'      => 5,
    'rounding_rate' => 1,
    'user'          => array('class' => 'DmUser', 'type'  => 'integer')
  );

  public function __construct(array $options = array())
  {
    parent::__construct($options);
    $this->_plugin = new Doctrine_DmRatable($this->_options);
  }

  /**
   * Initialize the DmRatable plugin for the template
   *
   * @return void
   */
  public function setUp()
  {
    $this->_plugin->initialize($this->_table);
  }

  /**
   * Get the plugin instance for the DmRatable template
   *
   * @return void
   */
  public function getDmRatable()
  {
    return $this->_plugin;
  }

  public function getRatableHash()
  {
    return dmString::encode(array(get_class($this->getInvoker()), $this->getInvoker()->getId()));
  }

  /**
   *
   * @return array() with accorded rating
   */
  public function getRating()
  {
    $select_format = 'AVG(r.%s) %s';

    $q = $this->getRatesQuery()->select($this->getDmRatable()->getOption('field'));
    
    $rate = $q->fetchValue();
    $rate = $rate ? $rate : 0;

    return $this->round($rate);
  }

  public function round($value)
  {
    $rounding = $this->getDmRatable()->getOption('rounding_rate');
    return (round($value/$rounding)*$rounding);
  }

  /**
   *
   * @return int number of votes
   */
  public function getRateCount()
  {
    return $this->getRatesQuery()->count();
  }

  /**
   *
   * @return boolean true if ok, false else
   */
  public function addRate(array $rateData)
  {
    if($rate = $this->getRatesQuery()->addWhere('dm_user_id = ?', $rateData['dm_user_id'])->fetchOne())
    {
      $rate->rate = $rateData['rate'];
    }
    else
    {
      $rate = dmDb::create($this->getDmRatable()->getOption('className'), $rateData);
      $rate->id = $this->getInvoker()->id;
    }

    $rate->save();
  }

  public function cancelRate(array $rateData)
  {
    if($rate = $this->getRatesQuery()->addWhere('dm_user_id = ?', $rateData['dm_user_id'])->fetchOne())
    {
      $rate->delete();
    }
  }

  /**
   *
   * @return boolean true if ok, false else
   */
  public function removeRatings()
  {
    return $this->getRatesQuery()->delete()->execute();
  }

  public function getRates($hydration = Doctrine::HYDRATE_RECORD)
  {
    return $this->getRatesQuery()->execute($hydration);
  }

  public function getRatesQuery()
  {
    return Doctrine_Query::create()
    ->from(get_class($this->getInvoker()) . 'Rate as r')
    ->where('r.' . $this->getDmRatable()->getRatedObjectFk() . ' = ?', array($this->getInvoker()->id));
  }
}
