<?php

class Doctrine_DmRatable extends Doctrine_Record_Generator
{

  /**
   * __construct
   *
   * @param string $options
   * @return void
   */
  public function __construct($options)
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  public function buildRelation()
  {
    $this->buildForeignRelation('Rates');
    $this->buildLocalRelation();
  }

  /**
   * buildDefinition
   *
   * @param object $Doctrine_Table
   * @return void
   */
  public function setTableDefinition()
  {
    if(is_int($this->_options['rounding_rate']))
    {
      $type = 'integer';
      $length = 4;
    }
    else
    {
      $type = 'float';
      $length = null;
    }

    $options['range'] = array(1 => $this->_options['max_rate']);

    $this->hasColumn($this->_options['field'], $type, $length, $options);
    unset($options['range']);

    if($this->_options['user'])
    {
      $user = $this->_options['user'];
      $this->hasColumn(Doctrine_Inflector::tableize($user['class']) . '_id', $user['type'], null, array('primary' => true));
    }
    else
    {
      $this->hasColumn($this->getRatedObjectFk(), 'integer', null, array('primary' => true));
      $this->hasColumn('id', 'integer', null, array('primary' => true, 'autoincrement' => true));
    }

  }

  public function buildLocalRelation($alias = null)
  {
    // relation to the main object
    $options['foreign'] = $this->_options['table']->getIdentifier();
    $options['local'] = $this->getRatedObjectFk();
    $options['type'] = Doctrine_Relation::ONE;
    $options['onDelete'] = 'CASCADE';
    $options['onUpdate'] = 'CASCADE';
    $this->_table->getRelationParser()->bind($this->_options['table']->getComponentName(), $options);

    // relation to the user
    if($this->_options['user'])
    {
      $user = $this->_options['user'];

      $table = Doctrine::getTable($user['class']);
      $options['foreign'] = $table->getIdentifier();
      $options['local'] = Doctrine_Inflector::tableize($user['class']) . '_id';
      $options['type'] = Doctrine_Relation::ONE;

      $this->_table->getRelationParser()->bind($table->getComponentName() . ' as User', $options);
    }
  }

  public function getRatedObjectFk()
  {
    return ($this->_options['user']) ? 'id' : Doctrine_Inflector::tableize($this->_options['table']->getComponentName()) . '_id';
  }
}
