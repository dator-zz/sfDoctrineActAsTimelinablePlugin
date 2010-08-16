Installation
============

1. Add this plugin to your project as Git submodules:

          $ git submodule add git://github.com/dator/sfDoctrineActAsTimelinablePlugin.git plugins/sfDoctrineActAsTimelinablePlugin


  2. Add this plugin to your ProjectConfiguration file:

          // config/ProjectConfiguration.class.php
          public function setup()
          {
              $this->enablePlugins(array(
                  // ...
                  'sfDoctrineActAsTimelinablePlugin',
                  // ...
              ));
          }

Usage
=====
This plugin requires you to have a TimelineEvent model. The simplest way is to use the doctrine generator:

          ./symfony doctrine:build --all --and-load
          
Next step is to determine what generates an event in your schema.yml

          Comment:
            actAs:
              Timelineable:
                new_comment: 
                  on: save
                  actor: User
                  secondary_subject: Post
                remove_comment:
                  on: delete
                  actor: User
                  secondary_subject: Post
            columns:
              user_id:
                type: integer
              post_id:
                type: integer
              content:
                type: clob
            relations:
              User:
                type: one
                local: user_id
                foreign: id
                onDelete: CASCADE
              Post:
                type: one
                local: post_id
                foreign: id
                onDelete: CASCADE
                
Parameters for Timelinable
==========================

- The first param is a custom name for the event type. It’ll be your way of figuring out what events your reading back from the timeline_events table later and to internationalize it. 
  - :new_comment in the example 

- The rest all fit neatly in an options hash. 
  - on => [String event] 
    - mandatory. You use it to specify whether you want the event created after a create, update or destroy. You can also supply an array of events, e.g. [save, delete, update, insert]. delete event is triggered after the deletion.
    
  - actor is your way of specifying who took this action.
    - in the example User, because an user post a comment (Need to be related in the relations section)
  - subject : is automatically set to the current class with the behavior
  - secondary_subject can let you specify something else that’s related to the event. A comment to a blog post would be a good example. 
  
  
How you actually get your timeline
==================================

In the controller:
            // config/ProjectConfiguration.class.php
            public function executeIndex(sfWebRequest $request)
            {
              $this->timeline = Doctrine_Query::create()->from('TimelineEvent te')->execute();
            }
            
In the view:

            <ul>
              <?php foreach($timeline as $ti):?>
                <li>
                  <?php echo $ti->getActor()?> 
                  <?php echo $ti->getEventType()?> 
                  in
                  <?php echo $ti->getSecondarySubject()?>
                </li>
              <?php endforeach;?>
            </ul>

            
Warning
-------

Does not work with many to many relationships.

License
-------
Copyright © 2010 Clément JOBEILI, released under the MIT license     