<?php

namespace Arkade\Emarsys\Modules;

use Arkade\Emarsys\Entities\Contact;
use GuzzleHttp\Exception\ClientException;

class Contacts extends AbstractModule
{
    /**
     * Fetch a contact by field value.
     *
     * @param  string  $id
     * @return Contact
     */
    public function getContactByFieldValue(int $field, string $value)
    {
        $payload = ['keyId' => $field, 'keyValues' => [$value]];
        return $this->buildEntity(
            $this->client->post('contact/getdata',$payload),
            Contact::class
        );
    }

}
