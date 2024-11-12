<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100 shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center space-x-8">
                            <!-- Logo -->
                            <div class="flex-shrink-0">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="h-10 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:flex">
                                <!-- Dashboard with Home Icon -->
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center space-x-2 p-2 rounded-md transition ease-in-out duration-150 hover:bg-gray-100 hover:shadow-sm">
                                    <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h5a1 1 0 001-1V10" />
                                    </svg>
                                    <span>Dashboard</span>
                                </NavLink>

                                <!-- Uploads with Upload Icon -->
                                <NavLink :href="route('uploads.index')" :active="route().current('uploads.index')" class="flex items-center space-x-2 p-2 rounded-md transition ease-in-out duration-150 hover:bg-gray-100 hover:shadow-sm">
                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm16 0v6m-4 4H8m4-4v6" />
                                    </svg>
                                    <span>Uploads</span>
                                </NavLink>

                                <NavLink :href="route('your-photos')" :active="route().current('your-photos')">
                                    Your Photos
                                </NavLink>
                                
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out">
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Profile with Profile Icon -->
                                    <DropdownLink :href="route('profile.edit')" class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100">
                                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 01-8 0m8 4v6m-8 0v-6m4-6a4 4 0 100-8 4 4 0 000 8z" />
                                        </svg>
                                        <span>Profile</span>
                                    </DropdownLink>

                                    <!-- Log Out with Log Out Icon -->
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100">
                                        <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 15l-3-3m0 0l3-3m-3 3h8m-4 9a9 9 0 100-18 9 9 0 000 18z" />
                                        </svg>
                                        <span>Log Out</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Hamburger for Mobile -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            <svg class="h-5 w-5 inline-block mr-2 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001-1V10" />
                            </svg>
                            Dashboard
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
