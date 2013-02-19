<?php
trait FdTLandingPageControllerTrait{

	public static function baseTag() {
		$base = Director::absoluteBaseURL();
		// Is the document XHTML?
		return "<base href=\"$base\"><!--[if lte IE 6]></base><![endif]-->";
	}

	private function getSubsite ()
	{
		$subsite = false;
		if ($this->SubsiteID) {
			$subsite = DataObject::get_by_id("Subsite", $this->SubsiteID);
		}

		return ($subsite);
	}

	private function getFormHashFromJs ($formCode)
	{
		$fieldString = '/<div id="wufoo-(.*)"/';
		preg_match ($fieldString, $formCode, $formHash);
		if (count ($formHash) == 2)
		{
			return $formHash[1];
		}
		trigger_error ( 'La stringa <div id=\"wufoo-codiceHash\"> non è presente nel codice del form');
	}

	public function replaceTemplateDefaultValues ($formCode, $sessionFieldCode = false)
	{
		Session::clear ('FormSessionID');
		if (!$sessionFieldCode or $sessionFieldCode == '')
		{
			trigger_error ( 'Compila il campo sessionFieldCode nella pagina o nei settaggi del sito');

		}

		if (stripos ($formCode, '{%defaultValues%}') === false)
		{
			trigger_error ( 'Inserisci la stringa \'defaultValues\':\'{%defaultValues%}\', nel codice di Wufoo dopo la stringa \'autoResize\':true,');
		}

		$formHash = $this->getFormHashFromJs($formCode);

		$sessionFormID = uniqid('form', true).'_'.$formHash;
		$urlParameters = strtolower($sessionFieldCode).'='.$sessionFormID;
		Session::set ('FormSessionID', $sessionFormID);
		$formCode = str_replace('{%defaultValues%}', $urlParameters, $formCode);
		return $formCode;
	}

	public function getCtaForm()
	{

		if ($this->HasForm == 'SI')
		{

			if ($this->FormCode != '')
			{
				$FormCode = $this->FormCode;
				$sessionFieldCode = $this->sessionFieldCode;
			}
			else if ($this->FormCode == '' and $this->Parent()->FormCode != '')
			{
				$FormCode = $this->Parent()->FormCode;
				$sessionFieldCode = $this->Parent()->sessionFieldCode;
			}
			else if ($this->FormCode == '' and $this->Parent()->FormCode == '' and $this->getSubsite())
			{
				$FormCode = $this->getSubsite()->FormCode;
				$sessionFieldCode = $this->getSubsite()->sessionFieldCode;
			}
			else
			{
				return null;
			}
			return $this->replaceTemplateDefaultValues($FormCode, $sessionFieldCode);
		}

		return NULL;

	}

	public function init() {
		parent::init();

		SS_Log::add_writer(new SS_LogFileWriter(BASE_PATH.'/logs/error.log'), SS_Log::NOTICE);

		if ($this->SubsiteID) {
			$subsite = $this->getSubsite();

			TwigContainer::extendConfig(array(
			    'twig.template_paths' => array(
			        THEMES_PATH . '/leadManager/'.$subsite->TemplateName,
			        THEMES_PATH . '/leadManager/base',
			        THEMES_PATH . '/leadManager'
			    )
			));
		}

	}

}