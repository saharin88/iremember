import {ref} from 'vue'

export default function useBattlePeople() {

        const battlePeople = ref([])
        const isLoading = ref(false)

        const getBattlePeople = async (slug) => {
            if (isLoading.value) return;
            isLoading.value = true
            return axios.get(`/api/v1/battle/${slug}/people`)
                .then(response => {
                    battlePeople.value = response.data;
                })
                .finally(() => {
                    isLoading.value = false
                })
        }

        return {
            battlePeople,
            getBattlePeople,
            isLoading
        }
}
