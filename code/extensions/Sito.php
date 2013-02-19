<?php
class Sito extends DataExtension
{
    public static $db = array(
            'codice_analitycs' => 'Varchar',
            'codice_conversione_adwords' => 'Varchar',
            'TemplateName' => 'Varchar',
            'FormCode' => 'Text',
            'sessionFieldCode' => 'Varchar(255)'

    );

    public static $has_one = array(
            'Cliente' => 'Cliente'
    );

    public static $has_many = array(
            'Leads' => 'Leads'
    );

    public function stat()
    {
        return;
    }

    private function addFieldsToAdminTab (FieldList $fields)
    {
        $arrayFieldsToBeAdded = $this->getFieldsToBeAdded ();

        if (count($arrayFieldsToBeAdded) > 0)
        {
            foreach ($arrayFieldsToBeAdded as $tabName => $arrayFields) {

                foreach ($arrayFields as $key => $value) {
                    $fields->addFieldToTab($tabName, $value);
                }

            }
        }

        return $fields;

    }

    public function updateCMSFields(FieldList $fields) {
        $fields = $this->addFieldsToAdminTab($fields);
        $fields->addFieldToTab('Root.Configuration', new DropdownField( 'ClienteID', 'Cliente', Cliente::get()->map('ID', 'ragione_sociale')), 'Title');
        $fields->addFieldToTab('Root.Configuration', new TextField('TemplateName', 'TemplateName'), 'Language');
        $fields->removeFieldFromTab ('Root.Configuration', 'Theme');

        $fields->insertBefore(new Tab('Leads', 'Leads'), 'Configuration');

        $config = GridFieldConfig_RelationEditor::create();
        $config->getComponentByType('GridFieldPaginator')->setItemsPerPage(20);


        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(array(
            'nome' => 'Nome',
            'cognome'=> 'Cognome',
            'email' => 'Email',
            'Created' => 'Data',
            'stato' => 'Stato'
        ));

        $leadsField = new GridField(
            'Leads', // Field name
            'Leads', // Field title
            $this->owner->Leads(), // List of all related lead
            $config
        );

        $fields->addFieldToTab('Root.Leads', $leadsField);
        return $fields;

    }


    public function getFieldsToBeAdded()
    {
        $arrayFieldsToBeAdded = array ();
        $arrayFieldsToBeAdded['Root.Settings']['analitycs'] = new TextareaField('codice_analitycs', 'Codice Analitycs');
        $arrayFieldsToBeAdded['Root.Settings']['adwords'] = new TextareaField('codice_conversione_adwords', 'Codice Conversione Adwords');

        $sessionFieldCodeField = new TextField('sessionFieldCode', 'Codice Field sessione');
        $arrayFieldsToBeAdded['Root.Settings']['sessionFieldCode'] = $sessionFieldCodeField;

        $formTextareaField = new TextareaField('FormCode', 'Form Code');
        $formTextareaField->setRows (20);
        $formTextareaField->setColumns (50);
        $arrayFieldsToBeAdded['Root.Settings']['formcode'] = $formTextareaField;



        return $arrayFieldsToBeAdded;
    }

}