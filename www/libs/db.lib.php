<?php
////////////////////////////////////////////////////
//                                                //
//  DataBase library, © 2005 i76                  //
//                                                //
// laatste aanpassing: 19-08-2008                 //
////////////////////////////////////////////////////


$db_handle=connect_db();
function connect_db()
{
 global $CFG;
 $db_handle = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass);
 mysql_select_db($CFG->dbname, $db_handle);
 return $db_handle;
}

function query($query, $showquery=false, $insert=false)
{
 //  $showquery=true;
  // Indien insert=true dan last_insert_id() als return value
  global $CFG, $db_handle, $queries;
  $start = timer();

  if (!$db_handle) 
   $db_handle=connect_db();
  
  $result = mysql_query($query, $db_handle);
  $time = timer($start);

  if ($CFG->debug) {
    if (!isset($queries)) $queries = array();
    $queries[] = "<pre>$query</pre><p><b>Time: $time</b></p><br>";
  }

  if (mysql_errno() > 0) 
   echo "<b>ERROR: </b>".mysql_errno() . ": " . mysql_error(). "\n<hr>";
  if ($showquery) 
  {
   echo "<hr>\n\n<pre>$query</pre>\n\n<b>Time: $time</b><br><hr>";
   if (mysql_errno() > 0) 
    echo "<b>ERROR: </b>".mysql_errno() . ": " . mysql_error(). "\n<hr>";
  }

  if(@mysql_num_rows($result)>0)
  {
   $retcode=array();
   while($retcode[] = mysql_fetch_assoc($result));
   array_pop($retcode);
  }
  else if (@mysql_affected_rows() > 0 && $insert == false)
{
   $retcode = mysql_affected_rows();}
  elseif($insert==true)
   $retcode=@mysql_insert_id();
  else 
   $retcode = false;
  return $retcode;
 }

function db_find_all($tbl, $options = array())
{
 // options are
 //  $fields='*',$conditions='',$order='',$limit=''
 // up to $limit records records from $tbl matching $conditions
 if(!empty($options['conditions']))
  $conditions='WHERE '.$options['conditions'];
 if(!empty($options['limit']))
  $limit='LIMIT '.$options['limit'];
 if(!empty($options['order']))
  $order=' ORDER BY '.$options['order'];
  
 $fields = '*';
 if(!empty($options['fields']))
   $fields = $options['fields'];
 
 $query='SELECT '.$fields;
 $query.=' FROM '.$tbl.'
  '.$conditions.'
  '.$order.'
  '.$limit;
 if($options['debug'] == true)
  echo "\n<pre>$query</pre>\n";
 return query($query);
}

function db_find($tbl, $options = array())
{
 // Returns the specified (or all if not specified) fields from 
 // the first record that matches $conditions. 
 // options: fields='*',$conditions='',$order='')
 $options['limit'] = 1;
 $ret=db_find_all($tbl, $options);
 if($ret)
  return $ret[0];
 return false;
}

function db_field($tbl, $field, $options = array())
{
 // Returns as a string a single field from the first record 
 // matched by $conditions as ordered by $order.
 $options['fields'] = $field;
 $res=db_find($tbl, $options);

 return array_shift($res);
}

function db_count($tbl, $options = array())
{
 // Returns the number of records that match the given conditions.
 return db_field($tbl, 'COUNT(*)', $options);
}

function db_delete($tbl, $options = array())
{
 if(empty($options['conditions']))
  return query('TRUNCATE TABLE '.$tbl);
  
 $query='DELETE FROM '.$tbl.'
  WHERE '.$options['conditions'];
 
 if(!empty($options['limit']))
   $query .= ' LIMIT '.$options['limit'];

 return query($query);
}

function db_update_field($tbl, $field, $value, $options = array())
{
 return db_update($tbl, array($field => $value), $options);
}

function db_update($tbl, $fields, $options = array())
{
 // auto quoting for the values, but sometimes
 // you dont want quotes. In that case prepend a @
 // autoset lastupdate
 if(!empty($options['conditions']))
  $conditions=' WHERE '.$options['conditions'];
  
 $query='UPDATE '.$tbl.' SET lastupdate=NOW(),';
 
 foreach($fields as $field => $value)
 {
  if(strlen($value)&&$value{0}=='@')
   $value=substr($value,1);
  else
   $value='\''.db_escape($value).'\'';
  $query.=$field.'='.$value.',';
 }
 $query=rtrim($query, ',');
 $query.=$conditions;
 if($options['debug'] == true)
  echo "\n<pre>$query</pre>\n";
 return query($query);
}

function db_insert($tbl,$fields)
{
 // insert record into $tbl
 // $fields = array of field=>value type
 // set creationdate
 // returns the autoinc value
 $query='INSERT INTO '.$tbl.' SET ';
 foreach($fields as $field => $value)
 {
  if(strlen($value)&&$value{0}=='@')
   $value=substr($value,1);
  elseif(is_null($value))
	 $value="NULL";
	else
   $value='\''.db_escape($value).'\'';
  $query.=$field."=".$value.",\n";
 }
 $query.='creationdate=NOW()';
 if($id = query($query, false, true))
 {
   $fields['id'] = $id;
	 return $fields;
 }
 return false;
}

function db_escape(&$str)
{
 $str = mysql_real_escape_string($str);
 return str_replace('%', '\%', $str);
}

function get_db_info()
{
 global $CFG;
 $query='SHOW TABLE STATUS FROM `'.$CFG->dbname.'`';
 $records=query($query);


 $tables=array();
 foreach($records as $record)
 {
  foreach($record as $fieldname=>$fieldvalue)
   $tables[$record['Name']][$fieldname]=$fieldvalue;
 }
 return $tables;
} 

function db_tmpfile_field($table, $field, $conditions)
{
 // calling env is responsible for clearing file
 $tmpfile='/tmp'.'/topko'.md5(time());
 $query='SELECT '.$field.' FROM '.$table.' WHERE '.$conditions.
  ' INTO DUMPFILE \''.$tmpfile.'\'';
 query($query, false, false);
 return $tmpfile;
}

function db_optimize_table($table)
{
 $query='OPTIMIZE TABLE '.$table;
 query($query);
}

function db_optimize()
{
 // optimize all table in db
 $query="SHOW TABLES";
 $tables=query($query);
 foreach($tables as $table)
  db_optimize_table(array_shift($table));
}

?>
