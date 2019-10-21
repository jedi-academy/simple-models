# simple-models

Lightweight models for CodeIgniter4

These models are an alternative for those who do not wish to use an RDBMS
to persist their data. They would suit small tables.

They have not been optimized - simplicity is the goal.

The models maintain an in-memory array of the records in a table, loading
it when an object is instantiated, and storing it when the collection changes.

