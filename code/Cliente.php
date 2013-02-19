<?php
class Cliente extends Member {

	public static $has_many = array(
            'Siti' => 'Subsite'
    );

	public static $db = array(
            'ragione_sociale' => 'Varchar',
            'iva_cf' => 'Varchar'

    );

    static $field_labels = array(
      'ragione_sociale' => 'Ragione Sociale' // renames the column to "Cost"
   );

    static $summary_fields = array(
      'ragione_sociale',
      'Email'
   );

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $ragioneSociale = new TextField('ragione_sociale', 'Ragione Sociale');
        $ivaCf = new TextField('iva_cf', 'Iva/Cf');

        $fields->addFieldToTab('Root.Main', $ragioneSociale, 'FirstName');
        $fields->addFieldToTab('Root.Main', $ivaCf, 'FirstName');

        return $fields;
    }


}