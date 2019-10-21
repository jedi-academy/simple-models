<?php
namespace Simple\Models;

/**
 * SimpleModel persisted as CSV document
 *  
 */
class CSVModel extends SimpleModel
{
	/**
	 * Constructor.
	 * @param string $origin Filename of the CSV file
	 * @param string $keyfield  Name of the primary key field
	 * @param string $entity	Entity name meaningful to the persistence
	 */
	function __construct($origin = null, $keyfield = 'id', $entity = null)
	{
		parent::__construct();

		// and populate the collection
		$this->load();
	}

	/**
	 * Load the collection state from a CSV document
	 */
	protected function load()
	{
		if (($handle = fopen($this->origin, "r")) !== FALSE)
		{
			$first = true;
			while (($data = fgetcsv($handle)) !== FALSE)
			{
				if ($first)
				{
					// populate field names from first row
					$this->fields = $data;
					$first = false;
				}
				else
				{
					// build object from a row
					$record = new stdClass();
					for ($i = 0; $i < count($this->fields); $i ++ )
						$record->{$this->fields[$i]} = $data[$i];
					$key = $record->{$this->keyfield};
					$this->_data[$key] = $record;
				}
			}
			fclose($handle);
		}
		// rebuild the keys table
		$this->reindex();
	}

	/**
	 * Store the collection state as a CSV document
	 */
	protected function store()
	{
		// rebuild the keys table
		$this->reindex();
		//---------------------
		if (($handle = fopen($this->origin, "w")) !== FALSE)
		{
			fputcsv($handle, $this->fields);
			foreach ($this->_data as $key => $record)
				fputcsv($handle, array_values((array) $record));
			fclose($handle);
		}
		// --------------------
	}
}
