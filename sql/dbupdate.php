<#1>
<?php
if(!$ilDB->tableExists('newsman_data'))
{
	$ilDB->createTable('newsman_data', [
		'id' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0
		],
		'title' => [
			'type'     => 'text',
			'length'   => 128,
			'notnull' => false,
			'default' => null
		],
		'content' => [
			'type'     => 'clob',
			'notnull' => true,
			'default' => ''
		],
		'created_at' => [
			'type'     => 'timestamp',
			'notnull' => true,
			'default' => ''
		],
		'updated_at' => [
			'type'     => 'timestamp',
			'notnull' => false,
			'default' => ''
		],
		'active' => [
			'type'     => 'integer',
			'length'   => 1,
			'notnull' => true,
			'default' => 0
		],
		'author' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0
		],
		'lang' => [
			'type'     => 'text',
			'length'   => 10,
			'notnull' => false
		],
	]);
	$ilDB->addPrimaryKey('newsman_data', array('id'));
	$ilDB->createSequence('newsman_data');
}

?>