<script setup>
import { ProductService } from '@/service/Product';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

onMounted(() => {
    ProductService.list().then((response) => {
        products.value = response.data.data ?? response.data;
    });
});

const toast = useToast();
const dt = ref();
const products = ref();
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref({});
const selectedProducts = ref();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);
const statuses = ref([
    { label: 'Active', value: 'Active' },
    { label: 'Deactivate', value: 'Deactivate' }
]);

const loadProducts = async () => {
    try {
        const response = await ProductService.list();
        products.value = response.data.data ?? response.data;
    } catch (err) {
        console.error('Failed to load products', err);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load products' });
    }
};

function formatCurrency(value) {
    if (value) return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    return;
}

function openNew() {
    product.value = {};
    submitted.value = false;
    productDialog.value = true;
}

function hideDialog() {
    productDialog.value = false;
    submitted.value = false;
}

async function saveProduct() {
    submitted.value = true;

    try {
        // Prepare FormData
        const formData = new FormData();
        formData.append('name', product.value.name);
        formData.append('description', product.value.description);
        formData.append('status', product.value.status?.value || ''); // only the value
        formData.append('price', product.value.price);
        formData.append('stock', product.value.stock);

        // Append thumbnail if user selected a file
        if (product.value.thumbnailFile) {
            formData.append('thumbnail', product.value.thumbnailFile);
        }

        if (product.value.id) {
            // Update product
            formData.append('_method', 'PUT'); // Laravel expects this for PUT via POST
            await ProductService.update(product.value.id, formData);
            toast.add({ severity: 'success', summary: 'Updated', detail: 'Product updated successfully' });
        } else {
            // Create new product
            await ProductService.create(formData);
            toast.add({ severity: 'success', summary: 'Created', detail: 'Product created successfully' });
        }

        productDialog.value = false;
        loadProducts(); // reload list after save
    } catch (err) {
        console.error(err);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save product' });
    }
}

async function deleteProduct() {
    try {
        if (!product.value.id) return;

        await ProductService.delete(product.value.id); // call API to delete
        toast.add({ severity: 'success', summary: 'Deleted', detail: `Product "${product.value.name}" deleted successfully` });

        deleteProductDialog.value = false;
        product.value = {};

        await loadProducts(); // reload product list from server
    } catch (err) {
        console.error(err);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete product' });
    }
}

function editProduct(prod) {
    product.value = { ...prod };
    productDialog.value = true;
}

function confirmDeleteProduct(prod) {
    product.value = prod;
    deleteProductDialog.value = true;
}

function findIndexById(id) {
    let index = -1;
    for (let i = 0; i < products.value.length; i++) {
        if (products.value[i].id === id) {
            index = i;
            break;
        }
    }

    return index;
}

function createId() {
    let id = '';
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (var i = 0; i < 5; i++) {
        id += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return id;
}

function exportCSV() {
    dt.value.exportCSV();
}

function confirmDeleteSelected() {
    deleteProductsDialog.value = true;
}

function deleteSelectedProducts() {
    products.value = products.value.filter((val) => !selectedProducts.value.includes(val));
    deleteProductsDialog.value = false;
    selectedProducts.value = null;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Products Deleted', life: 3000 });
}

function getStatusLabel(status) {
    switch (status) {
        case 'Active':
            return 'success';

        case 'Deactivate':
            return 'warn';

        default:
            return null;
    }
}

function handleThumbnailChange(event) {
    const file = event.target.files[0]; // Get the actual file
    if (file) {
        product.value.thumbnailFile = file; // Store file for upload

        // Optional: Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            product.value.thumbnail = e.target.result; // preview in UI
        };
        reader.readAsDataURL(file);
    }
}

</script>

<template>
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:selection="selectedProducts"
                :value="products"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Manage Products</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column field="name" header="Name" sortable style="min-width: 16rem"></Column>
                <Column header="Image">
                    <template #body="slotProps">
                        <img :src="`${slotProps.data.thumbnail}`" :alt="slotProps.data.thumbnail" class="rounded" style="width: 64px" />
                    </template>
                </Column>
                <Column field="price" header="Price" sortable style="min-width: 8rem">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.price) }}
                    </template>
                </Column>
                <Column field="status" header="Status" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status" :severity="getStatusLabel(slotProps.data.status)" />
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" :modal="true">
            <div class="flex flex-col gap-6">
                <!-- Show current thumbnail image -->
                <div v-if="product.thumbnail">
                    <img :src="product.thumbnail" alt="Product Thumbnail" class="block m-auto pb-4" style="max-width: 150px;" />
                </div>
                <!-- File input to upload new thumbnail -->
                <div>
                    <label for="thumbnail" class="block font-bold mb-3">Thumbnail</label>
                    <input
                        type="file"
                        id="thumbnail"
                        accept="image/*"
                        @change="handleThumbnailChange"
                        class="p-inputtext p-component"
                    />
                    <small v-if="submitted && !product.thumbnail" class="text-red-500">Thumbnail is required.</small>
                </div>

                <div>
                    <label for="name" class="block font-bold mb-3">Name</label>
                    <InputText id="name" v-model.trim="product.name" required="true" autofocus :invalid="submitted && !product.name" fluid />
                    <small v-if="submitted && !product.name" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="description" class="block font-bold mb-3">Description</label>
                    <Textarea id="description" v-model="product.description" required="true" rows="3" cols="20" fluid />
                </div>
                <div>
                    <label for="status" class="block font-bold mb-3"> Status</label>
                    <Select id="status" v-model="product.status" :options="statuses" optionLabel="label" placeholder="Select a Status" fluid></Select>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label for="price" class="block font-bold mb-3">Price</label>
                        <InputNumber id="price" v-model="product.price" mode="currency" currency="USD" locale="en-US" fluid />
                    </div>
                    <div class="col-span-6">
                        <label for="stock" class="block font-bold mb-3">Stock</label>
                        <InputNumber id="stock" v-model="product.stock" integeronly fluid />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle text-3xl!" />
                <span v-if="product"
                >Are you sure you want to delete <b>{{ product.name }}</b
                >?</span
                >
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle text-3xl!" />
                <span v-if="product">Are you sure you want to delete the selected products?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
            </template>
        </Dialog>
    </div>
</template>
