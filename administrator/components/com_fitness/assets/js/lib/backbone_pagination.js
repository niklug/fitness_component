(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    
    function BackbonePagination(options) {

        var Pagination_item = Backbone.Model.extend({});

        var Pagination_items = Backbone.Collection.extend({
            model: Pagination_item,
        });

        var Pagination_app_model = Backbone.Model.extend({
            defaults: {
                currentPage : "",
                items_number : 10
            },
            
            initialize: function(){
                this.reset();
                
                if(options.items_total) {
                    this.set({items_total : options.items_total});
                }
                this.container = $("#pagination_container");
                
                if(options.el) {
                    this.container = options.el;
                }
                
                this.bind("change:items_total", this.onChangeItemsTotal, this);
                this.bind("change:currentPage", this.onChangeCurrentPage, this);
                this.bind("change:items_number", this.onChangeItemsNumber, this);
                
                this.render_pagination_view();
                
            },
            
            render : function() {
                this.render_pagination_view();
            },
            
            onChangeItemsTotal : function() {
                
                this.render_pagination_view();
            }, 
            
            onChangeCurrentPage : function() {
                this.render_pagination_view();
            },
            
            onChangeItemsNumber : function() {
                this.render_pagination_view();
            },   
            
            render_pagination_view : function() {
                this.pagination_view = new Pagination_view({model : this});
                
                this.container.html(this.pagination_view.render().el);
            },

            checkLocalStorage : function() {
                if(typeof(Storage)==="undefined") {
                   return false;
                }
                return true;
            },
            setLocalStorageItem : function(name, value) {
                if(!this.checkLocalStorage) return;
                localStorage.setItem(name, value);
            },
            getLocalStorageItem : function(name) {
                var value = this.get(name);
                if(!this.checkLocalStorage) {
                    return value;
                }
                var store_value =  localStorage.getItem(name);
                if(!store_value) return value;
                return store_value;
            },
            
            reset : function() {
                this.set({'currentPage' : "", 'items_total' : 0});
                this.setLocalStorageItem('currentPage', 1);
            }
        });

        var Pagination_page_view = Backbone.View.extend({
            tagName: "li",
            template: _.template($("#template-page").html()),
            events: {
                "click .a-page-item": "onPageClick",
                
            },
            onPageClick: function(event) {
                var currentPage = this.options.pageIndex;
                this.model.setLocalStorageItem('currentPage',  currentPage);
                this.model.set({currentPage: currentPage});
                
            },
       
            render: function() {
                $(this.el).html(this.template({pageNumber: this.options.pageIndex, pageClass: this.options.pageClass}));
                return this;
            },
        });


         var Pagination_view = Backbone.View.extend({

            initialize: function(){
                this.itemsCollection = new Pagination_items();
                this.itemsCollection.bind("add", this.renderPages, this);
            },

            render : function(){
                var variables = {
                    'currentPage' : this.getCurrentPage(),
                    'pages' : this.getPages()
                };
                var template = _.template($("#backbone_pagination_template").html(), variables);
                this.$el.html(template);
                var items_number = this.model.getLocalStorageItem('items_number');
                $(this.el).find(".items_number").val(items_number);
                this.addItems();
                return this;
            },


            events: {
                "change .items_number": "onLimitChange",
                "click .next_pag": "onClickNext",
                "click .prev_pag": "onClickPrev",
                "click .first_pag": "onClickFirst",
                "click .last_pag": "onClickLast",
            },
            
            onLimitChange: function(event) {
                
                var items_number = $(event.target).val();
                this.model.setLocalStorageItem('currentPage',  1);
                this.model.setLocalStorageItem('items_number', items_number);
                this.model.set({'items_number' : items_number});
                this.model.set({'currentPage' : 1});
            },
            
            onClickNext : function() {
                var pages = this.getPages();
                var currentPage = this.model.getLocalStorageItem('currentPage');
                if(currentPage >= pages) return;
                var nextPage = parseInt(currentPage) + 1;
                this.setCurrentPage(nextPage);
            },
            
            onClickPrev : function() {
                var currentPage = this.model.getLocalStorageItem('currentPage');
                if(currentPage <= 1) return;
                var prevPage = parseInt(currentPage) - 1;
                this.setCurrentPage(prevPage);
            },
            
            onClickFirst : function() {
                this.setCurrentPage(1);
            },
            
            onClickLast : function() {
                var pages = this.getPages();
                this.setCurrentPage(pages);
            },
            
            setCurrentPage : function(value) {
                this.model.setLocalStorageItem('currentPage',  value);
                this.model.set({currentPage: value});
            },
            
            getCurrentPage : function(value) {
                return this.model.getLocalStorageItem('currentPage');
            },

            addItems: function() {
                var items_total = this.model.get('items_total');
                var self = this;
                self.itemsCollection.reset();
                for (var i = 0; i < items_total; i++) {
                    self.itemsCollection.add(new Pagination_item());
                }
            },
            
            getPages : function() {
                var items_total = this.model.get('items_total');
                var items_number = this.model.getLocalStorageItem('items_number');
                var pages = Math.ceil(items_total / items_number) | 0;
                return pages;
            },

            renderPages: function() {
                var items_total = parseInt(this.model.get('items_total'));

                if(this.itemsCollection.length < items_total) return;
                
                var currentPage = parseInt(this.model.getLocalStorageItem('currentPage'));
                var pages = parseInt(this.getPages());

                if (pages > 1) {
                    $(this.el).find(".ul-pagination li").remove();
                    
                    var start_page = 1;
                    var end_page = pages;
                    
                    if(pages > 10) end_page = 10;
                    
                    if(pages > 10) {
                        if(currentPage > 9) {
                            start_page = currentPage - 5;
                            if(currentPage < (pages - 5)) {
                                end_page = currentPage + 5;
                            } else {
                                end_page = currentPage;
                            }
                        }
                    }
                    
                    for (var i = start_page; i <= end_page; i++) {
                        var pageClass = '';
                        if(currentPage == i) pageClass = 'active_link';
                        var pageItem = new Pagination_page_view({pageIndex: i, pageClass: pageClass, model : this.model});
                        $(this.el).find(".ul-pagination").append(pageItem.render().el);
                    }
                }
            },
            
            
        });

        return new Pagination_app_model();
    }


    $.backbone_pagination = function(options) {

        var constr =  BackbonePagination(options);

        return constr;
    };

}));
