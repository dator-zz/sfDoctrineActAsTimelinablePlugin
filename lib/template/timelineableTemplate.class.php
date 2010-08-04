<?php

class Doctrine_Template_Timelineable extends Doctrine_Template
{
  protected $_options = array(
  );
  
  public function setTableDefinition()
  {
    $this->addListener(new TimelineableListener($this->_options)); 
  }

  public function setUp()
  {
    
  }
}
