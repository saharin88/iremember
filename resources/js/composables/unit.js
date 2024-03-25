import {ref} from 'vue'

export default function useUnit() {

    const unit = ref({})
    const isLoading = ref(false)

    const getUnit = async (slug, params) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/unit/${slug}`, {params})
            .then(response => {
                unit.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        unit,
        getUnit,
        isLoading
    }

}
