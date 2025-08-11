<script setup>
import 'flowbite';
import { ref, onMounted, computed, onUnmounted, watch, onBeforeUnmount} from 'vue';
import { usePage, Head} from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite';
import { ElNotification } from 'element-plus';
import { Delete, Edit } from '@element-plus/icons-vue';
import NewLayout from '@/Layouts/NewLayout.vue';
import Pagination from '@/Pages/Gcash/Pagination.vue';  
import { ElMessage, ElMessageBox } from 'element-plus';
import { debounce } from 'lodash';

const { props } = usePage();
const addDialogVisible = ref(false);
const editDialogVisible = ref(false);
const message = ref('');

const form = ref({
    id: null,
    name: '',
    amount: '',
    transaction_type_id: '',
    charge_type_id: '',
    reference_number: '',
});

const errors = ref({
    amount: '',
    reference_number: '',
    name: '',
});
const search = ref('')
const transactions = ref({ data: [], links: {} });
const currentPage = ref(1);
const selectedTypes = ref([]) // ⬅️ for checkbox filters

const totalPages = ref(1)

const fetchTransactions = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get('https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/transactions', {
      params: {
        page,
        search: search.value,
        transaction_types: selectedTypes.value,
      }
    })

    // Data
    transactions.value = response.data
    currentPage.value = response.data.current_page
    totalPages.value = response.data.last_page

  } catch (error) {
    console.error("Error fetching transactions:", error)
  } finally {
    isLoading.value = false
  }
}

// Auto-refresh every 60 seconds
let refreshInterval = null

// Debounce only search
watch(search, debounce(() => {
    fetchTransactions(1)
}, 300))

// Instant re-fetch when checkbox filter changes
watch(selectedTypes, () => {
    fetchTransactions(1)
})

watch(currentPage, (newPage) => {
    fetchTransactions(newPage)
})

const submitAddForm = async () => {
    try {
        await axios.post('https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/add/transactions', form.value);
        ElNotification({
            title: 'Success',
            message: 'Successfully added!',
            type: 'success',
            duration: 3000,
        });
    } catch (error) {
        ElNotification({
            title: 'Error',
            message: 'Failed to add',
            type: 'error',
            duration: 3000,
        });

        if (error.response) {
            message.value = 'Error adding form: ' + (error.response.data.message || 'Unknown error');
        } else {
            message.value = 'Error adding form: ' + error.message;
        }
    } finally {
        resetForm();
        addDialogVisible.value = false; // Close add dialog
        await fetchTransactions(); // Refresh the transactions list
    }
};

const submitEditForm = async () => {
    try {
        await updateTransaction();
        ElNotification({
            title: 'Success',
            message: 'Successfully updated!',
            type: 'success',
            duration: 3000,
        });
    } catch (error) {
        ElNotification({
            title: 'Error',
            message: 'Failed to update',
            type: 'error',
            duration: 3000,
        });

        if (error.response) {
            message.value = 'Error updating form: ' + (error.response.data.message || 'Unknown error');
        } else {
            message.value = 'Error updating form: ' + error.message;
        }
    } finally {
        resetForm();
        editDialogVisible.value = false; // Close edit dialog
        await fetchTransactions(); // Refresh the transactions list
    }
};

const submitForm = async () => {
    if (form.value.id) {
        await submitEditForm();
    } else {
        await submitAddForm();
    }
};

const updateTransaction = async () => {
    if (form.value.id) {
        await axios.put(`https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/update/transactions/${form.value.id}`, form.value);
    }
    editDialogVisible.value = false; // Close edit dialog
    await fetchTransactions(); // Refresh the transactions list
};

const deleteTransaction = async (id) => {
    const confirmDelete = await ElMessageBox.confirm(
        'Are you sure you want to delete this transactions?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    ).catch(() => {
        ElMessage({
            type: 'info',
            message: 'Delete canceled',
        });
        return null; // Return null if canceled
    });

    if (!confirmDelete) return; // Exit if the user canceled

    isLoading.value = true;

    try {
        const response = await axios.delete(`https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/delete/transactions/${id}`);
        ElMessage({
            type: 'success',
            message: response.data.message,
        });
    } catch (error) {
        console.error(error);
        ElMessage({
            type: 'error',
            message: 'Something went wrong!',
        });
    } finally {
        isLoading.value = false;
    }

    await fetchTransactions();
}

const isLoading = ref(false);
const deleteAllTransactions = async () => {
    const confirmDelete = await ElMessageBox.confirm(
        'Are you sure you want to delete all transactions?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    ).catch(() => {
        ElMessage({
            type: 'info',
            message: 'Delete canceled',
        });
        return null; // Return null if canceled
    });

    if (!confirmDelete) return; // Exit if the user canceled

    isLoading.value = true;

    try {
        const response = await axios.delete('https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/delete/all/transactions');
        ElMessage({
            type: 'success',
            message: response.data.message,
        });
    } catch (error) {
        console.error(error);
        ElMessage({
            type: 'error',
            message: 'Something went wrong!',
        });
    } finally {
        isLoading.value = false;
    }

    await fetchTransactions();
}


const fetchTransactionForEdit = (transaction) => {
    form.value = {
        id: transaction.id,
        name: transaction.name,
        amount: transaction.amount,
        transaction_type_id: transaction.transaction_type_id,
        charge_type_id: transaction.charge_type_id,
        reference_number: transaction.reference_number,
    };
    editDialogVisible.value = true; // Open edit modal
};

const resetForm = () => {
    form.value = {
        id: null,
        name: '',
        amount: '',
        transaction_type_id: '',
        charge_type_id: '',
        reference_number: '',
    };
};

const checkAmount = () => {
    let value = form.value.amount;

    if (/^\d*$/.test(value)) {
        errors.value.amount = '';
    } else {
        errors.value.amount = 'Only Numbers!';
        value = value.replace(/[^0-9]/g, '');  // Removes invalid characters
        form.value.amount = value;
    }
};

const checkLettersOnly = () => {
    let value = form.value.name;

    if (/^[A-Za-z\s]*$/.test(value)) {
        errors.value.name = '';
    } else {
        errors.value.name = 'Only Letters!';
        value = value.replace(/[^A-Za-z\s]/g, '');  // Removes numbers/symbols
        form.value.name = value;
    }
};

const checkReference = () => {
    let value = form.value.reference_number;

    if (/^\d*$/.test(value)) {
        errors.value.reference_number = '';
    } else {
        errors.value.reference_number = 'Only Numbers!';
        value = value.replace(/[^0-9]/g, '');  // Removes invalid characters
        form.value.reference_number = value;
    }
};



const width = ref(window.innerWidth);
const dialogWidth = computed(() => {
    return window.innerWidth < 640 ? '100%' : '680px'; // Adjust width based on screen size
});
// Listen to window resize
const updateWidth = () => {
    width.value = window.innerWidth;
};

watch(addDialogVisible, (newValue) => {
    if (!newValue) {
        resetForm()
    }
});

watch(editDialogVisible, (newValue) => {
    if (!newValue) {
        resetForm()
    }
});

onMounted(() => {
    window.addEventListener('resize', updateWidth);
    fetchTransactions(); // existing code
    initFlowbite(); // existing code
});

onUnmounted(() => {
    window.removeEventListener('resize', updateWidth);
});


onMounted(() => {
    fetchTransactions(); // Load transactions on component mount
    initFlowbite();
});


</script>

<template>
<Head title="Transactions" />

    <NewLayout>
        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">


                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-center p-3">
                        <img class="w-24" src="https://logos-marques.com/wp-content/uploads/2023/05/GCash-Logo-thmb.png"
                            alt="GCash Logo" />
                    </div>
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                        <div class="w-full md:w-1/2">
                            <label for="search-input" class="sr-only">Search</label>
                            <input id="search-input" v-model="search" type="text" placeholder="Search by name, reference number"
                                class="border p-2 w-full mb-4" />
                        </div>
                        <div
                            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                            <button type="button" @click="addDialogVisible = true"
                                class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-2 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                </svg>
                                Add Transaction
                            </button>
                            <div class="flex items-center space-x-3 w-full md:w-auto">
                                <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown"
                                    class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    type="button">
                                    <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                    Actions
                                </button>
                                <div id="actionsDropdown"
                                    class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                        aria-labelledby="actionsDropdownButton">
                                        <li>
                                            <a href="#"
                                                class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Mass
                                                Edit</a>
                                        </li>
                                    </ul>
                                    <div class="py-1 px-2">
                                        <el-button @click="deleteAllTransactions" :disabled="isLoading">Delete
                                            all</el-button>
                                    </div>
                                </div>
                                <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                                    class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                        class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Filter
                                    <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                </button>
                                <div id="filterDropdown"
                                    class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Filter
                                    </h6>
                                    <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                        <li class="flex items-center">
                                            <input id="cash-in" type="checkbox" value="Cash In" v-model="selectedTypes"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                            <label for="cash-in"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Cash
                                                In</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input id="cash-out" type="checkbox" value="Cash Out" v-model="selectedTypes"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                            <label for="cash-out"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Cash
                                                Out</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input id="date" type="checkbox" value="Date"
                                                class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                            <label for="date"
                                                class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Date</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="isLoading" class="px-4 mb-4">
                        <div role="status">
                            <svg aria-hidden="true"
                                class="w-8 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor" />
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentFill" />
                            </svg>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto" v-if="transactions.data && transactions.data.length">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Name</th>
                                    <th scope="col" class="px-4 py-3">Amount</th>
                                    <th scope="col" class="px-4 py-3">Charge</th>
                                    <th scope="col" class="px-4 py-3">Reference Number</th>
                                    <th scope="col" class="px-4 py-3">Transaction</th>
                                    <th scope="col" class="px-9 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700" v-for="transaction in transactions.data"
                                    :key="transaction.id">
                                    <th scope="row"
                                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ transaction.name }}
                                    </th>
                                    <td class="px-4 py-3">{{ transaction.amount }}</td>
                                    <td class="px-4 py-3">{{ transaction.charge_type?.charges }}</td>
                                    <td class="px-4 py-3">{{ transaction.reference_number }}</td>
                                    <td class="px-4 py-3 max-w-[12rem] truncate">{{ transaction.transaction_type ?
                                        transaction.transaction_type.name : 'N/A' }}</td>
                                    <td class="px-4 py-3 flex items-center ">
                                        <el-button plain @click="fetchTransactionForEdit(transaction)" type="primary"
                                            :icon="Edit" />
                                        <el-button type="primary" @click="deleteTransaction(transaction.id)"
                                            :icon="Delete" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div v-else class="flex justify-center py-7">
                        No transactions found.
                    </div>

                </div>
                <Pagination class="flex justify-center py-6 gap-4 " :links="transactions" :current-page="currentPage"
                    :is-loading="isLoading" @change-page="currentPage = $event" />
            </div>

            <el-dialog v-model="addDialogVisible" :show="addDialogVisible" title="Add Transaction"
                class="text-normal font-bold" :width="dialogWidth" draggable>
                <section class=" bg-white dark:bg-gray-900 p-3 antialiased">
                    <div>
                        <div class="flex justify-center p-3">
                            <img class="w-24"
                                src="https://logos-marques.com/wp-content/uploads/2023/05/GCash-Logo-thmb.png"
                                alt="GCash Logo" />
                        </div>
                        <div class="overflow-x-auto">
                            <form @submit.prevent="submitForm">
                                <div class="grid gap-5  mb-6 md:grid-cols-2 sm:grid-cols-1">
                                    <div>
                                        <label for="Name"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                        <input type="text" id="Name" v-model="form.name" @input="checkLettersOnly"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required />
                                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                                    </div>
                                    <div>
                                        <label for="amount"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount</label>
                                        <input v-model="form.amount" @input="checkAmount" type="text" id="amount"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required />
                                        <p v-if="errors.amount" class="text-red-500 text-xs mt-1">{{ errors.amount }}
                                        </p>
                                    </div>
                                    <div>
                                        <label for="charge"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Charge</label>
                                        <select id="charge" v-model="form.charge_type_id"
                                            class="mx-auto py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option v-for="selection in props.charge_types" :key="selection.id"
                                                :value="selection.id">{{ selection.charges }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="reference"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference
                                            Number</label>
                                        <input v-model="form.reference_number" @input="checkReference" type="text"
                                            id="reference"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required />
                                        <p v-if="errors.reference_number" class="text-red-500 text-xs mt-1">{{
                                            errors.reference_number }}</p>
                                    </div>
                                    <div>
                                        <label for="transaction"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Transaction</label>
                                        <select id="transaction" v-model="form.transaction_type_id"
                                            class="mx-auto py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option v-for="selection in props.transaction_types" :key="selection.id"
                                                :value="selection.id">{{ selection.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end pe-4">
                                    <button type="submit"
                                        class=" text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm  sm:w-auto px-10 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </el-dialog>
            <el-dialog v-model="editDialogVisible" :show="editDialogVisible" title="Edit Transaction"
                class="text-normal font-bold" :width="dialogWidth" draggable>
                <section class=" bg-white dark:bg-gray-900 p-3  antialiased">
                    <div>
                        <div class="flex justify-center p-3">
                            <img class="w-24"
                                src="https://logos-marques.com/wp-content/uploads/2023/05/GCash-Logo-thmb.png"
                                alt="GCash Logo" />
                        </div>
                        <div class="overflow-x-auto">
                            <form @submit.prevent="submitForm">
                                <div class="grid gap-5 mb-6 md:grid-cols-2 sm:grid-cols-1">
                                    <div class="w-full">
                                        <label for="Name"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                        <input type="text" id="Name" v-model="form.name"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required />
                                    </div>
                                    <div class="w-full">
                                        <label for="amount"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount</label>
                                        <input v-model="form.amount" type="text" id="amount"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Amount" required />
                                    </div>
                                    <div class="w-full">
                                        <label for="charge"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Charge</label>
                                        <select id="charge" v-model="form.charge_type_id"
                                            class="mx-auto py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option v-for="selection in props.charge_types" :key="selection.id"
                                                :value="selection.id">{{ selection.charge }}</option>
                                        </select>
                                    </div>
                                    <div class="w-full">
                                        <label for="reference"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference
                                            Number</label>
                                        <input v-model="form.reference_number" type="text" id="reference"
                                            class="mx-auto bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Reference Number" required />
                                    </div>
                                    <div class="w-full">
                                        <label for="transaction"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Transaction</label>
                                        <select id="transaction" v-model="form.transaction_type_id"
                                            class="mx-auto py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option>Select Transaction</option>
                                            <option v-for="selection in props.transaction_types" :key="selection.id"
                                                :value="selection.id">{{ selection.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-64 sm:w-auto px-10 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </el-dialog>
            
        </section>

        
    </NewLayout>

</template>