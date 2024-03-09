<script setup>
import {computed, ref} from "vue";
import axios from "axios";
import ShippingItemModal from '@/components/ShippingItemModal.vue'
import CustomerDetails from '@/components/CustomerDetails.vue'
import Panel from 'primevue/panel';
import Calendar from 'primevue/calendar';
import { useToast } from 'vue-toastification'
import { PDFDocument } from 'pdf-lib';

const showModal = ref(false);
const shipping = ref({});
const errors = ref({});
const loading = ref(false);
const showResult = ref(false);
const toast = useToast()
const pdfData = ref(null);
const trackingNumber = ref(null);

const getInitialShippingData = () => {
       shipping.value = {
            plannedShippingDateAndTime: '',
            shipperDetails: {
                postalCode: null,
                cityName: '',
                addressLine1: '',
                email: '',
                phone: '',
                mobilePhone: '',
                companyName: '',
                fullName: ''
            },
            receiverDetails: {
                postalCode: null,
                cityName: '',
                addressLine1: '',
                email: '',
                phone: '',
                mobilePhone: '',
                companyName: '',
                fullName: ''
            },
            packages: []
        }
};

getInitialShippingData();

const today = computed(() => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), now.getDate() +1 );
});

const showShipingModal = () => {
    showModal.value = true;
}

const resetForm = (all) => {
    errors.value = {};
    getInitialShippingData();

    if (all) {
        showResult.value = false;
    }
}

const removeItem = (i) => {
    shipping.value.packages.splice(i, 1);
}

const canAddPackage = computed(() => shipping.value.packages.length < 3);

// to prevent changing date time in request
const dateTransformer = (data) => {
    if (data instanceof Date) {
        return data.toLocaleString();
    }
    if (Array.isArray(data)) {
        return data.map((val) => dateTransformer(val));
    }
    if (typeof data === "object" && data !== null) {
        return Object.fromEntries(
            Object.entries(data).map(([key, val]) => [key, dateTransformer(val)])
        );
    }

    return data;
};

const createShipping = async () => {
    showResult.value = false;
    if (shipping.value.packages.length == 0) {
        toast.error("Please add at least one package");
        return
    }
    loading.value = true;

    // to prevent changing date time in request
    const customAxios = axios.create({
        transformRequest: [dateTransformer].concat(axios.defaults.transformRequest),
    });

    await customAxios.post('/api/dhl', shipping.value,).then((response => {
        errors.value = {};

        if (response.data.error) {
            throw new Error(response.data.message.detail);
        } else {
            pdfData.value = null;
            if(response.data.documents.length) {
                pdfData.value = response.data.documents;
                trackingNumber.value = response.data.shipmentTrackingNumber;
            }
            toast.success("New shipping created");
            resetForm(false);
            showResult.value = true;
        }
    })
    ).catch(error => {
        // backend form validation errors
        if (error.response && error.response.status == 422) {
            errors.value = error.response.data.errors;
        } else {
            toast.error(error.message);
        }
    }).finally(() => {
        loading.value = false;
    });
}

const addPageToDocument = async (base64String, pdfDoc) => {
    const pageDocument = await PDFDocument.load(base64String)
    const page = await pdfDoc.copyPages(pageDocument, [0]);
    pdfDoc.addPage(page[0]);
}

const fetchAndCreatePDF = async () => {
    const pdfDoc = await PDFDocument.create();

    for (const item of pdfData.value) {
        await addPageToDocument(item.content, pdfDoc);
    }

    const pdfBytes = await pdfDoc.save();
    downloadPDF(pdfBytes, 'document.pdf');
}

const downloadPDF = (pdfBytes, filename) => {
    const blob = new Blob([pdfBytes], { type: 'application/pdf' });
    const link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

</script>

<template>
    <div class="container mx-auto mb-3">

        <Panel header="New Shipping">
            <p class="m-0">
                <label for="shsipping-date" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Shipping Date</label>
                <Calendar placeholder="dd/mm/yyyy hh:mm" id="shsipping-date"
                          v-model="shipping.plannedShippingDateAndTime"
                          showTime
                          dateFormat="dd/mm/yy"
                          hourFormat="24"
                          showIcon
                          iconDisplay="input"
                          :minDate="today"
                          :pt="{

                    input: 'outline-none block text-gray-900 border-gray-300 p-1 text-sm',
                    monthtitle: 'text-sm p-1',
                    navigator: 'text-sm p-1',
                    header: 'text-sm p-0',
                    tableheadercell: 'text-sm p-0',
                    tablebody: 'text-sm p-0',
                    day: 'text-sm p-0 m-0',
                    panel: 'text-sm p-0',
                    timepicker: 'text-sm p-0 m-0',
                    hourPicker: 'text-sm p-0',
                    hour: 'text-sm p-0',
                    minutePicker: 'text-sm p-0',
                    minute: 'text-sm p-0',
                    separator: 'text-sm p-0 m-0',
                    timepickerbutton: 'text-sm p-0',
                  }"

                />

            </p>
            <p v-if="(errors && errors.plannedShippingDateAndTime)" class="mt-2 text-xs text-red-500">{{ errors.plannedShippingDateAndTime }}</p>
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-2 mt-5">
                <div class="w-full">
                    <CustomerDetails :errors="errors.shipperDetails" :formData="shipping.shipperDetails"/>
                </div>
                <div class="w-full">
                    <CustomerDetails :errors="errors.receiverDetails" :formData="shipping.receiverDetails"/>
                </div>
            </div>
        </Panel>

        <div class="w-full mt-2 block p-4 bg-white border border-gray-200 rounded-lg">

            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Packages</h5>

            <div class="relative overflow-x-auto">
                <table v-if="shipping.packages.length" class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Package name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Weight
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Dimentions <span class="lowercase">(l w h) cm</span>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Action</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(shipPackage, index) in shipping.packages" :key="index"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ shipPackage.description }}
                        </th>
                        <td class="px-6 py-4">
                            {{ shipPackage.weight }}
                        </td>
                        <td class="px-6 py-4">
                            {{ shipPackage.length }} x {{ shipPackage.width }} x {{ shipPackage.height }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p style="color: red; font-size: 24px;cursor: pointer;" @click="removeItem(index)">
                                &times;
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else class="w-full text-center font-medium text-red-500">Please Add Packages First</div>
            </div>
            <button v-if="canAddPackage" type="button"
                    class="w-50 mt-4 flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500
                                        focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                        focus-visible:outline-indigo-600 justify-center relative"
                    @click="showShipingModal">
                Add Package
            </button>
        </div>

        <div v-if="showResult" class="w-full mt-2 block p-4 bg-white border border-gray-200 rounded-lg">
            <h6 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Shipping Created</h6>
            <p class="text-lg font-medium text-gray-900 dark:text-white">
                <span class="font-semibold">Shipment Tracking Number: </span> {{ trackingNumber }}

                <button @click="fetchAndCreatePDF" type="button" class="px-3 py-2 text-xs
                font-medium text-center inline-flex items-center
                text-white bg-green-700 rounded-lg hover:bg-green-800
                focus:outline-none focus:ring-green-300">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                        <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                        <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                    </svg>
                    Download PDF
                </button>
            </p>
        </div>

        <div class="mt-4 flex gap-2 justify-center">
            <button :disabled="loading" type="button"
                    class="flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500
                                        focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                        focus-visible:outline-indigo-600 justify-center relative"
                    @click="resetForm(true)">

                Reset Shipping
            </button>

            <button :disabled="loading" @click="createShipping" type="button" class="flex rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500
                                        focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                        focus-visible:outline-indigo-600">
                <svg v-if="loading" aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                </svg>
                Create Shipping
            </button>
        </div>

        <ShippingItemModal v-model="showModal" :packages="shipping.packages" />

    </div>
</template>





