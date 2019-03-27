<?php

require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsEditorGUI.php');
require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsAdminGUI.php');

/**
 * Class ilNewsManMenuProvider
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class ilNewsManMenuProvider
{
	/**
	 * @return array
	 */
	public static function getMenuDefinition()
	{
		global $DIC;
		$plugin = \ilNewsManPlugin::getInstance();
		if ($DIC['autoload.lc.lcautoloader'] == '') {
			return [];
		}
//		if (!$DIC->access()->checkAccess('read', '', 7)) {
//			return [];
//		}

		$settings = new \LC\ILP\NewsMan\DataObjects\Settings($plugin->getSettings());

		// @Todo check which links should be shown in menu

		$DIC->language()->loadLanguageModule('ui_uihk_' . \ilNewsManPlugin::PLUGIN_ID);
		return [
			$DIC->language()->txt(
				'ui_uihk_' . \ilNewsManPlugin::PLUGIN_ID . '_manager'
			) => [
				'sub' => [
					$DIC->language()->txt(
						'ui_uihk_' . \ilNewsManPlugin::PLUGIN_ID . '_' . \ilNewsAdminGUI::TITLE
					) => ['link' => \ilNewsAdminGUI::getEntryLink(), 'attributes' => 'target="_self"'],
					$DIC->language()->txt(
						'ui_uihk_' . \ilNewsManPlugin::PLUGIN_ID . '_' . \ilNewsEditorGUI::TITLE
					) => ['link' => \ilNewsEditorGUI::getEntryLink(), 'attributes' => 'target="_self"'],
				]
			]
		];
	}

}