import {ref} from "vue";

export default function useMemorial() {

    const memorial = ref({})
    const memorials = ref([])
    const isLoading = ref(false)

    const getMemorial = async (slug) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/memorial/${slug}`)
            .then(response => {
                memorial.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    const getMemorials = async () => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get('/api/v1/memorials')
            .then(response => {
                memorials.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        memorial,
        memorials,
        getMemorial,
        getMemorials,
        isLoading
    }


}
