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

			let subscribeButtons = $('.container-fluid').find('[data-controller="deleteModule"]').get();
			subscribeButtons.forEach((element) => {
				element.disabled = true;
			})

			let moduleID = event.currentTarget.dataset.moduleId;

			fetch(`/api/v1/modules/${moduleID}/delete`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
			}).finally(() => {
				window.location = event.currentTarget.href
			});
		})
	}
}
