<script setup>
import {computed, inject, onMounted, ref, watch} from "vue";
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import axios from 'axios'
import {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    formData: Object,
    errors: Object,
    geoCountries: {
        type: Array,
        default: []
    }
});

/*
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
*/

const selectedCountry = ref(null);
const selectedCity = ref(null);
const geoSities = ref([]);
const query = ref('')
const cityQuery = ref('')
const makeResetForm = inject('makeResetForm');



const  loadCities = async () => {
    props.formData.countryCode = selectedCountry.value.countryCode
    props.formData.countryName = selectedCountry.value.countryName

    try {
        const response = await axios.get('/api/geocities', {
            params: {
                country: selectedCountry.value.countryCode
            }
        });
        geoSities.value = response.data.geonames;
    } catch (error) {
        console.error('Error loading options:', error)
    }
};

const loadCityPostalCode = async () => {
    props.formData.cityName = selectedCity.value.name

    try {
        const response = await axios.get('/api/geocode', {
            params: {
                country: selectedCountry.value.countryCode,
                place: encodeURI(selectedCity.value.name)
            }
        });
        props.formData.postalCode = response.data.postalCodes[0]?.postalCode
    } catch (error) {
        console.error('Error loading options:', error);
    }
}

const filteredCountries = computed(() =>
    query.value === '' ? props.geoCountries : props.geoCountries.filter((country) => {
        return country.countryName.toLowerCase().includes(query.value.toLowerCase())
    })
)

const filteredCities = computed(() =>
    cityQuery.value === '' ? geoSities.value : geoSities.value.filter((city) => {
        return city.name.toLowerCase().includes(cityQuery.value.toLowerCase())
    })
)

watch(selectedCountry, () => {
    selectedCity.value = null;
})

watch(makeResetForm, (val) => {
    selectedCountry.value = val ? null : selectedCountry.value;
})

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

    <!--
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
    -->

    <!-- Countries -->
    <div class="mb-2">
        <label for="new_country" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            Country <span class="font-bold text-red-500">*</span>:
        </label>
        <Combobox
            id="new_country"
            v-model="selectedCountry"
            @update:modelValue="loadCities"
            class="z-20">
            <div class="relative mt-1">
                <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white
                    text-left shadow-sm
                    focus-visible:ring-2
                    focus-visible:ring-red/75
                    focus-visible:ring-offset-2
                    focus-visible:ring-offset-teal-300
                    sm:text-sm"
                >
                    <ComboboxInput
                        class="z-20 w-full outline-none block p-1 text-gray-900 border border-gray-300
                        rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                        :displayValue="(country) => country?.countryName"
                        @change="query = $event.target.value"
                        placeholder="Select Country"
                    />
                    <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </ComboboxButton>
                </div>
                <TransitionRoot
                    leave="transition ease-in duration-100" leaveFrom="opacity-50" leaveTo="opacity-0" @after-leave="query = ''">
                    <ComboboxOptions
                        class="absolute mt-1 max-h-60 w-full
                        overflow-auto rounded-md bg-white py-1
                        text-base ring-1
                        ring-black/5 focus:outline-none sm:text-sm"
                    >
                        <div
                            v-if="filteredCountries.length === 0 && query !== ''"
                            class="relative cursor-default select-none px-4 py-2 text-gray-700"
                        >
                            Nothing found.
                        </div>

                        <ComboboxOption
                            v-for="country in filteredCountries"
                            as="template"
                            :key="country.geonameId"
                            :value="country"
                            v-slot="{ selected, active }"
                        >
                            <li
                                class="relative cursor-default select-none py-2 pl-10 pr-4"
                                :class="{
                                  'bg-blue-500 text-white': active,
                                  'text-gray-900': !active,
                                }"
                            >
                                <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                  {{ country.countryName }}
                                </span>
                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                    :class="{ 'text-white': active, 'text-blue-600': !active }"
                                >
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
        <p v-if="(props.errors && props.errors.countryName)" class="mt-2 text-xs text-red-500">{{ props.errors.countryName }}</p>
    </div>

    <!-- Cities -->
    <div class="mb-2">
        <label for="new_city" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">
            City <span class="font-bold text-red-500">*</span>:
        </label>

        <Combobox
            id="new_city"
            v-model="selectedCity"
            class="z-10"
            :disabled="!selectedCountry"
            @update:modelValue="loadCityPostalCode"
        >
            <div class="relative mt-1">
                <div class="relative w-full cursor-default overflow-hidden rounded-lg bg-white
                    text-left shadow-sm
                    focus-visible:ring-2
                    focus-visible:ring-red/75
                    focus-visible:ring-offset-2
                    focus-visible:ring-offset-teal-300
                    sm:text-sm"
                >
                    <ComboboxInput
                        class="z-20 w-full outline-none block p-1 text-gray-900 border border-gray-300
                        rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                        :displayValue="(city) => city?.name"
                        @change="cityQuery = $event.target.value"
                        placeholder="Select City"
                    />
                    <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </ComboboxButton>
                </div>
                <TransitionRoot
                    leave="transition ease-in duration-100" leaveFrom="opacity-50" leaveTo="opacity-0" @after-leave="cityQuery = ''">
                    <ComboboxOptions
                        class="absolute mt-1 max-h-60 w-full
                        overflow-auto rounded-md bg-white py-1
                        text-base ring-1
                        ring-black/5 focus:outline-none sm:text-sm"
                    >
                        <div
                            v-if="filteredCities.length === 0 && cityQuery !== ''"
                            class="relative cursor-default select-none px-4 py-2 text-gray-700"
                        >
                            Nothing found.
                        </div>

                        <ComboboxOption
                            v-for="city in filteredCities"
                            as="template"
                            :key="city.geonameId"
                            :value="city"
                            v-slot="{ selected, active }"
                        >
                            <li
                                class="relative cursor-default select-none py-2 pl-10 pr-4"
                                :class="{
                                  'bg-blue-500 text-white': active,
                                  'text-gray-900': !active,
                                }"
                            >
                                <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                  {{ city.name }}
                                </span>
                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                    :class="{ 'text-white': active, 'text-blue-600': !active }"
                                >
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
        <p v-if="(props.errors && props.errors.cityName)" class="mt-2 text-xs text-red-500">{{ props.errors.cityName }}</p>
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
