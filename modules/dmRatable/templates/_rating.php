<?php

echo _open('div.dm_ratable_rating', array('data-hash' => $record->getRatableHash()));

    echo _tag('div.message', isset($message) ? $message : '');

    echo _tag('div.stars', _tag('form', array('action' => _link('+/dmRatable/rate')->getHref()),
        $select->render('dm_ratable_select', $record->getRating(), array('disabled' => $sf_user->isAuthenticated() ? '' : 'disabled'))
    ));

    echo _tag('div.average', sprintf('Item popularity: %01.2f/%d', $record->getRating(), $record->getMaxRate()));

    $nbVotes = $record->getRateCount();
    echo _tag('div.quantity', sprintf('%d %s cast', $nbVotes, dmString::pluralizeNb('vote', $nbVotes)));

echo _close('div');
