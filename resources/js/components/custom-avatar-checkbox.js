import {Component} from './component';

export class CustomAvatarCheckbox extends Component {

    setup() {
        this.container = this.$el;
        this.checkbox = this.container.querySelector('input[type=checkbox]');
        this.display = this.container.querySelector('[role="checkbox"]');

        this.checkbox.addEventListener('change', this.stateChange.bind(this));
        this.container.addEventListener('keydown', this.onKeyDown.bind(this));
        this.container
            .querySelector('.custom-file-input')
            .parentNode.classList.toggle('hidden', this.checkbox.checked);
    }

    onKeyDown(event) {
        const isEnterOrSpace = event.key === ' ' || event.key === 'Enter';
        if (isEnterOrSpace) {
            event.preventDefault();
            this.toggle();
        }
    }

    toggle() {
        this.checkbox.checked = !this.checkbox.checked;
        this.checkbox.dispatchEvent(new Event('change'));
        this.stateChange();
    }

    stateChange() {
        const checked = this.checkbox.checked ? 'true' : 'false';
        this.display.setAttribute('aria-checked', checked);
        this.container
            .querySelector('.custom-file-input')
            .parentNode.classList.toggle('hidden', this.checkbox.checked);
        this.container.querySelector('.custom-file-input').disabled = this.checkbox.checked;
        this.container.querySelector('[refs="image-picker@reset-input"]').disabled = this.checkbox.checked;
        this.checkbox.value = this.checkbox.checked ? 0 : 1;
    }

}
