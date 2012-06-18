<html>
<head>
	<title>NeoInvoice Application Statistics</title>
	<script src="scripts/jquery.js"></script>
	<script>
		var updateInterval = 5000;
		$(function() {
			window.timer = setInterval("loader()", updateInterval);
			loader();
		});
		function loader() {
			$.ajax({
				type: "GET",
				url: "appstatistics/data",
				dataType: "xml",
				cache: true,
				success: function(xml) {
					$(xml).find('result').each(function(){
						var $targetElement = $('#'+$(this).attr('target'));
						var newValue = parseInt($(this).text());
						var oldValue = parseInt($targetElement.text());
						var difference = newValue - oldValue;
						if (difference > 0) {
							$targetElement.addClass("increase").removeClass("decrease");
						} else if (difference < 0) {
							$targetElement.addClass("decrease").removeClass("increase");
						} else {
							$targetElement.removeClass("decrease").removeClass("increase");
						}
						$targetElement.html(newValue);
						/*
						$targetElement.countTo({
							from: oldValue,
							to: newValue,
							speed: updateInterval * 0.9,
							refreshInterval: 50
						});
						*/
					});
				}
			});
		}
	</script>
	<style>
		body, td {
			font-family: verdana;
			font-size: 11px;
		}
		.result-container {
			font-size: 18px;
		}
		.result-container {
			color: #006fff;
			line-height: -4px;
			display: block;
			float: left;
		}
		body .decrease {
			color: #ff6f00;
		}
		body .increase {
			color: #00ff6f;
		}
		h1 {
			font-family: "trebuchet ms";
			font-size: 20px;
			font-weight: normal;
			margin: 0px; padding: 0px;
		}
	</style>
</head>
<body>
<h1>NeoInvoice Application Statistics</h1>
<table>
<tr><td align="right">Companies:</td><td id="number-a" class="result-container">loading...</td></tr>
<tr><td align="right">Time Segments:</td><td id="number-b" class="result-container">loading...</td></tr>
<tr><td align="right">Invoices:</td><td id="number-c" class="result-container">loading...</td></tr>
</table>
<script>
(function($) {
    $.fn.countTo = function(options) {
        // merge the default plugin settings with the custom options
        options = $.extend({}, $.fn.countTo.defaults, options || {});

        // how many times to update the value, and how much to increment the value on each update
        var loops = Math.ceil(options.speed / options.refreshInterval),
            increment = (options.to - options.from) / loops;

        return $(this).each(function() {
            var _this = this,
                loopCount = 0,
                value = options.from,
                interval = setInterval(updateTimer, options.refreshInterval);

            function updateTimer() {
                value += increment;
                loopCount++;
                $(_this).html(value.toFixed(options.decimals));

                if (typeof(options.onUpdate) == 'function') {
                    options.onUpdate.call(_this, value);
                }

                if (loopCount >= loops) {
                    clearInterval(interval);
                    value = options.to;

                    if (typeof(options.onComplete) == 'function') {
                        options.onComplete.call(_this, value);
                    }
                }
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0,  // the number the element should start at
        to: 100,  // the number the element should end at
        speed: 1000,  // how long it should take to count between the target numbers
        refreshInterval: 100,  // how often the element should be updated
        decimals: 0,  // the number of decimal places to show
        onUpdate: null,  // callback method for every time the element is updated,
        onComplete: null,  // callback method for when the element finishes updating
    };
})(jQuery);
</script>
</body>
</html>