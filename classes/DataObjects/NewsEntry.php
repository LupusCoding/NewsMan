<?php

namespace LC\ILP\NewsMan\DataObjects;

/**
 * Class NewsEntry
 * @package LC\ILP\NewsMan\DataObjects
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class NewsEntry
{
	/** @var \ilDBInterface */
	protected $database;

	/** @var int */
	private $id;

	/** @var string */
	private $title;

	/** @var string */
	private $content;

	/** @var int */
	private $created_at;

	/** @var int */
	private $updated_at;

	/** @var bool */
	private $active;

	/** @var int */
	private $author;

	/** @var string */
	private $lang;

	/**
	 * NewsEntry constructor.
	 * @param \ilDBInterface $database
	 */
	public function __construct(\ilDBInterface $database)
	{
		$this->database = $database;
	}

//	/**
//	 * @return \ilSetting
//	 */
//	public function getDatabase(): \ilDBInterface
//	{
//		return $this->database;
//	}
//
//	/**
//	 * @param \ilSetting $database
//	 */
//	public function setDatabase(\ilSetting $database)
//	{
//		$this->database = $database;
//	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title = '')
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent(string $content = '')
	{
		$this->content = $content;
	}

	/**
	 * @return int
	 */
	public function getCreatedAt(): int
	{
		return $this->created_at ?: 0;
	}

	/**
	 * @param int $created_at
	 */
	public function setCreatedAt(int $created_at = null)
	{
		$this->created_at = (isset($created_at) ? $created_at : time());
	}

	/**
	 * @return int
	 */
	public function getUpdatedAt(): int
	{
		return $this->updated_at ?: 0;
	}

	/**
	 * @param int $updated_at
	 */
	public function setUpdatedAt(int $updated_at = null)
	{
		$this->updated_at =  (isset($updated_at) ? $updated_at : time());
	}

	/**
	 * @return bool
	 */
	public function getActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;
	}

	/**
	 * @return int
	 */
	public function getAuthor(): int
	{
		return $this->author ?: 0;
	}

	/**
	 * @param int $author
	 */
	public function setAuthor(int $author)
	{
		$this->author = $author;
	}

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return $this->lang ?: '';
	}

	/**
	 * @param string $lang
	 */
	public function setLang(string $lang = null)
	{
		$this->lang = $lang;
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function loadById(int $id): bool
	{
		$query = 'SELECT * FROM `newsman_data` ';
		$query .= 'WHERE id = ' . $this->database->quote($id, 'integer') . ' ';
		$result = $this->database->query($query);

		$res = $this->database->fetchAll($result);
		if (!empty($res)) {
			$res = $res[0];

			try {
				$this->setId($res['id']);
				$this->setTitle($res['title']);
				$this->setContent($res['content']);
				$this->setCreatedAt(strtotime($res['created_at']));
				if (isset($res['updated_at'])) {
					$this->setUpdatedAt(strtotime($res['updated_at']));
				}
				$this->setActive(($res['active'] == true));
				$this->setAuthor($res['author']);
				$this->setLang($res['lang']);
				return true;
			} catch (\Exception $e) {
				return false;
			}
		}
		return false;
	}

	/**
	 * @param $array
	 * @return void
	 */
	public function setValuesByArray($array)
	{
		if (isset($array['id'])) {
			$this->setId( $array['id']);
		}
		if (isset($array['title'])) {
			$this->setTitle($array['title']);
		}
		if (isset($array['content'])) {
			$this->setContent($array['content']);
		}
		if (isset($array['created_at'])) {
			$this->setCreatedAt($array['created_at'] * 1);
		}
		if (isset($array['updated_at'])) {
			$this->setUpdatedAt($array['updated_at'] * 1);
		}
		if (isset($array['active'])) {
			$this->setActive(($array['active'] == true));
		}
		if (isset($array['author'])) {
			$this->setAuthor($array['author']);
		}
		if (isset($array['lang'])) {
			$this->setLang($array['lang']);
		}
	}

	/**
	 * @return void
	 */
	public function save()
	{
		if (isset($this->id) && $this->id > -1) {
			// update existing
			$this->_update();

		} else {
			// create new
			$this->_create();
		}
	}

	/**
	 * @return void
	 */
	private function _create()
	{
		$types = [
			'int',
			'string',
			'string',
			'timestamp',
			'timestamp',
			'int',
			'int',
			'string',
		];
		$values = [
			$this->database->nextId('newsman_data'),
			$this->getTitle(),
			$this->getContent(),
			($this->getCreatedAt() > 0 ? date('Y-m-d H:i:s', $this->getCreatedAt()) : date('Y-m-d H:i:s')),
			($this->getUpdatedAt() > 0 ? date('Y-m-d H:i:s', $this->getUpdatedAt()) : null),
			($this->getActive() == true ? 1 : 0),
			$this->getAuthor(),
			$this->getLang()
		];

		$query = 'INSERT INTO `newsman_data` ';
		$query .= '(`id`, `title`, `content`, `created_at`, `updated_at`, `active`, `author`, `lang`) ';
		$query .= 'VALUES (%s, %s, %s, %s, %s, %s, %s, %s) ';

		$this->database->manipulateF(
			$query,
			$types,
			$values
		);
	}

	/**
	 * @return void
	 */
	private function _update()
	{
		$types = [
			'string',
			'string',
			'timestamp',
			'timestamp',
			'int',
			'int',
			'string',
			'int',
		];
		$values = [
			$this->getTitle(),
			$this->getContent(),
			($this->getCreatedAt() > 0 ? date('Y-m-d H:i:s', $this->getCreatedAt()) : date('Y-m-d H:i:s')),
			($this->getUpdatedAt() > 0 ? date('Y-m-d H:i:s', $this->getUpdatedAt()) : null),
			($this->getActive() == true ? 1 : 0),
			$this->getAuthor(),
			$this->getLang(),
			$this->getId(),
		];

		$query = 'UPDATE `newsman_data` SET ';
		$query .= '`title` = %s, ';
		$query .= '`content` = %s, ';
		$query .= '`created_at` = %s, ';
		$query .= '`updated_at` = %s, ';
		$query .= '`active` = %s, ';
		$query .= '`author` = %s, ';
		$query .= '`lang` = %s ';
		$query .= 'WHERE `id` = %s ';

		$this->database->manipulateF(
			$query,
			$types,
			$values
		);
	}

}