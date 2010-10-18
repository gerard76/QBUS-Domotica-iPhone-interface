<?

function get_pages()
{
  return db_find_all('pages', array('order' => 'position'));
}

function get_modules_by_page($page = 1)
{
  return db_find_all('page_modules JOIN modules ON 
                        modules.id = page_modules.module_id', 
                      array('conditions' => 
                               'page_modules.page_id ='.db_escape($page),
                            'order' => 'page_modules.position',
                            'debug' => false));
}

function get_module_menu_numbers()
{
  $res = db_find_all('modules', array('fields' => 'DISTINCT menu'));
  foreach($res AS $record)
    $return[] = $record['menu'];
  return $return;
}

function update_module_status($module_id, $module_status)
{
  return db_update_field('modules', 'status', $module_status, 
                         array('conditions' =>  "id = ".db_escape($module_id), 
                               'debug' => false));
}
?>
