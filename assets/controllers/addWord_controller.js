import {Controller} from 'stimulus';
import $ from "jquery"

export default class extends Controller {

	connect() {
		$(this.element).click(function (event) {
			event.preventDefault()
			let elements = $(event.currentTarget.dataset.rowSelector);
			let element = elements.first().clone();

			const map = (new Map()).set(0,'word').set(1, 'count')
			element.find('input').each((index, input) => {
				input = $(input);
				input.val('');
				input.attr('required', false);
				input.attr('value', '');
				input.attr('id', 'article_create_words_' + elements.length + '_' + map.get(index));
				input.attr('name', 'article_create[words][' + elements.length + ']['+ map.get(index)+']');
			})
			element.find('label').each((index, label) => {
				$(label).attr('for', 'article_create_words_' + elements.length + '_' +  map.get(index));
			})

			element.insertBefore(event.currentTarget);
		})
	}
}
