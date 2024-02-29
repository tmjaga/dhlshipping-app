<script setup>
import {ref} from "vue";
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';

const props = defineProps({
    formData: Object,
    errors: Object
});

const cities = ref([
    { name: 'Sofia', value: 1000 },
    { name: 'Burgas', value: 8000 },
    { name: 'Plovdiv', value: 4000 },
    { name: 'Stara Zagora', value: 6000},
    { name: 'Varna', value: 9002 }
]);

const updateCity = () => {
    const selected = cities.value.find(option => option.value === props.formData.postalCode);
    props.formData.cityName = selected ? selected.name : '';
};

</script>

<template>

    <div class="mb-2 text-sm">
        <label for="fullName" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Full Name <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="fullName" v-model="formData.fullName" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.fullName)" class="mt-2 text-xs text-red-500">{{ props.errors.fullName }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="companyName" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Company Name <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="companyName" v-model="formData.companyName" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.companyName)" class="mt-2 text-xs text-red-500">{{ props.errors.companyName }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="postalCode" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            City <span class="font-bold text-red-500">*</span>:
        </label>
        <Dropdown id="postalCode" @change="updateCity" :pt="{
                root: 'shadow-sm outline-none p-0',
                input: 'shadow-none outline-none p-1 text-sm',
                list: 'p-1 text-sm',
            }"
                 v-model="formData.postalCode" :options="cities" optionLabel="name" optionValue="value" placeholder="Select a City"
        />
        <p v-if="(props.errors && props.errors.postalCode)" class="mt-2 text-xs text-red-500">{{ props.errors.postalCode }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="addressLine1" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Address <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="addressLine1" v-model="formData.addressLine1" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.addressLine1)" class="mt-2 text-xs text-red-500">{{ props.errors.addressLine1 }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="email" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Email <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="email" v-model="formData.email" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.email)" class="mt-2 text-xs text-red-500">{{ props.errors.email }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="phone" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Phone <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="phone" v-model="formData.phone" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.phone)" class="mt-2 text-xs text-red-500">{{ props.errors.phone }}</p>
    </div>

    <div class="mb-2 text-sm">
        <label for="mobilePhone" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Mobile Phone <span class="font-bold text-red-500">*</span>:
        </label>
        <InputText id="mobilePhone" v-model="formData.mobilePhone" :pt="{
                    root: 'w-full outline-none block p-1 text-gray-900 border-gray-300 text-sm'
        }" />
        <p v-if="(props.errors && props.errors.mobilePhone)" class="mt-2 text-xs text-red-500">{{ props.errors.mobilePhone }}</p>
    </div>

</template>
