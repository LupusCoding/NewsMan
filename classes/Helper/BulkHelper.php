<?php

namespace LC\ILP\NewsMan\Helper;

use LC\ILP\NewsMan\DataObjects\NewsEntry;

/**
 * Class BulkHelper
 * @package LC\ILP\NewsMan\Helper
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class BulkHelper
{
	/** @var \ilDBInterface */
	protected $database;

	/**
	 * BulkUpdater constructor.
	 * @param \ilDBInterface $database
	 */
	public function __construct(\ilDBInterface $database)
	{
		$this->database = $database;
	}

	/**
	 * @param bool $active
	 * @return array
	 */
	public function getAllEntries(bool $active = false):array
	{
		$query = 'SELECT * FROM `newsman_data` ';
		if ($active) {
			$query .= 'WHERE `active` = 1 ';
		}
		$result = $this->database->query($query);

		$entries = [];
		while ($res = $this->database->fetchAssoc($result)) {
			$entry = $this->createEntryObject($res);
			$entries[$res['id']] = $entry;
			unset($entry);
		}
		return $entries;
	}

	/**
	 * @param string $lang
	 * @param bool $active
	 * @return array
	 */
	public function getAllEntriesByLang(string $lang, bool $active = false): array
	{
		$query = 'SELECT * FROM `newsman_data` ';
		$query .= 'WHERE `lang` = ' . $this->database->quote($lang, 'string') . ' ';
		if ($active) {
			$query .= 'AND `active` = 1 ';
		}
		$result = $this->database->query($query);

		$entries = [];
		while ($res = $this->database->fetchAssoc($result)) {
			$entry = $this->createEntryObject($res);
			$entries[$res['id']] = $entry;
			unset($entry);
		}
		return $entries;
	}

	/**
	 * @param int $id
	 * @return NewsEntry|null
	 */
	public function getEntryById(int $id)
	{
		$entry = new NewsEntry($this->database);
		$res = $entry->loadById($id);
		return ($res === true ? $entry : null);
	}

	/**
	 * @param array $ids
	 * @return array
	 */
	public function bulkActivate(array $ids): array
	{
		$success = [];
		$failed = [];
		foreach ($ids as $id) {
			$entry = $this->getEntryById($id);
			if (isset($entry)) {
				$entry->setActive(true);
				$entry->save();
				$success[] = $id;
			} else {
				$failed[] = $id;
			}
			unset($entry);
		}
		return ['success' => $success, 'failed' => $failed];
	}

	/**
	 * @param array $ids
	 * @return array
	 */
	public function bulkDeactivate(array $ids): array
	{
		$success = [];
		$failed = [];
		foreach ($ids as $id) {
			$entry = $this->getEntryById($id);
			if (isset($entry)) {
				$entry->setActive(false);
				$entry->save();
				$success[] = $id;
			} else {
				$failed[] = $id;
			}
			unset($entry);
		}
		return ['success' => $success, 'failed' => $failed];
	}

	/**
	 * @param array $ids
	 * @return array
	 */
	public function bulkDelete(array $ids): array
	{

		$success = [];
		$failed = [];
		foreach ($ids as $id) {
			$entry = $this->getEntryById($id);
			if (isset($entry)) {
				$this->deleteEntryObject($entry);
				$success[] = $id;
			} else {
				$failed[] = $id;
			}
			unset($entry);
		}
		return ['success' => $success, 'failed' => $failed];
	}

	/**
	 * @param array $data
	 * @return NewsEntry
	 */
	private function createEntryObject(array $data): NewsEntry
	{
		$entry = new NewsEntry($this->database);
		$entry->setValuesByArray($data);
		return $entry;
	}

	/**
	 * @param NewsEntry $entry
	 * @return bool
	 */
	private function deleteEntryObject(NewsEntry $entry): bool
	{
		if ($entry->getId() != null) {
			$query = 'DELETE FROM `newsman_data` ';
			$query .= 'WHERE `id` = %s ';
			$this->database->manipulateF(
				$query,
				['int'],
				[$entry->getId()]
			);
			return true;
		}
		return false;
	}
}