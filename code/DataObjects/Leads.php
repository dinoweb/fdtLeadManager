<?php

class Leads extends DataObject {

   public static $db = array(
            'nome' => 'Varchar(255)',
            'cognome' => 'Varchar(255)',
            'email' => 'Varchar(255)',
            'text' => 'Text',
            'extraFields' => 'Text',
            'ragioneSociale' => 'Varchar(255)',
            'telefono'=>'Varchar(255)',
            'wfID'=>'Int',
            'clientIP'=>'Varchar(255)',
            'stato' => "Enum('OK, KO')",
            'sessionFieldCode' => 'Varchar(255)',
            'formHash' => 'Varchar(255)',
            'formName' => 'Varchar(255)'

    );

   private function getUnserializedExtraFields ()
   {
        return unserialize($this->extraFields);
   }

   private function getLabelByFieldId ($fieldID)
   {
        $extraFields = $this->getUnserializedExtraFields ();
        $fieldStructure = $extraFields['FieldStructure'];


        foreach ($fieldStructure['Fields'] as $key => $field) {

            if ($field['ID'] == $fieldID){
                return $field['Title'];
            }
        }
        return $fieldID;
   }

    public function addExtraFields($fields) {
        $extraFields = $this->getUnserializedExtraFields ();

        if (count ($extraFields) > 0)
        {
            foreach ($extraFields as $key => $value) {

                if (is_array($value))
                {
                  $value = json_encode($value);
                }

                if ($value != '')
                {
                  $readOnlyField = new ReadonlyField($key, $this->getLabelByFieldId($key), $value);
                  $fields->addFieldToTab('Root.Originale', $readOnlyField);
                }


            }
        }
        return $fields;

    }


   static $summary_fields = array(
        'nome' => 'Nome',
        'cognome'=> 'Cognome',
        'email' => 'Email',
        'Created' => 'Data',
        'stato' => 'Stato',
        'Sito.Title' => 'Sito'
   );

   static $searchable_fields = array(
      'cognome' => array('title' => 'Cognome'),
      'email' => array('title' => 'Email'),
      'stato',
      'Created' =>  array('title' => 'Data', 'field' => 'DateField',),
      'Sito.ID' => array('title' => 'Sito', 'filter' => 'ExactMatchFilter'),
   );

   static $default_sort = "Created DESC";


   static $defaults = array(
        'stato' => 'OK'
    );


   public static $has_one = array(
            'Sito' => 'Subsite'
    );

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $nome = new TextField('nome', 'Nome');
        $cognome = new TextField('cognome', 'Cognome');
        $email = new EmailField('email', 'Email');
        $text = new TextareaField('text', 'Testo');
        $ragioneSociale = new TextField('ragioneSociale', 'Ragione Sociale');
        $telefono = new TextField('telefono', 'Telefono');
        $wfID = new ReadonlyField('wfID', 'WuFoo ID');
        $clientIP = new ReadonlyField('clientIP', 'IP');
        $sessionFieldCode = new ReadonlyField('sessionFieldCode', 'Session ID');
        $formHash = new ReadonlyField('formHash', 'Form Hash');
        $formName = new ReadonlyField('formName', 'Form Name');
        $extraFields = new ReadonlyField('extraFields', 'Dati Form');

        $statoDropDown = new DropdownField( 'stato', 'Stato', array ('OK'=>'Fatturabile', 'KO'=>'Non Fatturabile'));
        $fields->addFieldToTab('Root.Main', $statoDropDown);
        $fields->addFieldToTab('Root.Main', $nome);
        $fields->addFieldToTab('Root.Main', $cognome);
        $fields->addFieldToTab('Root.Main', $email);
        $fields->addFieldToTab('Root.Main', $ragioneSociale);
        $fields->addFieldToTab('Root.Main', $telefono);
        $fields->addFieldToTab('Root.Main', $text);
        $fields->addFieldToTab('Root.Main', $formName);
        $fields->addFieldToTab('Root.Main', $formHash);
        $fields->addFieldToTab('Root.Main', $wfID);
        $fields->addFieldToTab('Root.Main', $clientIP);
        $fields->addFieldToTab('Root.Main', $sessionFieldCode);

        $fields = $this->addExtraFields ($fields);

        $fields->addFieldToTab('Root.Originale', $extraFields);


        return $fields;
    }
}