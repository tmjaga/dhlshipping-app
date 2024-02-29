<script setup>
import { computed, ref } from 'vue';
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue';
import { useVuelidate } from '@vuelidate/core';
import { required, integer, minValue, maxValue, helpers } from '@vuelidate/validators';

const props = defineProps({
    modelValue: Boolean,
    packages: Array
});

const form = ref({
    description: '',
    weight: '',
    length: '',
    width: '',
    height: ''
})
const emit = defineEmits(['update:modelValue']);

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const rules = {
    form: {
        description: {
            required,
            regex: helpers.withMessage('Only alphanumeric and spaces symbols allowed', helpers.regex(/^[a-zA-Z0-9\s]*$/))
        },
        weight: {
            required,
            integer,
            minValue: minValue(1),
            maxValue: maxValue(10)
        },
        length: {
            required,
            integer,
            minValue: minValue(1),
            maxValue: maxValue(10)
        },
        width: {
            required,
            integer,
            minValue: minValue(1),
            maxValue: maxValue(10)
        },
        height: {
            required,
            integer,
            minValue: minValue(1),
            maxValue: maxValue(10)
        }

    }
};

let v$ = useVuelidate(rules, {form});

const onSubmit = () => {
    v$.value.$validate();

    if (!v$.value.$error) {
        props.packages.push(form.value);
        closeModal();
    }
}

const closeModal = () => {
    form.value = {};
    v$.value.$reset();
    show.value = false;
}

</script>

<template>

    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <form @submit.prevent="onSubmit()" class="w-full max-w-lg">
                            <DialogTitle
                                as="h3"
                                class="font-bold text-lg font-medium leading-6 text-gray-900"
                            >
                                New Shipment Package
                            </DialogTitle>
                            <div class="mt-4">

                                    <div class="flex flex-wrap -mx-3 mb-4">
                                        <div class="w-full px-3">
                                            <label class="block tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
                                                Package Description <span class="font-bold text-red-500">*</span>:
                                            </label>
                                            <input v-model="form.description" id="description" type="text"
                                                   class="w-full outline-none block p-2 text-gray-900 border border-gray-300
                                                   rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                                            <p v-if="v$.form.description.$errors[0]" class="text-red-500 text-sm"> {{v$.form.description.$errors[0].$message}}</p>

                                        </div>
                                    </div>
                                    <div class="flex flex-wrap -mx-3 mb-4">
                                        <div class="w-full px-3">
                                            <label class="block tracking-wide text-gray-700 text-xs font-bold mb-2" for="weight">
                                                Package Weight (kg) <span class="font-bold text-red-500">*</span>:
                                            </label>
                                            <input v-model="form.weight" id="weight" type="text"
                                                   class="w-full outline-none block p-2 text-gray-900 border border-gray-300
                                                   rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                                            <p v-if="v$.form.weight.$errors[0]" class="text-red-500 text-sm"> {{v$.form.weight.$errors[0].$message}}</p>

                                        </div>
                                    </div>

                                    <p class="mb-3 text-gray-700 text-base font-medium">Package size</p>

                                    <div class="flex flex-wrap -mx-3 mb-2">
                                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                                            <label class="block tracking-wide text-gray-700 text-xs font-bold mb-2" for="length">
                                                Length (sm) <span class="font-bold text-red-500">*</span>:
                                            </label>
                                            <input v-model="form.length" id="length" type="text"
                                                   class="w-full outline-none block p-2 text-gray-900 border border-gray-300
                                                   rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                                            <label class="block tracking-wide text-gray-700 text-xs font-bold mb-2" for="width">
                                                Width (sm) <span class="font-bold text-red-500">*</span>:
                                            </label>
                                            <input v-model="form.width" id="width" type="text"
                                                   class="w-full outline-none block p-2 text-gray-900 border border-gray-300
                                                   rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">

                                        </div>
                                        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                                            <label class="block tracking-wide text-gray-700 text-xs font-bold mb-2" for="height">
                                                Height (sm) <span class="font-bold text-red-500">*</span>:
                                            </label>
                                            <input v-model="form.height" id="height" type="text"
                                                   class="w-full outline-none block p-2 text-gray-900 border border-gray-300
                                                   rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">

                                        </div>
                                        <div v-if="v$.$dirty" class="px-3 block w-full">
                                            <p class="text-red-500 text-sm" v-for="(error, index) in v$.$errors" :key="error.$uid">
                                                <span v-if="error.$property != 'description' && error.$property != 'weight'">
                                                    <span class="font-medium capitalize">{{error.$property}}: </span>
                                                    <span v-if="error.$validator == 'required' ">{{ error.$message }}</span>
                                                    <span v-if="error.$validator == 'integer' ">{{ error.$message }} </span>
                                                    <span v-if="error.$validator == 'minValue' ">{{ error.$message }}</span>
                                                    <span v-if="error.$validator == 'maxValue' ">{{ error.$message }}</span>
                                                    <span v-else></span>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                            </div>

                            <div class="mt-4 flex gap-2 justify-end">
                                <button
                                    type="button"
                                    class="flex rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500
                                        focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                        focus-visible:outline-indigo-600"
                                    @click="closeModal"
                                >
                                    Cancel
                                </button>

                                <button type="submit"
                                        data-tooltip-target="tooltip-light" data-tooltip-style="light"
                                        class="flex rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500
                                        focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                        focus-visible:outline-indigo-600"
                                >
                                    Add Item
                                </button>
                            </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
