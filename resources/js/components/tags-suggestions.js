import {Component} from './component';

/**
 * Global (header) search box handling.
 * Mainly to show live results preview.
 */
export class TagsSuggestions extends Component {

    setup() {
        this.container = this.$el;
        this.input = this.$refs.input;
        this.suggestionsTagModel = this.$refs.suggestionsTagModel;
        this.suggestionBox = this.$refs.suggestionBox;
        this.allTags = this.$opts.allTags;
        this.selectedTags = this.$opts.selectedTags;
        this.tags = this.$refs.tags;
        this.setupListeners();
    }

    setupListeners() {
        const tags = JSON.parse(this.allTags);
        this.input.addEventListener('click', () => {
            const {value} = this.input;
            if (value.length === 0) {
              this.suggestionBox.classList.toggle('hidden', false);
            }
        });

        this.input.addEventListener('input', () => {
            const {value} = this.input;
            const suggestions = tags.filter(item => item.name.toLowerCase().includes(value.toLowerCase()));

            if (value.length > 0) {
                if (suggestions.length > 0) {
                    this.updateSuggestions(suggestions);
                } else {
                    this.input.parentNode.querySelector('.card').classList.toggle('hidden', true);
                }
            } else {
                this.suggestionBox.classList.toggle('hidden', false);
                this.updateSuggestions(tags);
            }
        });

        document.addEventListener('click', event => {
            if (!this.input.contains(event.target)) {
                this.suggestionBox.classList.add('hidden');
            }
        });

        this.tags.addEventListener('click', event => {
            if (event.target.matches('input[type=checkbox][data-id]')) {
                const checkbox = event.target;
                const tagName = checkbox.getAttribute('data-id');
                const isChecked = checkbox.checked;

                if (isChecked) {
                    this.input.value = tagName;
                    checkbox.closest('form').submit();
                }
          }
        });
    }

    /**
       * @param {Array} suggestions
       */
    async updateSuggestions(suggestions) {
        this.tags.innerHTML = '';
        suggestions.forEach(element => {
            const isTagSelected = JSON.parse(this.selectedTags).some(
                item => item.toLowerCase() === element.name.toLowerCase(),
            );

            if (!isTagSelected) {
                const clone2 = this.suggestionsTagModel.cloneNode(true);
                clone2.classList.toggle('hidden', isTagSelected);
                clone2.querySelector('.toggle-switch').id = element.name;
                clone2.querySelector('.label').innerHTML = element.name;
                clone2.querySelector('input[type=checkbox]').checked = isTagSelected;
                clone2.querySelector('input[type=checkbox]').setAttribute('data-id', element.name);
                this.tags.append(clone2);
            }
        });
    }
}
