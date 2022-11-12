
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

				 // LazyLoad- & lightbox JS for EXT:t3sbootstrap - if you need a custom JS, you have to put it here
				if (resultContainer.dataset.lazy) {
						new LazyLoad({
							elements_selector: '.lazy',
							threshold: 0
						});
				}
				if (resultContainer.dataset.lightbox == 1) {
					// Baguette Box (lightbox)
					baguetteBox.run('.gallery, .image-gallery', {
						animation: 'fadeIn',
						noScrollbars: true
					});
				}
				if (resultContainer.dataset.lightbox == 2) {
					// halkabox-{data.uid} (lightbox)
					halkaBox.options({animation: 'fade',theme: 'dark'});
					halkaBox.run('gallery-'+contentByUid);
				}
				if (resultContainer.dataset.lightbox == 3) {
					// GLightbox (lightbox)
					const lightbox = GLightbox({loop: true});
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
	
