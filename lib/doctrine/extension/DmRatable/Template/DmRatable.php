<?php

/**
 * Add Rating capabilities to your models
 */
class Doctrine_Template_DmRatable extends Doctrine_Template
{
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

  /**
   *
   * @return array() with accorded rating
   */
  public function getRating()
  {
    $select_format = 'AVG(r.%s) %s';

    $q = $this->getRatesQuery();

    foreach ($this->getDmRatable()->getOption('criterias') as $column)
    {
      $select[] = sprintf($select_format, $column, $column);
    }

    $q->select(implode(', ', $select));
    
    $rates = $q->fetchArray();

    foreach ($rates[0] as $key => $value) {
      $rounded_rates[$key] = $this->round($value);
    }

    return $rounded_rates;
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
  public function addRate($rate)
  {
    $related = $this->getDmRatable()->getOption('className');
    $rate_obj = new $related();
    $rate_obj->merge($rate);

    if(!$this->getDmRatable()->getOption('user'))
    {
      $fk = $this->getDmRatable()->getRatedObjectFk();
      $rate_obj->$fk = $this->getInvoker()->id;
    }
    else
    {
      $rate_obj->id = $this->getInvoker()->id;
    }

    $rate_obj->save();

    $this->getInvoker()->link('Rates', $rate_obj->id);
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
