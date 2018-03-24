define([
    'underscore',
    'backbone',
    'app',
], function ( _, Backbone, app) {
    var collection = new Backbone.Collection([
        
        {
            id: '1',
            type: 'Primary Goal',
            action: 'created',
            template: '{created_by} created a new <a class="notifiction_open_list">Primary Goal</a> {user_id} beginning <a class="notifiction_open_form">{date}</a>',
            backend_list_url: 'index.php?option=com_fitness&view=goals',
            backend_form_url: 'index.php?option=com_fitness&view=goals#!/form_primary/{url_id_1}',
            frontend_list_url: 'index.php?option=com_fitness&view=goals',
            frontend_form_url: 'index.php?option=com_fitness&view=goals#!/form_primary/{url_id_1}'
        },
      
      
    
        {
            id: '2',
            type: 'Mini Goal',
            action: 'created',
            template: '{created_by} created a new <a class="notifiction_open_list">Mini Goal</a> {user_id} beginning <a class="notifiction_open_form">{date}</a>',
            backend_list_url: 'index.php?option=com_fitness&view=goals',
            backend_form_url: 'index.php?option=com_fitness&view=goals#!/form_mini/{url_id_1}/{url_id_2}',
            frontend_list_url: 'index.php?option=com_fitness&view=goals',
            frontend_form_url: 'index.php?option=com_fitness&view=goals#!/form_mini/{url_id_1}/{url_id_2}'
        },
      
      
      
            
      {id : "3", type : "Primary Goal", action : "started a conversation", template : "{created_by} started a new conversation about a {object} beginning {date}"},
      
      {id : "4", type : "Primary Goal", action : "client replied to conversation", template : "{created_by} replied to a conversation about a {object} beginning {date}"},
      
      {id : "5", type : "Primary Goal", action : "trainer replied to conversation", template : "{created_by} replied to a conversation about {user_id}’s {object} beginning {date}"},
      
      {id : "6", type : "Nutrition Diary", action : "started a conversation", template : "{created_by} started a new conversation about {user_id}’s nutrition diary entry for {date}"},
    ]);
    
    return collection;
});
