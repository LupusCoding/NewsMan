<?php

namespace LC\ILP\NewsMan\Views\Tables;

class OverviewTable extends AbstractTable
{

	/**
	 * @return void
	 */
	public function initFilter()
	{
		// @ToDo implement logic
	}

	/**
	 * @return array
	 */
	public function getCheckboxColumns(): array
	{

		return [
			'id',
		];
	}

	/**
	 * @return array
	 */
	public function getCustomColumns(): array
	{
		return [
			'created_at',
			'updated_at',
			'author',
			'lang',
			'active',
			'action',
		];
	}

	/**
	 * @return array
	 */
	public function getCommandButtons(): array
	{
		return [
			[
				'multi_command' => true,
				'cmd' => 'deleteConfirmation',
				'txt' => $this->lng->txt('delete'),
			],
			[
				'multi_command' => true,
				'cmd' => 'activateConfirmation',
				'txt' => $this->lng->txt('activate'),
			],
			[
				'multi_command' => true,
				'cmd' => 'deactivateConfirmation',
				'txt' => $this->lng->txt('deactivate'),
			],
		];
	}


	/**
	 * @param string $key
	 * @param $value
	 * @param \Closure|null $propGetter
	 * @return void
	 */
	protected function fillCustomColumn(string $key, $value, \Closure $propGetter = null)
	{
		switch ($key) {
			case 'created_at':
			case 'updated_at':
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE',  date('Y-m-d H:i:s', $propGetter($key))); // @ToDo fix wrong output
				$this->tpl->parseCurrentBlock();
				break;
			case 'author':
				$user = new \ilObjUser($propGetter($key));
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $user->getFullname());
				$this->tpl->parseCurrentBlock();
				break;
			case 'lang':
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $this->lng->txt($propGetter($key))); // @ToDo check if this is supported
				$this->tpl->parseCurrentBlock();
				break;
			case 'active':
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $this->lng->txt($propGetter($key) == true ? 'active' : 'inactive'));
				$this->tpl->parseCurrentBlock();
				break;
			case 'action':
				$link = \ilNewsEditorGUI::getEntryLink('edit') . '&nm_id=' . $propGetter('id');
				$this->tpl->setCurrentBlock('action');
				$this->tpl->setVariable('ACTION_LINK', $link);
				$this->tpl->setVariable('ACTION_TEXT', $this->lng->txt('edit'));
				$this->tpl->parseCurrentBlock();
				break;
		}
	}

	/**
	 * @return array
	 */
	protected function getColumns(): array
	{
		$cols = [];

		$cols['id'] = [
			'txt' => '',
			'width' => '',
			'sort_field' => 'id',
			'is_checkbox_action_column' => true,
			'default' => true,
		];

		$cols['title'] = [
			'txt' => $this->lng->txt('title'),
			'width' => 'auto',
			'sort_field' => 'title',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['content'] = [
			'txt' => $this->lng->txt('content'),
			'width' => 'auto',
			'sort_field' => 'content',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['created_at'] = [
			'txt' => $this->parent_obj->txt('created_at'),
			'width' => 'auto',
			'sort_field' => 'created_at',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['updated_at'] = [
			'txt' => $this->parent_obj->txt('updated_at'),
			'width' => 'auto',
			'sort_field' => 'updated_at',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['author'] = [
			'txt' => $this->lng->txt('author'),
			'width' => 'auto',
			'sort_field' => 'author',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['lang'] = [
			'txt' => $this->lng->txt('language'),
			'width' => 'auto',
			'sort_field' => 'lang',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['active'] = [
			'txt' => $this->lng->txt('active'),
			'width' => 'auto',
			'sort_field' => 'active',
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		$cols['action'] = [
			'txt' => $this->lng->txt('action'),
			'width' => 'auto',
			'sort_field' => null,
			'is_checkbox_action_column' => false,
			'default' => true,
		];

		return $cols;
	}

	protected function parseData()
	{
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setDefaultOrderField('id');

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$parameters = [];

		$options = array(
			'filters' => $this->filter,
			'limit' => array(),
			'count' => true,
			'sort' => array(
				'field' => $this->getOrderField(),
				'direction' => $this->getOrderDirection(),
			),
		);
		$options['limit'] = array(
			'start' => (int)$this->getOffset(),
			'end' => (int)$this->getLimit(),
		);
		$options['count'] = true;

		$data = $this->parent_obj->getData($options, $parameters);;
		$count = $data['count'];
		unset($data['count']);

		$this->setMaxCount($count);
		$this->setData($data);
	}

}