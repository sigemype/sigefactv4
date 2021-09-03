import state from './state'

function readStorageData(variable, json = false) {
    let w = localStorage.getItem(variable)
    if (w === 'undefined') {
        w = null;
    }
    if (json === true) {
        w = JSON.parse(w)
    }
    return w;
}

export default {
    loadConfiguration(store) {
        state.config = readStorageData('config', true);
        state.customers = readStorageData('customers', true);
        state.userType = readStorageData('userType', false);
        state.company = readStorageData('company', true);
        state.establishment = readStorageData('establishment', true);
        if (state.deb === undefined) state.deb = {};
        if (state.colors === undefined) state.colors = [];
        if (state.CatItemMoldProperty === undefined) state.CatItemMoldProperty = [];
        if (state.CatItemUnitBusiness === undefined) state.CatItemUnitBusiness = [];
        if (state.CatItemStatus === undefined) state.CatItemStatus = [];
        if (state.CatItemProductFamily === undefined) state.CatItemProductFamily = [];
        if (state.CatItemPackageMeasurement === undefined) state.CatItemPackageMeasurement = [];
        if (state.CatItemUnitsPerPackage === undefined) state.CatItemUnitsPerPackage = [];
        if (state.CatItemMoldCavity === undefined) state.CatItemMoldCavity = [];
        if (state.extra_colors === undefined) state.extra_colors = [];
        if (state.extra_CatItemUnitsPerPackage === undefined) state.extra_CatItemUnitsPerPackage = [];
        if (state.extra_CatItemMoldProperty === undefined) state.extra_CatItemMoldProperty = [];
        if (state.extra_CatItemUnitBusiness === undefined) state.extra_CatItemUnitBusiness = [];
        if (state.extra_CatItemStatus === undefined) state.extra_CatItemStatus = [];
        if (state.extra_CatItemPackageMeasurement === undefined) state.extra_CatItemPackageMeasurement = [];
        if (state.extra_CatItemMoldCavity === undefined) state.extra_CatItemMoldCavity = [];
        if (state.extra_CatItemProductFamily === undefined) state.extra_CatItemProductFamily = [];
        if (state.loading_submit === undefined) state.loading_submit = false;
        if (state.payment_method_types === undefined) state.payment_method_types = [];
        if (state.form_pos === undefined) state.form_pos = {};
        // Previenete limite de almacen exedido
        /*
        5MB per app per browser. According to the HTML5 spec, this limit can be increased by the user when needed;
         however, only a few browsers support this
         */
        // alternativa posible sessionStorage
        localStorage.removeItem('customers');
        localStorage.removeItem('offices');
        localStorage.removeItem('files');
        localStorage.removeItem('processes');
        localStorage.removeItem('actions');
        localStorage.removeItem('workers');
        localStorage.removeItem('warehouses');
        localStorage.removeItem('all_items');
    },
    clearExtraInfoItem() {
        state.extra_colors = [];
        state.extra_CatItemUnitsPerPackage = [];
        state.extra_CatItemMoldProperty = [];
        state.extra_CatItemUnitBusiness = [];
        state.extra_CatItemStatus = [];
        state.extra_CatItemPackageMeasurement = [];
        state.extra_CatItemMoldCavity = [];
        state.extra_CatItemProductFamily = [];
    },
    loadWarehouses(store) {
        if (state.warehouses === undefined) state.warehouses = [];
        // state.warehouses = readStorageData('warehouses', true)
    },
    loadOffices(store) {
        if (state.offices === undefined) state.offices = [];
        // state.offices = readStorageData('offices', true)
    },
    loadCustomers(store) {
        if (state.customers === undefined) state.customers = [];
        // state.customers = readStorageData('customers', true)
    },
    loadCurrencys(store) {
        if (state.currencys === undefined) state.currencys = [];
        // state.customers = readStorageData('customers', true)
    },
    loadActions(store) {
        if (state.actions === undefined) state.actions = [];
        // state.actions = readStorageData('actions', true)
    },
    loadProcesses(store) {
        if (state.processes === undefined) state.processes = [];
        // state.processes = readStorageData('processes', true)
    },
    loadFiles(store) {
        if (state.files === undefined) state.files = [];
        // state.files = readStorageData('files', true)
    },
    loadDocumentTypes(store) {
        state.documentTypes = readStorageData('documentTypes', true)
    },
    loadWorkers(store) {
        if (state.workers === undefined) state.workers = [];
        // state.workers = readStorageData('workers', true)
    },
    loadPos(store) {
        state.form_pos = readStorageData('form_pos', true);
        if (state.form_pos === undefined) state.form_pos = {};
    },
    loadAllItems(store) {
        if (state.all_items === undefined) state.all_items = [];
        // state.all_items = getUniqueArray(readStorageData('all_items', true), ['id'])
    },
    loadCompany(store) {
        let t = readStorageData('company', true)
        if (t !== null) {
            state.company = t
        } else {
            state.company = {
                logo: null,
                name: '',
            };
        }
    },
    loadEstablishment(store) {
        let t = readStorageData('establishment', true)
        if (t !== null) {
            state.establishment = t
        } else {
            state.establishment = {
                address: '-',
                district: {description: ''},
                province: {description: ''},
                department: {description: ''},
                country: {description: ''},
                telephone: '-',
                email: null,
            };
        }
    },
}
