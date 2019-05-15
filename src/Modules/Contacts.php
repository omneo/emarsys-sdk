<?php

namespace Arkade\Emarsys\Modules;

use Arkade\Emarsys\Entities\Contact;
use Illuminate\Support\Collection;

class Contacts extends AbstractModule
{
    /**
     * Fetch a contact by field value.
     *
     * @param  string  $id
     * @return Collection|Contact[]
     */
    public function getContactByFieldValue(int $field, string $value)
    {
        return $this->buildCollection(
            $this->client->post('contact/getdata',[
                'json' => [
                    'keyId' => $field,
                    'keyValues' => [$value]
                ]
            ]),
            Contact::class
        );
    }

}
