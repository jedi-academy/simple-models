<?php
//namespace Simple\Models;

/**
 * SimpleModel persisted as XML document
 *  
 */
class XMLModel extends SimpleModel
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
	 * Load the collection state from an XML document
	 */
	protected function load()
	{
		if (file_exists(realpath($this->origin)))
		{

			$xml = simplexml_load_file(realpath($this->origin));

			$first = true;
			foreach ($xml->children() as $child)
			{
				$record = new stdClass();
				foreach ($child->children() as $kid)
				{
					$key = $kid->getName();
					$value = (string) $kid;
					$record->$key = $value;
					if ($key == $this->keyField)
						$id = $value;
					if ($first)
						$this->fields[] = $key;
				}
				$this->data[$id] = $record;
				$first = false;
			}
		}

		// --------------------
		// rebuild the keys table
		$this->reindex();
	}

	/**
	 * Store the collection state s an XML document
	 */
	protected function store()
	{
		$xmlDoc = new DOMDocument("1.0");
		$xmlDoc->preserveWhiteSpace = false;
		$xmlDoc->formatOutput = true;
		$data = $xmlDoc->createElement('root');
		foreach ($this->data as $key => $value)
		{
			$record = $xmlDoc->createElement('record');
			foreach ($value as $itemkey => $datum)
			{
				$item = $xmlDoc->createElement($itemkey, htmlspecialchars($datum));
				$record->appendChild($item);
			}
			$data->appendChild($record);
		}
		$xmlDoc->appendChild($data);
		$xmlDoc->save($this->origin);
		//echo $xmlDoc->saveXML();
	}

}
