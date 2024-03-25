import {ref} from 'vue'

export default function useAward() {

        const award = ref({})
        const isLoading = ref(false)

        const getAward = async (slug) => {
            if (isLoading.value) return;
            isLoading.value = true
            return axios.get(`/api/v1/award/${slug}`)
                .then(response => {
                    award.value = response.data.data;
                })
                .finally(() => {
                    isLoading.value = false
                })
        }

        return {
            award,
            getAward,
            isLoading
        }
}
