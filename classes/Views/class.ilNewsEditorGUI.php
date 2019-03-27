<?php
require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsAdminGUI.php');

use \LC\ILP\NewsMan\DataObjects\Settings;

/**
 * Class ilNewsEditorGUI
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 *
 * @ilCtrl_isCalledBy ilNewsEditorGUI:ilUIPluginRouterGUI
 */
class ilNewsEditorGUI extends \LC\ILP\NewsMan\Views\AbstractView
{
	const ID         = 'newsman_editor';
	const TITLE      = 'newsman_editor';
	const CMD_INDEX  = 'index';
	const CMD_EDIT   = 'edit';
	const CMD_SAVE   = 'save';
	const CMD_CANCEL = 'cancel';

	/** @var ilNewsEditorGUI */
	protected static $instance;

	/** @var string  */
	protected static $cmd_index = self::CMD_INDEX;

	/**
	 * @return string
	 */
	public static function getEntryLink(string $cmd = null): string
	{
		global $DIC;
		if (!isset($cmd)) {
			$cmd = self::$cmd_index;
		}
		return $DIC->ctrl()->getLinkTargetByClass([
			\ilUiPluginRouterGUI::class,
			get_class()
		], $cmd);
	}

	/**
	 * @return ilNewsEditorGUI|\LC\ILP\NewsMan\Views\AbstractView
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return array
	 */
	public function getTabs(): array
	{
		return [];
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return \ilNewsManPlugin::getInstance()->txt(self::TITLE);
	}

	/**
	 * @return string
	 */
	public function getActiveTab(): string
	{
		return '';//self::TAB_NAME;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return self::ID;
	}

	/**
	 * @return void
	 */
	public function cancel()
	{
		$this->ctrl->redirect(\ilNewsAdminGUI::getInstance(), \ilNewsAdminGUI::CMD_INDEX);
	}

	/**
	 * @return void
	 */
	protected function index()
	{
		$form = $this->getEditForm(self::CMD_INDEX);

		$this->tpl->setContent($form->getHTML());
	}

	/**
	 * @return void
	 */
	protected function edit()
	{
		if (isset($_GET['nm_id'])) {
			$id = $_GET['nm_id'];
		} else {
			\ilUtil::sendInfo($this->txt('err_invalid_id'), true);
			$this->cancel();
		}
		$entry = new \LC\ILP\NewsMan\DataObjects\NewsEntry($this->database);
		$form = $this->getEditForm(self::CMD_EDIT);

		if ($entry->loadById($id)) {
			$authorObj = new \ilObjUser($entry->getAuthor());
			$_POST['id']            = $entry->getId();
			$_POST['title']         = $entry->getTitle();
			$_POST['content']       = $entry->getContent();
			$_POST['created_at']    = $entry->getCreatedAt();
			$_POST['active']        = $entry->getActive();
			$_POST['author']        = $entry->getAuthor();
			$_POST['lang']          = $entry->getLang();
			$_POST['nopost']        = $authorObj->getFullname();

		} else {
			\ilUtil::sendInfo($this->txt('err_load_entry_by_id') . ' ' . $id);
		}
		$form->setValuesByPost();
		$this->tpl->setContent($form->getHTML());
	}

	/**
	 * @return void
	 */
	protected function save()
	{
		$form = $this->getEditForm(self::CMD_SAVE);

		if ($form->checkInput() && isset($_POST['content'])) {

			$values = [
				'id' => $_POST['id'],
				'title' => $_POST['title'],
				'content' => $_POST['content'],
				'created_at' => $_POST['created_at'],
				'updated_at' => time(), // we want to save always current time as update time
				'active' => $_POST['active'],
				'author' => $_POST['author'],
				'lang' => $_POST['lang'],
			];

			$entry = new \LC\ILP\NewsMan\DataObjects\NewsEntry($this->database);
			$entry->setValuesByArray($values);
			$entry->save();

			ilUtil::sendSuccess($this->txt("saving_invoked"), true);
			$this->ctrl->redirect(\ilNewsAdminGUI::getInstance(), \ilNewsAdminGUI::CMD_INDEX);

		} else {
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHtml());
		}

	}

	/**
	 * @param $cmd
	 * @return ilPropertyFormGUI
	 */
	protected function getEditForm($cmd)
	{
		global $DIC;

		$form = new \ilPropertyFormGUI();
		$settings = new Settings($this->plugin->getSettings());
		$langHelper = new \LC\ILP\NewsMan\Helper\LanguageHelper();
		$this->lng->loadLanguageModule("meta");


		$sh = new \ilFormSectionHeaderGUI();
		if ($cmd === self::CMD_EDIT) {
			$sh->setTitle($this->txt('editor_edit'));
		} else {
			$sh->setTitle($this->txt('editor_create'));
		}
		$form->addItem($sh);

		$hif = new \ilHiddenInputGUI('id');
		if ($cmd === self::CMD_INDEX) {
			$hif->setValue(-1);
		}
		$form->addItem($hif);

		$hif = new \ilHiddenInputGUI('created_at');
		if ($cmd === self::CMD_INDEX) {
			$hif->setValue(time());
		}
		$form->addItem($hif);

		$hif = new \ilHiddenInputGUI('author');
		if ($cmd === self::CMD_INDEX) {
			$hif->setValue($DIC->user()->getId());
		}
		$form->addItem($hif);

		$title = new \ilTextInputGUI($this->lng->txt('title'),'title');
		$title->setSize(32);
		$title->setMaxLength(32);
		$title->setRequired(true);
		$form->addItem($title);

		$content = new \ilTextAreaInputGUI('', 'content');
		$content->setRows(10);
		$content->setMinNumOfChars($settings->getContentMinChars());
		$content->setMaxNumOfChars($settings->getContentMaxChars());
		$content->setUseRte(TRUE, '3.4.7');
		$content->setRteTags($settings->getContentAllowedTags());
		$form->addItem($content);

		if ($settings->getSupportLang()) {
			$lang = new \ilSelectInputGUI($this->lng->txt('language'), "lang");
			$languages = [];
			foreach ($langHelper->getInstalledLanguages(true) as $lid => $lkey) {
				$languages[$lid] = $this->lng->txt("meta_l_" . $lkey);
			}
			$lang->setOptions($languages);
			if ($cmd === self::CMD_INDEX) {
				$lang->setValue(($settings->getDefaultLang() > 0 ? $settings->getDefaultLang() : $this->lng->getDefaultLanguage()));
			}
		} else {
			$lang = new \ilHiddenInputGUI('lang');
			if ($cmd === self::CMD_INDEX) {
				$lang->setValue(($settings->getDefaultLang() > 0 ? $settings->getDefaultLang() : $this->lng->getDefaultLanguage()));
			}
		}
		$form->addItem($lang);

		$active = new ilCheckboxInputGUI($this->lng->txt("active"), "active");
		if ($cmd === self::CMD_INDEX) {
			$active->setChecked($settings->getDefaultActive());
		}
		$form->addItem($active);

		$nopost_author = new ilNonEditableValueGUI($this->txt('author'), 'nopost');
		if ($cmd === self::CMD_INDEX) {
			$author = $DIC->user()->getFullname();
		}
		$nopost_author->setValue($author);
		$form->addItem($nopost_author);

		$form->addCommandButton(self::CMD_SAVE, $this->lng->txt("save"));
		$form->addCommandButton(self::CMD_CANCEL, $this->lng->txt("cancel"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		return $form;
	}

}
