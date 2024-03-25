import {ref} from 'vue'

export default function useRank() {

    const rank = ref([])
    const isLoading = ref(false)

    const getRank = async (id) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/rank/${id}`)
            .then(response => {
                rank.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        rank,
        getRank,
        isLoading
    }

}
