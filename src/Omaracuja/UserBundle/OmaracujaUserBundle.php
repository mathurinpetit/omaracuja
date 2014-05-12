<?php

namespace Omaracuja\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OmaracujaUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
