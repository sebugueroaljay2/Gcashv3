<script setup>
import 'flowbite';
import { ref, reactive } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const {props} = usePage();

// const form = reactive({
//   email: '',
//   password: ''
// })

// const error = ref('')

// const resetForm = ref({
//     email: '',
//     password: '',
// }) 

const email = ref('')
const password = ref('')
const error = ref('')

const login = async () => {
  error.value = ''
  try {
    await axios.get('/sanctum/csrf-cookie') // importante
    await axios.post('/api/login', {
      email: email.value,
      password: password.value
    })
    router.visit('/dashboard') // redirect pag success
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  }
}

// const isLoading = ref(false);
// const submitLogin = async () => {
//     // console.error(props.errors)
//   try {
//     // await axios.get('/sanctum/csrf-cookie') // very important!
//     await axios.post('/cus-login', form.value)
// isLoading.value = true;
//     // resetForm();
//     // redirect to home page
//     router.visit('/dashboard')
//   } catch (err) {
//     error.value = err.response?.data?.message || 'Login failed.'
//   }
// }

// async function loginUser(form) {
//     try {
//         const response = await axios.post('/cus-login', form.value);
//         console.log('Login successful:', response.data);
//         // Handle successful login, e.g., redirect or show a success message
//     } catch (error) {
//         console.error('Login failed:', error.response ? error.response.data : error.message);
//         // Handle error, e.g., show an error message to the user
//     }
// }
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img src="/gcash.png" class="h-12 mr-2" alt=" Logo">
                Gcash
            </a>
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="flex justify-center text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Login
                    </h1>
                    <form @submit.prevent="login" class="space-y-4 md:space-y-6" action="#">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                                email</label>
                            <input v-model="email" type="email" name="email" id="email"
                                class="transform hover:translate-y-1 transition-transform ease-in duration-200 shadow-xl bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="name@company.com" required="">
                        </div>
                        <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input  v-model="password" type="password" name="password" id="password" placeholder="••••••••"
                                class="transform hover:translate-y-1 transition-transform ease-in duration-200 shadow-xl bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required="">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="remember" aria-describedby="remember" type="checkbox"
                                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800"
                                       >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                                </div>
                            </div>
                            <a href="#"
                                class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot
                                password?</a>
                        </div>
                        <button type="submit" 
                            class="w-full text-white border transform hover:translate-y-1 transition-transform ease-in duration-200 shadow-xl  bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                          Sign in</button>
                          <div v-if="error">{{ error }}</div>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Don’t have an account yet? <a href="#"
                                class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                        </p>


                    </form>
                    <div class="border"></div>

                    <div class="flex justify-center">
                        <a href="/auth/google/redirect"
                            class="flex justify-center border w-48 py-2 rounded-md transform hover:translate-y-1 transition-transform ease-in duration-200 shadow-xl bg-red-500 text-white hover:bg-red-600">
                        Google
                        Sign In</a>
                    </div>

                </div>

            </div>
        </div>
    </section>
</template>