<?php
class FdTLandingPage extends DataExtension
{
    public static $db = array(
            'codice_analitycs' => 'Varchar',
            'codice_conversione_adwords' => 'Varchar',
            'TemplateName' => 'Varchar',
            'HasForm' => "Enum('SI, NO')",
            'FormCode' => 'Text',
            'sessionFieldCode' => 'Varchar(255)'
    );


    static $defaults = array(
    	'HasForm' => 'NO'
    );

    public function updateCMSFields(FieldList $fields) {

        $fields->insertBefore(new Tab('FormSettings', 'Form Settings'), 'Related');

        $hasFormDropDown = new DropdownField( 'HasForm', 'Has Form', array ('SI'=>'Si', 'NO'=>'No'));
        $hasFormDropDown->setEmptyString ('La pagina ha un form?');
        $hasFormDropDown->setHasEmptyDefault (false);
        $fields->addFieldToTab('Root.FormSettings', $hasFormDropDown);

        $sessionFieldCodeField = new TextField('sessionFieldCode', 'Codice Field sessione');
        $fields->addFieldToTab('Root.FormSettings', $sessionFieldCodeField);

        $formTextareaField = new TextareaField('FormCode', 'Form Code');
        $formTextareaField->setRows (35);
        $formTextareaField->setColumns (50);
        $fields->addFieldToTab('Root.FormSettings', $formTextareaField);

        return $fields;
    }

}