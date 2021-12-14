import {Controller} from 'stimulus';
import $ from "jquery"

export default class EditableField_controller extends Controller {

	static isEditMode = false;
	id;
	value;
	newValue = null;
	fieldName;
	$buttons;
	$messagesList;

	connect() {
		$(this.element).click((event) => {
			event.preventDefault();
			if (EditableField_controller.isEditMode) {
				return;
			}
			this.value = event.target.innerHTML.trim();
			this.fieldName = event.target.dataset.fieldName;
			this.id = event.target.dataset.id
			this.$buttons = $(event.target.dataset.buttons);
			this.$messagesList = $(event.target.dataset.messagesList);


			this.element.innerHTML = `<textarea class="form-control">${this.value}</textarea>`;
			this.element.onchange = (event) => {
				this.newValue = event.target.value
			};

			this.activateEditMode();
		})
	}

	change() {
		fetch(`/api/v1/articles/${this.id}/update`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json;charset=utf-8'
			},
			body: JSON.stringify({
				fields: [{
					name: this.fieldName,
					value: this.newValue ?? this.value,
				}]
			})
		}).then(async response => {
			response = await response.json();
			this.deactivateEditMode()
			this.showMessage(response.message)
			this.element.innerHTML = this.newValue
		})
	}

	cancel() {
		this.deactivateEditMode()
		this.element.innerHTML = this.value
	}

	activateEditMode() {
		this.$buttons.each((index, item) => {
			item.hidden = false;
			item.onclick = (event) => {
				if (event.target.dataset.change === 'Y') {
					this.change();

				} else if (event.target.dataset.cancel === 'Y') {
					this.cancel();
				}
			};
		});
		EditableField_controller.isEditMode = true;
	}

	deactivateEditMode() {
		EditableField_controller.isEditMode = false;
		this.$buttons.each((index, item) => {
			item.hidden = true;
			item.onclick = () => {
			};
		})
	}

	showMessage(message) {
		let messageDiv = document.createElement("div")
		messageDiv.classList.add(`alert`)
		messageDiv.classList.add(`alert-${message.type}`)
		messageDiv.innerHTML = message.text;
		this.$messagesList.get(0).innerHTML = '';
		this.$messagesList.get(0).appendChild(messageDiv);
	}
}
