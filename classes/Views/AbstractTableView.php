<?php

namespace LC\ILP\NewsMan\Views;

use LC\ILP\NewsMan\Views\Tables\AbstractTable;

/**
 * Class AbstractTableView
 * @package LC\ILP\NewsMan\Views
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
abstract class AbstractTableView extends AbstractView
{
	/** @var AbstractTable */
	protected $table;

	/**
	 * @param array $options
	 * @param array|null $parameters
	 * @return mixed
	 */
	abstract public function getData(array $options = array(), array $parameters = null);

	/**
	 * @return void
	 */
	abstract protected function applyFilter();

	/**
	 * @return void
	 */
	abstract protected function resetFilter();

}