import { defineStore } from "pinia";
import { ref } from 'vue'; 
import axios from "@/axios.js";

export const UseNotificationStore = defineStore('notificaciones', () => {
    // state: () => ({
    //     count: 0,
    //     notifications: []
    // }),
    // getters: {
    //     amountNotifications: (state) => state.count = notifications.length
    // }
    const count = ref(0);
    const notifications = ref([]);

    const getNotifications  = async () => {
        axios.get('/notificaciones')
        .then(({data})=>{
            notifications.value = data.details;
            count.value = data.count
        })
    }

    return { count, notifications, getNotifications }
})