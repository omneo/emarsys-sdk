<?php

namespace Omneo\Emarsys\Entities;

use Omneo\Emarsys\Concerns;
use Illuminate\Support\Fluent;

abstract class Entity extends Fluent
{
    use Concerns\HasAttributes;
}
