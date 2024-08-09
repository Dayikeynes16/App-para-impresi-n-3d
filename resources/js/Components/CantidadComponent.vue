<template>
    <v-row align="center" class="mt-10">
        <v-col cols="2" class="text-center">
            <v-select
                label="Elementos por pagina:"
                v-model="paginate.perPage"
                :items="[10, 25, 50, 100]"
                density="compact"
                variant="outlined"
                hide-details="false"
                @update:model-value="emitCambios()"
            ></v-select>
        </v-col>
        <v-col cols="2"></v-col>
        <v-col cols="4" class="text-center">
            <v-pagination
                variant="outlined"
                v-model="paginate.page"
                :length="paginate.pageCount"
                :size="25"
                @input="emitCambios(page, perPage)"
            ></v-pagination>
        </v-col>
        <v-col cols="4" class="text-center">
            <div variant="outlined">
                <v-card-subtitle>
                    Mostrando: {{ paginacion.from }} a {{ paginacion.to }} de {{ paginacion.totalItems }} elementos.
                </v-card-subtitle>
            </div>
        </v-col>
    </v-row>
</template>

<script setup>
import { defineEmits, ref, defineProps, watch } from 'vue';

const props = defineProps({
    paginate: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['cambios']);

const paginacion = ref({});

const emitCambios = (page, perPage) => {
    emit('cambios', {
        page,
        perPage
    });
};

watch(() => props.paginate, (newVal) => {
    paginacion.value = newVal;
});
</script>
