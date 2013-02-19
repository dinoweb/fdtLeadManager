<?php
class FdtGraziePage extends SiteTree {
	public static $db = array(
            'messaggio' => 'Text'
    );

	public function getCMSFields() {
        $fields = parent::getCMSFields();

        $messaggioField = new TextareaField('messaggio', 'Testo pagina');
        $messaggioField->setRows (10);
        $messaggioField->setDescription ('Usare i token: {%nome%} - {%cognome% - {%email%} - {%text%} - {%ragioneSociale%} - {%telefono%}');
        $fields->addFieldToTab('Root.Main', $messaggioField, 'Metadata');

        $fields->removeFieldFromTab ('Root.Main', 'Content');

        return $fields;
    }



}
class FdtGraziePage_Controller extends Page_Controller {

	private function getManageWufooClass ()
    {
        return new manageWufoo ();
    }

    private function getFormHash ()
	{
		if ($this->getRequest ()->requestVar('formHash'))
		{
			return $this->getRequest ()->requestVar('formHash');
		}

		$formSessionID = Session::get ('FormSessionID');
		$arrayFormSessionID = explode('_', $formSessionID);
		return $arrayFormSessionID[1];
	}

	protected function getLeadData()
	{
		$formSessionID = Session::get ('FormSessionID');

		if ($formSessionID)
		{
			$entryId = $this->getRequest ()->requestVar('entryID');
			$formHash = $this->getFormHash();

			$manageWufoo = $this->getManageWufooClass ();
			$arrayFieldMap = $manageWufoo->getArrayFieldsMap ($entryId, $formHash);

			return $arrayFieldMap;

		}

		return false;
	}

	private function getFieldValue (array $lead, $fieldName)
	{
		if (isset($lead[$fieldName]))
		{
			return $lead[$fieldName];
		}

		trigger_error ( sprintf ('Il campo %s non esiste nella tabella Leads', $fieldName));
	}

	protected function getParsedMessage(array $lead)
	{
		$messaggioOk = $this->messaggio;

		$tokenString = '/{%[A-Za-z0-9_]+%}/';
		preg_match_all ($tokenString, $this->messaggio, $tokens);

		if (count ($tokens[0]) > 0)
		{
			foreach ($tokens[0] as $token) {
				$field = array();
				$fieldString = '/[A-Za-z0-9_]+/';
				preg_match ($fieldString, $token, $field);

				if (count ($field) == 1)
				{
					$fieldValue = ( $this->getFieldValue($lead, $field[0]));
					$messaggioOk = str_replace($token, $fieldValue, $messaggioOk);

				}
			}

		}
		return $messaggioOk;
	}


	public function getMessage()
	{
		$lead = $this->getLeadData ();

		if ($lead)
		{
			return $this->getParsedMessage ($lead);
		}

		return $this->messaggio;
	}

}