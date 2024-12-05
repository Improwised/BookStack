import {Component} from './component';

export class EncryptDecryptManager extends Component {

    setup() {
        this.container = this.$el;
        this.encryptBtn = this.$refs.encryptBtn;
        this.decryptSubmitBtn = this.$refs.decryptSubmitBtn;
        this.decryptPassword = this.$refs.decryptPasswordInput;
        this.editBtn = this.$refs.editBtn;
        this.invalidMsg = this.$refs.invalidPassword;
        this.exportMenu = this.$refs.exportMenu;

        this.url = this.$opts.url;
        this.pageName = this.$opts.pageName;
        this.pageIsEncrypted = this.$opts.pageEncrypted;

        this.encryptDialog = document.querySelector('.encrypt-decrypt-dialog');
        this.contentPart = document.querySelector('.page-content');
        this.pageDetailContent = document.querySelector('.page-content').querySelector('.page-detail-content');
        this.encryptInfo = document.querySelector('.page-content').querySelector('.encrypt-message');

        this.invalidPassword = this.encryptDialog.querySelector('.invalid-password');
        this.passwordInput = this.encryptDialog.querySelector('#page-encrypt-password');

        this.is_decrypt = false;

        this.setupListener();
    }

    setupListener() {
        this.encryptBtn.addEventListener('click', async event => {
            event.preventDefault();
            const response = await this.openDialog();
            if (response) {
                // eslint-disable-next-line radix
                if (parseInt(this.pageIsEncrypted) === 1) {
                    this.decryptContent(this.passwordInput.value, true, false);
                } else {
                    this.encryptContent();
                }
            }
        });

        // eslint-disable-next-line radix
        if (parseInt(this.pageIsEncrypted) === 1) {
            this.contentPart.addEventListener('mousedown', event => event.preventDefault());
            this.contentPart.addEventListener('selectstart', event => event.preventDefault());
            this.contentPart.addEventListener('copy', event => event.preventDefault());
            this.decryptPassword.addEventListener('input', () => { this.invalidMsg.classList.add('hidden'); });
            this.decryptSubmitBtn.addEventListener('click', () => this.decryptContent(this.decryptPassword.value));
            this.editBtn.addEventListener('click', event => this.decryptContentOnEdit(event));
            this.decryptPassword.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    this.decryptContent(this.decryptPassword.value);
                }
            });
            this.passwordInput.addEventListener('input', () => {
                this.invalidPassword.classList.add('hidden');
            });

            this.exportMenu.querySelectorAll('a').forEach(anchor => {
                anchor.addEventListener('click', event => this.decryptForExport(event, anchor));
            });
        }
    }

    encryptContent() {
        const decryptPassword = this.passwordInput.value;
        if (decryptPassword.length > 6) {
            const contents = this.pageDetailContent.innerHTML;

            window.$http.post(`${this.url}/encrypt`, {content: contents}).then(resp => {
                if (resp.data.success) {
                    const data = {
                        html: resp.data.content,
                        is_encrypted: true,
                        password: decryptPassword,
                    };
                    this.updateData(data, true, false);
                }
            });
        }
    }

    decryptContent(decryptPassword, updateDecryption = false) {
        if (decryptPassword.length > 6) {
            if(this.is_decrypt && updateDecryption)
            {
                const decryptData = {
                    html: this.pageDetailContent.innerHTML,
                    is_encrypted: false,
                    password: decryptPassword,
                };
                this.updateData(decryptData, false, false);
            }
            else
            {
                const decryptData = {
                    content: this.pageDetailContent.innerHTML,
                    password: decryptPassword,
                };
                window.$http.post(`${this.url}/decrypt`, decryptData).then(async resp => {
                    if (resp.data.success) {
                        this.decryptPassword.value = '';
    
                        if (updateDecryption) {
                            const data = {
                                html: resp.data.content,
                                is_encrypted: false,
                                password: decryptPassword,
                            };
                            this.updateData(data, false, false);
                        } else {
                            this.pageDetailContent.innerHTML = resp.data.content;
                            this.is_decrypt = true;
                            this.encryptInfo.classList.add('hidden');
                        }
                    } else {
                        if (updateDecryption) {
                            this.invalidPassword.innerHTML = resp.data.message;
                            this.invalidPassword.classList.remove('hidden');
                            const response = await this.openDialog();
                            if (response) {
                                this.decryptContent(this.passwordInput.value, updateDecryption);
                            }
                        } else {
                            this.invalidMsg.innerHTML = resp.data.message;
                            this.invalidMsg.classList.remove('hidden');
                        }
                    }
                });
            }
        }
    }

    async openDialog() {
        this.encryptDialog.querySelector('#page-encrypt-password').value = '';
        const dialog = window.$components.firstOnElement(this.encryptDialog, 'confirm-dialog');
        const response = await dialog.show();
        return response;
    }

    async decryptContentOnEdit(event) {
        event.preventDefault();
        const response = await this.openDialog();
        if (response) {
            const decryptPassword = this.encryptDialog.querySelector('#page-encrypt-password').value;

            const data = {
                is_decrypt: 'FOR_EDIT',
                password: decryptPassword,
            };

            this.updateData(data, false, true, this.editBtn.getAttribute('href'));
        }
    }

    async updateData(data, updateEncryption, tmpDecrypt = false, link = this.url) {
        try {
            if (updateEncryption) {
                window.$http.put(`${this.url}/update-encryption`, data).then(resp => {
                    if (resp.data.success) {
                        window.location.reload();
                    }
                });
            } else {
                window.$http.put(`${this.url}/update-decryption`, data).then(resp => {
                    if (resp.data.success) {
                        if (tmpDecrypt) {
                            window.location.href = link;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        this.invalidMsg.value = resp.data.message;
                        this.invalidMsg.classList.remove('hidden');
                    }
                });
            }
        } catch (error) {
            console.error(error);
        }
    }

    async decryptForExport(event, element) {
        event.preventDefault();
        const link = element.getAttribute('href');
        const response = await this.openDialog();
        if (response) {
            const decryptPassword = this.encryptDialog.querySelector('#page-encrypt-password').value;

            window.$http.post(`${this.url}/validate-password`, {password: decryptPassword}).then(async resp => {
                if (resp.data.success) {
                    const data = {
                        is_decrypt: 'FOR_EXPORT',
                        password: decryptPassword,
                    };

                    this.updateData(data, false, true, link);
                } else {
                    this.invalidPassword.innerHTML = 'Invalid Password';
                    this.invalidPassword.classList.remove('hidden');
                    this.decryptForExport(event, element);
                }
            });
        }
    }

}
