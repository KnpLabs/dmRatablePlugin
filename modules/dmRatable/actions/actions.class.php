<?php

class dmRatableActions extends dmBaseActions
{
  public function executeRate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('POST'));
    list($class, $id) = dmString::decode($request->getParameter('hash'));
    $this->forward404Unless($table = dmDb::table($class));
    $this->forward404Unless($record = $table->find($id));
    $this->forward404Unless($table->hasTemplate('DmRatable'));
    $template = $table->getTemplate('DmRatable');
    $options = $template->getOptions();
    $value = (int) $request->getParameter('value');
    $this->forward404Unless($value >= 0 && $value <= $options['max_rate']);

    $rate = array('rate' => $value);
    if($options['user']) {
      $this->forward404Unless($this->getUser()->isAuthenticated());
      $rate['dm_user_id'] = $this->getUser()->getUserId();
    }
    else {
      $rate['session_id'] = session_id();
    }
        
    if($value)
    {
      $record->addRate($rate);
    }
    else
    {
      $record->cancelRate($rate);
    }

    return $this->renderJson(array('new_value' => $record->getRating()));
  }
}
