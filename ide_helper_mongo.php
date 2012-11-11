<?php

/* Mongo classes */

/**
 * <p>A connection between PHP and MongoDB.</p>
 * <p>This class is used to create and manage connections. A typical use is:</p>
 * <pre>
 * &lt;?php
 *
 * $m = new Mongo(); // connect
 * $db = $m->foo; // get the database named "foo"
 *
 * ?gt;
 * </pre>
 * <p>See Mongo::__construct() and the section on connecting for more information
 * about creating connections. </p>
 * @link http://www.php.net/manual/en/class.mongo.php
 */
class Mongo {
    /* Constants */
    /**
     * PHP driver version. May be suffixed with "+" or "-" if it is in-between versions.
     * @link http://www.php.net/manual/en/class.mongo.php#mongo.constants.version
     */
    const VERSION = '1.2.7';
    /**
     * Host to connect to if no host is given.
     * @link http://www.php.net/manual/en/class.mongo.php#mongo.constants.defaulthost
     */
    const DEFAULT_HOST = "localhost";
    /**
     * Port to connect to if no port is given.
     * @link http://www.php.net/manual/en/class.mongo.php#mongo.constants.defaultport
     */
    const DEFAULT_PORT = 27017;
    
    
    /* Fields */

    /**
     * @var bool
     */
    public $connected = FALSE;
    /**
     * <p>If this is a persistent connection, if the connection was created for
     * this object or is being reused. If this is not a persistent connection,
     * this field should be NULL.</p>
     * @var string
     */
    public $status = NULL;
    /**
     * @var string
     */
    protected $server = NULL;
    /**
     * @var bool
     */
    protected $persistent = NULL;
    
    
    /* Methods */

    /**
     * <p>This method does not need to be called, except in unusual circumstances.
     * The driver will cleanly close the database connection when this Mongo
     * instance goes out of scope.</p>
     *
     * <p>If objects do not go out of scope between requests, you may wish to call
     * this method at the end of your program to keep old connections from hanging
     * around. However, it is probably more efficient use a persistent connection,
     * which will automatically create a connection if needed and use it for as
     * many requests as possible.</p>
     *
     * <p>If you are connected to a replica set, close() will only close the
     * connection to the primary.</p>
     *
     * @link http://www.php.net/manual/en/mongo.close.php
     * @return bool
     */
    public function close() {

    }

    /**
     * Connects to a database server
     * @link http://www.php.net/manual/en/mongo.connect.php
     * @return bool
     */
    public function connect() {

    }

    /**
     * Connects with a database server
     * @link http://www.php.net/manual/en/mongo.connectutil.php
     * @return bool
     */
    protected function connectUtil() {

    }

    /**
     * <p>If no parameters are passed, this connects to "localhost:27017" (or
     * whatever was specified in php.ini for mongo.default_host and mongo.default_port).</p>
     *
     * <p>server should have the form:</p>
     *
     * <p>mongodb://[username:password@]host1[:port1][,host2[:port2:],...]/db</p>
     *
     * <p>The connection string always starts with mongodb://, to indicate it is a
     * connection string in this form.</p>
     *
     * <p>If username and password are specified, the constructor will attempt to
     * authenticate the connection with the database before returning. Username
     * and password are optional and must be followed by an @, if specified.</p>
     *
     * <p>At least one host must be given (port optional, always defaulting to 27017)
     * and as many hosts as desired may be connected to. Host names are comma-separated
     * and the constructor will return successfully if it connected to at least one host.
     * If it could not connect to any of the hosts, it will throw a MongoConnectionException.</p>
     *
     * <p>Finally, if you specified a username and password, you may specify a
     * database to authenticate with. If db is not specified, "admin" will
     * be used.</p>
     *
     * @link http://www.php.net/manual/en/mongo.construct.php
     * @param string $server The server name.
     * @param array $options
     * <p>An array of options for the connection.  Currently available optionsinclude:</p>
     * <ul>
     *   <li>
     *     <p><i>"connect"</i></p>
     *     <p>If the constructor should connect before returning.  Default is
     *       <b>TRUE</b>.
     *     </p>
     *   </li>
     *   <li>
     *     <p><i>"persist"</i></p>
     *     <p>If the connection should be persistent.  If set, the connection will
     *       be persistent.  The string representation of the value is used as an
     *       id for the connection, so two instances of Mongo that are initialized with
     *       <i>array("persist" =&gt; "foobar")</i> will share the same
     *       database connection, whereas an instance initialized with
     *       <i>array("persist" =&gt; "barbaz")</i> will use a different
     *       database connection.
     *     </p>
     *   </li>
     *   <li class="listitem">
     *     <p><i>"timeout"</i></p>
     *     <p>For how long the driver should try to connect to the database (in
     *       milliseconds).</p>
     *   </li>
     *   <li class="listitem">
     *     <p><i>"replicaSet"</i></p>
     *     <p>If this hosts listed are seeds of a replica set.  If they are, the
     *       master will be determined by using the <i>ismaster</i>
     *       database command on the seeds, so the driver may end up connecting to
     *       a server that was not even listed.  See the replica set example below
     *       for details.</p>
     *   </li>
     *   <li class="listitem">
     *     <p><i>"username"</i></p>
     *     <p>The username can be specified here, instead of including it in the
     *       host list.  This is especially useful if a username has a ":" in it.</p>
     *   </li>
     *   <li class="listitem">
     *     <p><i>"password"</i></p>
     *     <p>The password can be specified here, instead of including it in the
     *       host list.  This is especially useful if a password has a "@" in it.</p>
     *   </li>
     * </ul>
     */
    public function __construct($server = "mongodb://localhost:27017", $options = array("connect" => TRUE)) {

    }

    /**
     * Drops a database [deprecated]
     * @link http://www.php.net/manual/en/mongo.dropdb.php
     * @param mixed $db
     * @return array
     * @deprecated Use MongoDB::drop() instead.
     */
    public function dropDB(mixed $db) {

    }

    /**
     * This is the cleanest way of getting a database. If the database name has
     * any special characters, Mongo::selectDB() will need to be used. However,
     * in most cases, this should be sufficient.
     * @link http://www.php.net/manual/en/mongo.get.php
     * @param string $dbname The database name.
     * @return MongoDB Returns a new db object.
     */
    public function __get(string $dbname) {

    }

    /**
     * <p>This method can only be used with a connection to a replica set. It
     * returns the status of all of the hosts in the set and updates the
     * connection information (the updating is invisible to the user).</p>
     *
     * <p>This is called automatically by the driver every five seconds.</p>
     *
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves. </p>
     * @link http://www.php.net/manual/en/mongo.getHosts.php
     * @return array
     * <p>Returns an array of information about the hosts in the set. Includes each
     * host's hostname, id in the set, health (1 is healthy), the uptime, and how
     * up-to-date the host is compared to the primary. For example, on a
     * three-member replica set running locally.</p>
     * <p>This returns NULL if the connection is not connected to a replica set
     * or has not been initialized, yet. </p>
     */
    public function getHosts() {

    }

    /**
     * <p>This finds the address of the slave currently being used for reads. It
     * is a read-only method: it does not change anything about the internal
     * state of the object.</p>
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongo.getslave.php
     * @return string
     * <p>The address of the slave this connection is using for reads.</p>
     * <p>This returns NULL if this is not connected to a replica set or
     * not yet initialized. </p>
     */
    public function getSlave() {

    }

    /**
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongo.getslaveokay.php
     * @return bool
     */
    public function getSlaveOkay() {

    }

    /**
     * @link http://www.php.net/manual/en/mongo.listdbs.php
     * @return array
     * Returns an associative array containing three fields. The first field is
     * databases, which in turn contains an array. Each element of the array is
     * an associative array corresponding to a database, giving the database's
     * name, size, and if it's empty. The other two fields are totalSize (in bytes)
     * and ok, which is 1 if this method ran successfully.
     */
    public function listDBs() {

    }

    /**
     * Gets a database collection
     * @link http://www.php.net/manual/en/mongo.selectcollection.php
     * @param string $db The database name.
     * @param string $collection The collection name.
     * @return MongoCollection Returns a new collection object.
     */
    public function selectCollection(string $db, string $collection) {

    }

    /**
     * Gets a database
     * @link http://www.php.net/manual/en/mongo.selectdb.php
     * @param string $name The database name.
     * @return MongoDB Returns a new db object.
     */
    public function selectDB(string $name) {

    }

    /**
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongo.setslaveokay.php
     * @param bool $ok
     * @return bool Returns the former value of slaveOkay for this instance.
     */
    public function setSlaveOkay(bool $ok) {

    }

    /**
     * <p>This choses a random slave for a connection to read from. It is called
     * automatically by the driver and should not need to be used. It calls
     * Mongo::getHosts() (to refresh the status of hosts) and Mongo::getSlave()
     * (to get the return value). </p>
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongo.switchslave.php
     * @return string
     * <p>The address of the slave this connection is using for reads. This may be
     * the same as the previous address as addresses are randomly chosen. It may
     * return only one address if only one secondary (or only the primary) is
     * available.</p>
     * <p>For example, if we had a three member replica set with a primary,
     * secondary, and arbiter this method would always return the address of
     * the secondary. If the secondary became unavailable, this method would
     * always return the address of the primary. If the primary also became
     * unavailable, this method would throw an exception, as an arbiter cannot
     * handle reads. </p>
     */
    public function switchSlave() {

    }

    /**
     * String representation of this connection
     * @link http://www.php.net/manual/en/mongo.tostring.php
     * @return string Returns hostname and port for this connection.
     */
    public function __toString() {

    }

}

/**
 * <p>Representations a database collection.</p>
 * <p>Collection names can use any character in the ASCII set. Some valid
 * collection names are "", "...", "my collection", and "*&#@".</p>
 * <p>User-defined collection names cannot contain the $ symbol. There are
 * certain system collections which use a $ in their names (e.g., local.oplog.$main),
 * but it is a reserved character. If you attempt to create and use a collection
 * with a $ in the name, MongoDB will assert. </p>
 * @link http://www.php.net/manual/en/class.mongocollection.php
 */
class MongoCollection {
    /* Constants */
    /**
     * Ascending direction for sorts and index creation. 
     * @link http://www.php.net/manual/en/class.mongocollection.php#mongocollection.constants.ascending
     */
    const ASCENDING = 1;

    /**
     * Descending direction for sorts and index creation. 
     * @link http://www.php.net/manual/en/class.mongocollection.php#mongocollection.constants.descending
     */
    const DESCENDING = -1;
    
    /* Fields */

    /**
     * The "parent" database for this collection. 
     * 
     * @var MongoDB
     */
    public $db = NULL;
    
    /**
     * The number of servers to replicate a change to before returning success. 
     * Value is inherited from the parent database. The @see MongoDB class has a more 
     * detailed description of how w works. 
     * @var integer
     */
    public $w;
    
    /**
     * The number of milliseconds to wait for $this->w replications to take place.
     * Value is inherited from the parent database. The @see MongoDB class has a more 
     * detailed description of how wtimeout works. 
     * 
     * @var integer
     */
    public $wtimeout;

    /* Methods */

    /**
     * Inserts multiple documents into this collection
     * @link http://www.php.net/manual/en/mongocollection.batchinsert.php
     * @param array $a An array of arrays.
     * @param array $options
     * Options for the inserts.
     * <ul>
     *   <li>"safe"
     *     Can be a boolean or integer, defaults to FALSE. If FALSE, the
     *     program continues executing without waiting for a database response.
     *     If TRUE, the program will wait for the database response and throw a
     *     MongoCursorException if the insert did not succeed.<br/>
     *     If safe is an integer, will replicate the insert to that many
     *     machines before returning success (or throw an exception if the
     *     replication times out, see wtimeout). This overrides the w variable
     *     set on the collection. 
     *   </li>
     *   <li>"fsync"
     *     Boolean, defaults to FALSE. Forces the insert to be synced to
     *     disk before returning success. If TRUE, a safe insert is implied
     *     and will override setting safe to FALSE.
     *   </li>
     * </ul>
     * @return mixed
     * If "safe" is set, returns an associative array with the status of the
     * inserts ("ok") and any error that may have occured ("err"). Otherwise,
     * returns TRUE if the batch insert was successfully sent, FALSE otherwise.
     */
    public function batchInsert(array $a, array $options = array()) {

    }

    /**
     * Creates a new collection
     * @link http://www.php.net/manual/en/mongocollection.construct.php
     * @param MongoDB $db Parent database.
     * @param string $name Name for this collection.
     */
    public function __construct(MongoDB $db, string $name) {

    }

    /**
     * Counts the number of documents in this collection
     * @link http://www.php.net/manual/en/mongocollection.count.php
     * @param array $query Associative array or object with fields to match.
     * @param int $limit Specifies an upper limit to the number returned.
     * @param int $skip Specifies a number of results to skip before starting the count. 
     * @return int Returns the number of documents matching the query.
     */
    public function count(array $query = array(), int $limit = 0, int $skip = 0) {

    }

    /**
     * Creates a database reference
     * @link http://www.php.net/manual/en/mongocollection.createdbref.php
     * @param array $a Object to which to create a reference.
     * @return array Returns a database reference array. 
     */
    public function createDBRef(array $a) {

    }

    /**
     * <p>Deletes an index from this collection</p>
     * <p>This method is identical to:</p>
     * <pre>
     * &lt;?php
     * 
     * public function deleteIndexes($keys) {
     *   // toIndexString is a protected method that turns strings, arrays, and objs
     *   //into index names
     *   $index = $this->toIndexString($keys);
     * 
     *   return $this->db->command(array("deleteIndexes" => $this->getName(),
     *     "index" => $index);
     * }
     * 
     * ?&gt;
     * </pre>
     * <p>Each index, when created, is given a unique name. This is generally
     * user-set (with MongoCollection::ensureIndex()'s "name" option) or
     * generated by the driver from a combination of key names and directions.
     * This name is then used by MongoCollection::deleteIndex() to remove the
     * function.</p>
     * <p>Unfortunately, the MongoCollection::ensureIndex() generates slightly
     * different names than the shell and, due to backwards compatibility issues,
     * MongoCollection::deleteIndex() cannot delete custom-named indexes as well.
     * Thus, the best way to delete indexes created in the shell or with custom
     * names is to directly call the deleteIndexes database command.</p>
     * <p>Thus, if you named an index "superfast query", you could delete it with:</p>
     * <pre>
     * &lt;?php
     *
     * $db->command(array("deleteIndexes" => $collection->getName(), "index" => "superfast query");
     *
     * ?&gt;
     * </pre>
     * <p>To find what an index is named, you can query the system.indexes
     * collection of a database and look for the name field. </p>
     * @link http://www.php.net/manual/en/mongocollection.deleteindex.php
     * @var string|array $keys Field or fields from which to delete the index.
     * @return array Returns the database response.
     */
    public function deleteIndex(mixed $keys) {

    }

    /**
     * Delete all indices for this collection
     * @link http://www.php.net/manual/en/mongocollection.deleteindexes.php
     * @return array Returns the database response.
     */
    public function deleteIndexes() {

    }

    /**
     * Drops this collection
     * @link http://www.php.net/manual/en/mongocollection.drop.php
     * @return array Returns the database response. 
     */
    public function drop() {

    }

    /**
     * <p>Creates an index on the given field(s), or does nothing if the index
     * already exists </p>
     * <p>A unique index cannot be created on a field if multiple existing
     * documents do not contain the field. The field is effectively NULL for
     * these documents and thus already non-unique. </p>
     * @link http://www.php.net/manual/en/mongocollection.ensureindex.php
     * @param array $keys Field or fields to use as index.
     * @param array $options
     * This parameter is an associative array of the form array("optionname" =>
     * &lt;boolean&gt;, ...). Currently supported options are:
     * <ul>
     *   <li ><i>"unique"</i>
     *     Create a unique index.
     *   </li>
     *   <li ><i>"dropDups"</i>
     *     If a unique index is being created and duplicate values exist, drop
     *     all but one duplicate value.
     *   </li>
     *   <li ><i>"background"</i>
     *     If you are using MongoDB version 1.3.2+, you can create indexes in the
     *     background while other operations are taking place.  By default, index
     *     creation happens synchronously.  If you specify <b>TRUE</b>
     *     with this option, index creation will be asynchronous.
     *   </li>
     *   <li ><i>"safe"</i>
     *     Starting with driver version 1.0.4, you can specify a boolean value
     *     for checking if the index creation succeeded.  The driver will throw
     *     a MongoCursorException if index creation failed.<br/>
     *     If you are using replication and the master has changed, using "safe"
     *     will make the driver disconnect from the master, throw and exception,
     *     and attempt to find a new master on the next operation (your
     *     application must decide whether or not to retry the operation on the
     *     new master). <br/>
     *     If you <em >do not</em> use "safe" with a replica set and
     *     the master changes, there will be no way for the driver to know about
     *     the change so it will continuously and silently fail to write.
     *   </li>
     *   <li ><i>"name"</i>
     *     After driver version 1.0.4 (NOT including 1.0.4) you can specify an
     *     index name.  This can be useful if you are indexing many keys and
     *     Mongo complains about the index name being too long.
     *   </li>
     *   <li ><i>"timeout"</i>
     *     Integer, defaults to <i>MongoCursor::$timeout</i>.  If
     *     "safe" is set, this sets how long (in milliseconds) for the client to
     *     wait for a database response.  If the database does not respond within
     *     the timeout period, a @see MongoCursorTimeoutException
     *     will be thrown.
     *   </li>
     *  </ul>
     * Integer, defaults to MongoCursor::$timeout. If "safe" is set, this sets
     * how long (in milliseconds) for the client to wait for a database response.
     * If the database does not respond within the timeout period, a
     * MongoCursorTimeoutException will be thrown.
     * @return bool
     */
    public function ensureIndex(array $keys, array $options) {

    }

    /**
     * Querys this collection
     * @link http://www.php.net/manual/en/mongocollection.find.php
     * @param array $query The fields for which to search.
     * @param array $fields Fields of the results to return.
     * @return MongoCursor Returns a cursor for the search results. 
     */
    public function find(array $query = array(), array $fields = array()) {

    }

    /**
     * Querys this collection, returning a single element
     * @link http://www.php.net/manual/en/mongocollection.findone.php
     * @param array $query The fields for which to search.
     * @param array $fields Fields of the results to return.
     * @return array Returns record matching the search or NULL. 
     */
    public function findOne(array $query = array(), array $fields = array()) {

    }

    /**
     * Gets a collection<br/>
     * A concise syntax for getting a collection with a dot-separated name. If
     * a collection name contains strange characters, you may have to use
     * MongoDB::selectCollection() instead.<br/>
     * <pre>
     * &lt;?php
     *
     * $mongo = new Mongo();
     *
     * // the following two lines are equivalent
     * $collection = $mongo->selectDB("foo")->selectCollection("bar.baz");
     * $collection = $mongo->foo->bar->baz;
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongocollection.get.php
     * @param string $name The next string in the collection name.
     * @return MongoCollection Returns the collection. 
     */
    public function __get(string $name) {

    }

    /**
     * Fetches the document pointed to by a database reference
     * @link http://www.php.net/manual/en/mongocollection.getdbref.php
     * @param array $ref A database reference.
     * @return array Returns the database document pointed to by the reference. 
     */
    public function getDBRef(array $ref) {

    }

    /**
     * Returns an array of index names for this collection
     * @link http://www.php.net/manual/en/mongocollection.getinsertinfo.php
     * @return array Returns a list of index names.
     */
    public function getIndexInfo() {

    }

    /**
     * Returns this collection's name
     * @link http://www.php.net/manual/en/mongocollection.getname.php
     * @return string Returns the name of this collection. 
     */
    public function getName() {

    }

    /**
     * Get slaveOkay setting for this collection<br/>
     * See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.
     * @link http://www.php.net/manual/en/mongocollection.getslaveokay.php
     * @return bool Returns the value of slaveOkay for this instance. 
     */
    public function getSlaveOkay() {

    }

    /**
     * Performs an operation similar to SQL's GROUP BY command
     * @link http://www.php.net/manual/en/mongocollection.group.php
     * @param mixed $keys
     * Fields to group by. If an array or non-code object is passed, it
     * will be the key used to group results.<br/>
     * 1.0.4+: If keys is an instance of MongoCode, keys will be treated as a
     * function that returns the key to group by (see the "Passing a keys function"
     * example below).
     * @param array $initial Initial value of the aggregation counter object.
     * @param MongoCode $reduce
     * A function that takes two arguments (the current document and the
     * aggregation to this point) and does the aggregation.
     * @param array $options
     * Optional parameters to the group command. Valid options include:
     * <ul>
     *  <li>"condition"
     *    Criteria for including a document in the aggregation.
     *  </li>
     *  <li>"finalize"
     *    Function called once per unique key that takes the final output of the reduce function.
     *  </li>
     * </ul>
     * @return array Returns an array containing the result. 
     */
    public function group(mixed $keys, array $initial, MongoCode $reduce, array $options = array()) {

    }

    /**
     * Inserts an array into the collection<br/>
     * All strings sent to the database must be UTF-8. If a string is not UTF-8,
     * a MongoException will be thrown. To insert (or query for) a non-UTF-8
     * string, use MongoBinData.
     * @link http://www.php.net/manual/en/mongocollection.insert.php
     * @param array $a An array.
     * @param array $options
     * <ul >
     *   <li >
     *     <i>"safe"</i>
     *     Can be a boolean or integer, defaults to <b>FALSE</b>.  If <b>FALSE</b>, the
     *     program continues executing without waiting for a database response.
     *     If <b>TRUE</b>, the program will wait for the database response and throw a
     *     <a  href="class.mongocursorexception.php">MongoCursorException</a> if the insert did not
     *     succeed.<br/>
     *     If you are using replication and the master has changed, using "safe"
     *     will make the driver disconnect from the master, throw an exception,
     *     and attempt to find a new master on the next operation (your
     *     application must decide whether or not to retry the operation on the
     *     new master). <br/>
     *     If you <em >do not</em> use "safe" with a replica set and
     *     the master changes, there will be no way for the driver to know about
     *     the change so it will continuously and silently fail to write.<br>
     *     If <i>safe</i> is an integer, will replicate the
     *     insert to that many machines before returning success (or throw an
     *     exception if the replication times out, see wtimeout).  This overrides
     *     the w variable set on the collection.
     *   </li>
     *   <li >
     *     <i>"fsync"</i>
     *     Boolean, defaults to <b>FALSE</b>.  Forces the insert to be synced to
     *     disk before returning success.  If <b>TRUE</b>, a safe insert is implied
     *     and will override setting <i>safe</i> to <b>FALSE</b>.
     *   </li>
     *   <li >
     *     <i>"timeout"</i>
     *     Integer, defaults to <i>MongoCursor::$timeout</i>.  If
     *     "safe" is set, this sets how long (in milliseconds) for the client to
     *     wait for a database response.  If the database does not respond within
     *     the timeout period, a @see MongoCursorTimeoutException
     *     will be thrown.
     *   </li>
     *  </ul>
     * @return mixed
     * If safe was set, returns an array containing the status of the insert.
     * Otherwise, returns a boolean representing if the array was not empty
     * (an empty array will not be inserted).
     */
    public function insert(array $a, array $options = array()) {

    }

    /**
     * Remove records from this collection
     * @link http://www.php.net/manual/en/mongocollection.remove.php
     * @param array $criteria Description of records to remove.
     * @param array $options
     * <ul >
     *   <li >
     *     <i>"safe"</i>
     *     Can be a boolean or integer, defaults to <b>FALSE</b>.  If <b>FALSE</b>, the
     *     program continues executing without waiting for a database response.
     *     If <b>TRUE</b>, the program will wait for the database response and throw a
     *     <a  href="class.mongocursorexception.php">MongoCursorException</a> if the insert did not
     *     succeed.<br/>
     *     If you are using replication and the master has changed, using "safe"
     *     will make the driver disconnect from the master, throw an exception,
     *     and attempt to find a new master on the next operation (your
     *     application must decide whether or not to retry the operation on the
     *     new master). <br/>
     *     If you <em >do not</em> use "safe" with a replica set and
     *     the master changes, there will be no way for the driver to know about
     *     the change so it will continuously and silently fail to write.<br>
     *     If <i>safe</i> is an integer, will replicate the
     *     insert to that many machines before returning success (or throw an
     *     exception if the replication times out, see wtimeout).  This overrides
     *     the w variable set on the collection.
     *   </li>
     *   <li >
     *     <i>"fsync"</i>
     *     Boolean, defaults to <b>FALSE</b>.  Forces the insert to be synced to
     *     disk before returning success.  If <b>TRUE</b>, a safe insert is implied
     *     and will override setting <i>safe</i> to <b>FALSE</b>.
     *   </li>
     *   <li >
     *     <i>"timeout"</i>
     *     Integer, defaults to <i>MongoCursor::$timeout</i>.  If
     *     "safe" is set, this sets how long (in milliseconds) for the client to
     *     wait for a database response.  If the database does not respond within
     *     the timeout period, a @see MongoCursorTimeoutException
     *     will be thrown.
     *   </li>
     *  </ul>
     * @return mixed
     * If "safe" is set, returns an associative array with the status of the
     * remove ("ok"), the number of items removed ("n"), and any error that may
     * have occured ("err"). Otherwise, returns TRUE if the remove was
     * successfully sent, FALSE otherwise.
     */
    public function remove(array $criteria = array(), array $options = array()) {

    }

    /**
     * Saves an object to this collection<br />
     * If the object is from the database, update the existing database object, otherwise insert this object.
     * @link http://www.php.net/manual/en/mongocollection.save.php
     * @param array $a Array to save.
     * @param array $options
     * <ul >
     *   <li >
     *     <i>"safe"</i>
     *     Can be a boolean or integer, defaults to <b>FALSE</b>.  If <b>FALSE</b>, the
     *     program continues executing without waiting for a database response.
     *     If <b>TRUE</b>, the program will wait for the database response and throw a
     *     <a  href="class.mongocursorexception.php">MongoCursorException</a> if the insert did not
     *     succeed.<br/>
     *     If you are using replication and the master has changed, using "safe"
     *     will make the driver disconnect from the master, throw an exception,
     *     and attempt to find a new master on the next operation (your
     *     application must decide whether or not to retry the operation on the
     *     new master). <br/>
     *     If you <em >do not</em> use "safe" with a replica set and
     *     the master changes, there will be no way for the driver to know about
     *     the change so it will continuously and silently fail to write.<br>
     *     If <i>safe</i> is an integer, will replicate the
     *     insert to that many machines before returning success (or throw an
     *     exception if the replication times out, see wtimeout).  This overrides
     *     the w variable set on the collection.
     *   </li>
     *   <li >
     *     <i>"fsync"</i>
     *     Boolean, defaults to <b>FALSE</b>.  Forces the insert to be synced to
     *     disk before returning success.  If <b>TRUE</b>, a safe insert is implied
     *     and will override setting <i>safe</i> to <b>FALSE</b>.
     *   </li>
     *   <li >
     *     <i>"timeout"</i>
     *     Integer, defaults to <i>MongoCursor::$timeout</i>.  If
     *     "safe" is set, this sets how long (in milliseconds) for the client to
     *     wait for a database response.  If the database does not respond within
     *     the timeout period, a @see MongoCursorTimeoutException
     *     will be thrown.
     *   </li>
     *  </ul>
     * @return mixed
     * If safe was set, returns an array containing the status of the save.
     * Otherwise, returns a boolean representing if the array was not empty
     * (an empty array will not be inserted).
     */
    public function save(array $a, array $options = array()) {

    }

    /**
     * Change slaveOkay setting for this collection<br/>
     * See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.
     * @link http://www.php.net/manual/en/mongocollection.setslaveokay.php
     * @param bool $ok If reads should be sent to secondary members of a replica
     * set for all possible queries using this MongoCollection instance.
     * @return bool Returns the former value of slaveOkay for this instance. 
     */
    public function setSlaveOkay(bool $ok) {

    }
    
    /**
     * Converts keys specifying an index to its identifying string
     * @link http://www.php.net/manual/en/mongocollection.toindexstring.php
     * @param mixed $keys Field or fields to convert to the identifying string 
     * @return string Returns a string that describes the index. 
     */
    protected static function toIndexString ( $keys ){}

    /**
     * String representation of this collection
     * @link http://www.php.net/manual/en/mongocollection.--tostring.php
     * @return string Returns the full name of this collection.
     */
    public function __toString() {

    }

    /**
     * Update records based on a given criteria
     * @link http://www.php.net/manual/en/mongocollection.update.php
     * @param array $criteria Description of the objects to update.
     * @param array $newobj The object with which to update the matching records.
     * @param array $options
     * This parameter is an associative array of the form array("optionname" =>
     * &lt;boolean&gt;, ...). Currently supported options are:
     *  <ul>
     *   <li>
     *     <i>"upsert"</i>
     *     If no document matches $criteria, a new document will be created from
     *     $criteria and $newobj (see upsert example below).
     *   </li>
     *   <li>
     *     <i>"multiple"</i>
     *     All documents matching $criteria will be updated.
     *     <b>MongoCollection::update()</b> has exactly the opposite
     *     behavior of MongoCollection::remove(): it updates
     *     one document by default, not all matching documents.  <em>It is
     *     recommended that you always specify whether you want to update
     *      multiple documents or a single document</em>, as the database
     *     may change its default behavior at some point in the future.
     *   </li>
     *   <li>
     *     <i>"safe"</i>
     *     Can be a boolean or integer, defaults to <b>FALSE</b>.  If <b>FALSE</b>, the
     *     program continues executing without waiting for a database response.
     *     If <b>TRUE</b>, the program will wait for the database response and throw a
     *     <a href="class.mongocursorexception.php">MongoCursorException</a> if the update did not
     *     succeed.  <br/>
     *     If you are using replication and the master has changed, using "safe"
     *     will make the driver disconnect from the master, throw and exception,
     *     and attempt to find a new master on the next operation (your
     *     application must decide whether or not to retry the operation on the
     *     new master). <br />
     *     If you <em>do not</em> use "safe" with a replica set and
     *     the master changes, there will be no way for the driver to know about
     *     the change so it will continuously and silently fail to write.<br />
     *     If <i>safe</i> is an integer, will replicate the
     *     update to that many machines before returning success (or throw an
     *     exception if the replication times out, see wtimeout).  This overrides
     *     the w variable set on the collection.
     *   </li>
     *   <li>
     *     <i>"fsync"</i>
     *     Boolean, defaults to <b>FALSE</b>.  Forces the update to be synced to
     *     disk before returning success.  If <b>TRUE</b>, a safe update is implied
     *     and will override setting <i>safe</i> to <b>FALSE</b>.
     *   </li>
     *   <li>
     *     <i>"timeout"</i>
     *     Integer, defaults to <i>MongoCursor::$timeout</i>.  If
     *     "safe" is set, this sets how long (in milliseconds) for the client to
     *     wait for a database response.  If the database does not respond within
     *     the timeout period, a MongoCursorTimeoutException will be thrown.
     *   </li>
     *  </ul>
     * @return bool
     */
    public function update(array $criteria, array $newobj, array $options = array()) {

    }

    /**
     * Validates this collection
     * @link http://www.php.net/manual/en/mongocollection.validate.php
     * @param bool $scan_data Only validate indices, not the base collection.
     * @return array Returns the database's evaluation of this object.
     */
    public function validate(bool $scan_data = FALSE) {

    }

}

/**
 * <p>Result object for database query.</p>
 * <p>A MongoCursor has two "life stages": pre- and post- query. A cursor can
 * be created manually by calling the constructor, but it is most often created
 * by calling MongoCollection::find(). When a cursor is created, it has not yet
 * contacted the database, so it is in its pre-query state. In this state, the
 * client can further specify what they want the query to do, including adding
 * limits, skips, sorts, and more advanced options.</p>
 * <p>When the client attempts to get a result (by calling MongoCursor::next(),
 * directly or indirectly), the cursor moves into the post-query stage. At this
 * point, the query has been executed by the database and cannot be modified
 * anymore.</p>
 * <pre>
 * &lt?php
 *
 * $cursor = $collection->find()->limit(10);
 *
 * // database has not yet been queried, so more search options can be added
 * $cursor = $cursor->sort(array("a" => 1));
 *
 * var_dump($cursor->getNext());
 * // now database has been queried and more options cannot be added
 *
 * // so this will throw an exception:
 * $cursor->skip(4);
 * ?&gt;
 * </pre>
 * @link http://www.php.net/manual/en/class.mongocursor.php
 */
class MongoCursor implements Iterator {
    /* Static Fields */

    /**
     * If the query should have the "slaveOkay" flag set, which allows reads on 
     * the slave (slaves are, by default, just for backup and unreadable). Can 
     * be overridden with @see MongoCursor::slaveOkay(). 
     * @var bool
     */
    static $slaveOkay = FALSE;
    
    /**
     * Set timeout in milliseconds for all database responses. To wait forever, 
     * use -1. Can be overridden with @see MongoCursor::timeout(). This does not cause 
     * the MongoDB server to cancel the operation, it just causes the driver to 
     * stop waiting for a response and throw a MongoCursorTimeoutException. 
     * @var int
     */
    static $timeout = 20000;

    /* Methods */

    /**
     * <p>This is an advanced function and should not be used unless you know
     * what you're doing.</p>
     * <p>A query can optionally be nested in a "query" field if other options,
     * such as a sort or hint, are given. For instance, adding a sort causes
     * the query to become a subfield of a bigger query object, like:</p>
     * <pre>
     * &lt;?php
     *
     * $query = array("query" => $query, "orderby" => $sort);
     *
     * ?&gt;
     * </pre>
     * <p>This method is for adding a top-level field to a query. It makes the
     * query a subobject (if it isn't already) and adds the key/value pair of
     * your chosing to the top level. </p>
     * <p style="color:red;"><b>Warning</b><br/>
     * It cannot be used to add extra criteria to a query on the fly. For instance, this will not work:
     * <pre>
     * &lt;?php
     *
     * // NOT CORRECT
     * $cursor = $users->find()->addOption("name", "joe")->addOption("age", 20);
     *
     * ?&gt;
     * </pre>
     * This does not query for a user named "joe" with an age of 20. </p>
     * @link http://www.php.net/manual/en/mongocursor.addoption.php
     * @param string $key
     * @param mixed $value
     * @return MongoCursor
     */
    public function addOption(string $key, mixed $value) {

    }

    /**
     * <p>This cannot override MongoDB's limit on the amount of data it will
     * return to the client (i.e., if you set batch size to 1,000,000,000,
     * MongoDB will still only return 4-16MB of results).</p>
     * <p>To ensure consistent behavior, the rules of batchSize and limit
     * behavior a little complex but work "as expected". The rules are: hard
     * limits override soft limits with preference given to MongoCursor::limit()
     * over MongoCursor::batchSize(). After that, whichever is set and lower
     * than the other will take precedence. Some examples:</p>
     * <pre>
     * &lt;?php
     *
     * // one batch, at most 20 items
     * $cursor->limit(-20)->batchSize(10);
     *
     * // one batch, at most 10 items
     * $cursor->limit(20)->batchSize(-10);
     *
     * // first batch: at most 10 items
     * $cursor->limit(10);
     *
     * // first batch: at most 10 items
     * $cursor->limit(10)->batchSize(20);
     *
     * // first batch: at most 10 items
     * $cursor->limit(20)->batchSize(10);
     *
     * $cursor->limit(30)->batchSize(7)
     * // if we iterate through 28 items, the next call to getNext() will contact the
     * // database and request a batch of 2 documents
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongocursor.batchsize.php
     * @param int $num The number of results to return in the next batch.
     * @return MongoCursor
     */
    public function batchSize(int $num) {

    }

    /**
     * Create a new cursor
     * @link http://www.php.net/manual/en/mongocursor.construct.php
     * @param Mongo $connection
     * @param string $ns
     * @param array $query
     * @param array $fields
     */
    public function __construct(Mongo $connection, string $ns, array $query = array(), array $fields = array()) {

    }

    /**
     * <p>This method does not affect the state of the cursor: if you haven't
     * queried yet, you can still apply limits, skips, etc. If you have started
     * iterating through results, it will not move the current position of the
     * cursor. If you have exhasted the cursor, it will not reset it. </p>
     * @link http://www.php.net/manual/en/mongocursor.count.php
     * @param bool $foundOnly Send cursor limit and skip information to the count function, if applicable.
     * @return int The number of documents returned by this cursor's query.
     */
    public function count(bool $foundOnly = FALSE) {

    }

    /**
     * <p>Returns the current element</p>
     * <p>This returns NULL until MongoCursor::next() is called. </p>
     * @link http://www.php.net/manual/en/mongocursor.current.php
     * @return array The current result as an associative array.
     */
    public function current() {

    }

    /**
     * <p>Checks if there are documents that have not been sent yet from the
     * database for this cursor</p>
     * <p>The database sends responses in batches of documents, up to 4Mb of
     * documents per response. This method checks if the database has more
     * batches or if the result set has been exhausted.</p>
     * <p>A cursor being "dead" does not mean that MongoCursor::hasNext() will
     * return FALSE, it only means that the database is done sending results to
     * the client. The client should continue iterating through results until
     * MongoCursor::hasNext() is FALSE. </p>
     * @link http://www.php.net/manual/en/mongocursor.dead.php
     * @return bool Returns if there are more results that have not been sent to the client, yet. 
     */
    public function dead() {

    }

    /**
     * Execute the query.
     * @link http://www.php.net/manual/en/mongocursor.doquery.php
     */
    protected function doQuery() {

    }

    /**
     * Return an explanation of the query, often useful for optimization and debugging
     * @link http://www.php.net/manual/en/mongocursor.explain.php
     * @return array Returns an explanation of the query.
     */
    public function explain() {

    }

    /**
     * <p>Sets the fields for a query</p>
     * <p>Fields are specified by "fieldname" : bool. TRUE indicates that a
     * field should be returned, FALSE indicates that it should not be returned.
     * You can also use 1 and 0 instead of TRUE and FALSE.</p>
     * <p>Thus, to return only the "summary" field, one could say:</p>
     * <pre>
     * &lt;?php
     *
     * $cursor->fields(array("summary" => true));
     *
     * ?&gt;
     * </pre>
     * <p>To return all fields except the "hidden" field:</p>
     * <pre>
     * &lt;?php
     *
     * $cursor->fields(array("hidden" => false));
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongocursor.fields.php
     * @param array $f Fields to return (or not return).
     * @return MongoCursor Returns this cursor. 
     */
    public function fields(array $f) {

    }

    /**
     * Return the next object to which this cursor points, and advance the cursor
     * @link http://www.php.net/manual/en/mongocursor.getNext.php
     * @return array Returns the next object.
     */
    public function getNext() {

    }

    /**
     * Checks if there are any more elements in this cursor
     * @link http://www.php.net/manual/en/mongocursor.hasnext.php
     * @return bool Returns if there is another element.
     */
    public function hasNext() {

    }

    /**
     * Gives the database a hint about the query
     * @link http://www.php.net/manual/en/mongocursor.hint.php
     * @param array $key_pattern Indexes to use for the query.
     * @return MongoCursor Returns this cursor.
     */
    public function hint(array $key_pattern) {

    }

    /**
     * <p>Sets whether this cursor will timeout</p>
     * <p>After remaining idle for some amount of time, cursor, by default,
     * "die." This is generally the behavior one wants. The database cleans up
     * a cursor once all of its results have been sent to the client, but if
     * the client doesn't request all of the results, the cursor will languish
     * there, taking up resources. Thus, after a few minutes, the cursor "times
     * out" and the database assumes the client has gotten everything it needs
     * and cleans up its the cursor's resources.</p>
     * <p>If, for some reason, you need a cursor to hang around for a long time,
     * you can prevent the database from cleaning it up by using this method.
     * However, if you make a cursor immortal, you need to iterate through all
     * of its results (or at least until <b>Cursor::dead()</b> returns TRUE) or the
     * cursor will hang around the database forever, taking up resources. </p>
     * @link http://www.php.net/manual/en/mongocursor.immortal.php
     * @param bool $liveForever If the cursor should be immortal.
     * @return MongoCursor Returns this cursor. 
     */
    public function immortal(bool $liveForever = true) {

    }

    /**
     * Gets the query, fields, limit, and skip for this cursor. This can be
     * called before or after the query.
     * @link http://www.php.net/manual/en/mongocursor.info.php
     * @return array Returns the namespace, limit, skip, query, and fields for this cursor.
     */
    public function info() {

    }

    /**
     * Returns the current result's _id
     * @link http://www.php.net/manual/en/mongocursor.key.php
     * @return string The current result's _id as a string. 
     */
    public function key() {

    }

    /**
     * Limits the number of results returned
     * @link http://www.php.net/manual/en/mongocursor.limit.php
     * @param int $num The number of results to return.
     * @return MongoCursor Returns this cursor. 
     */
    public function limit(int $num) {

    }

    /**
     * Advances the cursor to the next result
     * @link http://www.php.net/manual/en/mongocursor.next.php
     */
    public function next() {

    }

    /**
     * <p>If this query should fetch partial results from <b>mongos</b> if a shard is
     * down</p>
     * <p>This option allows <b>mongos</b> to send partial query results if a shard is
     * unreachable. This is only applicable when running a sharded MongoDB
     * cluster and connecting to a <b>mongos</b>.</p>
     * <p>If a shard goes down and a query needs to be sent to that shard,
     * <b>mongos</b> will return the results (if any) from shards it already contacted,
     * then an error message that it could not reach the shard (a
     * MongoCursorException in PHP). If you would like to get whatever results
     * <b>mongos</b> can provide and no exception, you can use this method. Note that
     * this means that you won't have an indication that a shard is down in your
     * query response.</p>
     * <p>This has no effect on the query if all shards are reachable. This flag
     * was implemented in MongoDB version 1.7.5, so will only work with that
     * version and higher. </p>
     * @link http://www.php.net/manual/en/mongocursor.partial.php
     * @param bool $okay If receiving partial results is okay.
     * @return MongoCursor Returns this cursor. 
     */
    public function partial(bool $okay = true) {

    }

    /**
     * Clears the cursor
     * @link http://www.php.net/manual/en/mongocursor.reset.php
     */
    public function reset() {

    }

    /**
     * Returns the cursor to the beginning of the result set
     * @link http://www.php.net/manual/en/mongocursor.rewind.php
     */
    public function rewind() {

    }

    /**
     * Skips a number of results
     * @link http://www.php.net/manual/en/mongocursor.skip.php
     * @param int $num The number of results to skip.
     * @return MongoCursor
     */
    public function skip(int $num) {

    }

    /**
     * <p>Sets whether this query can be done on a slave</p>
     * <p>Calling this will make the driver route reads to slaves if: </p>
     * <ul>
     *     <li>
     *       You are using a replica set and
     *     </li>
     *     <li>
     *       You created a <a class="classname" href="class.mongo.php">Mongo</a> instance using the option
     *       <i>"replicaSet" =&gt; true and</i>
     *     </li>
     *     <li class="listitem">
     *       There is a healthy slave that can be reached by the driver.
     *     </li>
     * </ul>
     * <p>You can check which server was used for this query by calling
     * MongoCursor::info() after running the query. It's server field will show
     * which server the query was sent to.</p>
     * <p>Note that you should use this function even if you do not use the
     * automatic routing to slaves. If you connect directly to a secondary in a
     * replica set, you still need to call this function, which basically tells
     * the database that you are aware that you might be getting older data and
     * you're okay with that. If you do not call this, you'll get "not master"
     * errors when you try to query.</p
     * <p>This method will override the static class variable MongoCursor::slaveOkay.
     * It will also override Mongo::setSlaveOkay(), MongoDB::setSlaveOkay() and
     * MongoCollection::setSlaveOkay(). </p>
     * @link http://www.php.net/manual/en/mongocursor.slaveokay.php
     * @param bool $okay
     * @return MongoCursor
     */
    public function slaveOkay(bool $okay = true) {

    }

    /**
     * <p>Use snapshot mode for the query. Snapshot mode assures no duplicates
     * are returned, or objects missed, which were present at both the start
     * and end of the query's execution (if an object is new during the query,
     * or deleted during the query, it may or may not be returned, even with
     * snapshot mode).</p>
     * <p>Note that short query responses (less than 1MB) are always effectively
     * snapshotted.</p>
     * <p>Currently, snapshot mode may not be used with sorting or explicit hints. </p>
     * @link http://www.php.net/manual/en/mongocursor.snapshot.php
     * @return MongoCursor
     */
    public function snapshot() {

    }

    /**
     * Sorts the results by given fields
     * @link http://www.php.net/manual/en/mongocursor.sort.php
     * @param array $fields The fields by which to sort. 
     * @return MongoCursor
     */
    public function sort(array $fields) {

    }

    /**
     * <p>Sets whether this cursor will be left open after fetching the last results</p>
     * <p>Mongo has a feature known as tailable cursors which are similar to the
     * Unix "tail -f" command.</p>
     * <p>Tailable means cursor is not closed when the last data is retrieved.
     * Rather, the cursor marks the final object's position. you can resume using
     * the cursor later, from where it was located, if more data were received.</p>
     * <p>Like any "latent cursor", the cursor may become invalid at some point --
     * for example if that final object it references were deleted. Thus, you
     * should be prepared to requery if the cursor is MongoCursor::dead(). </p>
     * @link http://www.php.net/manual/en/mongocursor.tailable.php
     * @param bool $tail If the cursor should be tailable. 
     * @return MongoCursor
     */
    public function tailable(bool $tail = true) {

    }

    /**
     * <p>Sets a client-side timeout for this query</p>
     * <p>A timeout can be set at any time and will affect subsequent queries on
     * the cursor, including fetching more results from the database. For example,
     * to wait forever for an initial response but timeout after 100 ms for
     * subsequent results, one could say:</p>
     * <pre>
     * &lt;?php
     *
     * $cursor = $collection->find();
     *
     * // $cursor->hasNext() executes the query.  No timeout has been set, so the
     * // program will wait as long as necessary for a response.
     *
     * while ($cursor->hasNext()) {
     *     $cursor->timeout(100);
     *
     *     // now the timeout has been set, so if the cursor needs to get more results
     *     // from the database, it will only wait 100 ms for the database's reply
     *
     *     try {
     *         print_r($cursor->getNext());
     *     }
     *     catch(MongoCursorTimeoutException $e) {
     *         echo "query took too long!";
     *     }
     * }
     *
     * ?&gt;
     * </pre>
     * <p>A timeout of 0 (or a negative number) will wait forever so it can be
     * used to reset the cursor if a timeout is no longer needed. </p>
     * @link http://www.php.net/manual/en/mongocursor.timeout.php
     * @param int $ms The number of milliseconds for the cursor to wait for a response. By default, the cursor will wait forever. 
     * @return MongoCursor
     */
    public function timeout(int $ms) {

    }

    /**
     * Checks if the cursor is reading a valid result.
     * @link http://www.php.net/manual/en/mongocursor.valid.php
     * @return bool If the current result is not null. 
     */
    public function valid() {

    }

}

/**
 * <p>Instances of this class are used to interact with a database. To get a database:</p>
 * <pre>
 * &lt;?php
 *
 * $m = new Mongo(); // connect
 * $db = $m->selectDB("example");
 *
 *
 * ?&gt;
 * </pre>
 * <p>Database names can use almost any character in the ASCII range. However,
 * they cannot contain " ", "." or be the empty string. The name "system" is also
 * reserved.</p>
 * <p>A few unusual, but valid, database names: "null", "[x,y]", "3", "\"", "/".
 * Unlike collection names, database names may contain "$". </p>
 * @link http://www.php.net/manual/en/class.mongodb.php
 */
class MongoDB {
    /* Constants */
    /**
     * @link http://www.php.net/manual/en/class.mongodb.php#mongodb.constants.profilingoff
     */
    const PROFILING_OFF = 0;
    /**
     * @link http://www.php.net/manual/en/class.mongodb.php#mongodb.constants.profilingslow
     */
    const PROFILING_SLOW = 1;
    /**
     * @link http://www.php.net/manual/en/class.mongodb.php#mongodb.constants.profilingon
     */
    const PROFILING_ON = 2;
    /* Fields */

    /**
     * 
     * <p>
     * The number of servers to replicate a change to before returning success.
     * Inherited by instances of <a href="class.mongocollection.php" class="classname">MongoCollection</a> derived
     * from this.  <i>w</i> functionality is only available in 
     * version 1.5.1+ of the MongoDB server and 1.0.8+ of the driver.
     * </p>
     * <p>
     * <i>w</i> is used whenever you perform a "safe" operation (
     * @see MongoCollection::insert(), @see MongoCollection::update(),
     * @see MongoCollection::remove(), @see MongoCollection::save(),
     * @see MongoCollection::ensureIndex() all support safe options). 
     * With the default value (1), a safe operation will return once
     * the database server has the operation.  If the server goes down before 
     * the operation has been replicated to a slave, it is possible to lose the
     * operation forever.  Thus, you can specify <i>w</i> to be 
     * higher than one and guarantee that at least one slave has the operation
     * before it is considered successful.
     * </p>
     * <p>
     * For example, if <i>w</i> is 2, the main server and one slave
     * must have a record of the operation or the driver will throw a 
     * <a href="class.mongocursorexception.php" class="classname">MongoCursorException</a>.  It is tempting to set
     * <i>w</i> to the total number of slaves + master, but then if 
     * one slave is down the op will fail and an exception will be thrown, so 
     * usually <i>w=2</i> is safest (master+1 slave).
     * </p>
     * 
     * @var int
     */
    public $w = 1;
    
    
    /**
     * <p>
     * The number of milliseconds to wait for <i>MongoDB::$w</i> 
     * replications to take place.  Inherited by instances of
     * @see MongoCollection derived from this.
     * <i>w</i> functionality is only available in version 1.5.1+ of
     * the MongoDB server and 1.0.8+ of the driver.
     * </p>
     * <p>
     * Unless <i>wtimeout</i> is set, the server waits forever for 
     * replicating to <i>w</i> servers to finish.  The driver 
     * defaults to waiting for 10 seconds, you can change this value to alter
     * its behavior.
     * </p>
     *      
     * @var int
     */
    public $wtimeout = 10000;

    /* Methods */

    /**
     * <p>This method causes its connection to be authenticated. If authentication
     * is enabled for the database server (it's not, by default), you need to
     * log in before the database will allow you to do anything.</p>
     * <p>In general, you should use the authenticate built into Mongo::__construct()
     * in preference to this method. If you authenticate on connection and the
     * connection drops and reconnects during your session, you'll be reauthenticated.
     * If you manually authenticated using this method and the connection drops,
     * you'll have to call this method again once you're reconnected.</p>
     * <p>This method is identical to running:</p>
     * <pre>&lt;?php
     *   $salted = "${username}:mongo:${password}";
     *   $hash = md5($salted);
     *
     *   $nonce = $db->command(array("getnonce" => 1));
     *
     *   $saltedHash = md5($nonce["nonce"]."${username}${hash}");
     *
     *   $result = $db->command(array("authenticate" => 1,
     *       "user" => $username,
     *       "nonce" => $nonce["nonce"],
     *       "key" => $saltedHash
     *   ));
     * ?&gt;
     * </pre>
     * <p>Once a connection has been authenticated, it can only be
     * un-authenticated by using the "logout" database command:</p>
     * <pre>&lt;?php
     * $db->command(array("logout" => 1));
     * ?&gt;</pre>
     * @link http://www.php.net/manual/en/mongodb.authenticate.php
     * @param string $username
     * @param string $password
     * @return array
     * <p>Returns database response. If the login was successful, it will return</p>
     * <pre>
     * &lt;?php
     *   array("ok" => 1);
     * ?&gt;
     * <p>If something went wrong, it will return</p>
     * <pre>
     * &lt?php
     * array("ok" => 0, "errmsg" => "auth fails");
     * ?&gt;
     * <p>("auth fails" could be another message, depending on database version
     * and what when wrong).</p>
     */
    public function authenticate(string $username, string $password) {

    }

    /**
     * <p>Almost everything that is not a CRUD operation can be done with a
     * database command. Need to know the database version? There's a command
     * for that. Need to do aggregation? There's a command for that. Need to
     * turn up logging? You get the idea.</p>
     * <p>This method is identical to: </p>
     * <pre>
     * &lt;?php
     * public function command($data) {
     *    return $this->selectCollection('$cmd')->findOne($data);
     * }
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.command.php
     * @param array $command The query to send.
     * @param array $options This parameter is an associative array of the form 
     * array("optionname" => <boolean>, ...). Currently supported options are:<br/>
     *  <i>"timeout"</i> - Integer, defaults to Mongo::$timeout. If "safe" is 
     * set, this sets how long (in milliseconds) for the client to wait for a 
     * database response. If the database does not respond within the timeout 
     * period, a @see MongoCursorTimeoutException will be thrown.
     * @return array Returns database response.
     */
    public function command(array $command, array $options = array()) {

    }

    /**
     * <p>Create a new data base</p>
     * <p>This method is not meant to be called directly. The preferred way to
     * create an instance of MongoDB is through Mongo::__get() or Mongo::selectDB().</p>
     * <p>If you're ignoring the previous paragraph and want to call it directly
     * you can do so: </p>
     * <pre>
     * &lt;?php
     *
     * $m = new Mongo();
     * $db = new MongoDB($m, 'mydbname');
     *
     * ?&gt;
     * </pre>
     * <p>But don't. Isn't this much nicer: </p>
     * <pre>
     * &lt;?php
     *
     * $m = new Mongo();
     * $db = $m->mydbname;
     *
     * // or, if the name contains weird characters:
     *
     * $db = $m->selectDB('my,db:name');
     *
     * ?&gt;
     * </pre>
     * @param Mongo $conn
     * @param string $name
     * @link http://www.php.net/manual/en/mongodb.construct.php
     */
    public function __construct(Mongo $conn, string $name) {

    }

    /**
     * <p>Create a collection</p>
     * <p>This method is used to create capped collections and other collections
     * requiring special options. It is identical to running:</p>
     * <pre>
     * &lt;?php
     *
     * $collection = $db->command(array("create" => $name, "size" => $size, "capped" => $capped, "max" => $max));
     *
     * ?&gt;
     * </pre>
     * See @see MongoDB::command() for more information about database commands.
     * @link http://www.php.net/manual/en/mongodb.createcollection.php
     * @param string $name The name of the collection.
     * @param bool $capped If the collection should be a fixed size.
     * @param int $size If the collection is fixed size, its size in bytes.
     * @param int $max If the collection is fixed size, the maximum number of elements to store in the collection.
     * @return MongoCollection Returns a collection object representing the new collection.
     */
    public function createCollection(string $name, bool $capped = FALSE, int $size = 0, int $max = 0) {

    }

    /**
     * <p>This method is a flexible interface for creating database refrences
     * (@see MongoDBRef). </p>
     * @link http://www.php.net/manual/en/mongodb.createdbref.php
     * @param string $collection The collection to which the database reference will point.
     * @param mixed $a Object or _id to which to create a reference. If an object
     * or associative array is given, this will create a reference using the _id field.
     * @return array Returns a database reference array.
     */
    public function createDBRef(string $collection, mixed $a) {

    }

    /**
     * <p>This drops the database currently being used.</p>
     * <p>This is identical to running:</p>
     * <pre>
     * &lt;?php
     *
     * public function drop() {
     *     $this->command(array("dropDatabase" => 1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.drop.php
     * @return array Returns the database response.
     */
    public function drop() {

    }

    /**
     * @deprecated Use MongoCollection::drop() instead.
     * @link http://www.php.net/manual/en/mongodb.dropcollection.php
     * @param mixed $coll
     * @return array
     */
    public function dropCollection(mixed $coll) {

    }

    /**
     * <p>The Mongo database server runs a JavaScript engine. This method
     * allows you to run arbitary JavaScript on the database. This can be
     * useful if you want touch a number of collections lightly, or process
     * some results on the database side to reduce the amount that has to be
     * sent to the client.</p>
     * <p>Running JavaScript in the database takes a write lock, meaning it
     * blocks other operations. Make sure you consider this before running a
     * long script.</p>
     * <p>This is a wrapper for a database command. This method is basically:</p>
     * <pre>
     * &lt;?php
     *
     * public function execute($code, $args) {
     *     return $this->command(array('$eval' => $code, args => $args));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.execute.php
     * @param mixed $code MongoCode or string to execute.
     * @param array $args Arguments to be passed to code.
     * @return array Returns the result of the evaluation.
     */
    public function execute(mixed $code, array $args = array()) {

    }

    /**
     * <p>Create a database error</p>
     * <p>This method is not very useful for normal MongoDB use. It forces a
     * database error to occur. This means that MongoDB::lastError() will return
     * a generic database error after running this command.</p>
     * <p>This command is identical to running:</p>
     * <pre>
     * &lt;?php
     *
     * public function forceError() {
     *     return $this->command(array('forceerror' => 1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.forceerror.php
     * @return bool Returns the database response.
     */
    public function forceError() {

    }

    /**
     * <p>This is the easiest way of getting a collection from a database object.
     * If a collection name contains strange characters, you may have to use
     * @see MongoDB::selectCollection() instead.</p>
     * <pre>
     * &lt;?php
     *
     * $mongo = new Mongo();
     *
     * // the following two lines are equivalent
     * $collection = $mongo->selectDB("foo")->selectCollection("bar");
     * $collection = $mongo->foo->bar;
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.get.php
     * @param string $name The name of the collection.
     * @return MongoCollection
     */
    public function __get(string $name) {

    }

    /**
     * Fetches the document pointed to by a database reference
     * @link http://www.php.net/manual/en/mongodb.getdbref.php
     * @param array $ref A database reference.
     * @return array Returns the document pointed to by the reference.
     */
    public function getDBRef(array $ref) {

    }

    /**
     * Fetches toolkit for dealing with files stored in this database
     * @link http://www.php.net/manual/en/mongodb.getgridfs.php
     * @param string $prefix The prefix for the files and chunks collections.
     * @return MongoGridFS Returns a new gridfs object for this database.
     */
    public function getGridFS(string $prefix = "fs") {

    }

    /**
     * <p>This returns the current database profiling level.</p>
     * <p>The database profiler tracks query execution times. If you turn it on
     * (say, using MongoDB::setProfilingLevel() or the shell), you can see how
     * many queries took longer than a given number of milliseconds or the timing
     * for all queries.</p>
     * <p>Note that profiling slows down queries, so it is better to use in
     * development or testing than in a time-sensitive application.</p>
     * <p>This function is equivalent to running:</p>
     * <pre>
     * &lt;?php
     *
     * public function getProfilingLevel() {
     *     return $this->command(array('profile' => -1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.getprofilinglevel.php
     * @return int Returns the profiling level.
     */
    public function getProfilingLevel() {

    }

    /**
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongodb.getslaveokay.php
     * @return bool Returns the value of slaveOkay for this instance.
     */
    public function getSlaveOkay() {

    }

    /**
     * <p>Check if there was an error on the most recent db operation performed</p>
     * <p>This method is equivalent to: </p>
     * <pre>
     * &lt;?php
     *
     * public function lastError() {
     *     return $this->command(array('getlasterror' => 1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.lasterror.php
     * @return array Returns the error, if there was one.
     */
    public function lastError() {

    }

    /**
     * Get a list of collections in this database
     * @link http://www.php.net/manual/en/mongodb.listcollections.php
     * @return array Returns a list of MongoCollections.
     */
    public function listCollections() {

    }

    /**
     * MongoDB::lastError() is usually preferred to this. This method returns
     * the last database error that occurred and how many operations ago it
     * occurred. It is mostly deprecated.
     * @link http://www.php.net/manual/en/mongodb.preverror.php
     * @return array Returns the error and the number of operations ago it occurred.
     */
    public function prevError() {

    }

    /**
     * <p>This creates a fresh copy of all database data. It will remove any
     * corrupt data and compact and large stretches of free space it finds.
     * This is a very slow operation on a large database.</p>
     * <p>This is usually run from the shell or the command line, not the driver.</p>
     * <p>It is equivalent to the function:</p>
     * <pre>
     * &lt;?php
     *
     * public function repair() {
     *     return $this->command(array('repairDatabase' => 1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.repair.php
     * @param bool $preserve_cloned_files If cloned files should be kept if the repair fails.
     * @param bool $backup_original_files If original files should be backed up.
     * @return array Returns db response.
     */
    public function repair(bool $preserve_cloned_files = FALSE, bool $backup_original_files = FALSE) {

    }

    /**
     * <p>This method is not used in normal operations. It resets the database
     * error tracker (which can be incremented with MongoDB::forceError(), also
     * not normally used).</p>
     * <p>It is equivalent to running:</p>
     * <pre>
     * &lt;?php
     *
     * public function resetError() {
     *     return $this->command(array('reseterror' => 1));
     * }
     *
     * ?&gt;
     * </pre>
     * @link http://www.php.net/manual/en/mongodb.reseterror.php
     * @return array Returns the database response.
     */
    public function resetError() {

    }

    /**
     * Gets a collection
     * @link http://www.php.net/manual/en/mongodb.selectcollection.php
     * @param string $name
     * @return MongoCollection Returns the collection.
     */
    public function selectCollection(string $name) {

    }

    /**
     * <p>This changes the current database profiling level. </p>
     * <p>This function is equivalent to running:</p>
     * <pre>
     * &lt;?php
     *
     * public function setProfilingLevel($level) {
     *     return $this->command(array('profile' => $level));
     * }
     *
     * ?&gt;
     * </pre>
     * <p>The options for level are 0 (off), 1 (queries > 100ms), and 2 (all
     * queries). If you would like to profile queries that take longer than
     * another time period, use the database command and pass it a second option,
     * the number of milliseconds. For example, to profile all queries that take
     * longer than one second, run: </p>
     * <pre>
     * &lt;?php
     *
     * $result = $this->command(array('profile' => 1, 'slowms' => 1000));
     *
     * ?&gt;
     * </pre>
     * <p>Profiled queries will appear in the system.profile collection of this
     * database. </p>
     * @link http://www.php.net/manual/en/mongodb.setprofilinglevel.php
     * @param int $level Profiling level.
     * @return int Returns the previous profiling level.
     */
    public function setProfilingLevel(int $level) {

    }

    /**
     * <p>See the query section (@link http://www.php.net/manual/en/mongo.queries.php)
     * of this manual for information on distributing
     * reads to slaves.</p>
     * @link http://www.php.net/manual/en/mongodb.setslaveokay.php
     * @param bool $ok If reads should be sent to secondary members of a replica
     * set for all possible queries using this MongoDB instance.
     * @return bool Returns the former value of slaveOkay for this instance.
     */
    public function setSlaveOkay(bool $ok) {

    }

    /**
     * The name of this database
     * @link http://www.php.net/manual/en/mongodb.--tostring.php
     * @return string The name of this database
     */
    public function __toString() {

    }

}

/* Mongo types */

/**
 * <p>A unique identifier created for database objects. If an object is inserted
 * into the database without an _id field, an _id field will be added to it with
 * a <b>MongoId</b> instance as its value. If the data has a naturally occuring unique
 * field (say, a username or timestamp) it is fine to use this as the _id field
 * instead, and it will not be replaced with a <b>MongoId</b>.</p>
 * <p>Instances of the <b>MongoId</b> class fulfill the role that autoincrementing does
 * in a relational database: to provide a unique key if the data does not natually
 * have one. Autoincrementing does not work well with a sharded database, as it
 * is impossible to find what the next number should be quickly. This class
 * fulfills the constraints of quickly generating a value that is unique across
 * shards.</p>
 * <p>Each <b>MongoId</b> is 12 bytes (making its string form 24 hexidecimal characters).
 * The first four bytes are a timestamp, the next three are a hash of the client
 * machine's hostname, the next two are the two least significant bytes of the
 * process id running the script, and the last three bytes are an incrementing
 * value.</p>
 * <p><b>MongoId</b>s are serializable/unserializable. Their serialized form is similar
 * to their string form:</p>
 * <p><b>C:7:"MongoId":24:{4af9f23d8ead0e1d32000000}</b></p>
 * @link http://www.php.net/manual/en/class.mongoid.php
 */
class MongoId {

    /**
     * @var string
     */
    public $id = NULL;
    /* Methods */

    /**
     *
     * @param string $id
     */
    public function __construct(string $id = NULL) {

    }

    /**
     * @return string
     */
    public static function getHostname() {

    }

    /**
     * @return int
     */
    public function getInc() {

    }

    /**
     * @return int
     */
    public function getPID() {

    }

    /**
     * @return int
     */
    public function getTimestamp() {

    }

    /**
     *
     * @param array $props
     * @return MongoId
     */
    public static function __set_state(array $props) {

    }

    /**
     * @return string
     */
    public function __toString() {

    }

}

/**
 * <p>Represents JavaScript code for the database.</p>
 * <p>MongoCode objects are composed of two parts: a string of code and an
 * optional scope. The string of code must be valid JavaScript. The scope is a
 * associative array of variable name/value pairs. </p>
 * @link http://www.php.net/manual/en/class.mongocode.php
 */
class MongoCode {
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongocode.construct.php
     * @param string $code
     * @param array $scope
     */
    public function __construct(string $code, array $scope = array()) {

    }

    /**
     * @link http://www.php.net/manual/en/mongocode.tostring.php
     * @return string
     */
    public public function __toString() {

    }

}

/**
 * <p>Represent date objects for the database. This class should be used to save
 * dates to the database and to query for dates. For example:</p>
 * <pre>
 * &lt;?php
 *
 * // save a date to the database
 * $collection->save(array("ts" => new MongoDate()));
 *
 * $start = new MongoDate(strtotime("2010-01-15 00:00:00"));
 * $end = new MongoDate(strtotime("2010-01-30 00:00:00"));
 *
 * // find dates between 1/15/2010 and 1/30/2010
 * $collection->find(array("ts" => array('$gt' => $start, '$lte' => $end)));
 *
 * ?&gt;
 * </pre>
 * <p>MongoDB stores dates as milliseconds past the epoch. This means that dates
 * do not contain timezone information. Timezones must be stored in a separate
 * field if needed. Second, this means that any precision beyond milliseconds
 * will be lost when the document is sent to/from the database.</p>
 * @link http://www.php.net/manual/en/class.mongodate.php
 */
class MongoDate {
    /* Fields */

    /**
     *
     * @var int
     */
    public $sec;
    /**
     *
     * @var int
     */
    public $usec;

    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongodate.construct.php
     * @param int $sec
     * @param int $usec
     */
    public function __construct(int $sec = 0, int $usec = 0) {

    }

    /**
     * @link http://www.php.net/manual/en/mongodate.tostring.php
     * @return string
     */
    public function __toString() {

    }

}

/**
 * <p>This class can be used to create regular expressions. Typically, these
 * expressions will be used to query the database and find matching strings.
 * More unusually, they can be saved to the database and retrieved.</p>
 * <p>Mongo recognizes six regular expression flags:</p>
 * <ul>
 *  <li>i - Case insensitive</li>
 *  <li>m - Multiline</li>
 *  <li>x - Can contain comments</li>
 *  <li>l - locale</li>
 *  <li>s - dotall, "." matches everything, including newlines</li>
 *  <li>u - match unicode</li>
 * </ul>
 * @link http://www.php.net/manual/en/class.mongoregex.php
 */
class MongoRegex {
    /* Fields */

    /**
     *
     * @var string
     */
    public $regex;
    /**
     *
     * @var string
     */
    public $flags;

    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongoregex.construct.php
     * @param string $regex
     */
    public function __construct(string $regex) {

    }

    /**
     * @link http://www.php.net/manual/en/mongoregex.tostring.php
     * @return string
     */
    public function __toString() {

    }

}

/**
 * <p>An object that can be used to store or retrieve binary data from the database.</p>
 * <p>The maximum size of a single object that can be inserted into the database
 * is 4Mb. For data that is larger than this (movies, music, Henry Kissinger's
 * autobiography), use MongoGridFS. For data that is smaller than 4Mb, it's
 * probably be easier to just embed it into the document using MongoBinData.</p>
 * <p>For example, to embed an image in a document, one could write:</p>
 * <pre>
 * &lt?php
 *
 * $profile = array("username" => "foobity",
 *     "pic" => new MongoBinData(file_get_contents("gravatar.jpg"))
 * );
 *
 * $users->save($profile);
 *
 * ?&gt;
 * </pre>
 * <p>This class contains a type field, which currently gives no additional
 * functionality in the driver or the database. There are five predefined types
 * (which are the class constants listed below), and the user can create their
 * own (at the risk of the BSON spec catching up with them). By default, the PHP
 * driver always uses type 2: a byte array. </p>
 * @link http://www.php.net/manual/en/class.mongobindata.php
 */
class MongoBinData {
    /* Constants */
    /**
     * @link http://www.php.net/manual/en/class.mongobindata.php#mongobindata.constants.func
     */
    const FUNC = 1;
    /**
     * @link http://www.php.net/manual/en/class.mongobindata.php#mongobindata.constants.bytearray
     */
    const BYTE_ARRAY = 2;
    /**
     * @link http://www.php.net/manual/en/class.mongobindata.php#mongobindata.constants.uuid
     */
    const UUID = 3;
    /**
     * @link http://www.php.net/manual/en/class.mongobindata.php#mongobindata.constants.md5
     */
    const MD5 = 5;
    /**
     * @link http://www.php.net/manual/en/class.mongobindata.php#mongobindata.constants.custom
     */
    const CUSTOM = 128;
    /* Fields */

    /**
     *
     * @var string
     */
    public $bin;
    /**
     *
     * @var int
     */
    public $type = 2;
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongobindata.construct.php
     * @param string $data
     * @param int $type
     */
    public function __construct(string $data, int $type = 2) {

    }

    /**
     * @link http://www.php.net/manual/en/mongobindata.tostring.php
     * @return function
     */
    public function __toString() {

    }

}

/**
 * The class can be used to save 32-bit integers to the database on a 64-bit system.
 * @link http://www.php.net/manual/en/class.mongoint32.php
 */
class MongoInt32 {
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongoint32.construct.php
     * @param string $value
     */
    public function __construct(string $value) {

    }

    /**
     * @link http://www.php.net/manual/en/mongoint32.tostring.php
     * @return string
     */
    public function __toString() {

    }

}

/**
 * The class can be used to save 64-bit integers to the database on a 32-bit system.
 * @link http://www.php.net/manual/en/class.mongoint64.php
 */
class MongoInt64 {
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongoint64.construct.php
     * @param string $value
     */
    public function __construct(string $value) {

    }

    /**
     * @link http://www.php.net/manual/en/mongoint64.tostring.php
     * @return string
     */
    public function __toString() {

    }

}

/**
 * <pThis class can be used to create lightweight links between objects in
 * different collections.</p>
 * <p>Motivation: Suppose we need to refer to a document in another collection.
 * The easiest way is to create a field in the current document. For example, if
 * we had a "people" collection and an "addresses" collection, we might want to
 * create a link between each person document and an address document:</p>
 * <pre>
 * &lt?php
 *
 * $people = $db->people;
 * $addresses = $db->addresses;
 *
 * $myAddress = array("line 1" => "123 Main Street",
 *     "line 2" => null,
 *     "city" => "Springfield",
 *     "state" => "Vermont",
 *     "country" => "USA");
 *
 * // save the address
 * $addresses->insert($myAddress);
 *
 * // save a person with a reference to the address
 * $me = array("name" => "Fred", "address" => $myAddress['_id']);
 * $people->insert($me);
 *
 * ?&gt;
 * </pre>
 * <p>Then, later on, we can find the person's address by querying the "addresses"
 * collection with the MongoId we saved in the "people" collection.</p>
 * <p>Suppose now that we have a more general case, where we don't know which
 * collection (or even which database) contains the referenced document.
 * <b>MongoDBRef</b> is a good choice for this case, as it is a common format that all
 * of the drivers and the database understand.</p>
 * <p>If each person had a list of things they liked which could come from
 * multiple collections, such as "hobbies", "sports", "books", etc., we could
 * use <b>MongoDBRef</b>s to keep track of what "like" went with what collection:</p>
 * <pre>
 * &lt;?php
 *
 * $people = $db->selectCollection("people");
 *
 * // model trains are in the "hobbies" collection
 * $trainRef = MongoDBRef::create("hobbies", $modelTrains['_id']);
 * // soccer is in the "sports" collection
 * $soccerRef = MongoDBRef::create("sports", $soccer['_id']);
 *
 * // now we'll know what collections the items in the "likes" array came from when
 * // we retrieve this document
 * $people->insert(array("name" => "Fred", "likes" => array($trainRef, $soccerRef)));
 *
 * ?&gt;
 * </pre>
 * <p>Database references can be thought of as hyperlinks: they give the unique
 * address of another document, but they do not load it or automatically
 * follow the link/reference.</p>
 * <p>A database reference is just a normal associative array, not an instance
 * of <b>MongoDBRef</b>, so this class is a little different than the other data type
 * classes. This class contains exclusively static methods for manipulating
 * database references. </p>
 * @link http://www.php.net/manual/en/class.mongodbref.php
 */
class MongoDBRef {
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongodbref.create.php
     * @param string $collection
     * @param mixed $id
     * @param string $database
     * @return array
     */
    public static function create(string $collection, mixed $id, string $database = NULL) {

    }

    /**
     * @link http://www.php.net/manual/en/mongodbref.get.php
     * @param MongoDB $db
     * @param array $ref
     * @return array
     */
    public static function get(MongoDB $db, array $ref) {

    }

    /**
     * @link http://www.php.net/manual/en/mongodbref.isref.php
     * @param mixed $ref
     * @return bool
     */
    public static function isRef(mixed $ref) {

    }

}

/**
 * <p><b>MongoMinKey</b> is a special type used by the database that evaluates to less
 * than any other type. Thus, if a query is sorted by a given field in ascending
 * order, any document with a <b>MongoMinKey</b> as its value will be returned first.</p>
 * <p><b>MongoMinKey</b> has no associated fields, methods, or constants. It is merely
 * the "smallest" thing that can be inserted into the database. </p>
 * <pre>
 * &lt;?php
 *
 * $collection->insert(array("task" => "lunch", "do by" => new MongoMinKey));
 * $collection->insert(array("task" => "staff meeting", "do by" => new MongoDate(strtotime("+4 days"))));
 *
 * $cursor = $collection->find()->sort(array("do by" => 1));
 *
 * ?&gt;
 * </pre>
 * <p>The cursor will contain the lunch document, then the staff meeting document.
 * The lunch document will always be returned first, regardless of what else is
 * added to the collection (unless other documents are added with <b>MongoMinKey</b> in
 * the "do by" field).</p>
 * @link http://www.php.net/manual/en/class.mongominkey.php
 */
class MongoMinKey {

}

/**
 * <p><b>MongoMaxKey</b> is a special type used by the database that evaluates to less
 * than any other type. Thus, if a query is sorted by a given field in ascending
 * order, any document with a <b>MongoMaxKey</b> as its value will be returned first.</p>
 * <p><b>MongoMaxKey</b> has no associated fields, methods, or constants. It is merely
 * the "smallest" thing that can be inserted into the database. </p>
 * <pre>
 * &lt;?php
 *
 * $collection->insert(array("task" => "dishes", "do by" => new MongoMaxKey));
 * $collection->insert(array("task" => "staff meeting", "do by" => new MongoDate(strtotime("+4 days"))));
 *
 * $cursor = $collection->find()->sort(array("do by" => 1));
 *
 * ?&gt;
 * </pre>
 * <p>The cursor will contain the staff meeting document, then the dishes document.
 * The dishes document will always be returned last, regardless of what else is
 * added to the collection (unless other documents are added with <b>MongoMaxKey</b>
 * in the "do by" field).</p>
 * @link http://www.php.net/manual/en/class.mongomaxkey.php
 */
class MongoMaxKey {

}

/**
 * <p><b>MongoTimestamp</b> is used by sharding. If you're not looking to write sharding
 * tools, what you probably want is MongoDate.</p>
 * <p><b>MongoTimestamp</b> is 4 bytes of timestamp (seconds since the epoch) and
 * 4 bytes of increment.</p>
 * <p>This class is not for measuring time, creating a timestamp on a document
 * or automatically adding or updating a timestamp on a document. Unless you are
 * writing something that interacts with the sharding internals, stop, go directly
 * to MongoDate, do not pass go, do not collect 200 dollars. This is not the class
 * you are looking for.</p>
 * <p>If you are writing sharding tools, read on.</p>
 * @link http://www.php.net/manual/en/class.mongotimestamp.php
 */
class MongoTimestamp {
    /* Fields */

    /**
     *
     * @var int
     */
    public $sec = 0;
    /**
     *
     * @var int
     */
    public $inc = 0;
    /* Methods */

    /**
     * @link http://www.php.net/manual/en/mongotimestamp.construct.php
     * @param int $sec
     * @param int $inc
     */
    public function __construct(int $sec, int $inc) {

    }

    /**
     * @link http://www.php.net/manual/en/mongotimestamp.tostring.php
     * @return string
     */
    public function __toString() {

    }

}

/* GridFS Classes */

/**
 * @link http://www.php.net/manual/en/class.mongogridfs.php
 */
class MongoGridFS extends MongoCollection {
    /* Fields */

    /**
     * @var MongoCollection
     */
    public $chunks = NULL;

    /**
     * @var string
     */
    protected $filesName = NULL;

    /**
     * @var string
     */
    protected $chunksName = NULL;
    /* Methods */

    /**
     * Creates new file collections<br/>
     * Files as stored across two collections, the first containing file meta
     * information, the second containing chunks of the actual file. By default,
     * fs.files and fs.chunks are the collection names used.<br/>
     * Use one argument to specify a prefix other than "fs":<br/>
     * <pre>$fs = new MongoGridFS($db, "myfiles");</pre>
     * uses myfiles.files and myfiles.chunks collections.
     * @link http://www.php.net/manual/en/mongogridfs.--construct.php
     * @param MongoDB $db Database.
     * @param string $prefix Optional collection name prefix. 
     * @param mixed $chunks
     */
    public function __construct ( MongoDB $db , string $prefix = "fs" , mixed $chunks = "fs"  ){}

    /**
     * Delete a file from the database
     * @link http://www.php.net/manual/en/mongogridfs.delete.php
     * @param mixed $id _id of the file to remove.
     * @return bool Returns if the remove was successfully sent to the database.
     */
    public function delete ( mixed $id ){}

    /**
     * Drops the files and chunks collections
     * @link http://www.php.net/manual/en/mongogridfs.drop.php
     * @return array The database response.
     */
    public function drop ( ){}

    /**
     * Queries for files
     * @link http://www.php.net/manual/en/mongogridfs.find.php
     * @param array $query The query.
     * @param array $fields Fields to return.
     * @return MongoGridFSCursor
     */
    public function find ( array $query = array() , array $fields = array()  ){}

    /**
     * Returns a single file matching the criteria
     * @link http://www.php.net/manual/en/mongogridfs.findone.php
     * @param mixed $query The filename or criteria for which to search.
     * @param mixed $fields Fields to return.
     * @return MongoGridFSFile Returns a MongoGridFSFile or NULL.
     */
    public function findOne ( mixed $query = array() , mixed $fields = array()  ){}

    /**
     * Retrieve a file from the database
     * @link http://www.php.net/manual/en/mongogridfs.get.php
     * @param mixed $id _id of the file to find.
     * @return MongoGridFSFile Returns the file, if found, or NULL.
     */
    public function get ( mixed $id ){}

    /**
     * Stores a file in the database
     * @link http://www.php.net/manual/en/mongogridfs.put.php
     * @param string $filename The name of the file.
     * @param array $extra Other metadata to add to the file saved. 
     * @return mixed Returns the _id of the saved object. 
     */
    public function put ( string $filename , array $extra = array()  ){}

    /**
     * Removes files from the collections
     * @link http://www.php.net/manual/en/mongogridfs.remove.php
     * @param array $criteria The filename or criteria for which to search.
     * @param array $options
     * Options for the remove. Valid options are:
     * <ul>
     *  <li>"safe"
     *    Check that the remove succeeded.
     *  </li>
     * </ul>
     * @return bool
     * Returns if the removal was successfully sent to the database.
     */
    public function remove ( array $criteria = array() , array $options = array()  ){}

    /**
     * Chunkifies and stores bytes in the database
     * @link http://www.php.net/manual/en/mongogridfs.storebytes.php
     * @param string $bytes A string of bytes to store.
     * @param array $extra Other metadata to add to the file saved.
     * @param array $options
     * Options for the store. 
     * <ul>
     *  <li>"safe"
     *    Check that this store succeeded.
     *  </li>
     * </ul>
     * @return mixed The _id of the object saved. і
     */
    public function storeBytes ( string $bytes , array $extra = array() , array $options = array()  ){}

    /**
     * Stores a file in the database
     * @link http://www.php.net/manual/en/mongogridfs.storefile.php
     * @param string $filename The name of the file.
     * @param array $extra Other metadata to add to the file saved.
     * @param array $options
     * Options for the store.
     * <ul>
     *  <li>"safe"
     *    Check that this store succeeded.
     *  </li>
     * </ul>
     * @return mixed Returns the _id of the saved object.
     */
    public function storeFile ( string $filename , array $extra = array() , array $options = array()  ){}

    /**
     * Saves an uploaded file to the database
     * @link http://www.php.net/manual/en/mongogridfs.storeupload.php
     * @param string $name The name field of the uploaded file.
     * @param string $filename String to be used as filename field in the database.
     * @return mixed Returns the _id of the uploaded file. 
     */
    public function storeUpload ( string $name , string $filename  ){}
}

/**
 * @link http://www.php.net/manual/en/class.mongogridfsfile.php
 */
class MongoGridfsFile{
    /* Fields */
    /**
     * @var array
     */
    public $file = NULL ;

    /**
     * @var MongoGridFS
     */
    protected $gridfs = NULL ;

    /* Methods */
    /**
     * Create a new GridFS file
     * @param MongoGridFS $gridfs The parent MongoGridFS instance.
     * @param array $file A file from the database.
     */
    public function __construct ( MongoGridFS $gridfs , array $file ){}

    /**
     * Returns this file's contents as a string of bytes<br/>
     * Warning: this will load the file into memory. If the file is bigger than
     * your memory, this will cause problems!
     * @return string Returns a string of the bytes in the file. 
     */
    public function getBytes (){}

    /**
     * Returns this file's filename
     * @return string Returns the filename.
     */
    public function getFilename (){}

    /**
     * Returns this file's size
     * @return int Returns this file's size
     */
    public function getSize (){}

    /**
     * Writes this file to the filesystem
     * @param string $filename
     * The location to which to write the file. If none is given, the stored
     * filename will be used.
     * @return int Returns the number of bytes written. 
     */
    public function write (string $filename = NULL ){}
}

/**
 * @link http://www.php.net/manual/en/class.mongogridfscursor.php
 */
class MongoGridFSCursor extends MongoCursor {
    /* Fields */
    /**
     * @var MongoGridFS
     */
    protected $gridfs = NULL ;
    /* Methods */

    /**
     * Create a new cursor
     * @link http://www.php.net/manual/en/mongogridfscursor.construct.php
     * @param MongoGridFS $gridfs Related GridFS collection.
     * @param resource $connection Database connection.
     * @param string $ns Full name of database and collection.
     * @param array $query Database query.
     * @param array $fields Fields to return.
     */
    public function __construct ( MongoGridFS $gridfs , resource $connection , string $ns , array $query , array $fields ){}

    /**
     * Returns the current file
     * @link http://www.php.net/manual/en/mongogridfscursor.current.php
     * @return MongoGridFSFile The current file.
     */
    public function current ( ){}

    /**
     * Return the next file to which this cursor points, and advance the cursor
     * @link http://www.php.net/manual/en/mongogridfscursor.getnext.php
     * @return MongoGridFSFile
     * Returns the next file.
     */
    public function getNext ( ){}

    /**
     * Returns the current result's filename
     * @link http://www.php.net/manual/en/mongogridfscursor.key.php
     * @return string The current result's filename. 
     */
    public function key ( ){}
}
/* function */

/**
 * This function is very beta and entirely useless for 99% of users. It is only
 * useful if you're doing something weird, such as writing your own driver on
 * top of the PHP driver.
 * @link http://www.php.net/manual/en/function.bson-encode.php
 * @param mixed $anything The variable to be serialized.
 * @return string Returns the serialized string.
 */
function bson_encode(mixed $anything) {

}

/**
 * This function is very beta and entirely useless for 99% of users. It is only
 * useful if you're doing something weird, such as writing your own driver on
 * top of the PHP driver.
 * @link http://www.php.net/manual/en/function.bson-decode.php
 * @param string $bson
 * @return array Returns the deserialized BSON object.
 */
function bson_decode(string $bson) {

}

/* exceptions */

/**
 * @link http://www.php.net/manual/en/class.mongoexception.php
 */
class MongoException extends Exceprion {

}

/**
 * @link http://www.php.net/manual/en/class.mongocursorexception.php
 */
class MongoCursorException extends MongoException {

}

/**
 * Caused by a query timing out. You can set the length of time to wait before
 * this exception is thrown by calling MongoCursor::timeout() on the cursor or
 * setting MongoCursor::$timeout. The static variable is useful for queries such
 * as database commands and MongoCollection::findOne(), both of which implicitly
 * use cursors.
 * @link http://www.php.net/manual/en/class.mongocursortimeoutexception.php
 */
class MongoCursorTimeoutException extends MongoCursorException {

}

/**
 * @link http://www.php.net/manual/en/class.mongoconnectionexception.php
 */
class MongoConnectionException extends MongoException {

}

/**
 * Thrown when there are errors reading or writing files to or from the database.
 * @link http://www.php.net/manual/en/class.mongogridfsexception.php
 */
class MongoGridFSException extends MongoException {

}

/**
 * Logging can be used to get detailed information about what the driver is 
 * doing. With PHP-CLI, log output will be printed to stderr. Under an app server,
 * log messages will generally be printed to an error log.<br/>
 * Logging is turned off, by default. This class allows you to turn on specific 
 * levels of logging for specific parts of the driver.
 * @link http://www.php.net/manual/en/class.mongolog.php
 */
class MongoLog {
    /* Constants */
    /**
     * Constant for turning logging off.  
     */
    const NONE = 0;
    /**
     * Constant for logging everything.  
     */
    const  ALL = 0;
    
    //level constants
    /**
     * This will print log messages about somewhat exceptional but 
     * not-quite-exception-worthy happenings.  
     */
    const WARNING = 0;
    /**
     * Logs events that may be of interest to administrators, but are not 
     * particularly noteworthy.  
     */
    const INFO = 0;
    /**
     * Logs most events that the driver performs. Depending on the module being 
     * logged, this can be extremely noisy and is primarily for debugging.  
     */
    const FINE = 0;
    
    // module constants
    /**
     * Log replica set activity. Failovers, pinging, chosing secondaries to read from, etc.  
     */
    const RS = 0;
     /**
      * Log connection pool activity. Creating new connections, reusing 
      * connections, and closing connections.  
      */
    const POOL = 0;
    /**
     * Logs traffic to/from the database. Unless your program is trivial, this 
     * will create an enormous number of log messages.  
     */
    const IO = 0;
    /**
     * Log server status changes. Detecting primary, secondary and duplication detection.  
     */
    const SERVER = 0;
    /**
     * Log server string parsing.  
     */
    const PARSE = 0;

    /* Fields */
    public $level;
    public $module;

    /* Methods */
    
    /**
     * Gets the log level
     * 
     * @return integer Returns the current level. 
     * @link http://www.php.net/manual/en/mongolog.getlevel.php
     */
    public static function getLevel(){}
    
    /**
     * Gets the modules currently being logged
     * 
     * @return integer  Returns the modules currently being logged. 
     * @link http://www.php.net/manual/en/mongolog.getmodule.php
     */
    public static function getModule(){}
    
    
    /**
     * Sets logging level
     * 
     * @param integer $level The levels you would like to log. 
     * @link http://www.php.net/manual/en/mongolog.setlevel.php
     */
    public static function setLevel(integer $level){}
    
    /**
     * Sets driver functionality to log
     * 
     * @param integers $module The module(s) you would like to log. 
     * @link http://www.php.net/manual/en/mongolog.setmodule.php
     */
    public static function setModule(integers $module){}
}

class MongoPool {
    /* Methods */
    
    /**
     * Get pool size for connection pools
     * 
     * @return integer Returns the current pool size. 
     * @link http://www.php.net/manual/en/mongopool.getsize.php
     */
    public static function getSize(){}
    
    /**
     * Returns information about all connection pools.
     * 
     * @return array Each connection pool has an identifier, which starts with 
     * the host. For each pool, this function shows the following fields: <br/>
     * <dt><span class="term"><i><tt class="parameter">in use</tt></i></span></dt>
     * <dd><p>The number of connections currently being used by
     * <a href="class.mongo.php" class="classname">Mongo</a> instances.
     * </p></dd>
     * 
     * <dt><span class="term"><i><tt class="parameter">in pool</tt></i></span></dt>
     * <dd><p>The number of connections currently in the pool (not being used).</p></dd>
     * 
     * <dt><span class="term"><i><tt class="parameter">remaining</tt></i></span></dt>
     * <dd><p>The number of connections that could be created by this pool. For
     * example, suppose a pool had 5 connections remaining and 3 connections in
     * the pool.  We could create 8 new instances of @see Mongo before we exhausted
     * this pool (assuming no instances of @see Mongo went out of scope, returning
     * their connections to the pool).</p>
     * <p>A negative number means that this pool will spawn unlimited connections.</p>
     * <p>Before a pool is created, you can change the max number of connections by
     * calling @see Mongo::setPoolSize(). Once a pool is showing up in the output
     *  of this function, its size cannot be changed.</p></dd>
     * 
     * <dt><span class="term"><i><tt class="parameter">total</tt></i></span></dt>
     * <dd><p>The total number of connections allowed for this pool.  This should be
     * greater than or equal to "in use" + "in pool" (or -1).</p></dd>
     * 
     * <dt><span class="term"><i><tt class="parameter">timeout</tt></i></span></dt>
     * <dd><p>The socket timeout for connections in this pool. This is how long
     * connections in this pool will attempt to connect to a server before
     * giving up.</p></dd>
     * 
     * <dt><span class="term"><i><tt class="parameter">waiting</tt></i></span></dt>
     * <dd><p>If you have capped the pool size, workers requesting connections from
     * the pool may block until other workers return their connections. This
     * field shows how many milliseconds workers have blocked for connections to
     * be released.  If this number keeps increasing, you may want to use
     * @see MongoPool::setSize() to add more connections to your pool.</p></dd>
     * @link http://www.php.net/manual/en/mongopool.info.php
     */
    public static function  info (){}
    
    /**
     * Set the size for future connection pools.
     * @param integer $size The max number of connections future pools will be 
     * able to create. Negative numbers mean that the pool will spawn an infinite
     * number of connections. 
     * @return integer Returns the former value of pool size. 
     * @link http://www.php.net/manual/en/mongopool.setsize.php
     */
    public static function setSize( integer $size ){}
}