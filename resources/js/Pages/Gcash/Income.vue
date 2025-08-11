<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import NewLayout from '@/Layouts/NewLayout.vue';  
import { Head } from '@inertiajs/vue3';

const dailyIncome = ref(0);
const weeklyIncome = ref(0);
const monthlyIncome = ref(0);

const isLoading = ref(false);

const cashinCount = ref(0)
const cashoutCount = ref(0)
const today = ref(new Date().toISOString().slice(0, 10))

const fetchCounts = async () => {
    try {
        const res = await axios.get('https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/daily-transaction-count')
        cashinCount.value = res.data.cashin_count
        cashoutCount.value = res.data.cashout_count
        today.value = res.data.date
    } catch (error) {
        console.error('Failed to fetch daily counts:', error)
    }
}

const checkDateChange = () => {
    setInterval(() => {
        const currentDate = new Date().toISOString().slice(0, 10)
        if (currentDate !== today.value) {
            fetchCounts()
        }
    }, 60000) // check every 1 minute
}

onMounted(() => {
    fetchCounts()
    checkDateChange()
})

const fetchIncomeStats = async () => {
    
    try {
        const response = await axios.get('https://tame-jacynth-aljaydev-53c82294.koyeb.app/api/income/income') // Or a custom route if needed

        dailyIncome.value = response.data.daily_income
        weeklyIncome.value = response.data.weekly_income
        monthlyIncome.value = response.data.monthly_income
    } catch (error) {
        console.error("Error fetching income stats:", error)
    } finally {
        isLoading.value = false
    }
}

// Auto-refresh every 60 seconds
let refreshInterval = null

onMounted(() => {
    fetchIncomeStats()

    refreshInterval = setInterval(() => {
        fetchIncomeStats()
    }, 60 * 1000)
})

onBeforeUnmount(() => {
    clearInterval(refreshInterval)
})
</script>

<template>
    <Head title="Dashboard" />
    <NewLayout>
        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 pt-8">
            <span class="text-2xl">Income</span>

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

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 my-4 pt-8">
                <div class="p-5 bg-white shadow rounded text-center transform hover:translate-y-1 transition-transform">
                    <p class="text-gray-600 text-sm">Today</p>
                    <p class="text-xl font-bold text-green-600">₱{{ dailyIncome }}</p>
                </div>
                <div class="p-5 bg-white shadow rounded text-center transform hover:translate-y-1 transition-transform">
                    <p class="text-gray-600 text-sm">This Week</p>
                    <p class="text-xl font-bold text-blue-600">₱{{ weeklyIncome }}</p>
                </div>
                <div class="p-5 bg-white shadow rounded text-center transform hover:translate-y-1 transition-transform">
                    <p class="text-gray-600 text-sm">This Month</p>
                    <p class="text-xl font-bold text-purple-600">₱{{ monthlyIncome }}</p>
                </div>
            </div>

            <div class="pt-8">
                <span class="text-2xl">Transaction</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-4 pt-8">
                    <div class="p-5 bg-white shadow rounded text-center transform hover:translate-y-1 transition-transform">
                        <p class="text-gray-600 text-sm">Cash In</p>
                        <p class="text-xl font-bold text-green-600">{{ cashinCount }}</p>
                    </div>
                    <div class="p-5 bg-white shadow rounded text-center transform hover:translate-y-1 transition-transform">
                        <p class="text-gray-600 text-sm">Cash Out</p>
                        <p class="text-xl font-bold text-blue-600">{{ cashoutCount }}</p>
                    </div>
                </div>
            </div>
        </section>
    </NewLayout>
</template>