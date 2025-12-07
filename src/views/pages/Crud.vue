<template>
    <div>
        <Button label="New Product" icon="pi pi-plus" @click="openNew" />
        <DataTable ref="dt" :value="products" selectionMode="multiple" v-model:selection="selectedProducts">
            <Column selectionMode="multiple" style="width: 3em" />
            <Column field="name" header="Name" />
            <Column field="description" header="Description" />
            <Column field="price" header="Price" :body="formatCurrency" />
            <Column field="stock" header="Stock" />
            <Column header="Actions" :body="actionTemplate" />
        </DataTable>

        <!-- Create/Edit Product Modal -->
        <Dialog v-model:visible="productDialog" header="Product Details" modal class="p-fluid" :closable="false">
            <div class="field">
                <label for="name">Name</label>
                <InputText id="name" v-model="product.name" required autofocus />
            </div>
            <div class="field">
                <label for="description">Description</label>
                <InputText id="description" v-model="product.description" />
            </div>
            <div class="field">
                <label for="price">Price</label>
                <InputNumber id="price" v-model="product.price" mode="currency" currency="USD" />
            </div>
            <div class="field">
                <label for="stock">Stock</label>
                <InputNumber id="stock" v-model="product.stock" />
            </div>
            <div class="field">
                <label for="images">Images</label>
                <FileUpload mode="basic" name="images[]" accept="image/*" :customUpload="true"
                            @select="onFileSelect" :auto="false" chooseLabel="Select Images" multiple />
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveProduct" />
            </template>
        </Dialog>

        <!-- Delete Confirmation -->
        <Dialog v-model:visible="deleteProductDialog" header="Confirm" modal>
            <p>Are you sure you want to delete <b>{{ product.name }}</b>?</p>
            <template #footer>
                <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteProductDialog=false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteProduct" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ProductService } from '@/service/ProductService';
import { useToast } from 'primevue/usetoast';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';

const toast = useToast();

const dt = ref();
const products = ref([]);
const selectedProducts = ref([]);
const product = ref({});
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const submitted = ref(false);
const productFiles = ref([]);

// Load products
onMounted(loadProducts);

async function loadProducts() {
    try {
        const res = await ProductService.list();
        products.value = res.data.data || res.data;
    } catch (err) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load products', life: 3000 });
    }
}

// Open modal
function openNew() {
    product.value = {};
    productFiles.value = [];
    submitted.value = false;
    productDialog.value = true;
}

// Close modal
function hideDialog() {
    productDialog.value = false;
    submitted.value = false;
}

// Save product
async function saveProduct() {
    submitted.value = true;
    if (!product.value.name?.trim()) return;

    const formData = new FormData();
    formData.append('name', product.value.name);
    formData.append('description', product.value.description || '');
    formData.append('price', product.value.price ?? 0);
    formData.append('stock', product.value.stock ?? 0);

    productFiles.value.forEach(file => formData.append('images[]', file));

    try {
        if (product.value.id) {
            await ProductService.update(product.value.id, formData);
            toast.add({ severity: 'success', summary: 'Updated', life: 3000 });
        } else {
            await ProductService.create(formData);
            toast.add({ severity: 'success', summary: 'Created', life: 3000 });
        }

        productDialog.value = false;
        loadProducts();
        product.value = {};
        productFiles.value = [];
    } catch (err) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save product', life: 3000 });
    }
}

// Edit product
function editProduct(prod) {
    product.value = { ...prod };
    productFiles.value = [];
    productDialog.value = true;
}

// Delete product
async function deleteProduct() {
    try {
        await ProductService.delete(product.value.id);
        toast.add({ severity: 'success', summary: 'Deleted', life: 3000 });
        deleteProductDialog.value = false;
        loadProducts();
    } catch (err) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete', life: 3000 });
    }
}

// File select
function onFileSelect(event) {
    productFiles.value = event.files || event.target?.files || [];
}

// Format currency
function formatCurrency(row) {
    return row.price?.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
}

// Actions template
// function actionTemplate(row) {
//     return (
//         <>
//             <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-info" onClick={() => editProduct(row)} />
//             <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" onClick={() => { product.value = row; deleteProductDialog.value = true; }} />
//         </>
//     );
// }
</script>
