<?php
class TimelinableListener extends Doctrine_Record_Listener
{
  protected $table = 'TimelineEvent';
  protected $_options;
  protected $_save_fires;
  protected $_update_fires;
  protected $_delete_fires;
  protected $_insert_fires;
  
  public function __construct(array $options)
  {
    $this->_options = $options;
    $this->_save_fires   = array();
    $this->_update_fires = array();
    $this->_delete_fires = array();
    $this->_insert_fires = array();
    
    foreach($options as $fire => $opts)
    {
      if($opts['on'] === "save")
      {
        $this->_save_fires[$fire] = $opts;
      }
      
      if($opts['on'] === "update")
      {
        $this->_update_fires[$fire] = $opts;
      }
      
      if($opts['on'] === "delete")
      {
        $this->_delete_fires[$fire] = $opts;
      }
      
      if($opts['on'] === "insert")
      {
        $this->_insert_fires[$fire] = $opts;
      }
    }
  }
  
  public function postSave(Doctrine_Event $event)
  {
    $this->newEvent($event, 'save');
  }
  
  public function postUpdate(Doctrine_Event $event)
  {
    $this->newEvent($event, 'update');
  }
  
  public function postInsert(Doctrine_Event $event)
  {
    $this->newEvent($event, 'insert');
  }
  
  public function preDelete(Doctrine_Event $event)
  {
    $this->newEvent($event, 'delete'); 
  }
  
  protected function newEvent(Doctrine_Event $event, $type)
  {
    $invoker      = $event->getInvoker();
    $subjectClass = get_class($invoker);
    $Ainvoker     = $invoker->toArray();
    $subjectId    = $invoker['id'];
    $attribut     = '_'.$type.'_fires';
    
    
    foreach($this->{$attribut} as $name => $options)
    {
      $actorClass   = $options['actor'];
      $actorId      = $invoker[$actorClass]['id'];
      $secondClass  = '';
      $secondId     = '';
      
      if(array_key_exists('secondary_subject', $options))
      {
        $secondClass  = $options['secondary_subject'];
        $secondId     = $invoker[$secondClass]['id'];
      }
      
      $eventClass = $this->table;
      $event = new $eventClass();
      $event->event_type              = ($name) ? $name : '';
      $event->subject_type            = ($subjectClass) ? $subjectClass :'';
      $event->subject_id              = ($subjectId) ? $subjectId :'';
      $event->actor_type              = ($actorClass) ? $actorClass : '';
      $event->actor_id                = ($actorId) ? $actorId: '' ;
      $event->secondary_subject_type  = ($secondClass)? $secondClass : '';
      $event->secondary_subject_id    = ($secondId)? $secondId : '';;
      $event->save();
    }
  }
} 