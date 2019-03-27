<?php

require_once ('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');

/**
 * Class ilNewsManUIHookGui
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class ilNewsManUIHookGui extends ilUIHookPluginGUI
{
	const TABS_PART = "tabs";

	/**
	 * @param $a_comp
	 * @param $a_part
	 * @param array $a_par
	 * @return array|void
	 */
	function getHTML($a_comp, $a_part, $a_par = array())
	{
		/* modify html */
//		if($a_comp == "Services/PersonalDesktop" /*&& $a_part = "center_column"*/) {
//			global $DIC;
//
//			$DIC->ctrl()->redirectByClass(array('ilUiPluginRouterGUI','SkillPointsGUI'), 'index', false, false);
//		}
	}

	/**
	 * @param $a_comp
	 * @param $a_part
	 * @param array $a_par
	 * @return void
	 */
	public function modifyGUI($a_comp, $a_part, $a_par = [])
	{
		/* modify tabs */
//		if ($a_part === self::TABS_PART) {
//			global $DIC;
//			if ($this->isSpecificGUI('myClass')) {}
//		}
	}

	/**
	 * @param string $class_name
	 * @return bool
	 */
	protected function isSpecificGUI(string $class_name): bool {
		global $DIC;
		return (count(array_filter($DIC->ctrl()->getCallHistory(), function (array $history, string $class_name): bool {
				return (strtolower($history["class"]) === strtolower($class_name));
			})) > 0);
	}
}