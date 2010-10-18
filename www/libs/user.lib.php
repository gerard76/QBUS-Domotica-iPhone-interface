<?php

function get_user($id)
{
 return db_find('users','*','id=\''.db_escape($id).'\'');
}

function get_user_by_email($email)
{
 return db_find('users', array('conditions' => "email='".db_escape($email)."'"));
}

function verify_user($username, $password)
{
  $db_user = get_user_by_email($username);
	if($db_user && md5($password) == $db_user['password'])
	  return $db_user;
	return false;
}

function update_user($user)
{
  if(empty($user['id']))
	{
	  log('update_user() missing user.id');
		return false;
	}
	return db_update('users', $user, 'id='.$user['id']);
}

function insert_user($user)
{
  if(empty($user['email']) ||
	   empty($user['password']))
	{
	  log('insert_user() email of password empty');
		return false;
	}
	$user['password'] = md5($user['password']);
	return db_insert('users', $user);
}

function delete_user($user_id)
{
  return  db_delete('users', 'id='.db_escape($user_id));
}

?>
