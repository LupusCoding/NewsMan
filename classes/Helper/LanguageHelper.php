<?php

namespace LC\ILP\NewsMan\Helper;

class LanguageHelper
{
	/**
	 * @param bool $stripped
	 * @return array
	 */
	public function getInstalledLanguages(bool $stripped = false)
	{
		$langlist = \ilObject::_getObjectsByType("lng");
		if ($stripped) {
			$installed = [];
			foreach ($langlist as $lang_id => $lang) {
				if (substr($lang["desc"], 0, 9) == "installed") {
					$installed[$lang_id] = $lang['title'];
				}
			}
			$langlist = $installed;
		}
		return $langlist;
	}
}