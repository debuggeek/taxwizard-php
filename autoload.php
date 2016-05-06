<?php

//include namespace

use DebugGeek\;
use Module\User\Entity\User;
use Module\User\Form\UserForm;

/**
 * Autload project files
 */
function __autoload($class)
{
    echo $class;
    require $class;
}
