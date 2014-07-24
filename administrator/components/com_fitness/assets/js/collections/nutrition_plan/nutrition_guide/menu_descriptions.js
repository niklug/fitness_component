define([
    'underscore',
    'backbone',
    'app',
], function ( _, Backbone, app) {
    var path = app.options.base_url + 'administrator/components/com_fitness/assets/images/nutrition_guide/';
    var collection = new Backbone.Collection([
      {id : "1", name : "Breakfast", image : path + 'menu_description_breakfast.png'},
      {id : "2", name : "Lunch", image : path + 'menu_description_breakfast.png'},
      {id : "3", name : "Dinner", image : path + 'menu_description_breakfast.png'},
      {id : "4", name : "Snack", image : path + 'menu_description_breakfast.png'},
      {id : "5", name : "Cheat Meal", image : path + 'menu_description_breakfast.png'},
      {id : "6", name : "Drinks", image : path + 'menu_description_breakfast.png'},
      {id : "7", name : "Tea & Coffee", image : path + 'menu_description_breakfast.png'},
      {id : "8", name : "Alcohol", image : path + 'menu_description_breakfast.png'},
      {id : "9", name : "Pre-Workout Meal", image : path + 'menu_description_breakfast.png'},
      {id : "10", name : "Post-Workout Meal", image : path + 'menu_description_breakfast.png'},
      {id : "11", name : "Pre-Workout Supplements", image : path + 'menu_description_breakfast.png'},  
      {id : "12", name : "Post-Workout Supplements", image : path + 'menu_description_breakfast.png'}, 
      {id : "13", name : "Supplement Protocol", image : path + 'menu_description_breakfast.png'}, 
    ]);
    
    return collection;
});
