define([
    'underscore',
    'backbone',
    'app',
], function ( _, Backbone, app) {
    var path = app.options.base_url + 'administrator/components/com_fitness/assets/images/nutrition_guide/';
    var collection = new Backbone.Collection([
      {id : "1", name : "Breakfast", image : path + 'icon_breakfast.png'},
      {id : "2", name : "Lunch", image : path + 'icon_lunch.png'},
      {id : "3", name : "Dinner", image : path + 'icon_dinner.png'},
      {id : "4", name : "Snack", image : path + 'icon_snack.png'},
      {id : "5", name : "Cheat Meal", image : path + 'icon_cheatmeal.png'},
      {id : "6", name : "Drinks", image : path + 'icon_drinks.png'},
      {id : "7", name : "Tea & Coffee", image : path + 'icon_coffee.png'},
      {id : "8", name : "Alcohol", image : path + 'icon_alcohol.png'},
      {id : "9", name : "Pre-Workout Meal", image : path + 'icon_preworkout-meal.png'},
      {id : "10", name : "Post-Workout Meal", image : path + 'icon_postworkout-meal.png'},
      {id : "11", name : "Pre-Workout Supplements", image : path + 'icon_preworkout-supplement.png'},  
      {id : "12", name : "Post-Workout Supplements", image : path + 'icon_postworkout-supplement.png'}, 
      {id : "13", name : "Supplement Protocol", image : path + 'icon_supplementprotocol.png'}, 
    ]);
    
    return collection;
});
