import {Component} from './component';

export class EncryptDecryptManager extends Component 
{
    setup()
    {
        this.container = this.$el;
        this.encryptBtn = this.$refs.encryptBtn;
        this.decryptSubmitBtn = this.$refs.decryptSubmitBtn;
        this.decryptPassword = this.$refs.decryptPasswordInput;
        this.editBtn = this.$refs.editBtn;
        this.invalidMsg = this.$refs.invalidPassword;

        this.url = this.$opts.url;
        this.pageName = this.$opts.pageName;
        this.pageIsEncrypted = this.$opts.pageEncrypted;

        this.encryptDialog = document.querySelector('.encrypt-decrypt-dialog');
        this.contentPart = document.querySelector('.page-content');
        this.pageContentPTags = document.querySelector('.page-content').querySelectorAll('p');
        this.pageContent = document.querySelector('.page-content').querySelector('div[dir="auto"]');
        this.pageDetailContent = document.querySelector('.page-content').querySelector('.page-detail-content');
        this.encryptIcon = document.querySelector('.page-content').querySelector('.encrypt-icon')

        this.invalidPassword = this.encryptDialog.querySelector('.invalid-password');
        this.passwordInput = this.encryptDialog.querySelector('#page-encrypt-password');

        this.is_decrypt = false;

        this.setupListener();
    }

    setupListener()
    {   
        this.encryptBtn.addEventListener("click",async event => {
            event.preventDefault();
            const response = await this.openDialog();
            if(response)
            {
                if(this.pageIsEncrypted == 1)
                {
                    this.decryptContent(this.passwordInput.value,true,false);
                }
                else
                {
                    this.encryptContent();
                }
            }
        });
        
        if(this.pageIsEncrypted == 1)
        {
            this.contentPart.addEventListener("mousedown", event => event.preventDefault());
            this.contentPart.addEventListener("selectstart", event => event.preventDefault());
            this.contentPart.addEventListener("copy", event => event.preventDefault());
            this.decryptPassword.addEventListener("input",()=>{this.invalidMsg.classList.add('hidden')});
            this.decryptSubmitBtn.addEventListener("click",()=>this.decryptContent(this.decryptPassword.value));
            this.editBtn.addEventListener("click",event=>this.decryptContentOnEdit(event));
            this.decryptPassword.addEventListener("keydown",event => {
                if(event.key == "Enter")
                {
                    this.decryptContent(this.decryptPassword.value);
                }
            });
            this.passwordInput.addEventListener("input",()=>{
                this.invalidPassword.classList.add("hidden");
            })
        }
    }

    encryptContent()
    {
        const password = this.passwordInput.value;
        if(password.length > 6)
        {
            const contents = this.pageDetailContent.innerHTML;

            window.$http.post(`${this.url}/encrypt`, {content: contents}).then(resp=>{
            if(resp.data.success)
            {
                // let contents = this.changePageContent(resp.data.contents);
                this.pageDetailContent.innerHTML = resp.data.content;
                
                const data = {
                    'html' : resp.data.content,
                    'is_encrypted' : true,
                    'decrypt_password' : password,
                }
                this.updateData(data,true,false);
            }
            });
        }
    }

    decryptContent(decryptPassword,updateDecryption = false,forEdit = false)
    {
        if(decryptPassword.length > 6)
        {

            const decryptData = {
                content: this.pageDetailContent.innerHTML,
                decrypt_password: decryptPassword,
            }
            if(!this.is_decrypt)
            {
                window.$http.post(`${this.url}/decrypt`, decryptData).then(async resp=>{
                    if(resp.data.success)
                    {
                        // let contents = this.changePageContent(resp.data.contents);
                        this.decryptPassword.value = "";
                        this.pageDetailContent.innerHTML = resp.data.content;
    
                        if(updateDecryption)
                        {
                            const data = {
                                'html' : resp.data.content,
                                'is_encrypted' : forEdit,
                                'is_decrypt' : forEdit,
                                'decrypt_password' : decryptPassword,
                            };
                            this.updateData(data,false,forEdit);
                        }
                        else
                        {
                            this.is_decrypt = true;
                            this.encryptIcon.classList.add("hidden");
                            this.pageDetailContent.classList.remove("hidden");
                        }
                    }
                    else
                    {
                        if(forEdit || updateDecryption)
                        {
                            this.invalidPassword.innerHTML = resp.data.message;
                            this.invalidPassword.classList.remove("hidden");
                            this.openDialog();
                        }
                        else
                        {
                            this.invalidMsg.innerHTML = resp.data.message;
                            this.invalidMsg.classList.remove("hidden");
                        }
                    }
                });
            }
            else
            {
                const data = {
                    'html' : this.pageDetailContent.innerHTML,
                    'is_encrypted' : forEdit,
                    'is_decrypt' : forEdit,
                    'decrypt_password' : decryptPassword,
                };
                this.updateData(data,false,forEdit);
            }
            
        }
    }

    async openDialog()
    {
        this.encryptDialog.querySelector('#page-encrypt-password').value = '';
        const dialog = window.$components.firstOnElement(this.encryptDialog, 'confirm-dialog');
        const response = await dialog.show();
        return response;
    }

    async decryptContentOnEdit(event)
    {
        event.preventDefault();
        const response = await this.openDialog();
        if(response)
        {
            this.decryptContent(this.encryptDialog.querySelector('#page-encrypt-password').value,true,true);
        }
    }

    getPageContent()
    {
        const contents = [];

        this.pageContentPTags.forEach(element => {
            contents.push(element.innerHTML);
        });
        return contents;
    }

    changePageContent(contents)
    {
        this.pageContentPTags.forEach((element,index) => {
            element.innerHTML = contents[index];
            contents[index] = element.outerHTML;
        });
        return contents;
    }

    async updateData(data,updateEncryption,forEdit)
    {
        try{
            if(updateEncryption)
            {
                window.$http.put(`${this.url}/update-encryption`,data).then(resp=>
                {
                    if(resp.data.success){
                        window.location.reload();   
                    }
                });
            }
            else
            {
                window.$http.put(`${this.url}/update-decryption`,data).then(resp=>
                {
                    if(resp.data.success){
                        if(forEdit)
                        {
                            window.location.href = this.editBtn.getAttribute('href');
                        }
                        else
                        {
                            window.location.reload();
                        }
                    }
                    else
                    {
                        this.invalidMsg.value = resp.data.message;
                        this.invalidMsg.classList.remove('hidden');
                    }
                });
            }
            
        }
        catch(error)
        {
            console.log(error);
        }
    }
}