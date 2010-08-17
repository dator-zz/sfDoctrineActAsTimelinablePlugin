<?php

class Doctrine_Template_Timelinable extends Doctrine_Template
{
  protected $_options = array(
  );
  
  public function setTableDefinition()
  {
    $this->addListener(new TimelinableListener($this->_options)); 
  }

  public function setUp()
  {
    
  }
}
