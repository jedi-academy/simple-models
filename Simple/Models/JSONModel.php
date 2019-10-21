<?php
namespace Simple\Models;

/**
 * SimpleModel persisted as JSON document
 *  
 */
class JSONModel extends SimpleModel
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
	 * Load the collection state from a JSON document
	 */
	protected function load()
	{

		/*
		if (($tasks = simplexml_load_file($this->_origin)) !== FALSE)
		{
			foreach ($tasks as $task) {
				$record = new stdClass();
				$record->id = (int) $task->id;
				$record->task = (string) $task->task;
				$record->priority = (int) $task->priority;
				$record->size = (int) $task->size;
				$record->group = (int) $task->group;
				$record->deadline = (string) $task->deadline;
				$record->status = (int) $task->status;
				$record->flag = (int) $task->flag;

				$this->_data[$record->id] = $record;
			}
		}

		// rebuild the keys table
		$this->reindex();

		*/
		if (file_exists(realpath($this->_origin))) {

		    $this->xml = simplexml_load_file(realpath($this->_origin));
		    if ($this->xml === false) {
			      // error so redirect or handle error
			      header('location: /404.php');
			      exit;
			}

		    $xmlarray =$this->xml;

		    //if it is empty; 
		    if(empty($xmlarray)) {
		    	return;
		    }

		    //get all xmlonjects into $xmlcontent
		    $rootkey = key($xmlarray);
		    $xmlcontent = (object)$xmlarray->$rootkey;

		    $keyfieldh = array();
		    $first = true;

		    //if it is empty; 
		    if(empty($xmlcontent)) {
		    	return;
		    }

		    $dataindex = 1;
		    $first = true;
		    foreach ($xmlcontent as $oj) {
		    	if($first){
			    	foreach ($oj as $key => $value) {
			    		$keyfieldh[] = $key;	
			    		//var_dump((string)$value);
			    	}
			    	$this->_fields = $keyfieldh;
			    }
		    	$first = false; 

		    	//var_dump($oj->children());
		    	$one = new stdClass();

		    	//get objects one by one
		    	foreach ($oj as $key => $value) {
		    		$one->$key = (string)$value;
		    	}
		    	$this->_data[$dataindex++] =$one; 
		    }	


		 	//var_dump($this->_data);
		} else {
		    exit('Failed to open the xml file.');
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
		/*
		// rebuild the keys table
		$this->reindex();
		//---------------------
		*/
		if (($handle = fopen($this->_origin, "w")) !== FALSE)
		{
		/*
			fputcsv($handle, $this->_fields);
			foreach ($this->_data as $key => $record)
				fputcsv($handle, array_values((array) $record));
			fclose($handle);
		}
		// --------------------
		*/
		$xmlDoc = new DOMDocument( "1.0");
        $xmlDoc->preserveWhiteSpace = false;
        $xmlDoc->formatOutput = true;
        $data = $xmlDoc->createElement($this->xml->getName());
        foreach($this->_data as $key => $value)
        {
            $task  = $xmlDoc->createElement($this->xml->children()->getName());
            foreach ($value as $itemkey => $record ) {
                $item = $xmlDoc->createElement($itemkey, htmlspecialchars($record));
                $task->appendChild($item);
                }
                $data->appendChild($task);
            }
            $xmlDoc->appendChild($data);
            $xmlDoc->saveXML($xmlDoc);
            $xmlDoc->save($this->_origin);
		}
	}}
