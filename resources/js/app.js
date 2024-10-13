import './bootstrap';

import Alpine from 'alpinejs';
import {startRegistration} from "@simplewebauthn/browser";

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('registerPasskey', () => ({
        async register(form) {
            console.log(form)
            const options= await axios.get('/api/passkeys/register');
            const passkey = await startRegistration(options.data)
            console.log(passkey)

            form.addEventListener('formdata', ({formData}) => {
                formData.set('passkey', JSON.stringify(passkey))
            })

            form.submit()
        }
    }))
})
Alpine.start();
