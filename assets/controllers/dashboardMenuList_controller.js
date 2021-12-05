import { Controller } from 'stimulus';

export default class extends Controller {

	connect() {

		let menuList = this.element.getElementsByTagName('a')
		for (let item of menuList) {
			if (item.pathname === window.location.pathname) {
				item.classList.add('active')
				item.classList.remove('bg-light')
			}
		}
	}
}
