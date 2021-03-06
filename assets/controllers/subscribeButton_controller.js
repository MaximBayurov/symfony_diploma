import { Controller } from 'stimulus';
import $ from "jquery"

export default class extends Controller {

	connect() {
		$(this.element)
		.click((event)=> {
			event.preventDefault()
			if (event.target.disabled === true) {
				return;
			}

			let subscribeButtons = $('.container-fluid').find('[data-controller="subscribeButton"]').get();
			subscribeButtons.forEach((element) => {
				element.disabled = true;
			})

			fetch('/api/v1/subscribe/', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify({
					'level': event.target.dataset.subscribtionType
				})
			}).finally(() => {
				window.location = event.target.href
			});
		})
	}
}
