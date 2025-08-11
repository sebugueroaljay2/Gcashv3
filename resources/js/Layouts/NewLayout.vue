<script setup>
import { onMounted, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import 'flowbite';

const logout = () => {
    router.post(route("logout"));
    router.visit('/login');
}

const toggleSidebarMobile = (sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose) => {
    sidebar.classList.toggle('hidden');
    sidebarBackdrop.classList.toggle('hidden');
    toggleSidebarMobileHamburger.classList.toggle('hidden');
    toggleSidebarMobileClose.classList.toggle('hidden');
};

onMounted(() => {
    const sidebar = document.getElementById('sidebar');
    const toggleSidebarMobileEl = document.getElementById('toggleSidebarMobile');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const toggleSidebarMobileHamburger = document.getElementById('toggleSidebarMobileHamburger');
    const toggleSidebarMobileClose = document.getElementById('toggleSidebarMobileClose');

    if (
        sidebar &&
        toggleSidebarMobileEl &&
        sidebarBackdrop &&
        toggleSidebarMobileHamburger &&
        toggleSidebarMobileClose
    ) {
        toggleSidebarMobileEl.addEventListener('click', () => {
            toggleSidebarMobile(sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose);
        });

        sidebarBackdrop.addEventListener('click', () => {
            toggleSidebarMobile(sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose);
        });
    } else {
        console.error('One or more elements not found in the DOM');
    }

   

    
});
</script>

<template>

    <div>
        <nav class="bg-white border-b border-gray-200 fixed z-30 w-full">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                            class="lg:hidden mr-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded">
                            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg id="toggleSidebarMobileClose" class="w-6 h-6 hidden" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <a href="#" class="text-xl font-bold flex items-center lg:ml-2.5">
                            <img src="https://images.seeklogo.com/logo-png/52/1/gcash-logo-png_seeklogo-522261.png"
                                class="h-6 mr-2" alt="Windster Logo">
                            <span class="self-center whitespace-nowrap">GPO</span>
                        </a>

                    </div>
                </div>
            </div>
        </nav>
        <div class="flex overflow-hidden bg-white pt-16">
            <aside id="sidebar"
                class="fixed hidden z-20 h-full top-0 left-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75"
                aria-label="Sidebar">
                <div class="min-h-screen flex flex-row ">
                    <div class="flex flex-col w-56 bg-white rounded-r-3xl overflow-hidden">
                        <!-- <div class="flex items-center justify-center h-20 py-10 shadow-md">
      
                            
    </div> -->
                        <ul class="flex flex-col py-4">
                            <li>
                                <Link :href="route('gcash.dashboard')"
                                    class="flex flex-row items-center h-12 transform hover:translate-x-2 transition-transform ease-in duration-200 text-gray-500 hover:text-gray-800">
                                <span class="inline-flex items-center justify-center h-12 w-12 text-lg text-gray-400"><i
                                        class="bx bx-home"></i></span>
                                <span class="text-sm font-medium">Dashboard</span>
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('gcash.transactions')"
                                    class="flex flex-row items-center h-12 transform hover:translate-x-2 transition-transform ease-in duration-200 text-gray-500 hover:text-gray-800">
                                <span class="inline-flex items-center justify-center h-12 w-12 text-lg text-gray-400"><i
                                        class="bx bx-receipt"></i></span>
                                <span class="text-sm font-medium">Gcash transactions</span>
                                </Link>
                            </li>
                            <li>
                                <a href="#"
                                    class="flex flex-row items-center h-12 transform hover:translate-x-2 transition-transform ease-in duration-200 text-gray-500 hover:text-gray-800">
                                    <span
                                        class="inline-flex items-center justify-center h-12 w-12 text-lg text-gray-400"><i
                                            class="bx bx-user"></i></span>
                                    <span class="text-sm font-medium">Profile</span>
                                </a>
                            </li>
                            <li>
                                <button @click="logout"
                                    class="flex flex-row items-center h-12 transform hover:translate-x-2 transition-transform ease-in duration-200 text-gray-500 hover:text-gray-800">
                                    <span
                                        class="inline-flex items-center justify-center h-12 w-12 text-lg text-gray-400"><i
                                            class="bx bx-log-out"></i></span>
                                    <span class="text-sm font-medium">Logout</span>
                                </button>
                            </li>
                        </ul>

                    </div>

                </div>

            </aside>

            <div class="bg-gray-900 opacity-50 hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>
            <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
                <main>
                    <slot />
                </main>

                <!-- <div class="text-center text-sm text-gray-500  h-screen">
            &copy; 2025 <a href="#" class="hover:underline" target="_blank">ARS</a>. All rights
            reserved.
      </div> -->
                <div class="text-center text-sm text-gray-500  py-2 bg-white">
                    &copy; 2025 <a href="#" class="hover:underline" target="_blank">ARS</a>. All rights
                    reserved.
                </div>
            </div>

        </div>

        <!-- <script async defer src="https://buttons.github.io/buttons.js"></script>
        <script src="https://demo.themesberg.com/windster/app.bundle.js"></script> -->
    </div>
</template>