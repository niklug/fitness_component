define([
    'underscore',
    'backbone',
    'app',
], function ( _, Backbone, app) {
    var collection = new Backbone.Collection([
      {id : "1", type : "Primary Goal", action : "created", template : "{created_by} created a new Primary Goal beginning {date}"},
      
      
      
      
      {id : "2", type : "Primary Goal", action : "submitted", template : "{created_by} submitted a new {object} beginning {date}"},
      
      {id : "3", type : "Primary Goal", action : "started a conversation", template : "{created_by} started a new conversation about a {object} beginning {date}"},
      
      {id : "4", type : "Primary Goal", action : "client replied to conversation", template : "{created_by} replied to a conversation about a {object} beginning {date}"},
      
      {id : "5", type : "Primary Goal", action : "trainer replied to conversation", template : "{created_by} replied to a conversation about {user_id}’s {object} beginning {date}"},
      
      {id : "6", type : "Nutrition Diary", action : "started a conversation", template : "{created_by} started a new conversation about {user_id}’s nutrition diary entry for {date}"},
    ]);
    
    return collection;
});
