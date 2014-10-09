define([
    'underscore',
    'backbone',
    'app',
], function ( _, Backbone, app) {
    var collection = new Backbone.Collection([
      {id : "1", name : "Assessment"},
      {id : "2", name : "Self Assessment"},
      {id : "3", name : "Trainer Assessment"},
      {id : "4", name : "Calendar"},
      {id : "5", name : "Exercise Video"},
      {id : "6", name : "Menu Plan"},
      {id : "7", name : "Message Centre"},
      {id : "8", name : "Nutrition Database"},
      {id : "9", name : "Nutrition Diary"},
      {id : "10", name : "Nutrition Plan"},  
      {id : "11", name : "Primary Goal"}, 
      {id : "12", name : "Mini Goal"}, 
      {id : "13", name : "Programs & Workouts"},
      {id : "14", name : "Available"},
      {id : "15", name : "Cardio Workout"},
      {id : "16", name : "Consultation"},
      {id : "17", name : "Personal Training"},
      {id : "18", name : "Resistance Workout"},
      {id : "19", name : "Semi-Private Training"},
      {id : "20", name : "Special Event"},
      {id : "21", name : "Unavailable"},
      {id : "22", name : "Recipe"},  
    ]);
    
    return collection;
});
