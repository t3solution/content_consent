
function ajaxConsentCall(contentByUid, currentRecord, cookies)
{
	let resultContainer = document.getElementById('content-'+contentByUid),
		hash = resultContainer.getAttribute('data-hash'),
		button = document.getElementById('trigger-button-'+contentByUid),
		url = button.dataset.url,
		ajaxCall = new XMLHttpRequest();

	ajaxCall.onreadystatechange = function () {
		 if (ajaxCall.readyState === 4) {
			 if (ajaxCall.status === 200) {
				 resultContainer.innerHTML = ajaxCall.responseText;
			} else {
				resultContainer.innerHTML = ajaxCall.statusText;
			}
		}
	};

	ajaxCall.open('POST', url);
	ajaxCall.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	ajaxCall.send('contentByUid='+contentByUid+'&currentRecord='+currentRecord+'&cookies='+cookies+'&hash='+hash);
}
