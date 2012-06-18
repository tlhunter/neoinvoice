var niScrollerInterval = 12,
	niCurrentIndex = 1,
	niScrollerTime = 0.5,
	niScrollerHandle;

$(document).ready(function () {
	$('#ni-slider ul a[rel]').click(function () {
		clearInterval(niScrollerHandle);
		niHandleSelection(this);
	    return false;
	});
	niScrollerHandle = setInterval("cyclePanels()", niScrollerInterval * 1000);
});

function niHandleSelection(link) {
	$('#ni-slider ul a').removeClass('active');
	$(link).addClass('active');
	$('#splash').scrollTo($(link).attr('rel'), niScrollerTime * 1000);
}

function cyclePanels() {
	niCurrentIndex++;
	if (niCurrentIndex > 4) {
		niCurrentIndex = 1;
	}
	niHandleSelection($('#panel-trigger-' + niCurrentIndex));
}