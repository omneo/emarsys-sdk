<?php

namespace Omneo\Emarsys\Modules;

use Omneo\Emarsys;

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

    /**
     * Return events module.
     *
     * @return Contacts
     */
    public function events()
    {
        return new Events($this);
    }
}
