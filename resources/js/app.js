import './bootstrap';

import Alpine from 'alpinejs';
import {startRegistration} from "@simplewebauthn/browser";

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('registerPasskey', () => ({
        async register() {
            const options= await axios.get('/api/passkeys/register');
            const passkey = await startRegistration(options.data)
            console.log(options.data)
        }
    }))
})
Alpine.start();
