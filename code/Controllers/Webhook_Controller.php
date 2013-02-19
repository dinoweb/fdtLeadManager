<?php

class Webhook_Controller extends ContentController {

    private function getManageWufooClass ()
    {
        return new manageWufoo ();
    }


    private function getParam ($paramName)
	{
		$request = $this->getRequest ();
		$params =  array_merge($request->allParams (), $request->postVars());

		if ($paramName == 'ALL')
		{
			return $params;
		}

		if (array_key_exists($paramName, $params))
		{
			return $params[$paramName];
		}

		return false;
	}

	private function getFormHash ()
	{
		$formStructure = $this->getParam ('FormStructure');
		$arrayFormStructure = json_decode($formStructure, true);
		return $arrayFormStructure['Hash'];
	}


    public function add($arguments) {

    	SS_Log::add_writer(new SS_LogFileWriter(BASE_PATH.'/logs/webhook.log'), SS_Log::NOTICE);

    	$manageWufoo = $this->getManageWufooClass ();

    	if ($this->getParam ('EntryId'))
    	{
    		$lead = $manageWufoo->addLead ($this->getParam ('EntryId'), $this->getFormHash());
    		return $lead;
    	}
    	return 'KO';


    }
}