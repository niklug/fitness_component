/*
 * Class providedrawing pie graph 
 * http://www.flotcharts.org/flot/examples/series-pie/index.html
 * http://people.iola.dk/olau/flot/examples/pie.html
 */
// Constructor
(function($) {
    function DrawPie(data, container) {
        this.data = data;
        this.container = container;
    }

    DrawPie.prototype.draw = function() {
            $.plot(this.container, this.data, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 3 / 4,
                            formatter: this.labelFormatter,
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
       return "<div style='font-size:8pt; text-align:center; padding:2px; color:#000000;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
    
    // Add the DrawPie function to the top level of the jQuery object
    $.drawPie = function(data, container) {

        var constr = new DrawPie(data, container);

        return constr;
    };
})(jQuery);