<?php

//namespace Simple\Models;

/**
 * Class SimpleModel
 *
 * Generic lightweight model, with data stored in memory only.
 * 
 * Extend this with other models that implement persistance.
 * 
 */
class SimpleModel
{

	//--------------------------------------------------------------------
	// Properties that might be over-ridden by a user model
	//--------------------------------------------------------------------

	/**
	 * Persistent path & filename for this model,
	 *
	 * @var string
	 */
	protected $origin = '';

	/**
	 * Name of the primary key field.
	 *
	 * @var string
	 */
	protected $keyField = 'id';

	/**
	 * Persistence-specific name if an entity in this collection.
	 * 
	 * @var string
	 */
	protected $entity;

	/**
	 * Rules used to validate data.
	 *
	 * @var array
	 */
	protected $validationRules = [];

	/**
	 * Any custom error messages to be used during data validation.
	 *
	 * @var array
	 */
	protected $validationMessages = [];

	//--------------------------------------------------------------------
	// Properties that should not be over-ridden
	//--------------------------------------------------------------------

	/**
	 * Place to hold all the data in this collection.
	 * 
	 * @var array
	 */
	protected $data = [];

	/**
	 * Fetadata about this collection (field names)
	 * 
	 * @var array
	 */
	protected $fields;

	//--------------------------------------------------------------------

	/**
	 * Model constructor.
	 * 
	 * Provide over-riding properties as needed.
	 *
	 * subclasses, invoke the parent constructor and then $this->load();
	 * 
	 * @param string $origin Persistent name of a collection
	 * @param string $keyField  Name of the primary key field
	 * @param string $entity	Entity name meaningful to the persistence
	 */
	public function __construct($origin = null, $keyField = 'id', $entity = null)
	{
		// over-ride any properties
		if ( ! empty($origin))
			$this->origin = $origin;
		if ( ! empty($keyField))
			$this->keyField = $keyField;
		if ( ! empty($entity))
			$this->entity = $entity;

		// start with an empty collection
		$this->data = []; // an array of objects
		$this->fields = []; // an array of strings

	}

	/**
	 * Load the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function load()
	{
		//---------------------
		// Your code goes here
		// --------------------
		// rebuild the keys table
		$this->reindex();
	}

	/**
	 * Store the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function store()
	{
		// rebuild the keys table
		$this->reindex();
		//---------------------
		// Your code goes here
		// --------------------
	}

	/**
	 *  Rebuild and resort the ordered data copy
	 */
	protected function reindex()
	{
		// rebuild the ordered collection
		$results = array();
		foreach ($this->data as $old => $record)
		{
			$key = $record->{$this->keyField};
			$results[$key] = $record;
		}
		// sort the collection
		ksort($results);
		// remember the new collection
		$this->data = $results;
		// reset the cursor
		reset($this->data);
	}

	//--------------------------------------------------------------------
	// FINDERS
	//--------------------------------------------------------------------

	/**
	 * Fetches the collection element with key matching $id.
	 *
	 * @param mixed $id "primary" key
	 *
	 * @return array|object|null    The resulting row of data, or null.
	 */
	public function find($id = null)
	{
		return (isset($this->data[$key])) ? $this->data[$key] : null;
	}

	/**
	 * Works with the current Query Builder instance to return
	 * all results, while optionally limiting them.
	 *
	 * @param integer $limit
	 * @param integer $offset
	 *
	 * @return array|null
	 */
	public function findAll(int $limit = 0, int $offset = 0)
	{
		return $this->data;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the first row of the result set. Will take any previous
	 * Query Builder calls into account when determining the result set.
	 *
	 * @return array|object|null
	 */
	public function first()
	{
		return array_values($this->data)[0];
	}

	//--------------------------------------------------------------------
	// CRUD
	//--------------------------------------------------------------------

	/**
	 * A convenience method that will attempt to determine whether the
	 * data should be inserted or updated. Will work with either
	 * an array or object. When using with custom class objects,
	 * you must ensure that the class will provide access to the class
	 * variables, even if through a magic method.
	 *
	 * @param array|object $data
	 *
	 * @return boolean
	 * @throws \ReflectionException
	 */
	public function save($data): bool
	{
		// convert object from associative array, if needed
		$record = (is_array($record)) ? (object) $record : $record;
		// update the collection appropriately
		$key = $record->{$this->keyField};
		if (isset($this->data[$key]))
		{
			$this->data[$key] = $record;
			$this->store();
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Inserts data into the current table. If an object is provided,
	 * it will attempt to convert it to an array.
	 *
	 * @param array|object $data
	 * @param boolean      $returnID Whether insert ID should be returned or not.
	 *
	 * @return integer|string|boolean
	 * @throws \ReflectionException
	 */
	public function insert($data = null, bool $returnID = true)
	{
		// convert object from associative array, if needed
		$record = (is_array($record)) ? (object) $record : $record;

		// update the DB table appropriately
		$key = $record->{$this->keyField};
		$this->data[$key] = $record;

		$this->store();
	}

	/**
	 * Updates a single record in $this->table. If an object is provided,
	 * it will attempt to convert it into an array.
	 *
	 * @param integer|array|string $id
	 * @param array|object         $data
	 *
	 * @return boolean
	 * @throws \ReflectionException
	 */
	public function update($id = null, $data = null): bool
	{
		// convert object from associative array, if needed
		$record = (is_array($record)) ? (object) $record : $record;
		// update the collection appropriately
		$key = $record->{$this->keyField};
		if (isset($this->data[$key]))
		{
			$this->data[$key] = $record;
			$this->store();
		}
	}

	/**
	 * Deletes a single record from $this->table where $id matches
	 * the table's primaryKey
	 *
	 * @param integer|array|null $id    The rows primary key(s)
	 * @param boolean            $purge Allows overriding the soft deletes setting.
	 *
	 * @return mixed
	 * @throws \CodeIgniter\Database\Exceptions\DatabaseException
	 */
	public function delete($id = null, bool $purge = false)
	{
		if (isset($this->data[$id]))
		{
			unset($this->data[$id]);
			$this->store();
		}
	}

	// Determine if a key exists
	function exists($key)
	{
		return isset($this->data[$key]);
	}
	/**
	 * Allows to set validation messages.
	 * It could be used when you have to change default or override current validate messages.
	 *
	 * @param array $validationMessages
	 *
	 * @return void
	 */
	public function setValidationMessages(array $validationMessages)
	{
		$this->validationMessages = $validationMessages;
	}

	//--------------------------------------------------------------------

	/**
	 * Allows to set field wise validation message.
	 * It could be used when you have to change default or override current validate messages.
	 *
	 * @param string $field
	 * @param array  $fieldMessages
	 *
	 * @return void
	 */
	public function setValidationMessage(string $field, array $fieldMessages)
	{
		$this->validationMessages[$field] = $fieldMessages;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes any rules that apply to fields that have not been set
	 * currently so that rules don't block updating when only updating
	 * a partial row.
	 *
	 * @param array      $rules
	 *
	 * @param array|null $data
	 *
	 * @return array
	 */
	protected function cleanValidationRules(array $rules, array $data = null): array
	{
		if (empty($data))
		{
			return [];
		}

		foreach ($rules as $field => $rule)
		{
			if ( ! array_key_exists($field, $data))
			{
				unset($rules[$field]);
			}
		}

		return $rules;
	}


	//--------------------------------------------------------------------

	/**
	 * Returns the model's defined validation rules so that they
	 * can be used elsewhere, if needed.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function getValidationRules(array $options = []): array
	{
		$rules = $this->validationRules;

		if (isset($options['except']))
		{
			$rules = array_diff_key($rules, array_flip($options['except']));
		}
		elseif (isset($options['only']))
		{
			$rules = array_intersect_key($rules, array_flip($options['only']));
		}

		return $rules;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the model's define validation messages so they
	 * can be used elsewhere, if needed.
	 *
	 * @return array
	 */
	public function getValidationMessages(): array
	{
		return $this->validationMessages;
	}

	//--------------------------------------------------------------------

	/**
	 * Override countAllResults to account for soft deleted accounts.
	 *
	 * @param boolean $reset
	 * @param boolean $test
	 *
	 * @return mixed
	 */
	public function countAllResults(bool $reset = true, bool $test = false)
	{
		return count($this->data);
	}

	//--------------------------------------------------------------------
	// Magic
	//--------------------------------------------------------------------

	/**
	 * Provides/instantiates the builder/db connection and model's table/primary key names and return type.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get(string $name)
	{
		if (property_exists($this, $name))
		{
			return $this->{$name};
		}
		return null;
	}

	/**
	 * Checks for the existence of properties across this model, builder, and db connection.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function __isset(string $name): bool
	{
		if (property_exists($this, $name))
		{
			return true;
		}
		return false;
	}


}
