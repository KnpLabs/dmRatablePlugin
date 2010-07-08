<?php

echo _tag('div.dm_ratable_rating', array('data-hash' => $record->getRatableHash()),
    _tag('div.message').
    _tag('form', array('action' => _link('+/dmRatable/rate')->getHref()),
        $select->render('dm_ratable_select', $record->getRating(), array('disabled' => $sf_user->isAuthenticated() ? '' : 'disabled'))
    )
);
