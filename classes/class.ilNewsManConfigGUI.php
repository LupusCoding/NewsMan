<?php

require_once ('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsEditorGUI.php');
require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsAdminGUI.php');

use \LC\ILP\NewsMan\DataObjects\Settings;

/**
 * Class ilNewsManConfigGUI
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 *
 */
class ilNewsManConfigGUI extends ilPluginConfigGUI
{
	/** @var \ilNewsManPlugin */
	protected $plugin;

	/** @var \ilCtrl */
	protected $ctrl;

	/** @var \ilLanguage */
	protected $lng;

	/** @var \ilTemplate */
	protected $tpl;

	/**
	 * ilNewsManConfigGUI constructor.
	 */
	public function construct() {
		global $DIC;

		$this->plugin = ilNewsManPlugin::getInstance();
		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->tpl = $DIC["tpl"];
	}

	/**
	 * @param $cmd
	 * @return void
	 */
	public function performCommand($cmd) {
		$this->construct();
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				switch ($cmd) {
					default:
						$this->{$cmd}();
						break;
				}
				break;
		}
	}

	/**
	 * @return void
	 */
	protected function configure() {
		$form = $this->getConfigurationForm();

		$this->tpl->setContent($form->getHTML());
	}

	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getConfigurationForm()
	{
		$form = new ilPropertyFormGUI();
//		$form->setTitle($this->plugin->getPluginName() . ' ' . $this->txt("plugin_configuration"));

		$settings = new Settings($this->plugin->getSettings());
		$langHelper = new \LC\ILP\NewsMan\Helper\LanguageHelper();
		$this->lng->loadLanguageModule("meta");

		$sh = new \ilFormSectionHeaderGUI();
		$sh->setTitle($this->txt('section_defaults'));
		$form->addItem($sh);

		$cb = new \ilCheckboxInputGUI($this->txt('default_set_active'), Settings::DEFAULT_ACTIVE);
		$cb->setInfo($this->txt('default_set_active_info'));
		$cb->setValue(true);
		$cb->setChecked(($settings->getDefaultActive() == true));
		$form->addItem($cb);
		unset($cb);

		$lang = new \ilCheckboxInputGUI($this->txt('support_lang'), Settings::SUPPORT_LANG);
		$lang->setInfo($this->txt('support_lang_info'));
		$lang->setValue(true);
		$lang->setChecked(($settings->getSupportLang() == true));

		$si = new \ilSelectInputGUI($this->txt('default_lang'), Settings::DEFAULT_LANG);
		$languages = [];
		foreach ($langHelper->getInstalledLanguages(true) as $lid => $lkey) {
			$languages[$lid] = $this->lng->txt("meta_l_".$lkey);
		}
		$si->setOptions($languages);
		$si->setValue(($settings->getDefaultLang() > 0 ? $settings->getDefaultLang() : $this->lng->getDefaultLanguage()));
		$lang->addSubItem($si);
		unset($si);

		$form->addItem($lang);

		$sh = new \ilFormSectionHeaderGUI();
		$sh->setTitle($this->txt('section_editor'));
		$form->addItem($sh);

		$ni = new \ilNumberInputGUI($this->txt('content_min_chars'), Settings::CONTENT_MIN_CHARS);
		$ni->setInfo($this->txt('content_min_chars_info'));
		$ni->setValue($settings->getContentMinChars());
		$form->addItem($ni);
		unset($ni);

		$ni = new \ilNumberInputGUI($this->txt('content_max_chars'), Settings::CONTENT_MAX_CHARS);
		$ni->setInfo($this->txt('content_max_chars_info'));
		$ni->setValue($settings->getContentMaxChars());
		$form->addItem($ni);
		unset($ni);

		$cbg = new \ilCheckboxGroupInputGUI($this->txt('content_allowed_tags'), Settings::CONTENT_ALLOWED_TAGS);
		foreach ($settings->getContentPossibleTags() as $tag) {
			$cbo = new \ilCheckboxOption($tag, $tag);
			$cbg->addOption($cbo);
		}
		$cbg->setValue($settings->getContentAllowedTags());
		$form->addItem($cbg);


		$form->addCommandButton("save", $this->lng->txt("save"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		return $form;
	}

	public function save()
	{
		$form = $this->getConfigurationForm();
		$settings = new Settings($this->plugin->getSettings());

		if ($form->checkInput()) {
			// save...
			if ($_POST[Settings::DEFAULT_ACTIVE]) {
				$settings->setDefaultActive($_POST[Settings::DEFAULT_ACTIVE]);
			}
			if ($_POST[Settings::SUPPORT_LANG]) {
				$settings->setSupportLang($_POST[Settings::SUPPORT_LANG]);
			}
			if ($_POST[Settings::DEFAULT_LANG]) {
				$settings->setDefaultLang($_POST[Settings::DEFAULT_LANG]);
			}
			if ($_POST[Settings::CONTENT_MIN_CHARS]) {
				$settings->setContentMinChars($_POST[Settings::CONTENT_MIN_CHARS]);
			}
			if ($_POST[Settings::CONTENT_MAX_CHARS]) {
				$settings->setContentMaxChars($_POST[Settings::CONTENT_MAX_CHARS]);
			}
			if ($_POST[Settings::CONTENT_ALLOWED_TAGS]) {
				$settings->setContentAllowedTags($_POST[Settings::CONTENT_ALLOWED_TAGS]);
			}

			ilUtil::sendSuccess($this->txt("saving_invoked"), true);
			$this->ctrl->redirect($this, "configure");

		} else {
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHtml());
		}
	}

	/**
	 * @param $a_var
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->plugin->txt($a_var);
	}

}