<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { ref } from 'vue';
import { register } from '@/service/Auth.js';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const toast = useToast();
const router = useRouter();

// Form fields
const name = ref('');
const email = ref('');
const password = ref('');
const confirm_password = ref('');
const errors = ref({});

// Validation function
function validate() {
    if (!name.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Name is required', life: 3000 });
        return false;
    }

    if (!email.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Email is required', life: 3000 });
        return false;
    }

    if (!password.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Password is required', life: 3000 });
        return false;
    }

    if (password.value !== confirm_password.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Passwords do not match', life: 3000 });
        return false;
    }

    return true;
}

async function handleRegister() {
    if (!validate()) return;

    try {
        await register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: confirm_password.value
        });

        toast.add({ severity: 'success', summary: 'Success', detail: 'Registered successfully!', life: 2000 });

        // Redirect after 1 sec
        setTimeout(() => {
            router.push({ name: 'dashboard' });
        }, 1000);

    } catch (e) {
        if (e.response && e.response.status === 422) {
            errors.value = e.response.data.errors;
        } else {
            console.error("Unexpected error:", e);
        }
    }
}
</script>

<template>
    <Toast />
    <FloatingConfigurator />

    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">

                    <div class="text-center mb-8">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Create Your Account</div>
                    </div>

                    <div>
                        <label for="name" class="block text-xl mb-2">Name</label>
                        <InputText id="name" type="text" class="w-full md:w-[30rem] mb-4" v-model="name" />

                        <label for="email" class="block text-xl mb-2">Email</label>
                        <InputText id="email" type="email" class="w-full md:w-[30rem] mb-4" v-model="email" />

                        <label for="password" class="block text-xl mb-2">Password</label>
                        <Password id="password" v-model="password" class="w-full md:w-[30rem] mb-4" :toggleMask="true" fluid />

                        <label for="confirm_password" class="block text-xl mb-2">Confirm Password</label>
                        <Password id="confirm_password" v-model="confirm_password" class="w-full md:w-[30rem] mb-6" :toggleMask="true" fluid />

                        <Button label="Register" class="w-full md:w-[30rem]" @click="handleRegister" />
                    </div>

                    <div class="text-center mt-4">
                        <span>If you already have an account,</span>
                        <router-link :to="{ name: 'login' }" class="text-primary font-semibold ml-1">Login</router-link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye, .pi-eye-slash { transform: scale(1.6); margin-right: 1rem; }
</style>
