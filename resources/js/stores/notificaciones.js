import { defineStore } from "pinia";
import { ref } from 'vue'; 
import axios from "@/axios.js";

export const UseNotificationStore = defineStore('notificaciones', () => {
  
    const count = ref(0);
    const notifications = ref([]);

    const getNotifications  = async () => {
        await axios.get('/notificaciones')
        .then(({data})=>{
            notifications.value = data.details;
            count.value = data.count
        })
    }

    return { count, notifications, getNotifications }
})