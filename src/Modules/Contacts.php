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

    /**
     * Output field mappings
     *
     * Emarsys uses an numeric ID reference to a particular field
     * this returns the field identifiers and field option identifiers (for single and multi choice field types)
     *
     * @return Collection
     */
    public function getFields(){

        $response = $this->client->get('field');

        $fields = json_decode((string) $response->getBody(), true)['data'];

        $result = collect([]);

        foreach($fields as $fieldData){
            if($fieldData['application_type'] === 'singlechoice' || $fieldData['application_type'] === 'multiplechoice'){
                $response = $this->client->get('field/'.$fieldData['id'].'/choice');
                $options = json_decode((string) $response->getBody(), true)['data'];
                $fieldData['options'] = $options;
            }
            $result->push($fieldData);
        }

        return $result;
    }

}
