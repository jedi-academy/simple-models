<?php
//namespace Simple\Models;

/**
 * SimpleModel persisted as JSON document
 *  
 */
class JSONModel extends SimpleModel
{

	/**
	 * Constructor.
	 * @param string $origin Filename of the CSV file
	 * @param string $keyField  Name of the primary key field
	 * @param string $entity	Entity name meaningful to the persistence
	 */
	function __construct($origin = null, $keyField = 'id', $entity = null)
	{
		parent::__construct();

		// and populate the collection
		$this->load();
	}

	/**
	 * Load the collection state from a JSON document
	 */
	protected function load()
	{

		if (file_exists(realpath($this->origin)))
		{

			$contents = file_get_contents($this->origin);
			$this->data = json_decode($contents);

			$record = (array) $this->data[0];
			$this->fields = [];
			foreach ($record as $key => $value)
				$this->fields[] = $key;
		}

		// --------------------
		// rebuild the keys table
		$this->reindex();
	}

	/**
	 * Store the collection state as a JSON document
	 */
	protected function store()
	{

		$output = json_encode($this->data);
		file_put_contents($this->origin, $output);
	}

}
