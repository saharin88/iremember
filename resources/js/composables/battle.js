import {ref} from 'vue'

export default function useBattle() {

        const battle = ref({})
        const isLoading = ref(false)

        const getBattle = async (slug, queryParams = {}) => {
            if (isLoading.value) return;
            isLoading.value = true
            return axios.get(`/api/v1/battle/${slug}`)
                .then(response => {
                    battle.value = response.data.data;
                })
                .finally(() => {
                    isLoading.value = false
                })
        }

        return {
            battle,
            getBattle,
            isLoading
        }
}
