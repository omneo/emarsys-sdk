<?php

namespace Arkade\Emarsys\Modules;

use Arkade\Emarsys;

trait BuildsModules
{
    /**
     * Return contact module.
     *
     * @return Contacts
     */
    public function contacts()
    {
        return new Contacts($this);
    }
}
