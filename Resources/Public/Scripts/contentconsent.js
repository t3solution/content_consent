
function ajaxConsentCall(contentByUid, currentRecord, cookies) 
{
	let resultContainer = document.getElementById('content-'+contentByUid)
		button = document.getElementById('trigger-button-'+contentByUid),
		url = button.dataset.url,
		ajaxCall = new XMLHttpRequest();

	ajaxCall.onreadystatechange = function () {
		 if (ajaxCall.readyState === 4) {
			 if (ajaxCall.status === 200) {
				 resultContainer.innerHTML = ajaxCall.responseText;
				 // LazyLoad JS for EXT:t3sbootstrap - if you need a custom lazy load JS for your images, you have to put it here
	 			if (resultContainer.dataset.lazy) {
					new LazyLoad({
						elements_selector: '.lazy',
						threshold: 0
					});
				}
			} else {
				resultContainer.innerHTML = ajaxCall.statusText;
			}
		}
	};

	ajaxCall.open('POST', url);
	ajaxCall.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	ajaxCall.send('contentByUid='+contentByUid+'&currentRecord='+currentRecord+'&cookies='+cookies);
}
