/*
 * The menu item editor. Provides tools for managing the 
 * menu items.
 */
+function ($) { "use strict";
    var MinorjetSlider = function (el, options) {
        this.$el = $(el)
        this.slider = this.$el.find('[data-control="slider"]');
        this.indicator = this.$el.find('[data-control="slider-indicator"]');
        this.target = this.$el.find('input[type="hidden"]');
        options.value = typeof this.$el.data('value') != 'undefined' ? this.$el.data('value') : options.value;
        options.min = typeof this.$el.data('min') != 'undefined' ? this.$el.data('min') : options.min;
        options.max = typeof this.$el.data('max') != 'undefined' ? this.$el.data('max') : options.max;
        options.target = typeof this.$el.data('target') != 'undefined' ? this.$el.data('target') : options.target;
        this.options = options

        this.init()
    }

    MinorjetSlider.prototype.init = function() {
        var self = this

        this.slider.slider({
            min: self.options.min,
            max: self.options.max,
            value: self.options.value,
            change: function(e, ui){
                self.target.val(ui.value);
            },
            slide: function(e, ui){
                self.indicator.find('[data-type="primary"]').find('.content').html( ui.value );
                self.indicator.find('[data-type="secondary"]').find('.content').html( (12 - ui.value) );
                if ( ui.value > 0 ) {
                    self.indicator.find('[data-type="primary"]').attr('class', 'col-xs-' + ui.value);
                }else {
                    self.indicator.find('[data-type="primary"]').attr('class', 'hidden');
                }
                if ( ui.value < 12 ) {
                    self.indicator.find('[data-type="secondary"]').attr('class', 'col-xs-' + (12 - ui.value));
                }else {
                    self.indicator.find('[data-type="secondary"]').attr('class', 'hidden');
                }            
            }
        });

    }

    $.fn.minorjetSlider = function (option) {
        var args = Array.prototype.slice.call(arguments, 1)
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.minorjetSlider')
            var options = $.extend({}, MinorjetSlider.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.minorjetSlider', (data = new MinorjetSlider(this, options)))
            else if (typeof option == 'string') data[option].apply(data, args)
        })
    }

    $.fn.minorjetSlider.Constructor = MinorjetSlider

    $(document).on('render', function() {
        $('[data-control="minorjet-slider"]').minorjetSlider({
            min: 0,
            max: 12,
            value: 6
        });
    });
}(window.jQuery);