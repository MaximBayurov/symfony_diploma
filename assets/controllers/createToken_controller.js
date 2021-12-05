import { Controller } from 'stimulus';
import $ from "jquery"

export default class extends Controller {

	connect() {
		$(this.element)
		.find('[data-create-token-button]')
		.on('click', ((event)=> {
			let button = event.target;
			button.disabled = true
			fetch(
				'/api/v1/token/create', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
				}
			).then(async (response)=>{
				let result = await response.json();
				if (result.newToken) {
					$('[data-token-span]').text(result.newToken);
				}
			}).finally(() => {
				button.disabled = false
			});
		}))
	}
}
