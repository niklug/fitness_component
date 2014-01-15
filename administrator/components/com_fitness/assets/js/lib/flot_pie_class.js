/*
 * Class providedrawing pie graph 
 * http://www.flotcharts.org/flot/examples/series-pie/index.html
 * http://people.iola.dk/olau/flot/examples/pie.html
 */
// Constructor
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function DrawPie(data, container, options) {
        this.data = data;
        this.container = container;
        this.options = options;
        
    }

    DrawPie.prototype.draw = function() {
        var self = this;
        var label_formater = this.labelFormatter;
        if(this.options.no_percent_label) label_formater = this.labelFormatter_no_percents;
        $.plot(this.container, this.data, {
            series: {
                pie: {
                    show: true,
                    radius: 1,
                    label: {
                        show: true,
                        radius: 3 / 4,
                        formatter: label_formater,
                        background: {
                            opacity: 0.5
                        }
                    }
                }
            },
            legend: {
                show: false
            }

        });
    }


    DrawPie.prototype.labelFormatter = function(label, series) {
       var label_total =  label + "<br/>" + Math.round(series.percent) + "%" ;
 
       return "<div style='font-size:8pt; text-align:center; padding:2px; color:#000000;'>" + label_total + "</div>";
    }
    
    DrawPie.prototype.labelFormatter_no_percents = function(label, series) {
 
       return "<div style='font-size:8pt; text-align:center; padding:2px; color:#000000;'>" + label + "</div>";
    }
    
    // Add the DrawPie function to the top level of the jQuery object
    $.drawPie = function(data, container, options) {

        var constr = new DrawPie(data, container, options);

        return constr;
    };
}));



