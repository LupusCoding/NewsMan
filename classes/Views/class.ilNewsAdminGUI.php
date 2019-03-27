<?php
require_once ('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/NewsMan/classes/Views/class.ilNewsEditorGUI.php');

/**
 * Class ilNewsAdminGUI
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 *
 * @ilCtrl_isCalledBy ilNewsAdminGUI:ilUIPluginRouterGUI
 */
class ilNewsAdminGUI extends \LC\ILP\NewsMan\Views\AbstractTableView
{
	const ID             = 'newsman_admin';
	const TITLE          = 'newsman_administration';
	const CMD_INDEX      = 'index';
	const CMD_DELETE     = 'delete';
	const CMD_ACTIVATE   = 'activate';
	const CMD_DEACTIVATE = 'deactivate';

	/** @var ilNewsAdminGUI */
	protected static $instance;

	/** @var string  */
	protected static $cmd_index = self::CMD_INDEX;

	/** @var \LC\ILP\NewsMan\Helper\BulkHelper */
	protected $bulk_helper;

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
	 * @return ilNewsAdminGUI
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

	public function getData(array $options = array(), array $parameters = null)
	{
		//Permissions
		// @ToDo check permissions?

		$_options = array(
			'filters' => array(),
			'sort' => array(),
			'limit' => array(),
			'count' => false,
		);
		$options = array_merge($_options, $options);

		$data = [];
		// @ToDo get "real" data
		$data = $this->bulk_helper->getAllEntries();

		return $data;
	}

	protected function applyFilter()
	{
		// TODO: Implement applyFilter() method.
//		$this->table = ???;
		$this->table->writeFilterToSession();
		$this->table->resetOffset();
		$this->index();
	}

	protected function resetFilter()
	{
		// TODO: Implement resetFilter() method.
//		$this->table = ???;
		$this->table->resetOffset();
		$this->table->resetFilter();
		$this->index();
	}


	protected function index()
	{
		$this->preinit();

		$new_button = \ilLinkButton::getInstance();
		$new_button->setCaption($this->txt('new_entry'), false);
		$new_button->setUrl(\ilNewsEditorGUI::getEntryLink());
		$this->toolbar->addButtonInstance($new_button);

		$this->table = new \LC\ILP\NewsMan\Views\Tables\OverviewTable($this);
		$this->tpl->setContent($this->table->getHTML());
	}

	protected function deleteConfirmation()
	{
		$this->preinit();
		if (!isset($_POST['id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$form = $this->getConfirmationForm('question_delete', 'delete', 'delete');
		$this->appendConfirmationList($form, $_POST['id']);
		$this->tpl->setContent($form->getHTML());
	}

	protected function delete()
	{
		$this->preinit();
		if (!isset($_POST['nm_id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$ids = $_POST['nm_id'];
		$res = $this->bulk_helper->bulkDelete($ids);

		if (count($res['success']) > 0) {
			\ilUtil::sendSuccess($this->txt('delete_successful'), true);

			if (count($res['failed']) > 0) {
				\ilUtil::sendInfo($this->txt('delete_failed_for') . ' ' . implode(', ', $res['failed']), true);
			}
		} else {
			\ilUtil::sendFailure($this->txt('delete_failed'), true);
		}

		$this->ctrl->redirect($this, 'index');
	}

	protected function activateConfirmation()
	{
		$this->preinit();
		if (!isset($_POST['id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$form = $this->getConfirmationForm('question_activate', 'activate', 'activate');
		$this->appendConfirmationList($form, $_POST['id']);
		$this->tpl->setContent($form->getHTML());
	}

	protected function activate()
	{
		$this->preinit();
		if (!isset($_POST['nm_id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$ids = $_POST['nm_id'];
		$res = $this->bulk_helper->bulkActivate($ids);

		if (count($res['success']) > 0) {
			\ilUtil::sendSuccess($this->txt('activate_successful'), true);

			if (count($res['failed']) > 0) {
				\ilUtil::sendInfo($this->txt('activate_failed_for') . ' ' . implode(', ', $res['failed']), true);
			}
		} else {
			\ilUtil::sendFailure($this->txt('activate_failed'), true);
		}

		$this->ctrl->redirect($this, 'index');
	}

	protected function deactivateConfirmation()
	{
		$this->preinit();
		if (!isset($_POST['id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$form = $this->getConfirmationForm('question_deactivate', 'deactivate', 'deactivate');
		$this->appendConfirmationList($form, $_POST['id']);
		$this->tpl->setContent($form->getHTML());
	}

	protected function deactivate()
	{
		$this->preinit();
		if (!isset($_POST['nm_id'])) {
			\ilUtil::sendFailure($this->txt('no_ids_given'), true);
			$this->ctrl->redirect($this, 'index');
		}

		$ids = $_POST['nm_id'];
		$res = $this->bulk_helper->bulkDeactivate($ids);

		if (count($res['success']) > 0) {
			\ilUtil::sendSuccess($this->txt('deactivate_successful'), true);

			if (count($res['failed']) > 0) {
				\ilUtil::sendInfo($this->txt('deactivate_failed_for') . ' ' . implode(', ', $res['failed']), true);
			}
		} else {
			\ilUtil::sendFailure($this->txt('deactivate_failed'), true);
		}

		$this->ctrl->redirect($this, 'index');
	}

	private function getConfirmationForm(string $title, string $cmd, string $cmd_text, string $cmd_cancel = 'index'): \ilPropertyFormGUI
	{
		$form = new \ilPropertyFormGUI();

		$question = new \ilFormSectionHeaderGUI();
		$question->setTitle($this->txt($title));
		$form->addItem($question);

		$form->addCommandButton($cmd, $this->txt($cmd_text));
		$form->addCommandButton($cmd_cancel, $this->lng->txt("cancel"));

		return $form;
	}

	private function appendConfirmationList(\ilPropertyFormGUI $form, array $ids)
	{
		foreach ($ids as $id) {
			$entry = $this->bulk_helper->getEntryById($id);
			if (isset($entry)) {
				$nev = new \ilNonEditableValueGUI('ID: ' . $id);
				$nev->setValue('Title: ' . $entry->getTitle());
				$form->addItem($nev);
				$hi = new \ilHiddenInputGUI('nm_id[]');
				$hi->setValue($id);
				$form->addItem($hi);
				unset($nev);
				unset($hi);
			}
			unset($entry);
		}
	}

	private function preinit()
	{
		$this->bulk_helper = new \LC\ILP\NewsMan\Helper\BulkHelper($this->database);
		$this->lng->loadLanguageModule('ui_uihk_newsman');
	}

}