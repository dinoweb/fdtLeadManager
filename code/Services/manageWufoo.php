<?php

class manageWufoo
{
	private $apiKey = 'ONUY-CS3Z-TN5Y-14NO';
	private $wufooUser = 'fucinadeltag';

	private $arrayFieldsMap = array (
	);

	public function __construct (){
	}

	public function getArrayFieldsMap ($entryId, $formHash, $refresh = false)
	{

		if (count ($this->arrayFieldsMap) == 0 or $refresh)
		{
			$data = $this->getAllEntryData ($entryId, $formHash);
			$this->manageFields ($data);
		}

		return $this->arrayFieldsMap;
	}

	private function getApyKey ()
	{
		return $this->apiKey;
	}

	private function getWufooUser ()
	{
		return $this->wufooUser;
	}

	private function getCurl ($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $this->getApyKey ());
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'FdT curl client');

		return $curl;
	}

	private function buildUrl ($query)
	{
		$url = 'https://';
		$url .= $this->getWufooUser ();
		$url .= '.wufoo.com/api/v3/';
		$url .= $query;

		return $url;

	}

	private function fetch ($query)
	{
		$url = $this->buildUrl ($query);
		$curl = $this->getCurl ($url);

		$response = curl_exec($curl);
		$resultStatus = curl_getinfo($curl);

		if($resultStatus['http_code'] == 200) {
    		return json_decode($response, true);
		} else {
    		echo 'Call Failed '.print_r($resultStatus);
		}


	}

	private function getEntry($entryId, $formHash)
	{

		$queryEntry = 'forms/'.$formHash.'/entries.json?system=true&Filter1=EntryId+Is_equal_to+'.$entryId;
		$result = ($this->fetch($queryEntry));

		if (count ($result['Entries']) == 1)
		{
			return $result['Entries'][0];
		}

		return false;
	}

	private function getFields($formHash)
	{

		$queryEntry = 'forms/'.$formHash.'/fields.json';
		$result = ($this->fetch($queryEntry));

		if (count ($result['Fields']) >= 1)
		{
			return $result;
		}

		return false;
	}

	private function getForm($formHash)
	{

		$queryEntry = 'forms/'.$formHash.'.json';
		$result = ($this->fetch($queryEntry));

		if (count ($result['Forms']) >= 1)
		{
			return $result['Forms'][0];
		}

		return false;
	}

	private function getAllEntryData ($entryId, $formHash, $returnAsJoson = false)
	{
		$arrayData = $this->getEntry($entryId, $formHash);

		if (!$arrayData)
		{
			return array ();
		}
		$arrayData['FieldStructure'] = $this->getFields ($formHash);
		$arrayData['FormStructure'] = $this->getForm ($formHash);

		if ($returnAsJoson)
		{
			return (json_encode ($arrayData));
		}
		return ($arrayData);

	}

	private function parseFieldStructure (array $arrayFieldsStructure, array $arrayWufooData)
    {
        foreach ($arrayFieldsStructure as $fieldStructure)
        {
            if ($fieldStructure['Type'] == 'shortname')
            {
                foreach ($fieldStructure['SubFields'] as $subfield)
                {
                    if (strtolower($subfield['Label']) == 'nome' or strtolower($subfield['Label']) == 'name')
                    {
                        $this->arrayFieldsMap['nome'] = $arrayWufooData[$subfield['ID']];
                    }

                    if (strtolower($subfield['Label']) == 'cognome' or strtolower($subfield['Label']) == 'second name')
                    {
                        $this->arrayFieldsMap['cognome'] = $arrayWufooData[$subfield['ID']];
                    }
                }
            }

            if ($fieldStructure['Type'] == 'email')
            {
                $this->arrayFieldsMap['email'] = $arrayWufooData[$fieldStructure['ID']];
            }

            if ($fieldStructure['Type'] == 'textarea' and  (strtolower($fieldStructure['Title']) == 'messaggio'))
            {
                $this->arrayFieldsMap['text'] = $arrayWufooData[$fieldStructure['ID']];
            }

            if (strtolower($fieldStructure['Title']) == 'telefono')
            {
                $this->arrayFieldsMap['telefono'] = $arrayWufooData[$fieldStructure['ID']];
            }

            if (strtolower($fieldStructure['Title']) == 'ragione sociale')
            {
                $this->arrayFieldsMap['ragioneSociale'] = $arrayWufooData[$fieldStructure['ID']];
            }

            if (strtolower($fieldStructure['Title']) == 'sessionfieldcode' and $fieldStructure['ClassNames'] == 'hide')
            {
                $this->arrayFieldsMap['sessionFieldCode'] = $arrayWufooData[$fieldStructure['ID']];
            }
        }

    }

    private function parseFormStructure (array $arrayFormStructure)
	{

		$this->arrayFieldsMap['formHash'] = $arrayFormStructure['Hash'];
		$this->arrayFieldsMap['formName'] = $arrayFormStructure['Name'];

	}

	private function getSiteId ()
	{
		SS_Log::log('SubsiteID: '.Subsite::currentSubsiteID(), SS_Log::NOTICE);

		if (Subsite::currentSubsiteID() >=0)
		{
			return Subsite::currentSubsiteID();
		}

		return false;
	}

	private function getSubite ()
	{

		if ($this->getSiteId () >= 0) {
			$subsite = DataObject::get_by_id("Subsite", $this->getSiteId () );
			return ($subsite);
		}
		return false;
	}

    public function manageFields (array $arrayWufooData = array ())
    {
    	if (count ($arrayWufooData) == 0)
    	{
    		return $arrayWufooData;
    	}

        $arrayFieldsStructure = ($arrayWufooData['FieldStructure']);
        $this->parseFieldStructure ($arrayFieldsStructure['Fields'], $arrayWufooData);

        $arrayFormStructure = $arrayWufooData['FormStructure'];
        $this->parseFormStructure ($arrayFormStructure, $arrayWufooData);

        $this->arrayFieldsMap['SitoID'] = $this->getSiteId ();
        $this->arrayFieldsMap['wfID'] = $arrayWufooData['EntryId'];
        $this->arrayFieldsMap['clientIP'] = $arrayWufooData['IP'];
        $this->arrayFieldsMap['extraFields'] = serialize($arrayWufooData);

    }

    private function generateLead($entryId, $formHash)
	{

		$arrayFieldMap = $this->getArrayFieldsMap($entryId, $formHash);

		if (count($arrayFieldMap) == 0)
		{
			return false;
		}

		$lead = new Leads();
		foreach ($arrayFieldMap as $field=>$value)
		{
			$lead->$field = $value;
		}

		SS_Log::log('Email: '.$lead->email, SS_Log::NOTICE);

		return $lead;
	}

	public function addLead($entryId, $formHash)
	{
		$lead = $this->generateLead($entryId, $formHash);
		if ($lead)
		{
			return $lead->write();
		}

		return false;

	}

}