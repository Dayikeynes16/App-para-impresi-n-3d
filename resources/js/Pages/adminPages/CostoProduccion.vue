<template>
    <v-container>
        <v-row>
            <v-col cols="4">
                <v-card>
                    <v-card-title>
                        Precio por minuto de impresión
                    </v-card-title>
                   
                    <v-card-text>
                        <v-text-field
                        type="number"
                        v-model="printing.price"
                        label="Precio"
                        variant="outlined">
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                       
                        <v-btn block @click="updatePrinting()" variant="outlined">
                            Guardar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-card-title>
                        Costo de envío
                    </v-card-title>
                    
                    <v-card-text>
                        <v-text-field
                        type="number"
                        v-model="shipment.price"
                        variant="outlined"
                        label="Precio">
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        
                        <v-btn block @click="updateShipment()" variant="outlined">
                            Guardar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-card-title>
                        Factor de conversión
                    </v-card-title>
                   
                    <v-card-text>
                        <v-text-field
                        type="number"
                        v-model="conversion.conversion"
                        variant="outlined"
                        label="¿cuantas veces es mas rapida nuestra impresora?">
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn block @click="updateConversion()" variant="outlined">
                            Guardar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>    
</template>

<script setup>
import axios from "@/axios";
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
const printing = ref({
    id: null,
    price: 0
})
const shipment = ref({
    id: null,
    price: 0
})
const conversion = ref({
    id: 1,
    conversion: 0
})
const open = (messages, success) => {
    if (success === true){
        ElMessage({
        message: messages,
        type: 'success'})
    } else {
        ElMessage.error( messages)
    }
  
}

const getPrice = async () => {
    axios.get('/precio-impresion')
    .then(({data}) => {
        printing.value.price = data.data.precio
        printing.value.id = data.data.id
    })
}

const getConversion = async () => {
    axios.get('/factor-conversion')
    .then((data) => {
        conversion.value.conversion = data.data.data.conversion
    })
}

const getShip = async () => {
    axios.get('/precio-envio')
    .then(({data}) => {
        shipment.value.price = data.data.precio
        shipment.value.id = data.data.id
    })
    .catch(() => {
        open()
    })
}

const updateShipment = () => {
    axios.put(`/precio-envio/${shipment.value.id}`,{ precio: shipment.value.price})
    .then(({data}) => {
     open('Costo de envio actualizado correctamente.', true)
    })
    .catch((error) => {
        open('Verifica el precio ingresado', false)
    })
}

const updateConversion = () => {
    axios.put(`/factor-conversion/${conversion.value.id}`,{ conversion: conversion.value.conversion})
    .then(({data}) => {
     open('Actualizado correctamente.', true)
    })
    .catch((error) => {
        open('Ocurrio un problema :c', false)
    })
}

const updatePrinting = () => {
    axios.put(`/precio-impresion/${printing.value.id}`,{ precio: printing.value.price})
    .then(({data}) => {
     open('Costo de impresión actualizado correctamente.', true)
    })
    .catch((error) => {
        open('Verifica el precio ingresado', false)
    })
    
}



onMounted(() => {
    getPrice()
    getShip()
    getConversion()
})
</script>