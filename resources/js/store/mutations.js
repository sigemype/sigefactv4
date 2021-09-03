import state from './state'
function writeLocal(variable, data) {
    if (data === undefined || data === 'undefined') {
        localStorage.removeItem(variable)
    } else {
        localStorage.setItem(variable, data)
    }
}
export default {
    setConfiguration(state, config) {
        if(config !== undefined && config.company !== undefined){
            state.company = config.company
            writeLocal('company', JSON.stringify(config.company))
        }
        if(config !== undefined && config.establishment !== undefined){
            state.establishment = config.establishment
            writeLocal('establishment', JSON.stringify(config.establishment))
        }
        state.config = config
        if(config === undefined){
            state.config=    JSON.parse(localStorage.getItem('config'))
            config = state.config;
        }
        writeLocal('config', JSON.stringify(config))
    },
    setEstablishment(state,establishment){
        writeLocal('establishment', JSON.stringify(establishment))
        return establishment;
    },
    setCompany(state,company){
        writeLocal('company', JSON.stringify(company))
        return company;
    },
    setCustomers(state, customers) {
        state.customers = customers
    },
    setCurrencys(state, currencys) {
        state.currencys = currencys
    },
    setOffices(state, offices) {
        state.offices = offices
    },
    setDocumentTypes(state, documentTypes) {
        state.documentTypes = documentTypes
        writeLocal('documentTypes', JSON.stringify(documentTypes))
    },
    setFiles(state, files) {
        state.files = files
    },
    setProcesses(state, processes) {
        state.processes = processes
    },
    setActions(state, actions) {
        state.actions = actions
    },

    setFile(state, file) {
        state.file = file
    },

    canShowColorDialog(state, showColorDialog) {
        state.showColorDialog = showColorDialog
    },
    setColor(state, color) {
        state.color = color
    },
    setLoadingSubmit(state, loading_submit) {
        if(loading_submit === undefined) loading_submit = false;
        state.loading_submit = loading_submit
    },
    setWorkers(state, workers) {
        state.workers = workers
    },
    setOffice(state, office) {
        state.office = office
    },
    setColors(state, colors) {
        if(colors === undefined ) colors = [];
        state.colors = colors
    },
    setCatItemStatus(state, CatItemStatus) {
        if(CatItemStatus === undefined ) CatItemStatus = [];
        state.CatItemStatus = CatItemStatus
    },
    setCatItemUnitsPerPackage(state, CatItemUnitsPerPackage) {
        if(CatItemUnitsPerPackage === undefined ) CatItemUnitsPerPackage = [];
        state.CatItemUnitsPerPackage = CatItemUnitsPerPackage
    },
    setCatItemMoldCavity(state, CatItemMoldCavity) {
        if(CatItemMoldCavity === undefined ) CatItemMoldCavity = [];
        state.CatItemMoldCavity = CatItemMoldCavity
    },
    setCatItemMoldProperty(state, CatItemMoldProperty) {
        if(CatItemMoldProperty === undefined ) CatItemMoldProperty = [];
        state.CatItemMoldProperty = CatItemMoldProperty
    },
    setCatItemUnitBusiness(state, CatItemUnitBusiness) {
        if(CatItemUnitBusiness === undefined ) CatItemUnitBusiness = [];
        state.CatItemUnitBusiness = CatItemUnitBusiness
    },
    setCatItemPackageMeasurement(state, CatItemPackageMeasurement) {
        if(CatItemPackageMeasurement === undefined ) CatItemPackageMeasurement = [];
        state.CatItemPackageMeasurement = CatItemPackageMeasurement
    },

    setCatItemProductFamily(state, CatItemProductFamily) {
        if(CatItemProductFamily === undefined ) CatItemProductFamily = [];
        state.CatItemProductFamily = CatItemProductFamily
    },
    setDeb(state,debug) {
        if(debug === undefined ) debug = {};
        state.deb = debug
    },
    setFromPos(state,form_pos) {
        if(form_pos === undefined ) form_pos = {};
        writeLocal('form_pos', JSON.stringify(form_pos))
        state.form_pos = form_pos
    },
    setPaymentMethodTypes(state,payment_method_types) {
        if(payment_method_types === undefined ) payment_method_types = {};
        state.payment_method_types = payment_method_types
    },

    setExtraColors(state, extra_colors) {
        if(extra_colors === undefined ) extra_colors = [];
        state.extra_colors = extra_colors
    },
    setExtraCatItemUnitsPerPackage(state, extra_CatItemUnitsPerPackage) {
        if(extra_CatItemUnitsPerPackage === undefined ) extra_CatItemUnitsPerPackage = [];
        state.extra_CatItemUnitsPerPackage = extra_CatItemUnitsPerPackage
    },
    setExtraCatItemMoldProperty(state, extra_CatItemMoldProperty) {
        if(extra_CatItemMoldProperty === undefined ) extra_CatItemMoldProperty = [];
        state.extra_CatItemMoldProperty = extra_CatItemMoldProperty
    },
    setExtraCatItemUnitBusiness(state, extra_CatItemUnitBusiness) {
        if(extra_CatItemUnitBusiness === undefined ) extra_CatItemUnitBusiness = [];
        state.extra_CatItemUnitBusiness = extra_CatItemUnitBusiness
    },
    setExtraCatItemStatus(state, extra_CatItemStatus) {
        if(extra_CatItemStatus === undefined ) extra_CatItemStatus = [];
        state.extra_CatItemStatus = extra_CatItemStatus
    },
    setExtraCatItemPackageMeasurement(state, extra_CatItemPackageMeasurement) {
        if(extra_CatItemPackageMeasurement === undefined ) extra_CatItemPackageMeasurement = [];
        state.extra_CatItemPackageMeasurement = extra_CatItemPackageMeasurement
    },
    setExtraCatItemMoldCavity(state, extra_CatItemMoldCavity) {
        if(extra_CatItemMoldCavity === undefined ) extra_CatItemMoldCavity = [];
        state.extra_CatItemMoldCavity = extra_CatItemMoldCavity
    },
    setExtraCatItemProductFamily(state, extra_CatItemProductFamily) {
        if(extra_CatItemProductFamily === undefined ) extra_CatItemProductFamily = [];
        state.extra_CatItemProductFamily = extra_CatItemProductFamily
    },


    setRecords(state, records) {
        if(records === undefined ) records = [];
        state.records = records
    },
    setPagination(state, pagination) {
        if(pagination === undefined ) pagination = {
            current_page : 1,
            total : 0,
            per_page : 25,
        };
        state.pagination = pagination
    },
    setTypeUser(state, userType) {
        state.userType = userType
        writeLocal('userType', userType)
    },
    setWarehouses(state, warehouses) {
        state.warehouses = warehouses
    },
    setAllItems(state, all_items) {
        if(state.all_items !== undefined) {
            let temp_item = [state.all_items, ...all_items]
            temp_item = temp_item.filter((item, index, self) =>
                index === self.findIndex((t) => (
                    t.id === item.id
                ))
            )
            state.all_items = temp_item
        }else{
            state.all_items = all_items
        }
    },
}
