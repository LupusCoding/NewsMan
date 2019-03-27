<?php

namespace LC\ILP\NewsMan\Views\Tables;

use LC\ILP\NewsMan\Closure\Factory;
use LC\ILP\NewsMan\Views\AbstractView;

/**
 * Class AbstractTable
 * @package LC\ILP\NewsMan\Views\Tables
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
abstract class AbstractTable extends \ilTable2GUI
{
	const DEFAULT_PREFIX = 'newsman_dt';
	const DEFAULT_FORM_NAME = 'newsman_dt';
	const DEFAULT_ID = 'newsman_dt';
	const DEFAULT_ROW_TPL = 'tpl.table_row.html';

	/** @var \ilAccessHandler  */
	protected $access;

	/** @var \ilNewsManPlugin */
	protected $plugin;

	/** @var \ilLanguage  */
	protected $lng;

	/** @var \ilObjUser  */
	protected $user;

	/** @var \ILIAS\DI\UIServices  */
	protected $ui;

	/** @var array  */
	protected $filter;

	/** @var string */
	protected $row_template;

	/** @var Factory  */
	protected $closure;

	/**
	 * @return array
	 */
	abstract protected function getColumns(): array;

	/**
	 * @return void
	 */
	abstract protected function parseData();

	/**
	 * @return array
	 */
	public function getSelectableColumns(): array
	{
		return [];
		/*
		 * example:
		 * return [
		 *   'usr_login' => [
		 *     'txt' => $this->lng->txt('login'),
		 *     'default' => true,
		 *   ],
		 * ];
		 */
	}

	/**
	 * @return array
	 */
	public function getCheckboxColumns(): array
	{
		return [];
		/*
		 * example:
		 * return [
		 *   'usr_id'
		 * ];
		 */
	}

	/**
	 * @return array
	 */
	public function getActionColumns(): array
	{
		return [];
		/*
		 * example:
		 * return [
		 *   'modify' => [
		 *     'action_link' => $this->ctrl->getLinkTargetByClass('myViewClass', "modify", false, true, false),
		 *     'action_text' => $this->lng->txt('modify'),
		 *   ],
		 * ];
		 */
	}

	/**
	 * @return array
	 */
	public function getCustomColumns(): array
	{
		// overwrite this to return an array of your custom columns
		return [];
		/*
		 * example:
		 * return [
		 *   'custom_column' => [
		 *     'any_key' => 'any_value',
		 *     'another_key' => 'another_value,
		 *   ],
		 * ];
		 */
	}

	/**
	 * @return array
	 */
	public function getCommandButtons(): array
	{
		return [];
		/*
		 * example:
		 * return [
		 *   [
		 *     'multi_command' => true|false,
		 *     'cmd' => 'command_name',
		 *     'txt' => $this->lng->txt('text'),
		 *   ],
		 * ];
		 */
	}

	/**
	 * @return string
	 */
	public function getHTML() {
		return parent::getHTML();
	}

	/**
	 * @param string $key
	 * @param $value
	 * @param \Closure|null $propGetter
	 * @return void
	 */
	protected function fillCustomColumn(string $key, $value, \Closure $propGetter = null)
	{
		// overwrite this function to process your own column behavior
		$this->fillEmptyColumn();
	}

	/**
	 * @param $data
	 * @return array
	 */
	protected function getFieldValuesForExport($data): array
	{
		$propGetter = $this->closure->propGetter($data);

		$field_values = array();
		$selectable = array_keys($this->getSelectableColumns());
		$checkboxes = $this->getCheckboxColumns();
		foreach ($this->getColumns() as $k => $v) {
			if (in_array($k, $selectable)) {
				if (!$this->isColumnSelected($k)) {
					continue;
				}
			} else if (in_array($k, $checkboxes)) {
				continue;
			}
			switch ($k) {
				default:
					$v = $propGetter($k);
					if ($v !== NULL) {
						$field_values[$k] = (is_array($v) ? implode(", ", $v) : $v);
					} else {
						$field_values[$k] = '';
					}
					break;
			}
		}

		return $field_values;
	}

	/**
	 * AbstractTable constructor.
	 * @param AbstractView $parent_obj
	 * @param string $parent_cmd
	 */
	final public function __construct(AbstractView $parent_obj, string $parent_cmd = 'index')
	{
		$this->setPrefix(self::DEFAULT_PREFIX);
		$this->setFormName(self::DEFAULT_FORM_NAME);
		$this->setId(self::DEFAULT_ID);

		parent::__construct($parent_obj, $parent_cmd);

		global $DIC;
		$this->access   = $DIC->access();
		$this->plugin   = \ilNewsManPlugin::getInstance();
		$this->lng      = $DIC->language();
		$this->user     = $DIC->user();
		$this->ui       = $DIC->ui();
		$this->report   = $parent_obj;
		$this->filter   = [];
		$this->closure  = new Factory();
		$this->row_template = self::DEFAULT_ROW_TPL;

		$this->setTable();
	}

	/**
	 * @param $data
	 * @return void
	 */
	protected function fillRow($data)
	{
		$propGetter = $this->closure->propGetter($data);

		$selectable = array_keys($this->getSelectableColumns());
		$checkboxes = $this->getCheckboxColumns();
		$customs = $this->getCustomColumns();

		foreach ($this->getColumns() as $k => $v) {
			$this->fillColumn($k, $v, $selectable, $checkboxes, $customs, $propGetter);
		}
		foreach ($this->getActionColumns() as $name => $data) {
			$this->fillActionColumn($data['action_link'], $data['action_text']);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param array $selectable
	 * @param array $checkboxes
	 * @param array $customs
	 * @param \Closure|null $propGetter
	 * @return void
	 */
	final protected function fillColumn(
		string $key,
		$value,
		array $selectable = [],
		array $checkboxes = [],
		array $customs = [],
		\Closure $propGetter = null
	) {

		if ($value !== NULL) {
			if (in_array($key, $selectable)) {
				if (!$this->isColumnSelected($key)) {
					return;
				}
			}
			if (in_array($key, $customs)) {
				$this->fillCustomColumn($key, $value, $propGetter);

			} else if (in_array($key, $checkboxes)) {
				$this->fillCheckboxColumn($key, $propGetter($key));

			} else {
				if (isset($propGetter)) {
					$value = $propGetter($key);
				}
				$this->fillDefaultColumn($value);
			}
		} else {
			if (in_array($key, $customs)) {
				$this->fillCustomColumn($key, $value, $propGetter);

			} else {
				$this->fillEmptyColumn();
			}
		}

	}

	/**
	 * @param $value
	 * @param string $array_glue
	 * @return void
	 */
	final protected function fillDefaultColumn($value, string $array_glue = ", ")
	{
		$this->tpl->setCurrentBlock('td');
		$this->tpl->setVariable('VALUE', (is_array($value) ? implode($array_glue, $value) : $value));
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * @return void
	 */
	final protected function fillEmptyColumn()
	{
		$this->tpl->setCurrentBlock('td');
		$this->tpl->setVariable('VALUE', '&nbsp;');
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	final protected function fillCheckboxColumn(string $key, string $value)
	{
		$this->tpl->setCurrentBlock('checkbox');
		$this->tpl->setVariable('CBNAME', $key);
		$this->tpl->setVariable('CBVAL', $value);
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * @param string $link
	 * @param string $text
	 * @return void
	 */
	final protected function fillActionColumn(string $link, string $text)
	{
		$this->tpl->setCurrentBlock('action');
		$this->tpl->setVariable('ACTION_LINK', $link);
		$this->tpl->setVariable('ACTION_TEXT', $text);
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * @return void
	 */
	final protected function setTable()
	{
		$this->setRowTemplate($this->row_template, $this->plugin->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($this->getParentObject()));

		$this->setShowRowsSelector(true);

		$this->setEnableTitle(true);
		$this->setDisableFilterHiding(true);
		$this->setEnableNumInfo(true);

		$this->setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));

		$this->setFilterCols(5);
		$this->initFilter();

		$this->addColumns();

		foreach ($this->getCommandButtons() as $cmd_button) {
			if (isset($cmd_button['multi_command']) && $cmd_button['multi_command'] === true) {
				$this->addMultiCommand($cmd_button['cmd'], $cmd_button['txt']);
			} else {
				$this->addCommandButton($cmd_button['cmd'], $cmd_button['txt']);
			}
		}

		$this->parseData();
	}

	/**
	 * @return void
	 */
	final protected function addColumns()
	{
		$selectable = array_keys($this->getSelectableColumns());
		foreach ($this->getColumns() as $k => $v) {
			if (in_array($k, $selectable)) {
				if (!$this->isColumnSelected($k)) {
					continue;
				}
			}
			if (isset($v['sort_field'])) {
				$sort = $v['sort_field'];
			} else {
				$sort = NULL;
			}
			if (isset($v['is_checkbox_action_column'])) {
				$checkbox = $v['is_checkbox_action_column'];
			} else {
				$checkbox = false;
			}
			$this->addColumn($v['txt'], $sort, $v['width'], $checkbox);
		}
		if (!empty($this->getActionColumns())) {
			$this->addColumn($this->lng->txt('actions'), null, 'auto');
		}
	}

	/**
	 * @param \ilExcel $a_excel
	 * @param int $a_row
	 * @param $data
	 * @return void
	 */
	final protected function fillRowExcel(\ilExcel $a_excel, &$a_row, $data)
	{
		$col = 0;
		foreach ($this->getFieldValuesForExport($data) as $k => $v) {
			$a_excel->setCell($a_row, $col, $v);
			$col ++;
		}
	}


	/**
	 * @param object           $a_csv
	 * @param $data
	 */
	final protected function fillRowCSV($a_csv, $data)
	{
		foreach ($this->getFieldValuesForExport($data) as $k => $v) {
			$a_csv->addColumn($v);
		}
		$a_csv->addRow();
	}

}