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
     * this returns the field identifiers and field options identifiers (for single and multi choice field types)
     *
     * @return void
     */
    public function getFields(){

        $fields = $this->send_request('GET', 'field');

        $result = collect([]);

        foreach($fields as $field){
            $fieldData = $field->toArray();
            if($field->application_type === 'singlechoice' || $field->application_type === 'multiplechoice'){
                $options = $this->send_request('GET', 'field/'.$field->id.'/choice');
                foreach($options as $option){
                    $fieldData['options'] = $option->toArray();
                }
            }
            $result->push($fieldData);
        }

        return $result;
    }

}
