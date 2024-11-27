import {Component} from './component';

export class BookshelfExportManager extends Component {

    setup() {
        this.container = this.$el;
        this.confirmDialog = this.$refs.confirmDialog;

        this.setupListeners();
    }

    setupListeners() {
        // Listening for the 'bookshelf-export-click' event
        window.$events.listen('bookshelf-export-click', async result => {
            const dialog = window.$components.firstOnElement(this.confirmDialog, 'confirm-dialog');
            this.confirmDialog.querySelector('[data-button-type="cancel"]').innerHTML = 'Single';
            this.confirmDialog.querySelector('[data-button-type="confirm"]').innerHTML = 'Split';

            const singleBtn = this.confirmDialog.querySelector('[data-button-type="cancel"]');
            const splitBtn = this.confirmDialog.querySelector('[data-button-type="confirm"]');
            singleBtn.addEventListener('click', () => this.redirectToLink(result, false));
            splitBtn.addEventListener('click', () => this.redirectToLink(result, true));
            dialog.show();
        });
    }

    redirectToLink(link, split) {
        window.location.href = `${link}?split=${split}`;
    }

}
