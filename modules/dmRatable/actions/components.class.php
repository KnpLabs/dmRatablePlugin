<?php

class dmRatableComponents extends dmBaseComponents
{
  public function executeRating()
  {
    $table = $this->record->getTable();
    $template = $table->getTemplate('DmRatable');
    $options = $template->getOptions();

    $choices = array();
    for($i=1; $i<=$options['max_rate']; $i++) {
      $choices[$i] = $i.'/'.$options['max_rate'];
    }
    $this->select = new sfWidgetFormSelect(array('choices' => $choices));
  }
}
