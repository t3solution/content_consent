
function ajaxConsentCall(contentByUid, currentRecord, cookies)
{
	let resultContainer = document.getElementById('content-'+contentByUid),
		hash = resultContainer.getAttribute('data-hash'),
		button = document.getElementById('trigger-button-'+contentByUid),
		removeRatio = resultContainer.getAttribute('data-removeratio'),
		url = button.dataset.url,
		ajaxCall = new XMLHttpRequest();

	ajaxCall.onreadystatechange = function () {
		 if (ajaxCall.readyState === 4) {
			if (ajaxCall.status === 200) {
				if (removeRatio == 1) {
					resultContainer.classList.remove('ratio');
				}
				resultContainer.innerHTML = ajaxCall.responseText;
				let lazyImages = document.querySelectorAll('.mainframe img.lazy');
				if(lazyImages.length) {
					lazyImages.forEach(
						function(node) {
							node.classList.remove('lazy');
						}
					);
				}
			} else {
				resultContainer.innerHTML = ajaxCall.statusText;
			}
		}
	};

	ajaxCall.open('POST', url);
	ajaxCall.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	ajaxCall.send('contentByUid='+contentByUid+'&currentRecord='+currentRecord+'&cookies='+cookies+'&hash='+hash);
}
