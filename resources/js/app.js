import './bootstrap';

import Alpine from 'alpinejs';
import {browserSupportsWebAuthn, startAuthentication, startRegistration} from "@simplewebauthn/browser";
import axios from "axios";

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('registerPasskey', () => ({
        name: '',
        errors: null,
        browserSupportsWebAuthn,
        async register(form) {
            console.log('name', this.name)
            this.errors = null;

            if (!this.browserSupportsWebAuthn()) {
                return;
            }

            const options = await axios.get('/api/passkeys/register', {
                params: {name: this.name, age: 32},
                validateStatus: (status) => [200, 422].includes(status)
            });

            if (options.status === 422) {
                this.errors = options.data.errors
                return;
            }

            try {
                var passkey = await startRegistration(options.data)
            } catch (e) {
                console.log(e)
                this.errors = {name: [e]}
            }

            form.addEventListener('formdata', ({formData}) => {
                formData.set('passkey', JSON.stringify(passkey))
            })

            form.submit()
        }
    }))

    Alpine.data('authenticatePasskey', () => ({
        async authenticate() {
            const options = await axios.get('/api/passkeys/authenticate')
            console.log(options.data)
            const answer = await startAuthentication(options.data)
        }

    }))
})
Alpine.start();
